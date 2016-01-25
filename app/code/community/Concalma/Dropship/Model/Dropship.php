<?php

class Concalma_Dropship_Model_Dropship extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('dropship/dropship');
    }
 
	public function getPair($id) {
		//$id = $this->getRequest()->getParam('id');
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		
	}
}
