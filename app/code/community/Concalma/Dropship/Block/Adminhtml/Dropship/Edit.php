<?php
class Concalma_Dropship_Block_Adminhtml_Dropship_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'dropship';
		$this->_controller = 'adminhtml_dropship';
		$this->_updateButton('save', 'label', Mage::helper('dropship')->__('Save Warehouse'));
		$this->_updateButton('delete', 'label', Mage::helper('dropship')->__('Delete Warehouse'));
	}
	public function getHeaderText()
	{
		if( Mage::registry('dropship_data') && Mage::registry('dropship_data')->getId() ) {
		return Mage::helper('dropship')->__("Edit Warehouse", $this->htmlEscape(Mage::registry('dropship_data')->getTitle()));
	} 
	else {
		return Mage::helper('dropship')->__('Add Warehouse');
	}
	}
}
