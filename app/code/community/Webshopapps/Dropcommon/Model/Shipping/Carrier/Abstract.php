<?php
/* UsaShipping
 *
 * User        karen
 * Date        1/19/14
 * Time        12:29 AM
 * @category   Webshopapps
 * @package    Webshopapps_ExtnName
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

abstract class Webshopapps_Dropcommon_Model_Shipping_Carrier_Abstract
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'dropship';
    protected static $_debug;

    protected $_shippingResults;
    protected $_shippingWarehouse;
    protected $_request;
    protected $_handlingCount;
    protected $_origUSPSOversized;
    protected $_sepItemsResults;
    protected $_handlingType;
    protected $_handlingAction;
    protected $_customError = "";


    /**
     * Used for merged rates
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return $this->collectDropshipRates($request);

    }

    public function collectDropshipRates(Mage_Shipping_Model_Rate_Request $request,&$warehouseResults=array(),$splitRates=false)
    {
        if (!Mage::helper('dropcommon')->calculateDropshipRates()) {
            return false;
        }
        $this->_shippingResults = array();
        $this->_shippingWarehouse = array();
        $this->_request = $request;
        $this->_origUSPSOversized = $request->getUspsSize();

        $cartPackageValue = $request->getPackageValue();
        $cartPackageValueWithDiscount = $request->getPackageValueWithDiscount();
        $cartPackagePhysicalValue = $request->getPackagePhysicalValue();
        $cartFreeShipping = $request->getFreeShipping();

        $this->_request->setCartPhysicalValue($cartPackagePhysicalValue);
        $this->_request->setCartValue($cartPackageValue);
        $this->_request->setCartValueWithDiscount($cartPackageValueWithDiscount);

        // get all items
        $items = $this->_request->getAllItems();
        self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');
        $this->_handlingType = Mage::getStoreConfig('carriers/dropship/handling_type');
        $this->_handlingAction = Mage::getStoreConfig('carriers/dropship/handling_action');

        // get warehouses
        $warehouseDetails = Mage::helper('dropcommon/shipcalculate')->getWarehouseDetails(
            $this->_request->getDestCountryId(),
            $this->_request->getDestRegionCode(),
            $this->_request->getDestPostcode(),
            $items
        );

        if ($this->getConfigFlag('handling_action') == 'W') {
            $this->_handlingCount = count($warehouseDetails);
        } else {
            $this->_handlingCount = 1;
        }

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Entered Dropship Carrier Logic', '');
        }
        if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvZHJvcHNoaXAvc2hpcF9vbmNl',
            'aG9sZGluZ3Vw', 'Y2FycmllcnMvZHJvcHNoaXAvc2VyaWFs')
        ) {
            return false;
        }
        if (count($warehouseDetails) < 2) {
            reset($warehouseDetails);
            $warehouse = key($warehouseDetails);
            $results = $this->processSingleWarehouse($warehouse);

            $result = $this->applyFreeText($results);

            return $result;
        }
        $useParent = Mage::getStoreConfigFlag('carriers/dropship/use_parent');

        $numWarehouses = count($warehouseDetails);
        foreach ($warehouseDetails as $warehouse => $warehouseItems) {


            // multiple warehouses - split out items
            $subTotal = 0;
            $qtyTotal = 0;
            $freeTotalWeight = 0;
            $weightTotal = 0;
            $subTotalInclTax = 0;
            foreach ($warehouseItems as $item) {
                $weight = 0;
                $qty = 0;
                $price = 0;
                $priceInclTax = 0;
                $freeMethodWeight = 0;
                $temp = array();
                if (!Mage::helper('wsacommon/shipping')->getItemInclFreeTotals($item, $weight, $qty, $price, $freeMethodWeight,
                    $useParent, false, $temp, false, $cartFreeShipping, false, false, $priceInclTax)
                ) {
                    continue;
                }
                $subTotal += $price;
                $qtyTotal += $qty;
                $subTotalInclTax += $priceInclTax;
                $freeTotalWeight += $freeMethodWeight;
                $weightTotal += $weight;
            }
            $this->_request->setFreeShipping($cartFreeShipping); // always reset as Free Shipping Carrier doesnt!

            $this->_request->setPackagePhysicalValue($subTotal);
            $this->_request->setPackageValue($subTotal);
            $this->_request->setPackageValueWithDiscount($subTotal);
            $this->_request->setBaseSubtotalInclTax($subTotalInclTax);

            $this->_request->setAllItems($warehouseItems);
            $this->_request->setPackageWeight($this->getCalculatedWeight($weightTotal));
            $this->_request->setPackageQty($qtyTotal);
            $this->_request->setFreeMethodWeight($freeTotalWeight);

            if (self::$_debug) {
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Weight Total', $weightTotal);
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Price Total', $subTotal);
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Qty Total', $qtyTotal);
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'SubtotalIncl Total', $subTotalInclTax);
            }

            if (!$this->setReqWarehouseDetails($warehouse)) {
                if (self::$_debug) {
                    Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Cant get any carriers to call', '');
                }
                return $this->getErrorResult();
            }

            // get rates for warehouse
            $this->collectWarehouseRates($warehouse, $numWarehouses);
        }
        $this->_request->setDropshipCollecting(false);

        if(count($this->_shippingResults) < $numWarehouses) {
            $this->_shippingResults = $this->getErrorResult();
        }

        if ($splitRates) {
            $warehouseResults = $this->_shippingWarehouse;
            return $this->_shippingResults;
        }


        if (empty($this->_shippingResults)) {
            $result = $this->getErrorResult();
        } else {
            // multiple rates returned
            // lets merge
            $result = $this->collectMergedRates();
        }
        $result = $this->applyFreeText($result);

        if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Adminshipping')) {
            $adminShippingResult = Mage::getModel('dropcommon/shipping_shipping') //DROP-112
            ->collectAdminShippingRates($this->_request)
                ->getResult();

            if ($result instanceof Mage_Shipping_Model_Rate_Result_Error) { //DROP-128
                return $result;
            }

            $result->append($adminShippingResult);
        }

        return $result;
    }

    protected function applyFreeText($result)
    {
        $rates = $result->getAllRates();

        if (empty($rates)) {
            return $result;
        }

        $newResult = Mage::getModel('shipping/rate_result');

        foreach ($rates as $rate) {
            if ($rate->getPrice() == 0 && $this->getConfigData('free_shipping_text') != '') {
                $rate->setMethodTitle($this->getConfigData('free_shipping_text'));
                $newResult->append($rate);
            } else {
                $newResult->append($rate);
            }
        }

        return $newResult;
    }


    /**
     * collect rates for each shipping carrier for particular warehouse
     * @param unknown_type $request
     * @param unknown_type $warehouse
     */
    protected function collectWarehouseRates($warehouse, $numWarehouses)
    {
        $wsaHelper = Mage::helper('wsacommon');
        $shippingOverrideEnabled = false;

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Collecting Rates for warehouse:', $warehouse);
        }
        if ($wsaHelper->isModuleEnabled('Webshopapps_Shippingoverride2', 'shipping/shippingoverride2/active')) {
            $result = $this->calloutCollectRates($this->_request);
            $shippingOverrideEnabled = true;
        } else {
            $result = Mage::getModel('dropcommon/shipping_shipping')
                ->collectRates($this->_request)
                ->getResult();
        }

        if(count($result->getAllRates()) < 1) {
            $result->append($this->getErrorResult());
        }

        if ($result) {
            if ($wsaHelper->isModuleEnabled('Webshopapps_Handlingmatrix', 'shipping/handlingmatrix/active')) {
                $handlingMatrixModel = Mage::getModel('handlingmatrix/handlingmatrix');
                $handlingMatrixModel->addHandlingCosts($this->_request, $result);
            }
            if ($wsaHelper->isModuleEnabled('Webshopapps_Handlingproduct', 'shipping/handlingproduct/active') &&
                !$wsaHelper->isModuleEnabled('Webshopapps_Shipusa', 'shipping/shipusa/active')
            ) {
                $handlingProductModel = Mage::getModel('handlingproduct/handlingproduct');
                $handlingProductModel->addHandlingCosts($this->_request, $result);
            }
            if ($wsaHelper->isModuleEnabled('Webshopapps_Insurance', 'shipping/insurance/active') && !$shippingOverrideEnabled) {//SI-1
                $result = Mage::helper('insurance')->getInsuranceResults($this->_request, $result);
            }
            if (self::$_debug) {
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Results:', $result);
            }
            if (Mage::getStoreConfig('carriers/dropship/use_cart_price') && $numWarehouses > 1) {
                $this->_getSharedRate($result, $numWarehouses);
            }
            // raise event so that rates can be manipulated if required
            Mage::dispatchEvent('warehouse_results',array(
                'warehouse'=> $warehouse,
                'result'=> $result));

            $this->_shippingResults[] = $result;
            $this->_shippingWarehouse[] = $warehouse;

        } else {
            return false;
        }
        return true;

    }

    protected function getCalculatedWeight($weight)
    {
        $weightAdjPercentage = $this->getConfig('weight_percentage');
        if (!empty($weightAdjPercentage)) {
            $calculatedWeight = $weight * (1 + ($weightAdjPercentage / 100));
        } else {
            $calculatedWeight = $weight;
        }

        return $calculatedWeight;
    }

    protected function _getSharedRate(&$result, $numWarehouses)
    {
        foreach ($result->getAllRates() as $rate) {
            if ($rate['carrier'] == 'premiumrate' && $rate['price'] > 0) {
                $rate['price'] = round($rate['price'] / $numWarehouses, 2, PHP_ROUND_HALF_DOWN);
            }
        }
        return true;
    }

    protected function processSingleWarehouse($warehouse)
    {
        $wsaHelper = Mage::helper('wsacommon');
        $shippingOverrideEnabled = false;

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Processing single warehouse, warehouse id is:', $warehouse);
        }

        $this->_request->setPackageWeight($this->getCalculatedWeight($this->_request->getPackageWeight()));

        if ($warehouse == '' || !$this->setReqWarehouseDetails($warehouse)) {
            if (self::$_debug) {
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Cant get any carriers to call for warehouse', $warehouse);
            }
            return $this->getErrorResult();
        }

        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shippingoverride2', 'shipping/shippingoverride2/active')) {
            $result = $this->calloutCollectRates($this->_request);
            $shippingOverrideEnabled = true;
        } else {
            $result = Mage::getModel('dropcommon/shipping_shipping')
                ->collectRates($this->_request)
                ->getResult();
        }

        if (empty($result) || count($result->getAllRates()) < 1) {
            $result = $this->getErrorResult();
        } else {
            $this->applyHandlingFees($result);
        }
        $this->_request->setDropshipCollecting(false);
        if ($wsaHelper->isModuleEnabled('Webshopapps_Handlingmatrix', 'shipping/handlingmatrix/active')) {
            $handlingMatrixModel = Mage::getModel('handlingmatrix/handlingmatrix');
            $handlingMatrixModel->addHandlingCosts($this->_request, $result);
        }
        if ($wsaHelper->isModuleEnabled('Webshopapps_Handlingproduct', 'shipping/handlingproduct/active') &&
            !$wsaHelper->isModuleEnabled('Webshopapps_Shipusa', 'shipping/shipusa/active')
        ) {
            $handlingProductModel = Mage::getModel('handlingproduct/handlingproduct');
            $handlingProductModel->addHandlingCosts($this->_request, $result);
        }
        if ($wsaHelper->isModuleEnabled('Webshopapps_Insurance', 'shipping/insurance/active') && !$shippingOverrideEnabled) {//SI-1
            return Mage::helper('insurance')->getInsuranceResults($this->_request, $result);
        }

        // raise event so that rates can be manipulated if required
        Mage::dispatchEvent('warehouse_results',array(
            'warehouse'=> $warehouse,
            'result'=> $result));

        return $result;
    }

    protected function applyHandlingFees(&$result)
    {

        $rates = $result->getAllRates();
        $result->reset();

        foreach ($rates as $key => $rate) {
            $rate->setPrice($this->getFinalPriceWithHandlingFee($rate->getPrice()));
            $result->append($rate);
        }
    }

    /**
     * Doesnt support insurance
     *
     * @param $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function calloutCollectRates($request)
    {
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shippingoverride2', 'shipping/shippingoverride2/active')) {
            $override2Request = clone $request;

            // else want to restrict what's sent to the carriers
            $override2ResourceModel = Mage::getResourceModel('shippingoverride2/shippingoverride2');
            $exclusionList = array();
            $weightIncArr = array();
            $minPriceArr = array();

            // Note: weightIncArr and do not work inside dropship
            $override2Groups = $override2ResourceModel->getNewRate($request, $exclusionList, $this->_customError, $weightIncArr,$minPriceArr);
            if (empty($override2Groups) && count($exclusionList) < 1) {
                return Mage::getModel('dropcommon/shipping_shipping')->collectRates($request)->getResult();
            }

            // now do override logic
            return Mage::getModel('shippingoverride2/shipping_shipping')
                ->collectSpecialRates($override2Request, $request, $exclusionList, $minPriceArr, $override2Groups, $this->_customError)
                ->getResult();
        }

        return null;
    }

    protected function _populateRequestObj($warehouse,$warehouseDetails) {
        if ($warehouse != '') {
            $this->_request
                ->setOrigCountry($warehouseDetails->getCountry())
                ->setOrigRegionCode($warehouseDetails->getRegion())
                ->setOrigCity($warehouseDetails->getCity())
                ->setOrigPostcode($warehouseDetails->getZipcode());


            $this->_request->setOrig(true);

            if ($warehouseDetails->getFedexAccountId() != '') {
                $this->_request->setFedexAccount($warehouseDetails->getFedexAccountId());
            }

            if ($warehouseDetails->getFedexsoapKey() != '') {
                $this->_request->setFedexSoapKey($warehouseDetails->getFedexsoapKey());
            }
            if ($warehouseDetails->getFedexsoapPassword() != '') {
                $this->_request->setFedexPassword($warehouseDetails->getFedexsoapPassword());
            }
            if ($warehouseDetails->getFedexsoapMeterNumber() != '') {
                $this->_request->setFedexMeterNumber($warehouseDetails->getFedexsoapMeterNumber());
            }
            if ($warehouseDetails->getFedexsoapAllowedMethods() != '') {
                $this->_request->setFedexsoapAllowedMethods($warehouseDetails->getFedexsoapAllowedMethods());
            }
            if ($warehouseDetails->getFedexsoapSpecifyAllowedMethods() != '') {
                $this->_request->setFedexsoapSpecifyAllowedMethods($warehouseDetails->getFedexsoapSpecifyAllowedMethods());
            }

            if ($warehouseDetails->getFedexsoapHubId() != '') {
                $this->_request->setFedexsoapHubId($warehouseDetails->getFedexsoapHubId());
            }

            if ($warehouseDetails->getFedexfreightAccountId() != '') {
                $this->_request->setFedexfreightAccountId($warehouseDetails->getFedexfreightAccountId());
            }
            if ($warehouseDetails->getFedexfreightKey() != '') {
                $this->_request->setFedexfreightKey($warehouseDetails->getFedexfreightKey());
            }
            if ($warehouseDetails->getFedexfreightPassword() != '') {
                $this->_request->setFedexfreightPassword($warehouseDetails->getFedexfreightPassword());
            }
            if ($warehouseDetails->getFedexfreightMeterNumber() != '') {
                $this->_request->setFedexfreightMeterNumber($warehouseDetails->getFedexfreightMeterNumber());
            }
            if ($warehouseDetails->getFedexfreightStreet() != '') {
                $this->_request->setFedexfreightStreet($warehouseDetails->getFedexfreightStreet());
            }
            if ($warehouseDetails->getFedexfreightCity() != '') {
                $this->_request->setFedexfreightCity($warehouseDetails->getFedexfreightCity());
            }
            if ($warehouseDetails->getFedexfreightZipcode() != '') {
                $this->_request->setFedexfreightZipcode($warehouseDetails->getFedexfreightZipcode());
            }
            if ($warehouseDetails->getFedexfreightState() != '') {
                $this->_request->setFedexfreightState($warehouseDetails->getFedexfreightState());
            }
            if ($warehouseDetails->getFedexfreightCountry() != '') {
                $this->_request->setFedexfreightCountry($warehouseDetails->getFedexfreightCountry());
            }
            if ($warehouseDetails->getFedexfreightFreightRole() != '') {
                $this->_request->setFedexfreightFreightRole($warehouseDetails->getFedexfreightFreightRole());
            }
            if ($warehouseDetails->getFedexfreightPaymentType() != '') {
                $this->_request->setFedexfreightPaymentType($warehouseDetails->getFedexfreightPaymentType());
            }


            if ($warehouseDetails->getUpsPassword() != '') {
                $this->_request->setUpsPassword($warehouseDetails->getUpsPassword());
            }
            if ($warehouseDetails->getUpsAccessLicenseNumber() != '') {
                $this->_request->setUpsAccessLicenseNumber($warehouseDetails->getUpsAccessLicenseNumber());
            }
            if ($warehouseDetails->getUpsUserId() != '') {
                $this->_request->setUpsUserId($warehouseDetails->getUpsUserId());
            }
            if ($warehouseDetails->getUpsShipperNumber() != '') {
                $this->_request->setUpsShipperNumber($warehouseDetails->getUpsShipperNumber());
            }

            if ($warehouseDetails->getMaxPackageWeight() != '') {
                $this->_request->setMaxPackageWeight($warehouseDetails->getMaxPackageWeight());
            }
            if ($warehouseDetails->getUspsUserId() != '') {
                $this->_request->setUspsUserid($warehouseDetails->getUspsUserId());
            }
            if ($warehouseDetails->getUspsAllowedMethods() != '') {
                $this->_request->setUspsAllowedMethods($warehouseDetails->getUspsAllowedMethods());
            }
            if ($warehouseDetails->getUspsSpecifyAllowedMethods() != '') {
                $this->_request->setUspsSpecifyAllowedMethods($warehouseDetails->getUspsSpecifyAllowedMethods());
            }
            if ($warehouseDetails->getUpsShippingOrigin() != '') {
                $this->_request->setUpsShippingOrigin($warehouseDetails->getUpsShippingOrigin());
                $this->_request->setUpsAllowedMethods($warehouseDetails->getUpsAllowedMethods());
                $this->_request->setUpsUnitOfMeasure($warehouseDetails->getUpsUnitOfMeasure());
            } else {
                $this->_request->setUpsShippingOrigin(null);
                $this->_request->setUpsAllowedMethods(null);
                $this->_request->setUpsUnitOfMeasure(null);
            }

            if ($warehouseDetails->getStorepickupApplicableMethod() != '') {
                $this->_request->setStorepickupApplicableMethod($warehouseDetails->getStorepickupApplicableMethod());
            }

            if ($warehouseDetails->getDestCountry() != '') {
                $this->_request->setWarehouseDestCountry($warehouseDetails->getDestCountry());
            }
        }
    }

    protected function setReqWarehouseDetails($warehouse)
    {
        // set origin
        $warehouseDetails = Mage::getModel('dropcommon/dropship')->load($warehouse);

        $this->_populateRequestObj($warehouse,$warehouseDetails);

        $carriersAllowed = $warehouseDetails->getShippingMethods();

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Finding carriers for warehouse:' . $warehouse, $carriersAllowed);
        }
        //determine shipping carriers for each warehouse
        $this->_request->setLimitCarrier($carriersAllowed);
        $this->_request->setDropshipCollecting(true);

        // freight check
        $limitCarriers = array();
        $removeFreightCarrier = array();

        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafreightcommon', 'shipping/wsafreightcommon/active')) {

            $freightCarrierNameArr = Mage::helper('wsafreightcommon')->getAllFreightCarriers();

            foreach ($freightCarrierNameArr as $freightCarrierName) {
                if ($this->limitFreight($freightCarrierName, $removeFreightCarrier)) {
                    $limitCarriers[] = $freightCarrierName;
                }
            }
        }

        if (count($limitCarriers) > 0) {
            // check for all carriers allowed
            $this->_addAllCarriersAllowed($carriersAllowed, $limitCarriers);
            $this->_request->setLimitCarrier($limitCarriers);
            return true;
        }
        $newLimitCarriers = array();
        foreach ($this->_request->getLimitCarrier() as $carrierCode) {
            if (in_array($carrierCode, $carriersAllowed) &&
                !in_array($carrierCode, $removeFreightCarrier)
            ) {
                $newLimitCarriers[] = $carrierCode;
            }
        }
        /**
         * DROP-89
         * /

        //get cross-warehouse carrier if configured and add to carriers
        /*if (Mage::getStoreConfig('carriers/dropship/use_cart_price') &&
            Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Premiumrate','carriers/premiumrate/active')) {
            $newLimitCarriers[] = 'premiumrate';
        }*/

        $this->_request->setLimitCarrier($newLimitCarriers);
        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Limiting carriers for warehouse:' . $warehouse, $this->_request->getLimitCarrier());
        }
        if (count($this->_request->getLimitCarrier()) == 0) {
            return false;
        }
        return true;
    }

    protected function _addAllCarriersAllowed($carriersAllowed, &$limitCarriers)
    {
        $alwaysShowCarriersArr = explode(',', Mage::getStoreConfig('shipping/wsafreightcommon/show_carriers'));
        foreach ($carriersAllowed as $carrierName) {
            if (in_array($carrierName, $alwaysShowCarriersArr)) {
                $limitCarriers[] = $carrierName;
            }
        }

        //Always add in admin shipping. Reduces required config and potential support.
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Adminshipping')) {
            $limitCarriers[] = 'adminshipping';
            $limitCarriers = array_unique($limitCarriers);
        }
    }


    protected function limitFreight($freightname, &$removeFreightCarrier)
    {
        $carriers = $this->_request->getLimitCarrier();

        $freightConfigPart = 'carriers/' . $freightname;
        if (!Mage::getStoreConfig($freightConfigPart . '/active') || !in_array($freightname, $carriers)) {
            return false;
        }

        $maxItemDimensions = 0;
        $dimLengthExceeded = 0;

        $hasFreightItems = Mage::helper('wsafreightcommon')->hasFreightItems($this->_request->getAllItems(),
            $maxItemDimensions,$dimLengthExceeded);

        if (Mage::helper('wsafreightcommon')->dontShowCommonFreight($this->_request->getAllItems(),
                $this->_request->getPackageWeight(),$hasFreightItems,$maxItemDimensions,$dimLengthExceeded)) {
            $removeFreightCarrier[] = $freightname;  // don't show this freight carrier
        } else {
            // should we remove other carriers?
            return Mage::helper('wsafreightcommon')->showOnlyCommonFreight(
                $this->_request->getPackageWeight(),$hasFreightItems,$maxItemDimensions,$dimLengthExceeded);
        }
        return false;
    }

    protected function getErrorResult()
    {
        $this->_request->setDropshipCollecting(false);

        if ($this->getConfigData('showmethod')) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('dropship');
            $error->setCarrierTitle($this->getConfigData('title'));
            if (!empty($this->_customError)) {
                $error->setErrorMessage($this->_customError);
            } else {
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            }
        } else {
            $error = Mage::getModel('shipping/rate_result');
        }
        return $error;
    }

    public function getAllowedMethods()
    {
        return array('dropship' => $this->getConfigData('name'));
    }

    public function createMergedRate($ratesToAdd)
    {
        $result = Mage::getModel('shipping/rate_result');
        foreach ($ratesToAdd as $rateToAdd) {
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setPrice((float)$rateToAdd['price']);
            $method->setCost((float)$rateToAdd['price']);
            $method->setCarrier('dropship');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($rateToAdd['title']);
            $method->setMethodTitle($rateToAdd['title']);
            $method->setFreightQuoteId($rateToAdd['freight_quote_id']);
            $method->setExpectedDelivery($rateToAdd['expected_delivery']);
            $method->setDispatchDate($rateToAdd['dispatch_date']);
            $result->append($method);
        }
        return $result;
    }


    /**
     * Have received more than one set of rates - multiple warehouses
     * Try to merge the rates together
     */
    protected function collectMergedRates()
    {
        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Merging Rates', $this->_shippingResults);
        }
        $ratesToAdd = array();
        $rateAdded = false;
        $freightErrorRate = -1;
        $innerRates = array();

        foreach ($this->_shippingResults as $key => $result) {

            $rates = $result->getAllRates();
            if (empty($rates)) {
                return $this->getErrorResult();
            }
            if ($key == 0) {

                $currentCarrier = -1;
                foreach ($rates as $rate) {
                    if ($rate->getCarrier() != $currentCarrier) {
                        if ($currentCarrier != -1) {
                            $ratesToAdd[$currentCarrier] = $innerRates;
                        }
                        $innerRates = array();
                        $currentCarrier = $rate->getCarrier();
                    }

                    if ($rate->getErrorMessage() != "") {
                        if ($rate->getCarrier() == 'freightrate') {
                            $freightErrorRate = $this->getFreightErrorRate($rate);
                            continue;
                        } else {
                            $innerRates[$currentCarrier] = array(
                                'price' => $rate['price'],
                                'found' => true,
                                'rate' => $rate,
                                'shipping_details' => array(array(
                                    'warehouse' => $this->_shippingWarehouse[$key],
                                    'code' => $rate->getMethod(),
                                    'price' => (float)$rate->getPrice(),
                                    'cost' => (float)$rate->getCost(),
                                    'carrierTitle' => $rate->getCarrierTitle(),
                                    'methodTitle' => $rate->getMethodTitle(),
                                )),
                            );
                            break; // error found lets exit
                        }
                    } else {
                        $innerRates[$rate->getMethod()] = array(
                            'price' => $rate['price'],
                            'found' => false,
                            'rate' => $rate,
                            'shipping_details' => array(array(
                                'warehouse' => $this->_shippingWarehouse[$key],
                                'code' => $rate->getMethod(),
                                'price' => (float)$rate->getPrice(),
                                'cost' => (float)$rate->getCost(),
                                'carrierTitle' => $rate->getCarrierTitle(),
                                'methodTitle' => $rate->getMethodTitle(),
                                'freightQuoteId' => $rate->getFreightQuoteId(),
                            )),
                        );
                    }
                }
                if (count($innerRates > 0)) {
                    $ratesToAdd[$currentCarrier] = $innerRates;
                }

                continue;
            }

            foreach ($rates as $rate) {
                $currentCarrier = $rate->getCarrier();
                if (array_key_exists($currentCarrier, $ratesToAdd)) {
                    if (array_key_exists($rate->getMethod(), $ratesToAdd[$currentCarrier])) {
                        $ratesToAdd[$currentCarrier][$rate->getMethod()]['price'] += $rate['price'];
                        $ratesToAdd[$currentCarrier][$rate->getMethod()]['shipping_details'][] = array(
                            'warehouse' => $this->_shippingWarehouse[$key],
                            'code' => $rate->getMethod(),
                            'price' => (float)$rate->getPrice(),
                            'cost' => (float)$rate->getCost(),
                            'carrierTitle' => $rate->getCarrierTitle(),
                            'methodTitle' => $rate->getMethodTitle(),
                            'freightQuoteId' => $rate->getFreightQuoteId(),
                        );
                        $ratesToAdd[$currentCarrier][$rate->getMethod()]['found'] = true;
                    }
                }
            }
            // clean out not found
            foreach ($ratesToAdd as $code => $rates) {
                foreach ($rates as $rateKey => $rate) {
                    if (!$rate['found']) {
                        $rates[$rateKey] = "";
                        unset($rates[$rateKey]);
                    } else {
                        $rates[$rateKey]['found'] = false;
                    }
                }
                if (count($rates) > 0) {
                    $ratesToAdd[$code] = $rates;
                } else {
                    $ratesToAdd[$code] = "";
                    unset($ratesToAdd[$code]);
                }
            }
        }

        $shipMethodModel = Mage::getModel("dropcommon/shipmethods");
        $shipMethodResource = Mage::getResourceModel("dropcommon/shipmethods");
        $shipMethods = $shipMethodModel->getShipmethods();
        $mergedRatesToAdd = array();

        foreach ($this->_shippingResults as $key => $result) {
            if ($key == 0) {
                foreach ($shipMethods as $shipMethodId => $shipMethod) {
                    $rates = $result->getAllRates();
                    foreach ($rates as $rate) {
                        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error && $rate->getCarrier() == 'freightrate') {
                            $freightErrorRate = $this->getFreightErrorRate($rate);
                            continue;
                        }
                        if ($shipMethodResource->isShipmethodPresent($shipMethodId, $rate->getCarrier(),
                                $rate->getMethod(), $rate->getWarehouse()) &&
                            !array_key_exists($shipMethodId, $mergedRatesToAdd)
                        ) {

                            $shippingDetails[] =
                                array(
                                    'warehouse' => $this->_shippingWarehouse[$key],
                                    'code' => $rate->getMethod(),
                                    'price' => (float)$rate->getPrice(),
                                    'cost' => (float)$rate->getCost(),
                                    'carrierTitle' => $rate->getCarrierTitle(),
                                    'methodTitle' => $rate->getMethodTitle(),
                                    'freightQuoteId' => $rate->getFreightQuoteId(),
                                    'carrierMethod' => $rate->getCarrier(),
                                );

                            $mergedRatesToAdd[$shipMethodId] = array('title' => $shipMethod->getTitle(),
                                'price' => $rate->getPrice(),
                                'freight_quote_id' => $rate->getFreightQuoteId(),
                                'dispatch_date' => $rate->getDispatchDate(),
                                'expected_delivery' => $rate->getExpectedDelivery(),
                                'shipping_details' => $shippingDetails);
                        }
                    }
                }
                continue;
            }
            // otherwise
            foreach ($mergedRatesToAdd as $shipMethodId => $mergedRate) {
                $found = false;
                $rates = $result->getAllRates();
                foreach ($rates as $rate) {
                    if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error && $rate->getCarrier() == 'freightrate') {
                        $freightErrorRate = $this->getFreightErrorRate($rate);
                        continue;
                    }
                    if (!$found && $shipMethodResource->isShipmethodPresent($shipMethodId, $rate->getCarrier(),
                            $rate->getMethod(), $rate->getWarehouse())
                    ) {
                        $mergedRatesToAdd[$shipMethodId]['price'] = $mergedRatesToAdd[$shipMethodId]['price'] + $rate['price'];
                        if ($rate->getFreightQuoteId() != '') {
                            $mergedRatesToAdd[$shipMethodId]['freight_quote_id'] = $rate->getFreightQuoteId();
                        }
                        if ($rate->getDispatchDate() != '') {
                            $mergedRatesToAdd[$shipMethodId]['dispatch_date'] = $rate->getDispatchDate();
                        }
                        if ($rate->getExpectedDelivery() != '') {
                            $mergedRatesToAdd[$shipMethodId]['expected_delivery'] = $rate->getExpectedDelivery();
                        }
                        $mergedRatesToAdd[$shipMethodId]['shipping_details'][] = array(
                            'warehouse' => $this->_shippingWarehouse[$key],
                            'code' => $rate->getMethod(),
                            'price' => (float)$rate->getPrice(),
                            'cost' => (float)$rate->getCost(),
                            'carrierTitle' => $rate->getCarrierTitle(),
                            'methodTitle' => $rate->getMethodTitle(),
                            'freightQuoteId' => $rate->getFreightQuoteId(),
                            'carrierMethod' => $rate->getCarrier(),
                        );
                        $found = true;
                    }
                }

                if (!$found) {
                    $mergedRatesToAdd[$shipMethodId] = "";
                    unset($mergedRatesToAdd[$shipMethodId]);
                }
            }
        }

        // now lets clear out merged rates where carrier is same throughout
        $origPrice = -1;
        foreach ($mergedRatesToAdd as $shipMethodId => $mergedRates) {
            $matchedCarrierMethod = '';
            $matchedTitle = '';
            $removeCarrierMethod = true;
            foreach ($mergedRates['shipping_details'] as $shippingDetails) {
                if ($matchedCarrierMethod == '') {
                    $matchedCarrierMethod = $shippingDetails['carrierMethod'];
                    $matchedTitle  = $shippingDetails['methodTitle'];
                    continue;
                }
                if ($shippingDetails['carrierMethod'] != $matchedCarrierMethod) {
                    $removeCarrierMethod = false;
                    break;
                }
                if ($shippingDetails['carrierMethod'] == 'productmatrix') { //DROP-76
                    if($shippingDetails['methodTitle'] != $matchedTitle) {
                        $removeCarrierMethod = false;
                        break;
                    }
                }
            }
            if ($mergedRates['price'] == $origPrice) {
                $removeCarrierMethod = true;
            } else {
                $origPrice = $mergedRates['price'];
            }

            if ($removeCarrierMethod) {
                $mergedRatesToAdd[$shipMethodId] = "";
                unset($mergedRatesToAdd[$shipMethodId]);
            }
        }

        if (!empty($ratesToAdd)) {
            $finalResult = Mage::getModel('shipping/rate_result');
            foreach ($ratesToAdd as $rates) {
                foreach ($rates as $rate) {
                    $rateToAdd = $rate['rate'];
                    $shippingDetails = $rate['shipping_details'];
                    if ($rateToAdd->getErrorMessage() != "") {
                        $method = Mage::getModel('shipping/rate_result_error');
                        $method->setErrorMessage($rateToAdd->getErrorMessage());
                        $method->setCarrier($rateToAdd->getCarrier());
                        $method->setCarrierTitle($rateToAdd->getCarrierTitle());
                        $finalResult->append($method);
                        $rateAdded = true;
                        break;
                    } else {
                        $rate['rate']['price'] = $rate['price'];
                        $method = Mage::getModel('shipping/rate_result_method');
                        $method->setPrice($this->getHandlingFee($rateToAdd['price']));
                        $method->setCost($rateToAdd['price']);
                        $method->setCarrier($rateToAdd->getCarrier());
                        $method->setCarrierTitle($rateToAdd->getCarrierTitle());
                        $method->setFreightQuoteId($rateToAdd->getFreightQuoteId());
                        $method->setExpectedDelivery($rateToAdd->getExpectedDelivery());
                        $method->setDispatchDate($rateToAdd->getDispatchDate());
                        $method->setMethod($rateToAdd->getMethod());
                        $method->setMethodTitle($rateToAdd->getMethodTitle());
                        $method->setWarehouseShippingDetails(Mage::helper('dropcommon')->encodeShippingDetails($shippingDetails));
                        $finalResult->append($method);
                        $rateAdded = true;
                    }
                }
            }
        }

        if (!empty($mergedRatesToAdd)) { // Will add merged rates to carrier rates as long as not same carrier code
            // See DROP-9 and DROP-20 for logic here
            if (!isset($finalResult)) {
                $finalResult = NULL;
            }
            $finalResult = $this->collectMergedResult($mergedRatesToAdd, $finalResult);
        } else if (!$rateAdded) {
            $finalResult = $this->getErrorResult();
        }

        if ($freightErrorRate instanceof Mage_Shipping_Model_Rate_Result_Error && ($rateAdded || !empty($mergedRatesToAdd))) {
            $finalResult->append($freightErrorRate);
        }

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Merged Result', $finalResult);
        }
        return $finalResult;
    }

    protected function getFreightErrorRate($rate)
    {
        $method = Mage::getModel('shipping/rate_result_error');
        $method->setErrorMessage($rate->getErrorMessage());
        $method->setCarrier($rate->getCarrier());
        $method->setCarrierTitle($rate->getCarrierTitle());
        return $method;
    }


    protected function collectMergedResult($ratesToAdd, $result)
    {

        if (is_null($result)) {
            $result = Mage::getModel('shipping/rate_result');
        }
        foreach ($ratesToAdd as $rateToAdd) {
            $insuranceString = '';

            if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Insurance', 'shipping/insurance/active')) {
                foreach ($rateToAdd['shipping_details'] as $detail) {
                    if(substr($detail['code'],-9,9) == 'insurance'){
                        $insuranceString = '_insurance';
                        break;
                    }
                }
            }

            $method = Mage::getModel('shipping/rate_result_method');
            $method->setPrice($this->getHandlingFee($rateToAdd['price']));
            $method->setCost((float)$rateToAdd['price']);
            $method->setCarrier('dropship');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setFreightQuoteId($rateToAdd['freight_quote_id']);
            $method->setExpectedDelivery($rateToAdd['expected_delivery']);
            $method->setDispatchDate($rateToAdd['dispatch_date']);
            $method->setMethod($rateToAdd['title'].$insuranceString);
            $method->setMethodTitle($rateToAdd['title']);
            if (array_key_exists('shipping_details', $rateToAdd)) {
                $method->setWarehouseShippingDetails(Mage::helper('dropcommon')->encodeShippingDetails($rateToAdd['shipping_details']));
            }
            $result->append($method);
        }
        return $result;
    }

    protected function getHandlingFee($price)
    {
        if ($this->_handlingCount > 1 && $this->_handlingType != self::HANDLING_TYPE_PERCENT
            && $this->_handlingAction == 'W'
        ) {
            $handlingFee = $this->getFinalPriceWithHandlingFee($price) - $price;
            if (self::$_debug) {
                Mage::helper('wsalogger/log')->postDebug('dropcommon', 'Handling Fee', 'Count:' . $this->_handlingCount . ', Fee:' . $handlingFee);
            }
            return $price + ($handlingFee * $this->_handlingCount);
        } else {
            return $this->getFinalPriceWithHandlingFee($price);
        }
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|bool
     */
    public function getCode($type, $code = '')
    {
        $codes = array(
            'weight' => array(
                'lbs' => 'Lbs',
                'kgs' => 'Kgs',
                'grams' => 'Grams',
                'none' => 'No weight',
            ),
            'handling_action' => array(
                'O' => 'Per Order',
                'W' => 'Per Warehouse',
            ),
            'shipment_email' => array(
                'order' => 'On Place Order',
                'invoice' => 'On Invoice Creation',
                'never' => 'Never',
            ),

        );

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

}

