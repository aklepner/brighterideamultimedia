<?php

class Concalma_Dropship_Block_Adminhtml_Dropship_Edit_Tab_Formfedex extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
        $form = new Varien_Data_Form(array('id' => 'adddropship', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','adddropship');
		$this->setForm($form);
		$fieldset = $form->addFieldset('fedex_form', array('legend'=>Mage::helper('dropship')->__('Fedex Login Details')));
		$fieldset->addField('fedex_account_id', 'text', array(
'label' => Mage::helper('dropship')->__('Account ID'),
'class' => 'required-entry',
'required' => true,
'name' => 'fedex_account_id',
		));

		/////////////////////////////////////     Fedex SOAP Additional Login Details
		$fieldsetaddition = $form->addFieldset('fedex_form_addition', array('legend'=>Mage::helper('dropship')->__('Fedex SOAP Additional Login Details')));
		
		$fieldsetaddition->addField('fedexsoap_key', 'text', array(
'label' => Mage::helper('dropship')->__('Key'),
'class' => 'required-entry',
'required' => true,
'name' => 'fedexsoap_key',
		));
		
		$fieldsetaddition->addField('fedexsoap_password', 'text', array(
'label' => Mage::helper('dropship')->__('Password'),
'class' => 'required-entry',
'required' => true,
'name' => 'fedexsoap_password',
		));
		
		$fieldsetaddition->addField('fedexsoap_meter_number', 'text', array(
'label' => Mage::helper('dropship')->__('Meter Number'),
'class' => 'required-entry',
'required' => true,
'name' => 'fedexsoap_meter_number',
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
