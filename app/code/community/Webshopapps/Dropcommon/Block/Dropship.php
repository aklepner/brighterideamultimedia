<?php


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Dropship extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getDropship()     
     { 
        if (!$this->hasData('dropship')) {
            $this->setData('dropship', Mage::registry('dropship'));
        }
        return $this->getData('dropship');
        
    }
}