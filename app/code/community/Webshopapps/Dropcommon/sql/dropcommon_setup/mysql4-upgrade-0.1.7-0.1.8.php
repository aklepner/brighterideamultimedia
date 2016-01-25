<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_dropship
 * User         Joshua Stewart
 * Date         02/09/2014
 * Time         17:21
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

/**
 * DROP-96
 */

$installer = $this;

$installer->startSetup();

$dropshipTable = $installer->getTable('dropship');

if (Mage::helper('wsalogger')->getNewVersion() > 10) {

    $hubID = array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'WebShopApps Dropcommon',
        'length' => 4,
        'nullable' => true,
        'default' => null);

    $installer->getConnection()->addColumn($dropshipTable, 'fedexsoap_hub_id', $hubID);

} else {
    if(!$installer->getConnection()->tableColumnExists($dropshipTable, 'dest_region')){
        $installer->run("
            ALTER IGNORE TABLE {$dropshipTable} ADD fedexsoap_hub_id INT(4) NULL DEFAULT NULL COMMENT 'WebShopApps Dropcommon';
        ");
    }
}

$installer->endSetup();