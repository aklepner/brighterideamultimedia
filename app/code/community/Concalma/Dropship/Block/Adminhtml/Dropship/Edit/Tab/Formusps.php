<?php

class Concalma_Dropship_Block_Adminhtml_Dropship_Edit_Tab_Formusps extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
		$form = new Varien_Data_Form(array('id' => 'adddropship', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','adddropship');
		$this->setForm($form);
		$fieldset = $form->addFieldset('dropship_form', array('legend'=>Mage::helper('dropship')->__('USPS Login Details')));
		$fieldset->addField('usps_user_id', 'text', array(
'label' => Mage::helper('dropship')->__('User ID'),
'class' => 'required-entry',
'required' => true,
'name' => 'usps_user_id',
		));
		if ( Mage::getSingleton('adminhtml/session')->getdropshipData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getdropshipData());
			Mage::getSingleton('adminhtml/session')->setdripshipData(null);
		} elseif ( Mage::registry('dropship_data') ) {
			$form->setValues(Mage::registry('dropship_data')->getData());
		}
		return parent::_prepareForm();

	}
}
