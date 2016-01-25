<?php


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function _construct()
  {
    $this->_controller = 'adminhtml_dropship';
    $this->_blockGroup = 'dropcommon';
    $this->_headerText = Mage::helper('dropcommon')->__('Warehouse Manager');
    $this->_addButtonLabel = Mage::helper('dropcommon')->__('Add Warehouse');
    parent::_construct();
  }
}