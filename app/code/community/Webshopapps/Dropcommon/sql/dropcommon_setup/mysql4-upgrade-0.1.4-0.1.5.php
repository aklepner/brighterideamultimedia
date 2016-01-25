<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Dropcommon
 * User         Joshua Stewart
 * Date         29/05/2014
 * Time         16:47
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

$installer = $this;

$installer->startSetup();

$installer->run("
    CREATE TABLE  IF NOT EXISTS `{$installer->getTable('webshopapps_dropship_order_shipping')}` (
      `id` int(11) NOT NULL auto_increment,
      `order_increment` varchar(50) NULL,
      `warehouse_id` int(11) unsigned NULL,
      `shipping_price` decimal(12,4) NULL,
      `shipping_method` varchar(255) NULL,
      `shipping_code` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();