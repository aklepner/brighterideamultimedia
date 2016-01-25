<?php
class Concalma_Dropship_Block_Adminhtml_Dropship_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
		$this->addTab('form_section', array('label' => Mage::helper('dropship')->__('Dropship Information'),'title' => Mage::helper('dropship')->__('Dropship Information'),'content' => $this->getLayout()->createBlock('dropship/adminhtml_dropship_edit_tab_form')->toHtml(),
		));
		
		$this->addTab('form_up',  array('label' => Mage::helper('dropship')->__('UPS Login Details'),'title' => Mage::helper('dropship')->__('UPS Login Details'),'content' => $this->getLayout()->createBlock('dropship/adminhtml_dropship_edit_tab_formup')->toHtml(),
		));
		$this->addTab('form_fedex',  array('label' => Mage::helper('dropship')->__('Fedex Login Details'),'title' => Mage::helper('dropship')->__('Fedex Login Details'),'content' => $this->getLayout()->createBlock('dropship/adminhtml_dropship_edit_tab_formfedex')->toHtml(),
		));
		
		$this->addTab('form_usps',  array('label' => Mage::helper('dropship')->__('USPS Login Details'),'title' => Mage::helper('dropship')->__('USPS Login Details'),'content' => $this->getLayout()->createBlock('dropship/adminhtml_dropship_edit_tab_formusps')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}
