<?php

/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Dropcommon
 * User         Joshua Stewart
 * Date         29/05/2014
 * Time         16:40
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('dropship')} (
      `dropship_id` int(11) unsigned NOT NULL auto_increment COMMENT 'webshopapps dropcommon',
      `title` varchar(255) NOT NULL default '' COMMENT 'webshopapps dropcommon',
      `description` varchar(255) NOT NULL default '' COMMENT 'webshopapps dropcommon',
      `warehouse_code` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `status` smallint(6) NOT NULL default '0' COMMENT 'webshopapps dropcommon',
      `country` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `region` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `zipcode` varchar(30) NULL COMMENT 'webshopapps dropcommon',
      `street` varchar(60) NULL COMMENT 'webshopapps dropcommon',
      `city` varchar(30) NULL COMMENT 'webshopapps dropcommon',
      `email` varchar (255) NULL COMMENT 'webshopapps dropcommon',
      `contact` varchar (255) NULL COMMENT 'webshopapps dropcommon',
      `manualmail` tinyint(1) NOT NULL default '1' COMMENT 'webshopapps dropcommon',
      `manualship` tinyint(1) NOT NULL default '1' COMMENT 'webshopapps dropcommon',
      `created_time` datetime NULL COMMENT 'webshopapps dropcommon',
      `update_time` datetime NULL COMMENT 'webshopapps dropcommon',
      `latitude` decimal(15,10) NOT NULL COMMENT 'webshopapps dropcommon',
      `longitude` decimal(15,10) NOT NULL COMMENT 'webshopapps dropcommon',
      `ismetapak` tinyint(1) NOT NULL default '0' COMMENT 'webshopapps dropcommon',
      `geo_address` text NULL COMMENT 'webshopapps dropcommon',
      `fedex_account_id` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexsoap_key` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexsoap_password` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexsoap_meter_number` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexsoap_allowed_methods` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_password` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_access_license_number` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_user_id` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_shipper_number` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_shipping_origin` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `max_package_weight` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_allowed_methods` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `ups_unit_of_measure` varchar(10) NULL COMMENT 'webshopapps dropcommon',
      `usps_user_id` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `usps_password` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `usps_allowed_methods` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_account_id` varchar(30) DEFAULT NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_key` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_password` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_meter_number` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_street` varchar(60) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_city` varchar(30) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_zipcode` varchar(30) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_state` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_country` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_freight_role` varchar(30) NULL COMMENT 'webshopapps dropcommon',
      `fedexfreight_payment_type` varchar(30) NULL COMMENT 'webshopapps dropcommon',
      `dest_country` text NULL,
      `warehouse_type` int(1) NOT NULL DEFAULT '0' COMMENT 'webshopapps dropcommon',
  PRIMARY KEY (`dropship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='webshopapps dropcommon';

CREATE TABLE IF NOT EXISTS {$this->getTable('dropship_shipping')} (
    `dropship_id` int(11)  unsigned NOT NULL default '0' COMMENT 'webshopapps dropcommon',
    `shipping_id` varchar(255)  NULL COMMENT 'webshopapps dropcommon',
    PRIMARY KEY  (`dropship_id`,`shipping_id`),
    CONSTRAINT `FK_DROPSHIP_SHIPPING_DROPSHIP` FOREIGN KEY (`dropship_id`) REFERENCES {$this->getTable('dropship')} (`dropship_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='webshopapps dropcommon';

CREATE TABLE IF NOT EXISTS {$this->getTable('webshopapps_shipmethods')} (
  `shipmethods_id` int(11) unsigned NOT NULL auto_increment COMMENT 'webshopapps dropcommon',
  `title` varchar(255) NOT NULL default '' COMMENT 'webshopapps dropcommon',
  `description` varchar(255) NOT NULL default '' COMMENT 'webshopapps dropcommon',
  `created_time` datetime NULL COMMENT 'webshopapps dropcommon',
  `update_time` datetime NULL COMMENT 'webshopapps dropcommon',
  PRIMARY KEY (`shipmethods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='webshopapps dropcommon';

CREATE TABLE IF NOT EXISTS {$this->getTable('webshopapps_shipmethods_carriers')} (
    `shipmethods_id` int(11)  unsigned NOT NULL default '0' COMMENT 'webshopapps dropcommon',
    `carrier_code` varchar(255)  NULL COMMENT 'webshopapps dropcommon',
    `carrier_title` varchar(255)  NULL COMMENT 'webshopapps dropcommon',
    `warehouse` int(5) NULL COMMENT 'webshopapps dropcommon',
     PRIMARY KEY  (`shipmethods_id`,`carrier_code`,`warehouse`),
   CONSTRAINT `FK_WEBSHOPAPPS_SHIPMETHODS_CARRIERS` FOREIGN KEY (`shipmethods_id`) REFERENCES {$this->getTable('webshopapps_shipmethods')} (`shipmethods_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='webshopapps dropcommon';

CREATE TABLE IF NOT EXISTS {$this->getTable('webshopapps_sm_upscarriers')} (
    `upscarriers_id` int(11)  unsigned NOT NULL default '0' COMMENT 'webshopapps dropcommon',
    `origin` varchar(255)  NULL COMMENT 'webshopapps dropcommon',
    `carrier_title` varchar(255)  NULL COMMENT 'webshopapps dropcommon',
    `warehouse` int(5) NULL COMMENT 'webshopapps dropcommon',
     PRIMARY KEY  (`upscarriers_id`,`origin`,`warehouse`),
   CONSTRAINT `FK_WEBSHOPAPPS_SM_UPSCARRIERS` FOREIGN KEY (`upscarriers_id`) REFERENCES {$this->getTable('webshopapps_shipmethods')} (`shipmethods_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='webshopapps dropcommon';

CREATE TABLE  IF NOT EXISTS `{$installer->getTable('webshopapps_dropship_order_shipping')}` (
      `id` int(11) NOT NULL auto_increment COMMENT 'webshopapps dropcommon',
      `order_increment` varchar(50) NULL COMMENT 'webshopapps dropcommon',
      `warehouse_id` int(11) unsigned NULL COMMENT 'webshopapps dropcommon',
      `shipping_price` decimal(12,4) NULL COMMENT 'webshopapps dropcommon',
      `shipping_method` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      `shipping_code` varchar(255) NULL COMMENT 'webshopapps dropcommon',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='catalog_product';

select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='Shipping' and attribute_set_id = @attribute_set_id;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id         = @entity_type_id,
        attribute_code         = 'warehouse',
        backend_type        = 'varchar',
        frontend_input        = 'multiselect',
    	source_model    = 'dropcommon/dropship',
        backend_model = 'eav/entity_attribute_backend_array',
        is_required = 0,
        frontend_label        = 'Warehouse';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='warehouse' and entity_type_id = @entity_type_id;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;


");

if (Mage::helper('wsalogger')->getNewVersion() > 10) {

    $wareAttr = array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Warehouse',
        'length' => '5',
        'nullable' => 'false',
        'default' => '0');

    $wareDetails = array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Warehouse Details',
        'nullable' => 'true',
    );

    $wareHtml = array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Warehouse Html',
        'nullable' => 'true',
    );

    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'warehouse', $wareAttr);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'warehouse_shipping_details', $wareDetails);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'), 'warehouse', $wareAttr);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'), 'warehouse_shipping_details', $wareDetails);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'warehouse_shipping_html', $wareHtml);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'warehouse', $wareAttr);
    $installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'warehouse', $wareAttr);

} else  {

    $wareAttr = array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Warehouse',
        'length' => '5',
        'nullable' => 'false',
        'default' => '0');

    $quoteAddressTable = $installer->getTable('sales/quote_address');
    $quoteRateTable = $installer->getTable('sales/quote_address_shipping_rate');
    $quoteItemTable = $installer->getTable('sales/quote_item');
    $orderItemTable = $installer->getTable('sales/order_item');



    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'warehouse_shipping_details')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD warehouse_shipping_details text COMMENT 'webshopapps dropcommon';
        ");
    }

    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'warehouse')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
        ");
    }

    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'warehouse_shipping_html')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD warehouse_shipping_html text COMMENT 'webshopapps dropcommon';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteRateTable, 'warehouse_shipping_details')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteRateTable} ADD warehouse_shipping_details text COMMENT 'webshopapps dropcommon';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteRateTable,  'warehouse')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteRateTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteItemTable,  'warehouse')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteItemTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($orderItemTable,  'warehouse')){
        $installer->run("
            ALTER IGNORE TABLE {$orderItemTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
        ");
    }
}


if (Mage::helper('wsalogger')->getNewVersion() >= 8) {
    if (Mage::helper('wsalogger')->getNewVersion() > 10) {
        $installer->getConnection()->addColumn($installer->getTable('sales/shipment_grid'),'warehouse',$wareAttr);
        $installer->getConnection()->addColumn($installer->getTable('sales/shipment'),'warehouse',$wareAttr);
        $installer->getConnection()->addColumn($installer->getTable('sales/shipment'),'shipping_description',array(
            'type'    	=> Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'	=> 255,
            'comment' 	=> 'Shipping Description',
            'nullable' 	=> 'true',));
        $installer->getConnection()->addColumn($installer->getTable('sales/order'), 'warehouse', $wareAttr);
        $installer->getConnection()->addColumn($installer->getTable('sales/order'), 'warehouse_shipping_html', $wareHtml);
        $installer->getConnection()->addColumn($installer->getTable('sales/order'), 'warehouse_shipping_details', $wareDetails);

        if  (Mage::helper('wsalogger')->isEnterpriseEdition() ) {
            $installer->getConnection()->addColumn($installer->getTable('enterprise_sales_shipment_grid_archive'),'warehouse',$wareAttr);
        }

    } else {
        $orderTable = $installer->getTable('sales/order');
        $shipmentTable = $installer->getTable('sales/shipment');
        $shipmentGridTable = $installer->getTable('sales/shipment_grid');

        if(!$installer->getConnection()->tableColumnExists($shipmentTable, 'shipping_description')){
            $installer->run("
                ALTER IGNORE TABLE {$shipmentTable} ADD shipping_description varchar(255) COMMENT 'webshopapps dropship';
            ");
        }

        if(!$installer->getConnection()->tableColumnExists($shipmentTable, 'warehouse')){
            $installer->run("
                ALTER IGNORE TABLE {$shipmentTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
            ");
        }

        if(!$installer->getConnection()->tableColumnExists($orderTable, 'warehouse_shipping_details')){
            $installer->run("
                ALTER IGNORE TABLE {$orderTable} ADD warehouse_shipping_details text COMMENT 'webshopapps dropcommon';
            ");
        }

        if(!$installer->getConnection()->tableColumnExists($orderTable, 'warehouse_shipping_html')){
            $installer->run("
                ALTER IGNORE TABLE {$orderTable} ADD warehouse_shipping_html text COMMENT 'webshopapps dropcommon';
            ");
        }
        if(!$installer->getConnection()->tableColumnExists($orderTable, 'warehouse')){
            $installer->run("
                ALTER IGNORE TABLE {$orderTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
            ");
        }

        if(!$installer->getConnection()->tableColumnExists($shipmentGridTable, 'warehouse')){
            $installer->run("
                ALTER IGNORE TABLE {$shipmentGridTable} ADD warehouse INT(5) NOT NULL COMMENT 'Warehouse' default 0;
            ");
        }
    }
}

$installer->run("

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='order';

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'warehouse',
    	backend_type	= 'int',
    	frontend_input	= 'text';

select @attribute_set_id:=attribute_set_id from {$this->getTable('eav_attribute_set')} where entity_type_id=@entity_type_id;
select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='warehouse';
select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='General' and attribute_set_id=@attribute_set_id;

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
    	attribute_set_id 	= @attribute_set_id,
    	attribute_group_id	= @attribute_group_id,
    	attribute_id		= @attribute_id;

insert ignore into {$this->getTable('eav_attribute')}
	set entity_type_id 	= @entity_type_id,
		attribute_code 	= 'warehouse_shipping_details',
		backend_type	= 'text',
    	frontend_input	= 'text';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='warehouse_shipping_details';
select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='General' and attribute_set_id=@attribute_set_id;

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
    	attribute_set_id 	= @attribute_set_id,
    	attribute_group_id	= @attribute_group_id,
    	attribute_id		= @attribute_id;

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='shipment';

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'warehouse',
    	backend_type	= 'int',
    	frontend_input	= 'text';

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'shipping_description',
    	backend_type	= 'varchar',
    	frontend_input	= 'text';

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='order';

insert ignore into {$this->getTable('eav_attribute')}
	set entity_type_id 	= @entity_type_id,
		attribute_code 	= 'warehouse_shipping_html',
		backend_type	= 'text',
    	frontend_input	= 'text';
");

$entityTypeId = $installer->getEntityTypeId('catalog_product');

$attributeSetArr = $installer->getConnection()->fetchAll("SELECT attribute_set_id FROM {$this->getTable('eav_attribute_set')} WHERE entity_type_id={$entityTypeId}");

$attributeId = $installer->getAttributeId($entityTypeId, 'warehouse');

foreach ($attributeSetArr as $attr) {
    $attributeSetId = $attr['attribute_set_id'];

    $installer->addAttributeGroup($entityTypeId, $attributeSetId, 'Shipping', '99');

    $attributeGroupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, 'Shipping');

    $installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, '99');
};

$shipManager = $installer->getConnection()->fetchAll("select * from {$this->getTable('core_config_data')} where path in ('carriers/shipmanager/active','carriers/shipmanager/serial','carriers/shipmanager/title','carriers/shipmanager/default_warehouse','carriers/shipmanager/use_parent','carriers/shipmanager/handling_type','carriers/shipmanager/handling_action','carriers/shipmanager/handling_fee','carriers/shipmanager/showmethod','carriers/shipmanager/specificerrmsg')");

$search = array('carriers','shipmanager');
$replace = array ('carriers','dropship');

foreach ($shipManager as $r) {
    $r['path'] = str_replace($search,$replace,$r['path']);
    $installer->getConnection()->update($this->getTable('core_config_data'), $r, 'config_id='.$r['config_id']);
}

$installer->endSetup();