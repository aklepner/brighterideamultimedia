<?php
class HN_Combine_Block_Adminhtml_Combine_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('combine_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('combine')->__('Combine Information'));
	}
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array('label' => Mage::helper('combine')->__('Combine Information'),'title' => Mage::helper('combine')->__('Combine Information'),'content' => $this->getLayout()->createBlock('combine/adminhtml_combine_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}