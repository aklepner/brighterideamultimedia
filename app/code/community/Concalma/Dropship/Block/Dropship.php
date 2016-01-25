<?php
class Concalma_Dropship_Block_Adminhtml_Dropship extends Mage_Adminhtml_Block_Widget_Grid_Container
{
public function __construct()
	{
echo "grid";
		$this->_controller = 'adminhtml_dropship';
		$this->_blockGroup = 'dropship';
		$this->_headerText = Mage::helper('dropship')->__('Warehouse manager');
		$this->_addButtonLabel = Mage::helper('dropship')->__('Add Warehouse');
		parent::__construct();
	}
}
