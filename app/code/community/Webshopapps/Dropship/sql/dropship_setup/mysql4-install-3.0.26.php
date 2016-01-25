<?php

$installer = $this;

$installer->startSetup();

if  (Mage::helper('wsalogger')->getNewVersion() >= 8 ) {
    if  (Mage::helper('wsalogger')->getNewVersion() > 10 ) {


        $dropshipStatus = array(
            'type'    	=> Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'	=> 50,
            'comment' 	=> 'Dropship Status',
            'nullable' 	=> 'true',);

        $manualShip = array(
            'type'    	=> Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'	=> 1,
            'comment' 	=> 'Dropship Manual Shipment',
            'nullable' 	=> 'false',
            'default' 	=> '0');


        $installer->getConnection()->addColumn($installer->getTable('sales/order'),'manual_ship',$manualShip);
        $installer->getConnection()->addColumn($installer->getTable('sales/shipment_grid'),'dropship_status',$dropshipStatus);
        $installer->getConnection()->addColumn($installer->getTable('sales/shipment'),'dropship_status',$dropshipStatus);

        if  (Mage::helper('wsalogger')->isEnterpriseEdition()) {
            $installer->getConnection()->addColumn($installer->getTable('enterprise_sales_shipment_grid_archive'),'dropship_status',$dropshipStatus);
        }

    } else {
        $installer->run("
			ALTER IGNORE TABLE {$this->getTable('sales_flat_order')}  ADD manual_ship INT(1) DEFAULT 0;
			ALTER IGNORE TABLE {$this->getTable('sales_flat_shipment_grid')}  ADD dropship_status varchar(50) COMMENT 'webshopapps dropship';
			ALTER IGNORE TABLE {$this->getTable('sales_flat_shipment')}  ADD dropship_status varchar(50) COMMENT 'webshopapps dropship';

		");
    }

}

$installer->run("


    select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='shipment';

    insert ignore into {$this->getTable('eav_attribute')}
        set entity_type_id 	= @entity_type_id,
            attribute_code 	= 'dropship_status',
            backend_type	= 'varchar',
            frontend_input	= 'text';

");




$installer->endSetup();