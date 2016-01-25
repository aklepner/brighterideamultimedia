<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately. 
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

include_once('Mage/Shipping/Model/Shipping.php');
class Concalma_Dropship_Model_Shipping extends Mage_Shipping_Model_Shipping
{
    /**
     * Store address
     */
    const XML_PATH_STORE_ADDRESS1     = 'shipping/origin/street_line1';
    const XML_PATH_STORE_ADDRESS2     = 'shipping/origin/street_line2';
    const XML_PATH_STORE_CITY         = 'shipping/origin/city';
    const XML_PATH_STORE_REGION_ID    = 'shipping/origin/region_id';
    const XML_PATH_STORE_ZIP          = 'shipping/origin/postcode';
    const XML_PATH_STORE_COUNTRY_ID   = 'shipping/origin/country_id';

    /**
     * Default shipping orig for requests
     *
     * @var array
     */
    protected $_orig = null;

    /**
     * Cached result
     *
     * @var Mage_Sales_Model_Shipping_Method_Result
     */
    protected $_result = null;

    /**
     * Part of carrier xml config path
     *
     * @var string
     */
    protected $_availabilityConfigField = 'active';

    /**
     * Get shipping rate result model
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function getResult()
    {
        if (empty($this->_result)) {
            $this->_result = Mage::getModel('shipping/rate_result');
        }
        return $this->_result;
    }

    /**
     * Set shipping orig data
     *
     * @param array $data
     * @return null
     */
    public function setOrigData($data)
    {
        $this->_orig = $data;
    }

    /**
     * Reset cached result
     *
     * @return Mage_Shipping_Model_Shipping
     */
    public function resetResult()
    {
        $this->getResult()->reset();
        return $this;
    }

    /**
     * Retrieve configuration model
     *
     * @return Mage_Shipping_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('shipping/config');
    }

    /**
     * Retrieve all methods for supplied shipping data
     *
     * @todo make it ordered
     * @param Mage_Shipping_Model_Shipping_Method_Request $data
     * @return Mage_Shipping_Model_Shipping
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $storeId = $request->getStoreId();
        if (!$request->getOrig()) {
            $request
                ->setCountryId(Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $request->getStore()))
                ->setRegionId(Mage::getStoreConfig(self::XML_PATH_STORE_REGION_ID, $request->getStore()))
                ->setCity(Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $request->getStore()))
                ->setPostcode(Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $request->getStore()));
        }

        $limitCarrier = $request->getLimitCarrier();
        if (!$limitCarrier) {
            $carriers = Mage::getStoreConfig('carriers', $storeId);

          //  foreach ($carriers as $carrierCode => $carrierConfig) {
            	//devhungnam
            	//if ($carrierCode == 'ups') {
            	$request->setPackageWeight(123);
                $this->collectCarrierRates('ups', $request);
                 $this->collectCarrierRates('dhl', $request);
                 $request->setPackageQty(10);
                 $this->collectCarrierRates('flatrate', $request);
                 $request->setPackageWeight($this->getWeightByWarehouse('CL'));
                  $request->setPackageQty(10);
                  $this->collectCarrierRates('ups', $request);
                 
            	//}
                //dev 
             //foreach ( $carrierConfig as $key=> $value) {
                //	echo $key.'===========';
                //	echo "value is  ". $value;
             // }
            // echo "end".$carrierCode."***************";
             // echo (get_class($request));
                /**
                 * we do not store the carrier config on the table core_data_config
                * foreach warehouse we retrieve the carier config relate to it.
                * example: new york wareouse have ups and DHL 
                * then foreach 
                 */
           // }
        } else {
            if (!is_array($limitCarrier)) {
                $limitCarrier = array($limitCarrier);
            }
            foreach ($limitCarrier as $carrierCode) {
                $carrierConfig = Mage::getStoreConfig('carriers/' . $carrierCode, $storeId);
                if (!$carrierConfig) {
                    continue;
                }
                //del
                $this->collectCarrierRates($carrierCode, $request);
            }
        }

        return $this;
    }

    /**
     * Collect rates of given carrier
     *
     * @param string $carrierCode
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Shipping
     */
    public function collectCarrierRates($carrierCode, $request)
    {
        $carrier = $this->getCarrierByCode($carrierCode, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }
        $carrier->setActiveFlag($this->_availabilityConfigField);
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !($result instanceof Mage_Shipping_Model_Rate_Result_Error)) {
            $result = $carrier->proccessAdditionalValidation($request);
        }
        /*
        * Result will be false if the admin set not to show the shipping module
        * if the devliery country is not within specific countries
        */
        if (false !== $result){
            if (!$result instanceof Mage_Shipping_Model_Rate_Result_Error) {
                $result = $carrier->collectRates($request);
                if (!$result) {
                    return $this;
                }
            }
            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                return $this;
            }
            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice')) {
                $result->sortRatesByPrice();
            }
            $this->getResult()->append($result);
        }
        return $this;
    }

    /**
     * Collect rates by address
     *
     * @param Varien_Object $address
     * @param null|bool|array $limitCarrier
     * @return Mage_Shipping_Model_Shipping
     */
    public function collectRatesByAddress(Varien_Object $address, $limitCarrier = null)
    {
        /** @var $request Mage_Shipping_Model_Rate_Request */
        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($address->getAllItems());
        $request->setDestCountryId($address->getCountryId());
        $request->setDestRegionId($address->getRegionId());
        $request->setDestPostcode($address->getPostcode());
        $request->setPackageValue($address->getBaseSubtotal());
        $request->setPackageValueWithDiscount($address->getBaseSubtotalWithDiscount());
        $request->setPackageWeight($address->getWeight());
        $request->setFreeMethodWeight($address->getFreeMethodWeight());
        $request->setPackageQty($address->getItemQty());
        $request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $request->setBaseCurrency(Mage::app()->getStore()->getBaseCurrency());
        $request->setPackageCurrency(Mage::app()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($limitCarrier);

        return $this->collectRates($request);
    }

    /**
     * Set part of carrier xml config path
     *
     * @param string $code
     * @return Mage_Shipping_Model_Shipping
     */
    public function setCarrierAvailabilityConfigField($code = 'active')
    {
        $this->_availabilityConfigField = $code;
        return $this;
    }

    /**
     * Get carrier by its code
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCarrierByCode($carrierCode, $storeId = null)
    {
        if (!Mage::getStoreConfigFlag('carriers/'.$carrierCode.'/'.$this->_availabilityConfigField, $storeId)) {
            return false;
        }
        $className = Mage::getStoreConfig('carriers/'.$carrierCode.'/model', $storeId);
        if (!$className) {
            return false;
        }
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }

    /**
     * Prepare and do request to shipment
     *
     * @param Mage_Sales_Model_Order_Shipment $orderShipment
     * @return Varien_Object
     */
    public function requestToShipment(Mage_Sales_Model_Order_Shipment $orderShipment)
    {
        $admin = Mage::getSingleton('admin/session')->getUser();
        $order = $orderShipment->getOrder();
        $address = $order->getShippingAddress();
        $shippingMethod = $order->getShippingMethod(true);    
        //thuydebug
        //Mage::log($shippingMethod, Zend_Log::ERR);             
        $shipmentStoreId = $orderShipment->getStoreId();
        $shipmentCarrier = $order->getShippingCarrier();
        //thuydebug
       // Mage::Log($shipmentCarrier, Zend_Log::ERR);
        $baseCurrencyCode = Mage::app()->getStore($shipmentStoreId)->getBaseCurrencyCode();
        if (!$shipmentCarrier) {
            Mage::throwException('Invalid carrier: ' . $shippingMethod->getCarrierCode());
        }
        $shipperRegionCode = Mage::getStoreConfig(self::XML_PATH_STORE_REGION_ID, $shipmentStoreId);
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = Mage::getModel('directory/region')->load($shipperRegionCode)->getCode();
        }

        $recipientRegionCode = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();

        $originStreet1 = Mage::getStoreConfig(self::XML_PATH_STORE_ADDRESS1, $shipmentStoreId);
        $originStreet2 = Mage::getStoreConfig(self::XML_PATH_STORE_ADDRESS2, $shipmentStoreId);
        $storeInfo = new Varien_Object(Mage::getStoreConfig('general/store_information', $shipmentStoreId));

        //thuydebug
         Mage::log('admin firstname '.  $admin->getFirstname(), Zend_Log::ERR);
          Mage::log('admin lastname '.  $admin->getLastname(), Zend_Log::ERR);
           Mage::log('store name'.  $storeInfo->getName(), Zend_Log::ERR);
            Mage::log('store phone '.  $storeInfo->getPhone(), Zend_Log::ERR);
             Mage::log('origin Street '.  $originStreet1, Zend_Log::ERR);
              Mage::log('xml path store id '.  Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId), Zend_Log::ERR);
               Mage::log('shipperRegionCode '.  $shipperRegionCode, Zend_Log::ERR);
                Mage::log('XML_PATH_STORE_COUNTRY_ID '. Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId), Zend_Log::ERR);
                
        //thuydebug
        
        if (!$admin->getFirstname() || !$admin->getLastname() || !$storeInfo->getName() || !$storeInfo->getPhone()
            || !$originStreet1 || !Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId)
            || !$shipperRegionCode || !Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId)
            || !Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId)
        ) {
            Mage::throwException(
                Mage::helper('sales')->__('Insufficient information to create shipping label(s). Please verify your Store Information and Shipping Settings.')
            );
        }

        /** add by Luu Thanh Thuy luuthuy205@gmail.com */
       // $warehouse = Mage::helper('dropship')->getWarehouseTitleByOrderId($order->getId());
        //if ($warehouse) 
        if ($orderShipment ->getData('warehouse') != '' && $orderShipment ->getData('warehouse') != null) {
        	$request->setWarehouse($orderShipment ->getData('warehouse') );
        }
        
        /** @var $request Mage_Shipping_Model_Shipment_Request */
        $request = Mage::getModel('shipping/shipment_request');
        $request->setOrderShipment($orderShipment);
        $request->setShipperContactPersonName($admin->getName());
        $request->setShipperContactPersonFirstName($admin->getFirstname());
        $request->setShipperContactPersonLastName($admin->getLastname());
        $request->setShipperContactCompanyName($storeInfo->getName());
        $request->setShipperContactPhoneNumber($storeInfo->getPhone());
        $request->setShipperEmail($admin->getEmail());
        $request->setShipperAddressStreet($originStreet1 . ' ' . $originStreet2);
        $request->setShipperAddressStreet1($originStreet1);
        $request->setShipperAddressStreet2($originStreet2);
        $request->setShipperAddressCity(Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId));
        $request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $request->setShipperAddressPostalCode(Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId));
        $request->setShipperAddressCountryCode(Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId));
        $request->setRecipientContactPersonName($address->getFirstname() . ' ' . $address->getLastname());
        $request->setRecipientContactPersonFirstName($address->getFirstname());
        $request->setRecipientContactPersonLastName($address->getLastname());
        $request->setRecipientContactCompanyName($address->getCompany());
        $request->setRecipientContactPhoneNumber($address->getTelephone());
        $request->setRecipientEmail($address->getEmail());
        $request->setRecipientAddressStreet($address->getStreetFull());
        $request->setRecipientAddressStreet1($address->getStreet1());
        $request->setRecipientAddressStreet2($address->getStreet2());
        $request->setRecipientAddressCity($address->getCity());
        $request->setRecipientAddressStateOrProvinceCode($address->getRegion());
        $request->setRecipientAddressRegionCode($recipientRegionCode);
        $request->setRecipientAddressPostalCode($address->getPostcode());
        $request->setRecipientAddressCountryCode($address->getCountryId());
        $request->setShippingMethod($shippingMethod->getMethod());
        $request->setPackageWeight($order->getWeight());
        $request->setPackages($orderShipment->getPackages());
        $request->setBaseCurrencyCode($baseCurrencyCode);
        $request->setStoreId($shipmentStoreId);

        return $shipmentCarrier->requestToShipment($request);
    }
    
    


	/**
	 * @author Luu Thanh Thuy
	 * description: get all the items in shopping cart
	 *
	 */
	/**
	 * get All Items in the cart
	 */
	public function getAllItems() {
		$items = Mage::getSingleton('checkout/cart')->getQuote()->getAllItems();

		return $items;
	}


	/**
	 * get all the warehouse
	 * @author : Luu Thanh Thuy
	 * email : luuthuy205@gmail
	 * return array of warehouse Id
	 */
	public function getAllWarehouses () {
		$itemcollection = $this->getAllItems();

		$arr_warehouse = array();
		foreach ($itemcollection as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);
			//// in case of product have multiple option
			$tempArr = explode(",", $warehouse);
			if (count($tempArr) > 0) {
				for ($i = 0; $i < count($tempArr) ; $i++) {
					if ( !in_array($tempArr[$i], $arr_warehouse ) ) {
						$arr_warehouse[] = $tempArr[$i];

					}
				}
			}	else {
				if ( !in_array($warehouse, $arr_warehouse ) ) {
					$arr_warehouse[] = $warehouse;

				}
			}
		}
		return $arr_warehouse;
	}

	/**
	 * @ mailto : luuthuy205@gmail.com
	 * get all the items in warehouse when checkout
	 * @return array of item correspondent with the warehouse information
	 */
	public function getItemsInWarehouse ($warehouseTitle) {
		$warehouseTitle = trim($warehouseTitle);
		$itemcollection = $this->getAllItems();

		$arr_item = array();
		foreach ($itemcollection as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);

			//incase the $warehouse is multiple
           //  echo "======warehouse is ".$warehouse . " warehous title.". $warehouseTitle;

			if (strpos($warehouse, $warehouseTitle) || $warehouse == $warehouseTitle ) {
				
				if ( !in_array($item->getId(), $arr_item) ) {
					$arr_item[] = $item->getProductId();
				}
			}

		}
       return $arr_item;
	}

	/**
	 * @author : Luu Thanh Thuy luuthuy205@gmail.com
	 * @param string $warehouseTitle
	 * @return array of information about the warehouse;
	 */
	public function getWarehouseInformation($warehouseTitle) {
        $warehouseTitle = trim($warehouseTitle);
		$query = "select * from `dropship` where `title`= '$warehouseTitle'";
		//echo "query ==========================>".$query;
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		return $rows[0];
	}

	/**
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 * return html of the shipping method
	 */
	public function  getHTMLShippingMethod() {
		$html = "your order is shipped from multiple warehouses <br/>";
		$warehouseArr = $this->getAllWarehouses();
		for ($i = 0; $i < count($warehouseArr) ;$i++) {
			$html .= $this->getHTMLByWarehouse($warehouseArr[$i]) ;
		}
		return $html;
	}
	
	/**
	 * @atuhor Luu Thanh Thuy luuthuy205@gmail.com
	 * @param warehouse title
	 * return html
	 */
	public function  getHTMLByWarehouse($title) {
		
		$warehouseInfo = $this->getWarehouseInformation($title);
		$html = "<strong>Shipping from warehouse :".$warehouseInfo['description']." </strong> <br/>";
		$items = $this->getItemsInWarehouse($title);
		//$arr_productNames = array();
		for ($i = 0; $i < count($items) ; $i++) {
		   $html .= Mage::getModel('catalog/product')->load($items[$i])->getName();
		   $html .="<br/> END";
		   $html .= $this->test();
		} 
		return $html;
	}
	
	/**
	 * 
	 *get total weight of products in warehouse to calculate price
	 * @param string $title
	 */
	public function getWeightByWarehouse($title) {
		$weight = 0;
		$arrProducts = $this->getItemsInWarehouse($title);
		for ($i = 0; $i < count($arrProducts); $i++) {
			$weight += Mage::getModel('catalog/product')->load($arrProducts[$i])->getWeight();
		}
		return $weight;
	}
	
	/**
	 * @parameter 1: the title of warehouse, 
	 * @parameter 2: the request
	 * @paramter 3: the wi
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 */
 public function collectCarrierRatesDropship($warehouseTitle, $request, $weight)
    {
    	$request->setData('warehouse', $warehouseTitle);
    	$_result = Mage::getModel('shipping/rate_result');
    	$warehouseInfo = Mage::helper('dropship')->getWarehouseInformation($warehouseTitle);
    	if ($warehouseInfo['flat_enable'] == 1) {
    		$result= $this->collectCarrierRatesDropshipIn('flatrate', $request, $weight);
    		// hungnamvn jsc debug
    		Mage::log('Flat Rate Result', Zend_Log::INFO);
    		Mage::log($result, Zend_Log::INFO);
    		$_result->append($result);
    	}
    	if ($warehouseInfo['ups_enable'] == 1) {
    		
    		$result = $this->collectCarrierRatesDropshipIn('ups', $request, $weight);
    		$_result->append($result);
    	}
    	
    if ($warehouseInfo['usps_enable'] == 1) {
    		$result = $this->collectCarrierRatesDropshipIn('usps', $request, $weight);
    		$_result->append($result);
    	}
    	
    if ($warehouseInfo['fedex_enable'] == 1) {
    	    ///$request->getFedexAccount();
    		$result = $this->collectCarrierRatesDropshipIn('fedex', $request, $weight);
    		$_result->append($result);
    	}
    if ($warehouseInfo['dhl_enable'] == 1) {
    		$result = $this->collectCarrierRatesDropshipIn('dhl', $request, $weight);
    		$_result->append($result);
    	}
    	//$request-> setPackageWeight($weight);
       // $carrier = $this->getCarrierByCode($carrierCode, $request->getStoreId());
       // if (!$carrier) {
//            return $this;
//        }
//        $carrier->setActiveFlag($this->_availabilityConfigField);
//        $result = $carrier->checkAvailableShipCountries($request);
//        if (false !== $result && !($result instanceof Mage_Shipping_Model_Rate_Result_Error)) {
//            $result = $carrier->proccessAdditionalValidation($request);
//        }
//        /*
//        * Result will be false if the admin set not to show the shipping module
//        * if the devliery country is not within specific countries
//        */
//        if (false !== $result){
//            if (!$result instanceof Mage_Shipping_Model_Rate_Result_Error) {
//                $result = $carrier->collectRates($request);
//                //echo "BEgin+++++++++++++++++++++";
//                //var_dump($result);
//               // echo "End +++++++++++++++++++++++++++++";
//                if (!$result) {
//                    return false;
//                }
//            }
//            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
//                return false;
//            }
//            // sort rates by price
//            if (method_exists($result, 'sortRatesByPrice')) {
//                $result->sortRatesByPrice();
//            }
//           // $this->getResult()->append($result);
//        }
        return $_result;
    }
    
    /**
     * need refactor here
     * 
     */
 public function collectCarrierRatesDropshipIn($carrierCode, $request, $weight)
    {
    	$request->setPackageQty(4);
    	$request-> setPackageWeight($weight);
        $carrier = $this->getCarrierByCode($carrierCode, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }
        $carrier->setActiveFlag($this->_availabilityConfigField);
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !($result instanceof Mage_Shipping_Model_Rate_Result_Error)) {
            $result = $carrier->proccessAdditionalValidation($request);
        }
        /*
        * Result will be false if the admin set not to show the shipping module
        * if the devliery country is not within specific countries
        */
        if (false !== $result){
            if (!$result instanceof Mage_Shipping_Model_Rate_Result_Error) {
                $result = $carrier->collectRates($request);
                //var_dump($result);
                if (!$result) {
                    return false;
                }
            }
            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                return false;
            }
            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice')) {
                $result->sortRatesByPrice();
            }
           // $this->getResult()->append($result);
        }
        return $result;
    }
}
