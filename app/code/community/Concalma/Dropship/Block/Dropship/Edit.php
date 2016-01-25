<?php
class HN_Combine_Block_Adminhtml_Combine_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'combine';
		$this->_controller = 'adminhtml_combine';
		$this->_updateButton('save', 'label', Mage::helper('combine')->__('Save Combined Product'));
		$this->_updateButton('delete', 'label', Mage::helper('combine')->__('Delete Combined Product'));
	}
	public function getHeaderText()
	{
		if( Mage::registry('combine_data') && Mage::registry('combine_data')->getId() ) {
		return Mage::helper('combine')->__("Edit Combined Product", $this->htmlEscape(Mage::registry('combine_data')->getTitle()));
	} 
	else {
		return Mage::helper('combine')->__('Add Combined Product');
	}
	}
}