<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_dropnotify
 * User         Joshua Stewart
 * Date         18/02/2014
 * Time         15:20
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type extends Varien_Object
{
    const STATUS_STANDARD	= 0;
    const STATUS_PRIMARY	= 1;
    const STATUS_SUPER	    = 2;

    const STATUS_STANDARD_TEXT  = 'Standard Warehouse';
    const STATUS_PRIMARY_TEXT   = 'Primary Warehouse';
    const STATUS_SUPER_TEXT     = 'Super Warehouse';


    static public function getOptionArray()
    {
        return array(
            self::STATUS_SUPER    => Mage::helper('dropcommon')->__(self::STATUS_SUPER_TEXT),
            self::STATUS_PRIMARY  => Mage::helper('dropcommon')->__(self::STATUS_PRIMARY_TEXT),
            self::STATUS_STANDARD => Mage::helper('dropcommon')->__(self::STATUS_STANDARD_TEXT),
        );
    }
}