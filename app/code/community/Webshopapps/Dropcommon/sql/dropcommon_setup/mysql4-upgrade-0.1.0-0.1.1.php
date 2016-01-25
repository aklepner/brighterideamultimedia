<?php
/* ExtName
 *
 * User        karen
 * Date        1/26/14
 * Time        11:23 PM
 * @category   Webshopapps
 * @package    Webshopapps_Dropcommon
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

$installer = $this;

$installer->startSetup();

if(!$installer->getConnection()->tableColumnExists($this->getTable('dropship'), 'dest_country')){
    $installer->run("
        ALTER IGNORE TABLE {$this->getTable('dropship')} ADD dest_country text NULL COMMENT 'webshopapps dropship';
    ");
}

$installer->endSetup();