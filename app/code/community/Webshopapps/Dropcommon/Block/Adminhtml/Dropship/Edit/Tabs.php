<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function _construct()
  {
      parent::_construct();
      $this->setId('dropship_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('dropcommon')->__('Warehouse Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('dropcommon')->__('Warehouse Information'),
          'title'     => Mage::helper('dropcommon')->__('Warehouse Information'),
          'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_destination', array(
          'label'     => Mage::helper('dropcommon')->__('Allowed Destinations'),
          'title'     => Mage::helper('dropcommon')->__('Allowed Destinations'),
          'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_destination')->toHtml(),
      ));

      $this->addTab('form_ups', array(
          'label'     => Mage::helper('dropcommon')->__('UPS Login Details'),
          'title'     => Mage::helper('dropcommon')->__('UPS Login Details'),
          'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_ups')->toHtml(),
      ));
      
      $this->addTab('form_fedex', array(
          'label'     => Mage::helper('dropcommon')->__('Fedex Login Details'),
          'title'     => Mage::helper('dropcommon')->__('Fedex Login Details'),
          'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_fedex')->toHtml(),
      ));

      if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsafedexfreight','carriers/wsafedexfreight/active')) {
              $this->addTab('form_fedex_freight', array(
              'label'     => Mage::helper('dropcommon')->__('Fedex Freight Login Details'),
              'title'     => Mage::helper('dropcommon')->__('Fedex Freight Login Details'),
              'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_fedexfreight')->toHtml(),
          ));
      }
      
      $this->addTab('form_usps', array(
          'label'     => Mage::helper('dropcommon')->__('USPS Login Details'),
          'title'     => Mage::helper('dropcommon')->__('USPS Login Details'),
          'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_usps')->toHtml(),
      ));

      if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsastorepickup','carriers/wsastorepickup/active')) {
          $this->addTab('form_storepickup', array(
              'label'     => Mage::helper('dropcommon')->__('Store Pickup Details'),
              'title'     => Mage::helper('dropcommon')->__('Store Pickup Details'),
              'content'   => $this->getLayout()->createBlock('dropcommon/adminhtml_dropship_edit_tab_storepickup')->toHtml(),
          ));
      }

     
      return parent::_beforeToHtml();
  }
}