<?php

class Concalma_Dropship_Block_Adminhtml_Dropship_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
        $form = new Varien_Data_Form(array('id' => 'adddropship', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','adddropship');
		$this->setForm($form);
		$fieldset = $form->addFieldset('dropship_form', array('legend'=>Mage::helper('dropship')->__('Warehouse  Information')));
		$fieldset->addField('title', 'text', array(
'label' => Mage::helper('dropship')->__('Title'),
'class' => 'required-entry',
'required' => true,
'name' => 'title',
		));

$fieldset->addField('description', 'text', array(
'label' => Mage::helper('dropship')->__('Description'),
'class' => 'required-entry',
'required' => true,
'name' => 'description',
		));

$fieldset->addField('street', 'text', array(
'label' => Mage::helper('dropship')->__('Origin street'),
'class' => 'required-entry',
'required' => false,
'name' => 'street',
		));


$fieldset->addField('city', 'text', array(
'label' => Mage::helper('dropship')->__('Origin city'),
'class' => 'required-entry',
'required' => false,
'name' => 'city',
		));



$fieldset->addField('state_code', 'text', array(
'label' => Mage::helper('dropship')->__('State Code'),
'class' => 'required-entry',
'required' => false,
'name' => 'state_code',
		));


$fieldset->addField('zip_code', 'text', array(
'label' => Mage::helper('dropship')->__('Origin Zip/Postal Code'),
'class' => 'required-entry',
'required' => false,
'name' => 'zip_code',
		));


$fieldset->addField('country', 'text', array(
'label' => Mage::helper('dropship')->__('Country'),
'class' => 'required-entry',
'required' => false,
'name' => 'country',
		));

$fieldset->addField('email', 'text', array(
'label' => Mage::helper('dropship')->__('Email'),
'class' => 'required-entry',
'required' => false,
'name' => 'email',
		));

$fieldset->addField('contact_name', 'text', array(
'label' => Mage::helper('dropship')->__('Contact Name'),
'class' => 'required-entry',
'required' => false,
'name' => 'contact_name',
		));


		$fieldset->addField('spm', 'select', array(
'label' => Mage::helper('dropship')->__('Send Packing Slips to Warehouses Manually'),
'name' => 'spm',
'values' => array(
		array(
'value' => 1,
'label' => Mage::helper('dropship')->__('Yes'),
		),
		array(
'value' => 0,
'label' => Mage::helper('dropship')->__('No'),
		),
		),
		));
		
		
//add the shipping method

		$fieldset->addField('method', 'select', array(
'label' => Mage::helper('dropship')->__('Applicalbe shipping method'),
'name' => 'method[]',
'multiple' => 'multiple',
'size'	=> '10',			
'values' => array(
		array(
'value' => 0,
'label' => Mage::helper('dropship')->__('Flat Rate - Fixed'),
		),
		array(
'value' => 1,
'label' => Mage::helper('dropship')->__('Free Ship - Free'),
		),
		array(
'value' => 2,
'label' => Mage::helper('dropship')->__('Best Way - Table Rate'),
		),
	array(
'value' => 3,
'label' => Mage::helper('dropship')->__('DHL'),
		),	
		array(
'value' => 4,
'label' => Mage::helper('dropship')->__('Federal Express'),
		),
		array(
'value' => 5,
'label' => Mage::helper('dropship')->__('United Parcel Service'),
		),
		array(
'value' => 6,
'label' => Mage::helper('dropship')->__('United State Postal Service'),
		),
		),
		));

//end of shipping method		
		
		
		
		if ( Mage::getSingleton('adminhtml/session')->getdropshipData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getdropshipData());
			Mage::getSingleton('adminhtml/session')->setdripshipData(null);
		} elseif ( Mage::registry('dropship_data') ) {
			$form->setValues(Mage::registry('dropship_data')->getData());
		}
		return parent::_prepareForm();
	}
 public function getFormHtml()
    {   $html = parent::getFormHtml();
       
        $html.= '<script type= "text/javascript" > 
        function hungnam() {
                  var select = document.getElementsByTagName("select");
                  select[1].setAttribute("multiple", "multiple");
        }
       
       hungnam();
        </script>;';
         
        return $html;
      
        
    }
}
