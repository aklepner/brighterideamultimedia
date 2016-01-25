<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Dropcommon
 * User         Joshua Stewart
 * Date         05/08/2014
 * Time         12:25
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

/**
 * DROP
 */

$installer = $this;

$installer->startSetup();

/**
 * DROP-94
 */

$dropshipTable = $installer->getTable('dropship');

if (Mage::helper('wsalogger')->getNewVersion() > 10) {


    $wareType = array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Warehouse Type',
        'length' => '1',
        'nullable' => false,
        'default' => '0');

    $installer->getConnection()->addColumn($dropshipTable, 'warehouse_type', $wareType);

} else {
    if(!$installer->getConnection()->tableColumnExists($dropshipTable, 'warehouse_type')){
        $installer->run("
            ALTER IGNORE TABLE {$dropshipTable} ADD warehouse_type INT(1) NOT NULL DEFAULT '0' COMMENT 'webshopapps dropcommon';
        ");
    }
}

$installer->endSetup();