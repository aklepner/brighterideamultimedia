<?php
/* ExtName
 *
 * User        karen
 * Date        1/26/14
 * Time        8:49 PM
 * @category   Webshopapps
 * @package    Webshopapps_ExtnName
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Dropcommon_Model_Observer extends Mage_Core_Model_Abstract {


    public function postError($observer) {
        if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvZHJvcHNoaXAvc2hpcF9vbmNl',
            'aG9sZGluZ3Vw','Y2FycmllcnMvZHJvcHNoaXAvc2VyaWFs')) {
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIFNoaXBwaW5nIE1ncg==')));
        }
    }


    public function salesConvertQuoteItemToOrderItem($observer) {

        try {
            if (!Mage::getStoreConfig('carriers/dropship/active')) {
                return;
            }
            $quoteItem = $observer->getEvent()->getItem();
            $orderItem = $observer->getEvent()->getOrderItem();

            $warehouseChanged = false;
            $quote = $quoteItem->getQuote();
            $shippingAddress = $quote->getShippingAddress();
            $allWh = Mage::getStoreConfig('carriers/dropship/common_warehouse') ?
                Mage::helper('dropcommon/shipcalculate')->findAllWarehousesInQuote($quote->getAllItems(), $shippingAddress->getCountryId()) : array();

            $warehouse = Mage::Helper('dropcommon/shipcalculate')->determineWhichWarehouse($quoteItem,
                                                                                           $shippingAddress->getCountryId(),
                                                                                           $shippingAddress->getRegionCode(),
                                                                                           $shippingAddress->getPostcode(),
                                                                                           $warehouseChanged,
                                                                                           $allWh);

            if ($warehouseChanged) {
                $orderItem->setWarehouse($warehouse);
                $quoteItem->setWarehouse($warehouse);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

    }


    /**
     * event checkout_type_onepage_save_order_after
     */
    public function saveCartBefore($observer){

        if (!Mage::getStoreConfig('carriers/dropship/active')) {
            return;
        }
        $cart = $observer->getEvent()->getCart();
        $cart->getQuote()->getShippingAddress()->setWarehouse('');
    }

    /*
     * controller_action_predispatch_shipestimate_estimate_estimate
     */
    public function onEstimateShippingProductPage($observer)
    {
        if (!Mage::getStoreConfig('carriers/dropship/active')) {
            return;
        }
        $quote = $this->_getQuote();
        if($quote->getShippingAddress()) {
            $quote->getShippingAddress()->setWarehouse('');
        }
    }

    /**
     * Save shipping breakdown per warehouse
     * @param $observer
     */
    public function saveShippingMethod($observer) {

        try {
            $request = $observer->getEvent()->getRequest();
            $this->_saveExtraInfo($request);
            $shippingMethod = $request->getPost('shipping_method', '');
            if (empty($shippingMethod)) {
                return;
            }
            $quote = $observer->getEvent()->getQuote();
            $rate = $quote->getShippingAddress()->getShippingRateByCode($shippingMethod);
            if (!$rate) {
                return;
            }

            if ($rate->getWarehouseShippingDetails()!='') {
                $quote->getShippingAddress()
                    ->setWarehouseShippingDetails($rate->getWarehouseShippingDetails())
                    ->setWarehouseShippingHtml(Mage::helper('dropcommon')->getWarehouseShippingHtml($rate->getWarehouseShippingDetails()))
                    ->save();
            }

            $quote->collectTotals()
                ->save();
        }catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Saves the extra delivery information to sales_flat_quote_address
     *
     * @param $request
     */
    private function _saveExtraInfo($request) {
        $dateInput	        = (string) trim($request->getPost('dropship_date_input'));
        $extraInfo	        = (string) trim($request->getPost('dropship_extrainfo_input'));
        $save               = false;

        if($dateInput != ''){
            $this->_getQuote()->getShippingAddress()
                ->setDropshipDeliveryDate($dateInput);
            $save = true;
        }

        if($extraInfo != ''){
            $this->_getQuote()->getShippingAddress()
                ->setDropshipExtraInfo($extraInfo);
            $save = true;
        }

        if ($save) {
            $this->_getQuote()->save();
        }
    }

    /**
     * Retrieve the quote object
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Obtain the cart singleton
     * @return Mage_Core_Model_Abstract
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
}