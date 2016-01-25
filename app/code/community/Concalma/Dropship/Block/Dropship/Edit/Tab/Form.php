<?php

class HN_Combine_Block_Adminhtml_Combine_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//$form = new Varien_Data_Form(array("encrypt","multipart/form-data"));
        $form = new Varien_Data_Form(array('id' => 'addcombine', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
		$form->setData('enctype','multipart/form-data');
		$form->setData('id','addcombine');
		$this->setForm($form);
		$fieldset = $form->addFieldset('combine_form', array('legend'=>Mage::helper('combine')->__('Combined Product Information')));
		$fieldset->addField('first_product_id', 'text', array(
'label' => Mage::helper('combine')->__('First Product Id'),
'class' => 'required-entry',
'required' => true,
'name' => 'first_product_id',
		));
		$fieldset->addField('status', 'select', array(
'label' => Mage::helper('combine')->__('Status'),
'name' => 'status',
'values' => array(
		array(
'value' => 1,
'label' => Mage::helper('combine')->__('Active'),
		),
		array(
'value' => 0,
'label' => Mage::helper('combine')->__('Inactive'),
		),
		),
		));
		$fieldset->addField('second_product_id', 'text', array(
'label' => Mage::helper('combine')->__('Second Product Id'),
'class' => 'required-entry',
'required' => true,
'name' => 'second_product_id',
		));
		
		if ( Mage::getSingleton('adminhtml/session')->getcombineData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getcombineData());
			Mage::getSingleton('adminhtml/session')->setcombineData(null);
		} elseif ( Mage::registry('combine_data') ) {
			$form->setValues(Mage::registry('combine_data')->getData());
		}
		return parent::_prepareForm();
	}
 public function getFormHtml()
    {   $html = parent::getFormHtml();
       
        $html.= '<script type= "text/javascript" > 
        document.observe("dom:loaded", function() {
    // initially hide all containers for tab content
         document.getElementById("edit_form").setAttribute("enctype","multipart/form-data");
         document.getElementById("edit_form").setAttribute("onsubmit","submitproduct()");
         var edit_form = document.getElementById("edit_form");
         Event.observe(edit_form,"submit",function() { alert("Luu Thuy"); });
        });
        function thuy() {
        //var edit_form = document.getElementById("edit_form");
        Event.observe($("edit_form"),"submit",function() { alert("Luu Thuy"); });
        }
        function submitproduct() {
        	alert("Luu Thuy");
        	new Ajax.Request("http://127.0.0.1/magdemo/index.php/admin/catalog_product/save/set/9/type/simple/?product[name]=gift&product[sku]=gift&product[weight]=0&product[status]=1&product[tax_class_id]=0&product[visibility]=1&product[price]=12&product[description]=test&product[short_description]=test&product[stock_data][qty]=10000&product[stock_data][is_in_stock]=1", {
  method: "get",
  
});
        }
        </script>;';
         
        return $html;
      
        
    }
}