<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_shipmanupg
 * User         Joshua Stewart
 * Date         11/02/2014
 * Time         12:17
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

$installer = $this;

$installer->startSetup();

$shipManager = $installer->getConnection()->fetchAll("select * from {$this->getTable('core_config_data')} where path in ('carriers/shipmanager/active','carriers/shipmanager/serial','carriers/shipmanager/title','carriers/shipmanager/default_warehouse','carriers/shipmanager/use_parent','carriers/shipmanager/handling_type','carriers/shipmanager/handling_action','carriers/shipmanager/handling_fee','carriers/shipmanager/showmethod','carriers/shipmanager/specificerrmsg')");

$search = array('carriers','shipmanager');
$replace = array ('carriers','dropship');

foreach ($shipManager as $r) {
    $r['path'] = str_replace($search,$replace,$r['path']);
    $installer->getConnection()->update($this->getTable('core_config_data'), $r, 'config_id='.$r['config_id']);
}

$installer->endSetup();