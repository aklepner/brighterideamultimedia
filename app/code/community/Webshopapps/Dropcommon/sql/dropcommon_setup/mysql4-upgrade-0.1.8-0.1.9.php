<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_dropship
 * User         Joshua Stewart
 * Date         11/09/2014
 * Time         15:52
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */


$installer = $this;

$installer->startSetup();

$deliveryDate =  array(
    'type'    	=> Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'	=> 255,
    'comment' 	=> 'WebShopApps Dropship',
    'nullable' 	=> 'true',
);

$extraInfo =  array(
    'type'    	=> Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'	=> 255,
    'comment' 	=> 'WebShopApps Dropship',
    'nullable' 	=> 'true',
);

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'dropship_delivery_date', $deliveryDate );
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'dropship_delivery_date',$deliveryDate);
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'dropship_extra_info', $extraInfo );
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'dropship_extra_info',$extraInfo);

if  (Mage::helper('wsalogger')->getNewVersion() > 10 ) {
    $installer->addAttribute('order', 'dropship_delivery_date', array(
        'type'              => 'varchar',
        'label'             => 'Dropship Delivery Date',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'unique'            => false,
    ));
}

if  (Mage::helper('wsalogger')->getNewVersion() > 10 ) {
    $installer->addAttribute('order', 'dropship_extra_info', array(
        'type'              => 'varchar',
        'label'             => 'Dropship Delivery Info',
        'input'             => 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'unique'            => false,
    ));
}

$installer->endSetup();