<?php

$installer = $this;

$installer->startSetup();

if(!$installer->getConnection()->tableColumnExists($this->getTable('dropship'), 'storepickup_applicable_method')) {
    $installer->run("
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD storepickup_applicable_method varchar(255) NULL COMMENT 'webshopapps dropship';
    ");
}
$installer->endSetup();