<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Shipmethods_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('shipmethods_form', array('legend'=>Mage::helper('dropcommon')->__('Combine Shipping Methods')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('dropcommon')->__('Method Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('description', 'text', array(
          'label'     => Mage::helper('dropcommon')->__('Description'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'description',
      ));
      
      if ( Mage::getSingleton('adminhtml/session')->getShipmethodsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getShipmethodsData());
          Mage::getSingleton('adminhtml/session')->setShipmethodsData(null);
      } elseif ( Mage::registry('shipmethods_data') ) {
          $form->setValues(Mage::registry('shipmethods_data')->getData());          
      } 
      
      return parent::_prepareForm();
  }
  
  
}