<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Edit_Tab_Fedexfreight extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldsetFedex = $form->addFieldset('dropship_form', array('legend' =>
            Mage::helper('dropcommon')->__('Fedex Freight Login Details')));
		$fieldsetFedexSoap = $form->addFieldset('dropship_soap', array('legend' =>
            Mage::helper('dropcommon')->__('Fedex Freight SOAP Additional Login Details')));
        $fieldsetFedexSoapBilling = $form->addFieldset('dropship_soap_billing_address', array('legend' =>
            Mage::helper('dropcommon')->__('Fedex Freight Billing Address')));
        $fieldsetFedexSoapRolePayment = $form->addFieldset('dropship_soap_role_payment', array('legend' =>
            Mage::helper('dropcommon')->__('Fedex Freight Warehouse Freight Role and Payment Type')));
        $countryOptions = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();

        Mage::helper('dropcommon')->addDimensionalWarning($fieldsetFedex, 'FedEx Freight');

        $fieldsetFedex->addField('fedexfreight_account_id', 'text', array(
				'label' => Mage::helper('dropcommon')->__('Freight Account ID'),
				'required' => false,
				'name' => 'fedexfreight_account_id',
		));

        $fieldsetFedexSoap->addField('fedexfreight_meter_number', 'password', array(
            'label' => Mage::helper('dropcommon')->__('Meter Number'),
            'required' => false,
            'name' => 'fedexfreight_meter_number',
        ));

        $fieldsetFedexSoap->addField('fedexfreight_key', 'password', array(
				'label' => Mage::helper('dropcommon')->__('Key'),
				'required' => false,
				'name' => 'fedexfreight_key',
		));

		$fieldsetFedexSoap->addField('fedexfreight_password', 'password', array(
				'label' => Mage::helper('dropcommon')->__('Password'),
				'required' => false,
				'name' => 'fedexfreight_password',
		));

        $fieldsetFedexSoapBilling->addField('fedexfreight_street', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Street'),
            'required' => false,
            'name' => 'fedexfreight_street',
        ));

        $fieldsetFedexSoapBilling->addField('fedexfreight_city', 'text', array(
            'label' => Mage::helper('dropcommon')->__('City'),
            'required' => false,
            'name' => 'fedexfreight_city',
        ));

        $fieldsetFedexSoapBilling->addField('fedexfreight_zipcode', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Zip/Postal Code'),
            'required' => false,
            'name' => 'fedexfreight_zipcode',
        ));

        $fieldsetFedexSoapBilling->addField('fedexfreight_state', 'text', array(
            'label' => Mage::helper('dropcommon')->__('Region/State Code'),
            'required' => false,
            'name' => 'fedexfreight_state',
        ));

        $fieldsetFedexSoapBilling->addField('fedexfreight_country', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Country Code'),
            'required' => false,
            'name' => 'fedexfreight_country',
            'values' => $countryOptions,
        ));

        $fieldsetFedexSoapRolePayment->addField('fedexfreight_freight_role', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Region/Freight Role'),
            'name' => 'fedexfreight_freight_role',
            'values'    => array(
                array(
                    'value'     => "",
                    'label'     => Mage::helper('dropcommon')->__(''),
                ),

                array(
                    'value'     => "CONSINEE",
                    'label'     => Mage::helper('dropcommon')->__('Consignee'),
                ),

                array(
                    'value'     => "SHIPPER",
                    'label'     => Mage::helper('dropcommon')->__('Shipper'),
                ),
                array(
                    'value'     => "THIRD_PARTY",
                    'label'     => Mage::helper('dropcommon')->__('Third Party'),
                ),
            ),
        ));

        $fieldsetFedexSoapRolePayment->addField('fedexfreight_payment_type', 'select', array(
            'label' => Mage::helper('dropcommon')->__('Payment Type'),
            'name' => 'fedexfreight_payment_type',
            'values'    => array(
                array(
                    'value'     => "",
                    'label'     => Mage::helper('dropcommon')->__(''),
                ),
                array(
                    'value'     => "COLLECT",
                    'label'     => Mage::helper('dropcommon')->__('Collect'),
                ),

                array(
                    'value'     => "PREPAID",
                    'label'     => Mage::helper('dropcommon')->__('Prepaid'),
                ),
            ),
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