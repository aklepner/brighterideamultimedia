<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function _construct()
    {
        parent::_construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'dropcommon';
        $this->_controller = 'adminhtml_dropship';
        
        $this->_updateButton('save', 'label', Mage::helper('dropcommon')->__('Save Warehouse'));
        $this->_updateButton('delete', 'label', Mage::helper('dropcommon')->__('Delete Warehouse'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('dropship_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'dropship_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'dropship_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('dropship_data') && Mage::registry('dropship_data')->getId() ) {
            return Mage::helper('dropcommon')->__("Edit Warehouse '%s'", $this->htmlEscape(Mage::registry('dropship_data')->getTitle()));
        } else {
            return Mage::helper('dropcommon')->__('Add Warehouse');
        }
    }
}