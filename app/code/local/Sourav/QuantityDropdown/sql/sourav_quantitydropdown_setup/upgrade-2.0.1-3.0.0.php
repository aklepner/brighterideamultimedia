<?php

$installer = $this;

$installer->startSetup();


//$installer->removeAttribute('catalog_product','simple_quantity_enable');
//$installer->removeAttribute('catalog_product','simple_quantity');


$enableOption=array(
'group' => 'Product Quantity DropDown',
'type' => 'int',
'label' => 'Enable Dropdown Quantity Option',
'input' => 'boolean',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
'visible' => true,
'source'  => 'eav/entity_attribute_source_boolean',
'required' => false, 
'searchable' => false,
'filterable' => false,
'comparable' => false,
'visible_on_front' =>true,
'unique' => false,
'apply_to' => '',

);

$installer->addAttribute('catalog_product','dream_quantity_enable',$enableOption);







$data=array(
'group' => 'Product Quantity DropDown',
'type' => 'varchar',
'label' => 'Product Quantity Options',
'input' => 'text',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
'visible' => true,
'required' => false, 
'searchable' => false,
'filterable' => false,
'comparable' => false,
'visible_on_front' =>true,
'unique' => false,
'apply_to' => '',
);

$installer->addAttribute('catalog_product','dream_quantity_option',$data);







$installer->endSetup();