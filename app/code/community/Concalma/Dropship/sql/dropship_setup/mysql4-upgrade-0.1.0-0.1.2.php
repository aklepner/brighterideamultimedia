<?php
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'shipping_warehouse', array(
        'group'             => 'General',
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Warehouse',
        'input'             => '',
        'class'             => '',
        'source'            => '',
        'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
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


$setup->updateAttribute('catalog_product', 'shipping_warehouse  ', 'used_in_product_listing', '1');

$setup->endSetup();


$INSTALLER = $THIS;
$setup  = new Mage_Eav_Model_Entity_Setup();
 
