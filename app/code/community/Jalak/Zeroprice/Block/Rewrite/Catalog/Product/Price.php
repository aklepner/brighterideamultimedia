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
class Jalak_Zeroprice_Block_Rewrite_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price {

    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product)
        {
            $product = Mage::registry('product');
        }
        return $product;
    }

    protected function _toHtml()
    {
        $helper = Mage::helper('jalak_zeroprice');

        if ($helper->isEnabled() && $this->getProduct()->price < $helper->priceValue())
            return '';
        
        //return $this->getProduct()->getTypeID();

        return parent::_toHtml();
    }

}
