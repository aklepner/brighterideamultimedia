<?php

class Concalma_Dropship_Model_Warehouse extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('dropship/warehouse');
    }
 
	public function getPair($id) {
		
		//$id = $this->getRequest()->getParam('id');
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		
	}
}
