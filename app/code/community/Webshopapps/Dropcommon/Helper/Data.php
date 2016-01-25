<?php

/**
 * @category    Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license)
 * @license     http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Helper_Data extends Mage_Core_Helper_Abstract
{

    private static $_debug;

    private static $_isActive;

    private static $_calculateDropshipRates;

    protected static $_quotesCache = array();

    public static function isDebug()
    {
        if (self::$_debug == null) {
            self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');
        }

        return self::$_debug;
    }

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve checkout quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function getWarehouseDescription()
    {
        return Mage::getStoreConfig('carriers/dropship/warehouse_desc');
    }

    public function getGeoUrl()
    {
        return Mage::getStoreConfig('ustorelocator/general/google_geo_url');
    }

    /**
     * Check if multishipping checkout is available.
     * There should be a valid quote in checkout session. If not, only the config value will be returned.
     *
     * @return bool
     */
    public function isMultipleWarehouses()
    {
        $quote = $this->getQuote();
        if ((!$quote) || !$quote->hasItems()) {
            return false;
        }
        if (Mage::getStoreConfig('carriers/dropship/use_parent')) {
            $items = $quote->getAllVisibleItems();
        } else {
            $items = $quote->getAllItems();
        }
        $address = $this->getQuote()->getShippingAddress();

        return $quote->validateMinimumAmount(true) && (($quote->getItemsSummaryQty() - $quote->getItemVirtualQty()) > 0) && Mage::helper('dropcommon/shipcalculate')->isMultipleWarehouses($address->getCountryId(), $address->getRegionCode(), $address->getPostcode(), $items);
    }

    public function getWarehouseShippingHtml($encodedDetails)
    {
        $decodedDetails = $this->decodeShippingDetails($encodedDetails);
        $htmlText = '';
        foreach ($decodedDetails as $shipLine) {
            $htmlText .= $this->getDescription($shipLine['warehouse']) . ' : ' . $shipLine['carrierTitle'] . ' - ' . $shipLine['methodTitle'] . ' ' . $this->getQuote()->getStore()->formatPrice($shipLine['price']) . '<br/>';
        }

        return $htmlText;
    }

    public function getWarehouseTitle($item)
    {
        $title = "";

        if ($item->getOrderItem()) {
            if ($item->getOrderItem()->hasChildren) {
                $sku = $item->getSku();
                foreach ($item->getOrderItem()->getChildrenItems() as $child) {
                    if ($sku == $child->getSku()) {
                        $item = $child;
                    }
                }
            } else {
                $item = $item->getOrderItem();
            }
        }

        if ($item->getWarehouse() != '') {
            $title = Mage::getModel('dropcommon/dropship')->load($item->getWarehouse())->getTitle();
        } else {
            $product = Mage::getModel('catalog/product')->loadByAttribute('entity_id', $item->getProductId(), 'warehouse');
            if (is_object($product) && $product->getData('warehouse') != '') {
                $title = Mage::getModel('dropcommon/dropship')->load($product->getData('warehouse'))->getTitle();
            }
        }

        return $title;
    }

    public function getWarehouseByItem($item)
    {
        $warehouse = "";

        if ($item->getOrderItem()) {
            if ($item->getOrderItem()->hasChildren) {
                $sku = $item->getSku();
                foreach ($item->getOrderItem()->getChildrenItems() as $child) {
                    if ($sku == $child->getSku()) {
                        $item = $child;
                    }
                }
            } else {
                $item = $item->getOrderItem();
            }
        }

        if ($item->getWarehouse() != '') {
            $warehouse = Mage::getModel('dropcommon/dropship')->load($item->getWarehouse());
        } else {
            $product = Mage::getModel('catalog/product')->loadByAttribute('entity_id', $item->getProductId(), 'warehouse');
            if (is_object($product) && $product->getData('warehouse') != '') {
                $warehouse = Mage::getModel('dropcommon/dropship')->load($product->getData('warehouse'));
            }
        }

        return $warehouse;
    }

    public function encodeShippingDetails($shippingDetails)
    {
        return Zend_Json::encode($shippingDetails);
    }

    public function decodeShippingDetails($shippingDetailsEnc)
    {
        return Zend_Json::decode($shippingDetailsEnc);
    }

    public function getDescription($warehouseId)
    {
        $warehouseId == '' ? $warehouseId = Mage::getStoreConfig('carriers/dropship/default_warehouse') : 1;

        return Mage::getModel('dropcommon/dropship')->load($warehouseId)->getDescription();
    }

    public function fetchCoordinates($country, $region, $postcode, &$latitude, &$longitude)
    {
        $url = $this->getGeoUrl();
        if (!$url) {
            $url = "http://maps.googleapis.com/maps/api/geocode/xml"; //New API V3
        }
        $region = urlencode(preg_replace('#\r|\n#', ' ', $region));
        $country = urlencode(preg_replace('#\r|\n#', ' ', $country));
        $postcode = urlencode(preg_replace('#\r|\n#', ' ', $postcode));

        $url .= strpos($url, '?') !== false ? '&' : '?';
        $urlCountryRegion = $url;
        $urlCountry = $url;
        $url .= 'components=administrative_area:' . $region . '|country:' . $country . '|postal_code:' . $postcode . '&sensor=false';
        $urlCountryRegion .= 'components=administrative_area:' . $region . '|country:' . $country . '&sensor=false';
        $urlCountry .= 'components=country:' . $country . '&sensor=false';
        $key = "";

        for ($i = 0; $i < 3; $i++) {
            switch ($i) {
                case 0:
                    Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Get Nearest Warehouse Search', $region . ' ' . $country . ' ' . $postcode, self::isDebug());
                    $key = $region . $country . $postcode;
                    break;
                case 1:
                    Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Get Nearest Warehouse Search', $region . ' ' . $country, self::isDebug());
                    $url = $urlCountryRegion;
                    $key = $region . $country;
                    break;
                case 2:
                    Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Get Nearest Warehouse Search', $country, self::isDebug());
                    $url = $urlCountry;
                    $key = $country;
                    break;
                default:
                    break;
            }

            $response = unserialize($this->_getCachedQuotes($key)); //DROP-125

            if (!$this->isValidResponse($response)) {
                $cinit = curl_init();
                curl_setopt($cinit, CURLOPT_URL, $url);
                curl_setopt($cinit, CURLOPT_HEADER, 0);
                curl_setopt($cinit, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
                curl_setopt($cinit, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($cinit);

                if (!$this->isValidResponse($response)) {
                    Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Nearest Warehouse Search Failed', "Google Maps API Did Not Respond", self::isDebug());

                    return $this;
                }

                $this->_setCachedQuotes($key, serialize($response));
            }

            $xml = new Varien_Simplexml_Config();
            $xml->loadString($response);
            Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Nearest Warehouse Response', $response, self::isDebug());
            $status = $xml->getXpath("//GeocodeResponse/status/text()");
            $failed = true;

            if ($status[0][0] == "OK") {
                $failed = false;
                break;
            }
            Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Nearest Warehouse - No Match Found', 'Searching for a more generic location', self::isDebug());
        }

        if ($failed) {
            Mage::helper('wsalogger/log')->postCritical('dropcommon', 'Nearest Warehouse', 'Nearest Warehouse Search Failed', self::isDebug());

            return $this;
        }

        $arr = $xml->getXpath("//GeocodeResponse/result/geometry/location/text()");

        foreach ($arr as $element) {
            if ($element->lat != 0) {
                $latitude = $element->lat;
                $longitude = $element->lng;
                break;
            }
        }

        return $this;
    }

    private function isValidResponse($response)
    {
        if ($response === null || !is_string($response) || empty($response)) {
            return false;
        }

        return true;
    }

    /**
     * Returns cache key for some request to carrier quotes service
     *
     * @param string|array $requestParams
     * @return string
     */
    protected function _getQuotesCacheKey($requestParams)
    {
        $requestParams = 'dropship_googleapi' . $requestParams;

        return crc32($requestParams);
    }

    /**
     * Checks whether some request to rates have already been done, so we have cache for it
     * Used to reduce number of same requests done to carrier service during one session
     *
     * Returns cached response or null
     *
     * @param string|array $requestParams
     * @return null|string
     */
    protected function _getCachedQuotes($requestParams)
    {
        $key = $this->_getQuotesCacheKey($requestParams);

        return isset(self::$_quotesCache[$key]) ? self::$_quotesCache[$key] : null;
    }

    /**
     * Sets received carrier quotes to cache
     *
     * @param string|array $requestParams
     * @param string       $response
     * @return Mage_Usa_Model_Shipping_Carrier_Abstract
     */
    protected function _setCachedQuotes($requestParams, $response)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        self::$_quotesCache[$key] = $response;

        return $this;
    }

    public function getNearestWarehouse($country, $region, $postcode, $warehouses)
    {
        $shipLat = 0;
        $shipLong = 0;
        $this->fetchCoordinates($country, $region, $postcode, $shipLat, $shipLong);

        Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Get Nearest Warehouse Lat/Long', $shipLat . '/' . $shipLong, self::isDebug());
        Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Get Nearest Warehouse from these Warehouses', $warehouses, self::isDebug());

        try {
            $units = Mage::getStoreConfig('ustorelocator/general/distance_units');
            $collection = Mage::getModel('dropcommon/dropship')->getCollection()->addAreaFilter($shipLat, $shipLong, $warehouses, $units);

            foreach ($collection as $loc) {
                $data = $loc->getData();
                if (self::isDebug()) {
                    Mage::helper('wsalogger/log')->postInfo('dropcommon', 'Location Data', $data, self::isDebug());
                }

                return $data['dropship_id'];
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage());

            return $warehouses[0];
        }

        return $warehouses[0];
    }

    public static function isActive($storeId = null)
    {
        if (self::$_isActive == null) {
            self::$_isActive = Mage::getStoreConfig('carriers/dropship/active', $storeId);
        }

        return self::$_isActive;
    }

    public function addDimensionalWarning(&$fieldSet, $extensionName = 'Dimensional Shipping')
    {

        if (!Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shipusa', 'shipping/shipusa/active')) {

            $fieldSet->addField('', 'note', array(
                'text' => '<ul class="messages"><li class="notice-msg"><ul><li>' . Mage::helper("dropcommon")->__("Note: Requires " . $extensionName . " Extension") . '</li></ul></li></ul>',
            ));
        }
    }

    public static function calculateDropshipRates($storeId = null)
    {
        if (!self::isActive($storeId)) {
            return false; // if not active false anyhow
        }
        if (self::$_calculateDropshipRates == null) {
            self::$_calculateDropshipRates = !Mage::getStoreConfigFlag('carriers/dropship/ignore_shipping');
        }

        return self::$_calculateDropshipRates;
    }

    public function getCarrierModel()
    {
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shipmanager')) {
            return Mage::getSingleton('shipmanager/carrier_shipmanager');
        } else {
            return Mage::getSingleton('dropship/carrier_dropship');
        }
    }

    /**
     * Added for credit/invoice adminhtml.
     *
     * @param $items
     * @return mixed
     */
    public function getSimpleWarehouseText($items)
    {
        $itemWarehouses = array();
        foreach ($items as $item) {
            if ($item->getParentItem() || $item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                continue;
            }
            $itemWarehouses[] = Mage::helper('dropcommon')->getWarehouseTitle($item);
        }

        return Mage::helper('core')->jsonEncode($itemWarehouses);
    }

    /**
     * Added for credit/invoice adminhtml.
     *
     * @param $items
     * @return mixed
     */
    public function getBundleWarehouseText($items)
    {
        $itemWarehouses = array();
        foreach ($items as $item) {
            if (!$item->getParentItem() || $item->getParentItem()->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                continue;
            }
            // child of bundle
            $itemWarehouses[] = Mage::helper('dropcommon')->getWarehouseTitle($item);
        }

        return Mage::helper('core')->jsonEncode($itemWarehouses);
    }

    public function isStocked($item)
    {
        $inStock = true;

        if ($item->getBackorders() != Mage_CatalogInventory_Model_Stock::BACKORDERS_NO) {
            $inStock = false;
        }

        return $inStock;
    }

    public function skipItem($item, $useParent, $virtualCheck = true)
    {
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && !$useParent) {
            return true;
        }

        //We want to allow configurables, the parent config product has the correct item totals. The associated simple
        //will only show qty 1, weight of 1 of the item etc, won't be correct. Can get the warehouse of the simple if need be
        //and set that on the parent

        if ($virtualCheck) {
            if ($item->getProduct()->isVirtual()) {
                return true;
            }
        }

        return false;
    }
}