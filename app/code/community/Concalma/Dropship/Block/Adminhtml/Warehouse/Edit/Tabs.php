<?php
class Concalma_Dropship_Block_Adminhtml_Warehouse_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{ 
		parent::__construct();
		$this->setId('dropship_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('dropship')->__('Warehouse Information'));
	}
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array('label' => Mage::helper('dropship')->__('Dropship Information'),'title' => Mage::helper('dropship')->__('Dropship Information'),'content' => $this->getLayout()->createBlock('dropship/adminhtml_warehouse_edit_tab_form')->toHtml(),
		));
		
		
		return parent::_beforeToHtml();
	}
}
