<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'warehouse', array(
        'group'             => 'General',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Warehouse',
        'input'             => 'select',
        'class'             => '',
        'source'            => '',
        'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable'   => false,
        'used_in_product_listing', '1'
    ));


$installer->updateAttribute('catalog_product', 'shipping_warehouse  ', 'used_in_product_listing', '1');

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('dropship')};
CREATE TABLE {$this->getTable('dropship')} (
`id` int(11) unsigned NOT NULL auto_increment,
`state_code` int(11) NOT NULL default '0',
`zip_code` int(11) NOT NULL default '0',
`title` varchar(127) NOT NULL default '',
`description` varchar(127) NOT NULL default '',
`street` varchar(127) NOT NULL default '',
`city` varchar(127) NOT NULL default '',
`country` varchar(127) NOT NULL default '',
`email` varchar(127) NOT NULL default '',
`name` varchar(127) NOT NULL default '',
`contact_name` varchar(127) NOT NULL default '',
`spm` smallint(6) NOT NULL default '0',

`shipping_method` int(11) NOT NULL default '0',
`ups_user_id` varchar(127) NOT NULL default '',
`ups_password` varchar(127) NOT NULL default '',
`ups_access_license_number` varchar(127) NOT NULL default '',
`ups_shipper_number` varchar(127) NOT NULL default '',

`fedex_account_id` varchar(127) NOT NULL default '',
`fedexsoap_key` varchar(127) NOT NULL default '',
`fedexsoap_password` varchar(127) NOT NULL default '',
`fedexsoap_meter_number` varchar(127) NOT NULL default '',

`usps_user_id` varchar(127) NOT NULL default '',

`created_time` datetime NULL,
`update_time` datetime NULL,
`flat_enable` tinyint(4) DEFAULT '0',
 `table_enable` tinyint(4) DEFAULT '0',
 `free_enable` tinyint(4) NOT NULL DEFAULT '0',
 `ups_enable` tinyint(4) NOT NULL DEFAULT '0',
 `fedex_enable` tinyint(4) NOT NULL DEFAULT '0',
 `usps_enable` tinyint(4) NOT NULL DEFAULT '0',
 `dhl_enable` tinyint(4) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");





$installer->run("
DROP TABLE IF EXISTS {$this->getTable('zip_codes')};
CREATE TABLE {$this->getTable('zip_codes')} (
  `zip` varchar(5) NOT NULL default '',
  `state` char(2) NOT NULL default '',
  `latitude` varchar(10) NOT NULL default '',
  `longitude` varchar(10) NOT NULL default '',
  `city` varchar(50) default NULL,
  `full_state` varchar(50) default NULL,
  UNIQUE KEY `zip` (`zip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"
);
$orderTable = $installer->getTable('sales/order');

$installer ->run("
ALTER TABLE $orderTable ADD `warehouse` varchar(50) DEFAULT NULL;
ALTER TABLE $orderTable ADD `pseudo_increment_id` int(11) DEFAULT NULL;
ALTER TABLE $orderTable ADD `use_customer_account` varchar(10) DEFAULT 'false';
ALTER TABLE $orderTable ADD `account_id` varchar(10) DEFAULT NULL;
ALTER TABLE $orderTable ADD  `account_zipcode` varchar(10) DEFAULT NULL;
"
);
$installer->endSetup();
