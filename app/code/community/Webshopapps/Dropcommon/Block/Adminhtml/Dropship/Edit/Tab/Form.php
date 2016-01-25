<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('dropship_form', array('legend' => Mage::helper('dropcommon')->__('Warehouse information')));
        $fieldsetAdvanced = $form->addFieldset('dropship_adv', array('legend' => Mage::helper('dropcommon')->__('Advanced Settings')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('description', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Description'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'description',
        ));

        $options = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();

        $fieldset->addField('country', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Origin Country'),
            'required' => false,
            'name' => 'country',
            'values' => $options,
        ));

        $fieldset->addField('region', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Origin Region/State Code'),
            'required' => false,
            'name' => 'region',
        ));

        $fieldset->addField('zipcode', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Origin Zip/Postal Code'),
            'required' => false,
            'name' => 'zipcode',
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Origin City'),
            'required' => false,
            'name' => 'city',
        ));

        $fieldset->addField('street', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Origin Street'),
            'required' => false,
            'name' => 'street',
        ));


        /*    $fieldset->addField('longitude', 'text', array(
                'name'      => 'longitude',
                'label'     => Mage::helper('dropcommon')->__('Longitude'),
                'note'      => Mage::helper('dropcommon')->__('This is set automatically.'),
            ));

            $fieldset->addField('latitude', 'text', array(
                'name'      => 'latitude',
                'label'     => Mage::helper('dropcommon')->__('Latitude'),
                'note'      => Mage::helper('dropcommon')->__('This is set automatically.'),
            )); */


        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Email Address'),
            'required' => false,
            'name' => 'email',
        ));

        $fieldset->addField('contact', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Contact Name'),
            'required' => false,
            'name' => 'contact',
        ));

        $fieldset->addField('manualmail', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Send Packing Slip Email to Warehouse Manually'),
            'name' => 'manualmail',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('dropcommon')->__('Yes'),
                ),

                array(
                    'value' => 0,
                    'label' => Mage::helper('dropcommon')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('manualship', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Manually Create Packing Slips for this Warehouse'),
            'name' => 'manualship',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('dropcommon')->__('Yes'),
                ),

                array(
                    'value' => 0,
                    'label' => Mage::helper('dropcommon')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('shippingmethods', 'multiselect', array(
            'label' => Mage::helper('dropcommon')->__('Applicable Shipping Carriers'),
            'name' => 'shippingmethods[]',
            'required' => true,
            'values' => $this->getShippingMethods()
        ));

        $fieldsetAdvanced->addField('ismetapak', 'select', array(
            'name' => 'ismetapak',
            'note' => Mage::helper('dropcommon')->__('Only use if site is integrated with MetaPack.'),
            'label' => Mage::helper('dropcommon')->__('Uses Metapack'),
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('dropcommon')->__('No'),
                ),

                array(
                    'value' => 1,
                    'label' => Mage::helper('dropcommon')->__('Yes'),
                ),
            ),
        ));

        $fieldsetAdvanced->addField('warehouse_code', 'text', array(
            'name' => 'warehouse_code',
            'note' => Mage::helper('dropcommon')->__('Leave empty if warehouse code not required.'),
            'label' => Mage::helper('dropcommon')->__('Warehouse Code'),
            'required' => false,
        ));

        $warehouseType = Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::getOptionArray();

        $fieldsetAdvanced->addField('warehouse_type', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Warehouse Type'),
            'name' => 'warehouse_type',
            'values' => $warehouseType,
        ));


        if (Mage::getSingleton('adminhtml/session')->getDropshipData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipData());
            Mage::getSingleton('adminhtml/session')->setDropshipData(null);
        } elseif (Mage::registry('dropship_data')) {
            $form->setValues(Mage::registry('dropship_data')->getData());
            $form->getElement('shippingmethods')->setValue(Mage::registry('dropship_data')->getShippingMethods());

        }
        return parent::_prepareForm();
    }

    // get the shipping methods and put in a suitable array
    private function getShippingMethods()
    {
        $options = array();
        //todo store id
        foreach (Mage::getStoreConfig('carriers', $this->getStoreId()) as $carrierCode => $carrierConfig) {
            if (!isset($carrierConfig['title']) || $carrierCode == 'dropship') {
                continue;
            }
            $title = $carrierConfig['title'];
            if (isset($carrierConfig['name'])) {
                $title = $title . " - " . $carrierConfig['name'];
            }
            $options[] = array(
                'value' => $carrierCode,
                'label' => $title
            );
        }
        return $options;
    }
}
