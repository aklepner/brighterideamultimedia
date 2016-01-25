<?php

/**
 * Jalak
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Jalak
 * @package     Jalak_Zeroprice
 * @author      Hendrathings <hendrathings@gmail.com>
 */
class Jalak_Zeroprice_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_model;

    const XML_MODULE_ENABLED = 'jalak_zeroprice/general/enabled';
    const XML_MODULE_PRICE_VALUE = 'jalak_zeroprice/general/price_value';
    const XML_MODULE_HIDE_CART = 'jalak_zeroprice/general/enabled_hide_cart';

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_MODULE_ENABLED) ? true : false;
    }
    
    public function priceValue()
    {
        return Mage::getStoreConfig(self::XML_MODULE_PRICE_VALUE);
    }
    
    public function hideAddtoCart()
    {
        return Mage::getStoreConfig(self::XML_MODULE_HIDE_CART) ? true : false;
    }

    public function getCanAddToCart()
    {
        if (!$this->isEnabled())
            return true;
        
        return !$this->hideAddtoCart();
    }

}
