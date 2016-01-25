<?php

$installer = $this;

$installer->startSetup();

$installer->run("
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_key varchar(255) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_password varchar(255) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_meter_number varchar(255) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_street varchar(60) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_city varchar(30) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_zipcode varchar(30) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_state varchar(255) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_country varchar(255) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_freight_role varchar(30) NULL COMMENT 'webshopapps dropship';
            ALTER IGNORE TABLE {$this->getTable('dropship')} ADD fedexfreight_payment_type varchar(30) NULL COMMENT 'webshopapps dropship';
        ");

$installer->endSetup();