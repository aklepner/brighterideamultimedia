<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_dropship
 * User         Joshua Stewart
 * Date         02/09/2014
 * Time         15:09
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

/**
 * DROP-99
 */

$installer = $this;

$installer->startSetup();

$dropshipTable = $installer->getTable('dropship');

if (Mage::helper('wsalogger')->getNewVersion() > 10) {

    $wareType = array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'WebShopApps Dropcommon',
        'nullable' => true,
        'default' => null);

    $installer->getConnection()->addColumn($dropshipTable, 'dest_region', $wareType);

} else {
    if(!$installer->getConnection()->tableColumnExists($dropshipTable, 'dest_region')){
        $installer->run("
            ALTER IGNORE TABLE {$dropshipTable} ADD dest_region TEXT NULL DEFAULT NULL COMMENT 'WebShopApps Dropcommon';
        ");
    }
}

$installer->endSetup();