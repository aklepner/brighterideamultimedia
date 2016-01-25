<?php

class Concalma_Dropship_Model_Zipcodes extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('dropship/zipcodes');
    }
 
	public function getPair($id) {
		
		//$id = $this->getRequest()->getParam('id');
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		
	}
}
