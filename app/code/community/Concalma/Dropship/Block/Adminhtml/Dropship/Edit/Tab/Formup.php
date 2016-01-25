<?php

class Concalma_Dropship_Block_Adminhtml_Dropship_Edit_Tab_Formup extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
        $form = new Varien_Data_Form(array('id' => 'adddropship', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','adddropship');
		$this->setForm($form);
		$fieldset = $form->addFieldset('dropship_form', array('legend'=>Mage::helper('dropship')->__('UPS Login Details')));
		$fieldset->addField('ups_user_id', 'text', array(
'label' => Mage::helper('dropship')->__('User Id'),
'class' => 'required-entry',
'required' => false,
'name' => 'ups_user_id',
		));

$fieldset->addField('ups_password', 'text', array(
'label' => Mage::helper('dropship')->__('Password'),
'class' => 'required-entry',
'required' => false,
'name' => 'ups_password',
		));

$fieldset->addField('ups_access_license_number', 'text', array(
'label' => Mage::helper('dropship')->__('Access License Number'),
'class' => 'required-entry',
'required' => false,
'name' => 'ups_access_license_number',
		));


$fieldset->addField('ups_shipper_number', 'text', array(
'label' => Mage::helper('dropship')->__('Shipper Number'),
'class' => 'required-entry',
'required' => false,
'name' => 'ups_shipper_number',
		));



$fieldset->addField('state_code', 'text', array(
'label' => Mage::helper('dropship')->__('Origin Zip/Postal Code'),
'class' => 'required-entry',
'required' => false,
'name' => 'state_code',
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
