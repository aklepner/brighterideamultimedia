<?php
/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropship_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected static $_createShipmentEmail;
    private static $_debug;


    public static function isDebug()
    {
        if (self::$_debug==NULL) {
            self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropship');
        }
        return self::$_debug;
    }



    public function isMergedCheckout()
    {
        return !(!Mage::getStoreConfig('carriers/dropship/merged_checkout') && Mage::helper('dropcommon')->isMultipleWarehouses() );
    }

	public static function isCreateShipmentEmail($store='')
    {
    	if(!Mage::getStoreConfig('carriers/dropship/active',$store)){
    		return false;
    	}

	    if (self::$_createShipmentEmail==NULL) {
			self::$_createShipmentEmail = Mage::getStoreConfig('carriers/dropship/shipment_email',$store);
		}

		return self::$_createShipmentEmail;
    }

    public static function getAvailableTemplate()
    {
        if(Mage::helper('dropcommon')->isActive() && !Mage::getStoreConfig('carriers/dropship/merged_checkout') &&
            Mage::getStoreConfig('carriers/dropship/use_cart_price')) {
            return 'webshopapps/dropship/checkout/onepage/shipping_method/available_combined.phtml';
        }

        return 'webshopapps/dropship/checkout/onepage/shipping_method/available.phtml';
    }

    /**
     * Gets the vendor specific SKU for use in the shipment emails.
     *
     * @param $item
     * @return bool|mixed
     */
    public function getVendorSku($item)
    {
        $altSkuCode = trim(Mage::getStoreConfig('carriers/dropship/alt_sku'));

        if ($altSkuCode != '') {
            if ($item->getOrderItem()) {//DROP-103
                if ($item->getOrderItem()->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    if ($item->getOrderItem()->getHasChildren()) {
                        foreach ($item->getOrderItem()->getChildrenItems() as $child) {
                            $item = $child;
                            break;
                        }
                    }
                }
            }
            $product = Mage::getModel('catalog/product')->load($item->getProductId());

            return $product->getData($altSkuCode);
        }

        return false;
    }
}