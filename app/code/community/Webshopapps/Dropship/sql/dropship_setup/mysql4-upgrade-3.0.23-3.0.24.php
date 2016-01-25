<?php

$installer = $this;

$installer->startSetup();

$installer->run("
        ALTER IGNORE TABLE {$this->getTable('dropship')} ADD storepickup_applicable_method varchar(225) NULL COMMENT 'webshopapps dropship';
    ALTER IGNORE TABLE {$this->getTable('dropship')} ADD `fedexfreight_account_id` varchar(30) DEFAULT NULL COMMENT 'webshopapps dropship';
");

$installer->endSetup();
