<?php
class Concalma_Dropship_Block_Adminhtml_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Container
{
public function __construct()
	{

		$this->_controller = 'adminhtml_warehouse';
		$this->_blockGroup = 'dropship';
		$this->_headerText = Mage::helper('dropship')->__('Shipping Method Combiner');
		$this->_addButtonLabel = Mage::helper('dropship')->__('Add Definition');
		parent::__construct();
	}
}
