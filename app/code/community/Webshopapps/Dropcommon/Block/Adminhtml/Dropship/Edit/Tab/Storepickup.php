<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Exampleextension
 * User         karen
 * Date         25/11/2013
 * Time         02:38
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tab_Storepickup extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldsetFedex = $form->addFieldset('dropship_form', array('legend' =>
        Mage::helper('dropcommon')->__('Store Pickup  (leave empty to use defaults)')));

        $options = Mage::getModel('wsastorepickup/shipping_carrier_source_shippingmethods')->toOptionArray();
            array_unshift($options, array('value' => '', 'label' => Mage::helper('shipping')->__('Use Default from Shipping Methods')));

        $fieldsetFedex->addField('storepickup_applicable_method', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Applicable Shipping Carrier/Method'),
            'required' => false,
            'name' => 'storepickup_applicable_method',
            'values' => $options,
        ));

        if (Mage::getSingleton('adminhtml/session')->getDropshipData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipData());
            Mage::getSingleton('adminhtml/session')->setDropshipData(null);
        } elseif (Mage::registry('dropship_data')) {
            $form->setValues(Mage::registry('dropship_data')->getData());

        }

        return parent::_prepareForm();
    }

}