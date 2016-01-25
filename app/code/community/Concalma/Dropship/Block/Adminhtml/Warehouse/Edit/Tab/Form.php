<?php

class Concalma_Dropship_Block_Adminhtml_Warehouse_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
        $form = new Varien_Data_Form(array('id' => 'adddropship', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','adddropship');
		$this->setForm($form);
		$fieldset = $form->addFieldset('dropship_form', array('legend'=>Mage::helper('dropship')->__('Warehouse  Information')));
		$fieldset->addField('product_id', 'text', array(
'label' => Mage::helper('dropship')->__('Product Id'),
'class' => 'required-entry',
'required' => true,
'name' => 'product_id',
		));
		
	
		$fieldset->addField('status', 'select', array(
'label' => Mage::helper('dropship')->__('Approve this'),
'name' => 'status',
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



//end of shipping method		
		
		
		
		if ( Mage::getSingleton('adminhtml/session')->getdropshipData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getdropshipData());
			Mage::getSingleton('adminhtml/session')->setdropshipData(null);
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
