<?php
/* UsaShipping
 *
 * User        karen
 * Date        1/18/14
 * Time        11:25 PM
 * @category   Webshopapps
 * @package    Webshopapps_Dropcommon
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
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

} else {
    $installer->run("
		ALTER IGNORE TABLE {$this->getTable('sales_flat_quote_address')}  ADD warehouse int(5) COMMENT 'webshopapps dropcommon';
		ALTER IGNORE TABLE {$this->getTable('sales_flat_quote_address')}  ADD warehouse_shipping_details text COMMENT 'webshopapps dropcommon';
		ALTER IGNORE TABLE {$this->getTable('sales_flat_quote_shipping_rate')}  ADD warehouse int(5) COMMENT 'webshopapps dropcommon';
		ALTER IGNORE TABLE {$this->getTable('sales_flat_quote_shipping_rate')}  ADD warehouse_shipping_details text COMMENT 'webshopapps dropcommon';
		ALTER IGNORE TABLE {$this->getTable('sales_flat_quote_address')}  ADD warehouse_shipping_html text COMMENT 'webshopapps dropcommon';
		ALTER IGNORE TABLE {$this->getTable('sales_flat_quote_item')}  ADD warehouse int(5) COMMENT 'webshopapps dropcommon';
		ALTER IGNORE TABLE {$this->getTable('sales_flat_order_item')}  ADD warehouse int(5) COMMENT 'webshopapps dropcommon';
	");

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
        $installer->run("
			ALTER IGNORE TABLE {$this->getTable('sales_flat_shipment_grid')}  ADD warehouse int(5) COMMENT 'webshopapps dropship';
			ALTER IGNORE TABLE {$this->getTable('sales_flat_shipment')}  ADD warehouse int(5) COMMENT 'webshopapps dropship';
			ALTER IGNORE TABLE {$this->getTable('sales_flat_shipment')}  ADD shipping_description varchar(255) COMMENT 'webshopapps dropship';
			ALTER IGNORE TABLE {$this->getTable('sales_flat_order')}  ADD warehouse int(5) COMMENT 'webshopapps dropcommon';
			ALTER IGNORE TABLE {$this->getTable('sales_flat_order')}  ADD warehouse_shipping_details text COMMENT 'webshopapps dropcommon';
			ALTER IGNORE TABLE {$this->getTable('sales_flat_order')}  ADD warehouse_shipping_html text COMMENT 'webshopapps dropcommon';
		
		");
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


$installer->endSetup();