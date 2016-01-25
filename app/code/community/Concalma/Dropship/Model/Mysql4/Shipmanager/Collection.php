<?php

class Concalma_Dropship_Model_Mysql4_Shipmanager_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('dropship/shipmanager');
    }
}