<?php
/* ExtName
 *
 * User        karen
 * Date        1/26/14
 * Time        11:20 PM
 * @category   Webshopapps
 * @package    Webshopapps_Dropcommon
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tab_Destination extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldsetDestination = $form->addFieldset('dropcommon_form', array('legend'=>
            Mage::helper('dropcommon')->__('Applicable Countries for this Warehouse (if empty will use defaults)')));

        $countries = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();

        $countries[0]['label'] = Mage::helper('shipping')->__('USE DEFAULT');

        $fieldsetDestination->addField('dest_country', 'multiselect', array(
            'label'     => Mage::helper('dropcommon')->__('Allowed Destination Countries'),
            'required'  => false,
            'name'      => 'dest_country',
            'values'    => $countries
        ));

        $fieldsetDestination->addField('dest_region', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Allowed Region/State Codes'),
            'required' => false,
            'name' => 'dest_region',
        ));//DROP-99

        if (Mage::getSingleton('adminhtml/session')->getDropshipData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipData());
            Mage::getSingleton('adminhtml/session')->setDropshipData(null);
        } elseif (Mage::registry('dropship_data')) {
            $form->setValues(Mage::registry('dropship_data')->getData());

            $form->getElement('dest_country')->setValue(Mage::registry('dropship_data')->getDestCountry());
        }
        return parent::_prepareForm();
    }

}