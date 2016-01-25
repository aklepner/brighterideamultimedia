<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout processing model
 */

include_once('Mage/Checkout/Model/Type/Onepage.php');
class Concalma_Dropship_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
	/**
	 * Checkout types: Checkout as Guest, Register, Logged In Customer
	 */
	const METHOD_GUEST    = 'guest';
	const METHOD_REGISTER = 'register';
	const METHOD_CUSTOMER = 'customer';

	/**
	 * Error message of "customer already exists"
	 *
	 * @var string
	 */
	private $_customerEmailExistsMessage = '';

	/**
	 * @var Mage_Customer_Model_Session
	 */
	protected $_customerSession;

	/**
	 * @var Mage_Checkout_Model_Session
	 */
	protected $_checkoutSession;

	/**
	 * @var Mage_Sales_Model_Quote
	 */
	protected $_quote = null;

	/**
	 * @var Mage_Checkout_Helper_Data
	 */
	protected $_helper;

	/**
	 * Class constructor
	 * Set customer already exists message
	 */
	public function __construct()
	{
		$this->_helper = Mage::helper('checkout');
		$this->_customerEmailExistsMessage = $this->_helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
		$this->_checkoutSession = Mage::getSingleton('checkout/session');
		$this->_customerSession = Mage::getSingleton('customer/session');
	}

	/**
	 * Get frontend checkout session object
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	public function getCheckout()
	{
		return $this->_checkoutSession;
	}

	/**
	 * Quote object getter
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		if ($this->_quote === null) {
			return $this->_checkoutSession->getQuote();
		}
		return $this->_quote;
	}

	/**
	 * Declare checkout quote instance
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 */
	public function setQuote(Mage_Sales_Model_Quote $quote)
	{
		$this->_quote = $quote;
		return $this;
	}

	/**
	 * Get customer session object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	public function getCustomerSession()
	{
		return $this->_customerSession;
	}

	/**
	 * Initialize quote state to be valid for one page checkout
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function initCheckout()
	{
		$checkout = $this->getCheckout();
		$customerSession = $this->getCustomerSession();
		if (is_array($checkout->getStepData())) {
			foreach ($checkout->getStepData() as $step=>$data) {
				if (!($step==='login' || $customerSession->isLoggedIn() && $step==='billing')) {
					$checkout->setStepData($step, 'allow', false);
				}
			}
		}

		/**
		 * Reset multishipping flag before any manipulations with quote address
		 * addAddress method for quote object related on this flag
		 */
		if ($this->getQuote()->getIsMultiShipping()) {
			$this->getQuote()->setIsMultiShipping(false);
			$this->getQuote()->save();
		}

		/*
		 * want to laod the correct customer information by assiging to address
		 * instead of just loading from sales/quote_address
		 */
		$customer = $customerSession->getCustomer();
		if ($customer) {
			$this->getQuote()->assignCustomer($customer);
		}
		return $this;
	}

	/**
	 * Get quote checkout method
	 *
	 * @return string
	 */
	public function getCheckoutMethod()
	{
		if ($this->getCustomerSession()->isLoggedIn()) {
			return self::METHOD_CUSTOMER;
		}
		if (!$this->getQuote()->getCheckoutMethod()) {
			if ($this->_helper->isAllowedGuestCheckout($this->getQuote())) {
				$this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
			} else {
				$this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
			}
		}
		return $this->getQuote()->getCheckoutMethod();
	}

	/**
	 * Get quote checkout method
	 *
	 * @deprecated since 1.4.0.1
	 * @return string
	 */
	public function getCheckoutMehod()
	{
		return $this->getCheckoutMethod();
	}

	/**
	 * Specify chceckout method
	 *
	 * @param   string $method
	 * @return  array
	 */
	public function saveCheckoutMethod($method)
	{
		if (empty($method)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
		}

		$this->getQuote()->setCheckoutMethod($method)->save();
		$this->getCheckout()->setStepData('billing', 'allow', true);
		return array();
	}

	/**
	 * Get customer address by identifier
	 *
	 * @param   int $addressId
	 * @return  Mage_Customer_Model_Address
	 */
	public function getAddress($addressId)
	{
		$address = Mage::getModel('customer/address')->load((int)$addressId);
		$address->explodeStreetAddress();
		if ($address->getRegionId()) {
			$address->setRegion($address->getRegionId());
		}
		return $address;
	}

	/**
	 * Save billing address information to quote
	 * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
	 *
	 * @param   array $data
	 * @param   int $customerAddressId
	 * @return  Mage_Checkout_Model_Type_Onepage
	 */
	public function saveBilling($data, $customerAddressId)
	{
		if (empty($data)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
		}

		$address = $this->getQuote()->getBillingAddress();
		/* @var $addressForm Mage_Customer_Model_Form */
		$addressForm = Mage::getModel('customer/form');
		$addressForm->setFormCode('customer_address_edit')
		->setEntityType('customer_address')
		->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

		if (!empty($customerAddressId)) {
			$customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
			if ($customerAddress->getId()) {
				if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
					return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
					);
				}

				$address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
				$addressForm->setEntity($address);
				$addressErrors  = $addressForm->validateData($address->getData());
				if ($addressErrors !== true) {
					return array('error' => 1, 'message' => $addressErrors);
				}
			}
		} else {
			$addressForm->setEntity($address);
			// emulate request object
			$addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
			$addressErrors  = $addressForm->validateData($addressData);
			if ($addressErrors !== true) {
				return array('error' => 1, 'message' => $addressErrors);
			}
			$addressForm->compactData($addressData);
			//unset billing address attributes which were not shown in form
			foreach ($addressForm->getAttributes() as $attribute) {
				if (!isset($data[$attribute->getAttributeCode()])) {
					$address->setData($attribute->getAttributeCode(), NULL);
				}
			}

			// Additional form data, not fetched by extractData (as it fetches only attributes)
			$address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
		}

		// validate billing address
		if (($validateRes = $address->validate()) !== true) {
			return array('error' => 1, 'message' => $validateRes);
		}

		$address->implodeStreetAddress();

		if (true !== ($result = $this->_validateCustomerData($data))) {
			return $result;
		}

		if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
			if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
				return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
			}
		}

		if (!$this->getQuote()->isVirtual()) {
			/**
			 * Billing address using otions
			 */
			$usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

			switch($usingCase) {
				case 0:
					$shipping = $this->getQuote()->getShippingAddress();
					$shipping->setSameAsBilling(0);
					break;
				case 1:
					$billing = clone $address;
					$billing->unsAddressId()->unsAddressType();
					$shipping = $this->getQuote()->getShippingAddress();
					$shippingMethod = $shipping->getShippingMethod();

					// don't reset original shipping data, if it was not changed by customer
					foreach ($shipping->getData() as $shippingKey => $shippingValue) {
						if (!is_null($shippingValue)
						&& !is_null($billing->getData($shippingKey))
						&& !isset($data[$shippingKey])) {
							$billing->unsetData($shippingKey);
						}
					}
					$shipping->addData($billing->getData())
					->setSameAsBilling(1)
					->setSaveInAddressBook(0)
					->setShippingMethod($shippingMethod)
					->setCollectShippingRates(true);
					$this->getCheckout()->setStepData('shipping', 'complete', true);
					break;
			}
		}

		$this->getQuote()->collectTotals();
		$this->getQuote()->save();

		$this->getCheckout()
		->setStepData('billing', 'allow', true)
		->setStepData('billing', 'complete', true)
		->setStepData('shipping', 'allow', true);

		return array();
	}

	/**
	 * Validate customer data and set some its data for further usage in quote
	 * Will return either true or array with error messages
	 *
	 * @param array $data
	 * @return true|array
	 */
	protected function _validateCustomerData(array $data)
	{
		/* @var $customerForm Mage_Customer_Model_Form */
		$customerForm    = Mage::getModel('customer/form');
		$customerForm->setFormCode('checkout_register')
		->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

		$quote = $this->getQuote();
		if ($quote->getCustomerId()) {
			$customer = $quote->getCustomer();
			$customerForm->setEntity($customer);
			$customerData = $quote->getCustomer()->getData();
		} else {
			/* @var $customer Mage_Customer_Model_Customer */
			$customer = Mage::getModel('customer/customer');
			$customerForm->setEntity($customer);
			$customerRequest = $customerForm->prepareRequest($data);
			$customerData = $customerForm->extractData($customerRequest);
		}

		$customerErrors = $customerForm->validateData($customerData);
		if ($customerErrors !== true) {
			return array(
                'error'     => -1,
                'message'   => implode(', ', $customerErrors)
			);
		}

		if ($quote->getCustomerId()) {
			return true;
		}

		$customerForm->compactData($customerData);

		if ($quote->getCheckoutMethod() == self::METHOD_REGISTER) {
			// set customer password
			$customer->setPassword($customerRequest->getParam('customer_password'));
			$customer->setConfirmation($customerRequest->getParam('confirm_password'));
		} else {
			// emulate customer password for quest
			$password = $customer->generatePassword();
			$customer->setPassword($password);
			$customer->setConfirmation($password);
		}

		$result = $customer->validate();
		if (true !== $result && is_array($result)) {
			return array(
                'error'   => -1,
                'message' => implode(', ', $result)
			);
		}

		if ($quote->getCheckoutMethod() == self::METHOD_REGISTER) {
			// save customer encrypted password in quote
			$quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
		}

		// copy customer/guest email to address
		$quote->getBillingAddress()->setEmail($customer->getEmail());

		// copy customer data to quote
		Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $quote);

		return true;
	}

	/**
	 * Validate customer data and set some its data for further usage in quote
	 * Will return either true or array with error messages
	 *
	 * @deprecated since 1.4.0.1
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return true|array
	 */
	protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address)
	{
		// set customer date of birth for further usage
		$dob = '';
		if ($address->getDob()) {
			$dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
			$this->getQuote()->setCustomerDob($dob);
		}

		// set customer tax/vat number for further usage
		if ($address->getTaxvat()) {
			$this->getQuote()->setCustomerTaxvat($address->getTaxvat());
		}

		// set customer gender for further usage
		if ($address->getGender()) {
			$this->getQuote()->setCustomerGender($address->getGender());
		}

		// invoke customer model, if it is registering
		if (self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
			// set customer password hash for further usage
			$customer = Mage::getModel('customer/customer');
			$this->getQuote()->setPasswordHash($customer->encryptPassword($address->getCustomerPassword()));

			// validate customer
			foreach (array(
                'firstname'    => 'firstname',
                'lastname'     => 'lastname',
                'email'        => 'email',
                'password'     => 'customer_password',
                'confirmation' => 'confirm_password',
                'taxvat'       => 'taxvat',
                'gender'       => 'gender',
			) as $key => $dataKey) {
				$customer->setData($key, $address->getData($dataKey));
			}
			if ($dob) {
				$customer->setDob($dob);
			}
			$validationResult = $customer->validate();
			if (true !== $validationResult && is_array($validationResult)) {
				return array(
                    'error'   => -1,
                    'message' => implode(', ', $validationResult)
				);
			}
		} else if (self::METHOD_GUEST == $this->getQuote()->getCheckoutMethod()) {
			$email = $address->getData('email');
			if (!Zend_Validate::is($email, 'EmailAddress')) {
				return array(
                    'error'   => -1,
                    'message' => $this->_helper->__('Invalid email address "%s"', $email)
				);
			}
		}

		return true;
	}

	/**
	 * Save checkout shipping address
	 *
	 * @param   array $data
	 * @param   int $customerAddressId
	 * @return  Mage_Checkout_Model_Type_Onepage
	 */
	public function saveShipping($data, $customerAddressId)
	{
		if (empty($data)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
		}
		$address = $this->getQuote()->getShippingAddress();

		/* @var $addressForm Mage_Customer_Model_Form */
		$addressForm    = Mage::getModel('customer/form');
		$addressForm->setFormCode('customer_address_edit')
		->setEntityType('customer_address')
		->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

		if (!empty($customerAddressId)) {
			$customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
			if ($customerAddress->getId()) {
				if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
					return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
					);
				}

				$address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
				$addressForm->setEntity($address);
				$addressErrors  = $addressForm->validateData($address->getData());
				if ($addressErrors !== true) {
					return array('error' => 1, 'message' => $addressErrors);
				}
			}
		} else {
			$addressForm->setEntity($address);
			// emulate request object
			$addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
			$addressErrors  = $addressForm->validateData($addressData);
			if ($addressErrors !== true) {
				return array('error' => 1, 'message' => $addressErrors);
			}
			$addressForm->compactData($addressData);
			// unset shipping address attributes which were not shown in form
			foreach ($addressForm->getAttributes() as $attribute) {
				if (!isset($data[$attribute->getAttributeCode()])) {
					$address->setData($attribute->getAttributeCode(), NULL);
				}
			}

			// Additional form data, not fetched by extractData (as it fetches only attributes)
			$address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
			$address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
		}

		$address->implodeStreetAddress();
		$address->setCollectShippingRates(true);

		if (($validateRes = $address->validate())!==true) {
			return array('error' => 1, 'message' => $validateRes);
		}

		$this->getQuote()->collectTotals()->save();

		$this->getCheckout()
		->setStepData('shipping', 'complete', true)
		->setStepData('shipping_method', 'allow', true);

		return array();
	}

	/**
	 * Specify quote shipping method
	 *
	 * @param   string $shippingMethod
	 * @return  array
	 */
	public function saveShippingMethod($shippingMethod)
	{
		if (empty($shippingMethod)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
		}
		$rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
		if (!$rate) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
		}
		$this->getQuote()->getShippingAddress()
		->setShippingMethod($shippingMethod);
		$this->getQuote()->collectTotals()
		->save();

		$this->getCheckout()
		->setStepData('shipping_method', 'complete', true)
		->setStepData('payment', 'allow', true);

		return array();
	}

	/**
	 * Specify quote payment method
	 *
	 * @param   array $data
	 * @return  array
	 */
	public function savePayment($data)
	{
		if (empty($data)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
		}
		$quote = $this->getQuote();
		if ($quote->isVirtual()) {
			$quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
		} else {
			$quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
		}

		// shipping totals may be affected by payment method
		if (!$quote->isVirtual() && $quote->getShippingAddress()) {
			$quote->getShippingAddress()->setCollectShippingRates(true);
		}

		$payment = $quote->getPayment();
		$payment->importData($data);

		$quote->save();

		$this->getCheckout()
		->setStepData('payment', 'complete', true)
		->setStepData('review', 'allow', true);

		return array();
	}

	/**
	 * Validate quote state to be integrated with one page checkout process
	 */
	public function validate()
	{
		$helper = Mage::helper('checkout');
		$quote  = $this->getQuote();
		if ($quote->getIsMultiShipping()) {
			Mage::throwException($helper->__('Invalid checkout type.'));
		}

		if ($quote->getCheckoutMethod() == self::METHOD_GUEST && !$quote->isAllowedGuestCheckout()) {
			Mage::throwException($this->_helper->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.'));
		}
	}

	/**
	 * Prepare quote for guest checkout order submit
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	protected function _prepareGuestQuote()
	{
		$quote = $this->getQuote();
		$quote->setCustomerId(null)
		->setCustomerEmail($quote->getBillingAddress()->getEmail())
		->setCustomerIsGuest(true)
		->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
		return $this;
	}

	/**
	 * Prepare quote for customer registration and customer order submit
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	protected function _prepareNewCustomerQuote()
	{
		$quote      = $this->getQuote();
		$billing    = $quote->getBillingAddress();
		$shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

		//$customer = Mage::getModel('customer/customer');
		$customer = $quote->getCustomer();
		/* @var $customer Mage_Customer_Model_Customer */
		$customerBilling = $billing->exportCustomerAddress();
		$customer->addAddress($customerBilling);
		$billing->setCustomerAddress($customerBilling);
		$customerBilling->setIsDefaultBilling(true);
		if ($shipping && !$shipping->getSameAsBilling()) {
			$customerShipping = $shipping->exportCustomerAddress();
			$customer->addAddress($customerShipping);
			$shipping->setCustomerAddress($customerShipping);
			$customerShipping->setIsDefaultShipping(true);
		} else {
			$customerBilling->setIsDefaultShipping(true);
		}

		Mage::helper('core')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);
		$customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
		$customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
		$quote->setCustomer($customer)
		->setCustomerId(true);
	}

	/**
	 * Prepare quote for customer order submit
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	protected function _prepareCustomerQuote()
	{
		$quote      = $this->getQuote();
		$billing    = $quote->getBillingAddress();
		$shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

		$customer = $this->getCustomerSession()->getCustomer();
		if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
			$customerBilling = $billing->exportCustomerAddress();
			$customer->addAddress($customerBilling);
			$billing->setCustomerAddress($customerBilling);
		}
		if ($shipping && !$shipping->getSameAsBilling() &&
		(!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())) {
			$customerShipping = $shipping->exportCustomerAddress();
			$customer->addAddress($customerShipping);
			$shipping->setCustomerAddress($customerShipping);
		}

		if (isset($customerBilling) && !$customer->getDefaultBilling()) {
			$customerBilling->setIsDefaultBilling(true);
		}
		if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
			$customerShipping->setIsDefaultShipping(true);
		} else if (isset($customerBilling) && !$customer->getDefaultShipping()) {
			$customerBilling->setIsDefaultShipping(true);
		}
		$quote->setCustomer($customer);
	}

	/**
	 * Involve new customer to system
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	protected function _involveNewCustomer()
	{
		$customer = $this->getQuote()->getCustomer();
		if ($customer->isConfirmationRequired()) {
			$customer->sendNewAccountEmail('confirmation', '', $this->getQuote()->getStoreId());
			$url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
			$this->getCustomerSession()->addSuccess(
			Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
			);
		} else {
			$customer->sendNewAccountEmail('registered', '', $this->getQuote()->getStoreId());
			$this->getCustomerSession()->loginById($customer->getId());
		}
		return $this;
	}

	/**
	 * Create order based on checkout type. Create customer if necessary.
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function saveOrder()
	{
		$this->validate();
		$isNewCustomer = false;
		switch ($this->getCheckoutMethod()) {
			case self::METHOD_GUEST:
				$this->_prepareGuestQuote();
				break;
			case self::METHOD_REGISTER:
				$this->_prepareNewCustomerQuote();
				$isNewCustomer = true;
				break;
			default:
				$this->_prepareCustomerQuote();
				break;
		}

		$service = Mage::getModel('sales/service_quote', $this->getQuote());
		//$service->submitAll();
         //hungnamvnjsc debug
         Mage::log("prepare save dropship", Zend_Log::INFO);
		//add by Luu Thanh Thuy
		$this->_prepareOrderDropship($this->getQuote()->getShippingAddress());

		if ($isNewCustomer) {
			try {
				$this->_involveNewCustomer();
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}

		$this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
		->setLastSuccessQuoteId($this->getQuote()->getId())
		->clearHelperData();
           Mage::log("1st point" , Zend_Log::INFO); //thuy debug
           
           $this->_checkoutSession->setLastQuoteId(111)
		->setLastSuccessQuoteId(111)
		->clearHelperData();
		$order = $service->getOrder();
		//if ($_SESSION['order']) $order = $_SESSION['order'];
		if ($order) {
			 Mage::log("2nd point" , Zend_Log::INFO); //thuy debug
			Mage::dispatchEvent('checkout_type_onepage_save_order_after',
			array('order'=>$order, 'quote'=>$this->getQuote()));

			/**
			 * a flag to set that there will be redirect to third party after confirmation
			 * eg: paypal standard ipn
			 */
			$redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
						 Mage::log("3rd point" , Zend_Log::INFO); //thuy debug
			              Mage::log($redirectUrl , Zend_Log::INFO); //thuy debug
			/**
			 * we only want to send to customer about new order when there is no redirect to third party
			 */
			if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
				try {
					$order->sendNewOrderEmail();
				} catch (Exception $e) {
					Mage::logException($e);
				}
			}

			// add order information to the session
			$this->_checkoutSession->setLastOrderId($order->getId())
			->setRedirectUrl($redirectUrl)
			->setLastRealOrderId($order->getIncrementId());
			
			$this->_checkoutSession->setLastOrderId(111)
			->setRedirectUrl($redirectUrl)
			->setLastRealOrderId(111);

			// as well a billing agreement can be created
			$agreement = $order->getPayment()->getBillingAgreement();
			if ($agreement) {
				$this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
			}
		}

		// add recurring profiles information to the session
		$profiles = $service->getRecurringPaymentProfiles();
		if ($profiles) {
			$ids = array();
			foreach ($profiles as $profile) {
				$ids[] = $profile->getId();
			}
			$this->_checkoutSession->setLastRecurringProfileIds($ids);
			// TODO: send recurring profile emails
		}

		// hungnamvnjsc debug
		
		Mage::dispatchEvent(
            'checkout_submit_all_after',
		array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
		);
         Mage::log("end point" , Zend_Log::INFO); //thuy debug
		return $this;
	}

	/**
	 * Validate quote state to be able submited from one page checkout page
	 *
	 * @deprecated after 1.4 - service model doing quote validation
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	protected function validateOrder()
	{
		$helper = Mage::helper('checkout');
		if ($this->getQuote()->getIsMultiShipping()) {
			Mage::throwException($helper->__('Invalid checkout type.'));
		}

		if (!$this->getQuote()->isVirtual()) {
			$address = $this->getQuote()->getShippingAddress();
			$addressValidation = $address->validate();
			if ($addressValidation !== true) {
				Mage::throwException($helper->__('Please check shipping address information.'));
			}
			$method= $address->getShippingMethod();
			$rate  = $address->getShippingRateByCode($method);
			if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
				Mage::throwException($helper->__('Please specify shipping method.'));
			}
		}

		$addressValidation = $this->getQuote()->getBillingAddress()->validate();
		if ($addressValidation !== true) {
			Mage::throwException($helper->__('Please check billing address information.'));
		}

		if (!($this->getQuote()->getPayment()->getMethod())) {
			Mage::throwException($helper->__('Please select valid payment method.'));
		}
	}

	/**
	 * Check if customer email exists
	 *
	 * @param string $email
	 * @param int $websiteId
	 * @return false|Mage_Customer_Model_Customer
	 */
	protected function _customerEmailExists($email, $websiteId = null)
	{
		$customer = Mage::getModel('customer/customer');
		if ($websiteId) {
			$customer->setWebsiteId($websiteId);
		}
		$customer->loadByEmail($email);
		if ($customer->getId()) {
			return $customer;
		}
		return false;
	}

	/**
	 * Get last order increment id by order id
	 *
	 * @return string
	 */
	public function getLastOrderId()
	{
		$lastId  = $this->getCheckout()->getLastOrderId();
		$orderId = false;
		if ($lastId) {
			$order = Mage::getModel('sales/order');
			$order->load($lastId);
			$orderId = $order->getIncrementId();
		}
		return $orderId;
	}


	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	//    public function saveOrder1()
	//    {
	//        $this->validateOrder();
	//        $billing = $this->getQuote()->getBillingAddress();
	//        if (!$this->getQuote()->isVirtual()) {
	//            $shipping = $this->getQuote()->getShippingAddress();
	//        }
	//
	//        /*
	//         * Check if before this step checkout method was not defined use default values.
	//         * Related to issue with some browsers when checkout method was not saved during first step.
	//         */
	//        if (!$this->getQuote()->getCheckoutMethod()) {
	//            if ($this->_helper->isAllowedGuestCheckout($this->getQuote(), $this->getQuote()->getStore())) {
	//                $this->getQuote()->setCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST);
	//            } else {
	//                $this->getQuote()->setCheckoutMethod(Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER);
	//            }
	//        }
	//
	//        switch ($this->getQuote()->getCheckoutMethod()) {
	//        case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
	//            if (!$this->getQuote()->isAllowedGuestCheckout()) {
	//                Mage::throwException($this->_helper->__('Sorry, guest checkout is not enabled. Please try again or contact the store owner.'));
	//            }
	//            $this->getQuote()->setCustomerId(null)
	//                ->setCustomerEmail($billing->getEmail())
	//                ->setCustomerIsGuest(true)
	//                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
	//            break;
	//
	//        case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
	//            $customer = Mage::getModel('customer/customer');
	//            /* @var $customer Mage_Customer_Model_Customer */
	//
	//            $customerBilling = $billing->exportCustomerAddress();
	//            $customer->addAddress($customerBilling);
	//
	//            if (!$this->getQuote()->isVirtual() && !$shipping->getSameAsBilling()) {
	//                $customerShipping = $shipping->exportCustomerAddress();
	//                $customer->addAddress($customerShipping);
	//            }
	//
	//            if ($this->getQuote()->getCustomerDob() && !$billing->getCustomerDob()) {
	//                $billing->setCustomerDob($this->getQuote()->getCustomerDob());
	//            }
	//
	//            if ($this->getQuote()->getCustomerTaxvat() && !$billing->getCustomerTaxvat()) {
	//                $billing->setCustomerTaxvat($this->getQuote()->getCustomerTaxvat());
	//            }
	//
	//            if ($this->getQuote()->getCustomerGender() && !$billing->getCustomerGender()) {
	//                $billing->setCustomerGender($this->getQuote()->getCustomerGender());
	//            }
	//
	//            Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);
	//
	//            $customer->setPassword($customer->decryptPassword($this->getQuote()->getPasswordHash()));
	//            $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
	//
	//            $this->getQuote()->setCustomer($customer);
	//            break;
	//
	//        default:
	//            $customer = Mage::getSingleton('customer/session')->getCustomer();
	//
	//            if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
	//                $customerBilling = $billing->exportCustomerAddress();
	//                $customer->addAddress($customerBilling);
	//            }
	//            if (!$this->getQuote()->isVirtual() &&
	//                ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling()) ||
	//                (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {
	//
	//                $customerShipping = $shipping->exportCustomerAddress();
	//                $customer->addAddress($customerShipping);
	//            }
	//            $customer->setSavedFromQuote(true);
	//            $customer->save();
	//
	//            $changed = false;
	//            if (isset($customerBilling) && !$customer->getDefaultBilling()) {
	//                $customer->setDefaultBilling($customerBilling->getId());
	//                $changed = true;
	//            }
	//            if (!$this->getQuote()->isVirtual() && isset($customerBilling) &&
	//                !$customer->getDefaultShipping() && $shipping->getSameAsBilling()) {
	//                $customer->setDefaultShipping($customerBilling->getId());
	//                $changed = true;
	//            }
	//            elseif (!$this->getQuote()->isVirtual() && isset($customerShipping) && !$customer->getDefaultShipping()){
	//                $customer->setDefaultShipping($customerShipping->getId());
	//                $changed = true;
	//            }
	//
	//            if ($changed) {
	//                $customer->save();
	//            }
	//        }
	//
	//        $this->getQuote()->reserveOrderId();
	//        $convertQuote = Mage::getModel('sales/convert_quote');
	//        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
	//        //$order = Mage::getModel('sales/order');
	//        if ($this->getQuote()->isVirtual()) {
	//            $order = $convertQuote->addressToOrder($billing);
	//        }
	//        else {
	//            $order = $convertQuote->addressToOrder($shipping);
	//        }
	//        /* @var $order Mage_Sales_Model_Order */
	//        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
	//
	//        if (!$this->getQuote()->isVirtual()) {
	//            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
	//        }
	//
	//        $order->setPayment($convertQuote->paymentToOrderPayment($this->getQuote()->getPayment()));
	//
	//        foreach ($this->getQuote()->getAllItems() as $item) {
	//            $orderItem = $convertQuote->itemToOrderItem($item);
	//            if ($item->getParentItem()) {
	//                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
	//            }
	//            $order->addItem($orderItem);
	//        }
	//
	//        /**
	//         * We can use configuration data for declare new order status
	//         */
	//        Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$this->getQuote()));
	//        // check again, if customer exists
	//        if ($this->getQuote()->getCheckoutMethod() == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
	//            if ($this->_customerEmailExists($customer->getEmail(), Mage::app()->getWebsite()->getId())) {
	//                Mage::throwException($this->_customerEmailExistsMessage);
	//            }
	//        }
	//        $order->place();
	//
	//        if ($this->getQuote()->getCheckoutMethod()==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
	//            $customer->save();
	//            $customerBillingId = $customerBilling->getId();
	//            if (!$this->getQuote()->isVirtual()) {
	//                $customerShippingId = isset($customerShipping) ? $customerShipping->getId() : $customerBillingId;
	//                $customer->setDefaultShipping($customerShippingId);
	//            }
	//            $customer->setDefaultBilling($customerBillingId);
	//            $customer->save();
	//
	//            $this->getQuote()->setCustomerId($customer->getId());
	//
	//            $order->setCustomerId($customer->getId());
	//            Mage::helper('core')->copyFieldset('customer_account', 'to_order', $customer, $order);
	//
	//            $billing->setCustomerId($customer->getId())->setCustomerAddressId($customerBillingId);
	//            if (!$this->getQuote()->isVirtual()) {
	//                $shipping->setCustomerId($customer->getId())->setCustomerAddressId($customerShippingId);
	//            }
	//
	//            try {
	//                if ($customer->isConfirmationRequired()) {
	//                    $customer->sendNewAccountEmail('confirmation');
	//                }
	//                else {
	//                    $customer->sendNewAccountEmail();
	//                }
	//            } catch (Exception $e) {
	//                Mage::logException($e);
	//            }
	//        }
	//
	//        /**
	//         * a flag to set that there will be redirect to third party after confirmation
	//         * eg: paypal standard ipn
	//         */
	//        $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
	//        if(!$redirectUrl){
	//            $order->setEmailSent(true);
	//        }
	//
	//        $order->save();
	//
	//        Mage::dispatchEvent('checkout_type_onepage_save_order_after',
	//            array('order'=>$order, 'quote'=>$this->getQuote()));
	//
	//        /**
	//         * need to have somelogic to set order as new status to make sure order is not finished yet
	//         * quote will be still active when we send the customer to paypal
	//         */
	//
	//        $orderId = $order->getIncrementId();
	//        $this->getCheckout()->setLastQuoteId($this->getQuote()->getId());
	//        $this->getCheckout()->setLastOrderId($order->getId());
	//        $this->getCheckout()->setLastRealOrderId($order->getIncrementId());
	//        $this->getCheckout()->setRedirectUrl($redirectUrl);
	//
	//        /**
	//         * we only want to send to customer about new order when there is no redirect to third party
	//         */
	//        if(!$redirectUrl){
	//            try {
	//                $order->sendNewOrderEmail();
	//            } catch (Exception $e) {
	//                Mage::logException($e);
	//            }
	//        }
	//
	//        if ($this->getQuote()->getCheckoutMethod(true)==Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
	//            && !Mage::getSingleton('customer/session')->isLoggedIn()) {
	//            /**
	//             * we need to save quote here to have it saved with Customer Id.
	//             * so when loginById() executes checkout/session method loadCustomerQuote
	//             * it would not create new quotes and merge it with old one.
	//             */
	//            $this->getQuote()->save();
	//            if ($customer->isConfirmationRequired()) {
	//                Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
	//            }
	//            else {
	//                Mage::getSingleton('customer/session')->loginById($customer->getId());
	//            }
	//        }
	//
	//        //Setting this one more time like control flag that we haves saved order
	//        //Must be checkout on success page to show it or not.
	//        $this->getCheckout()->setLastSuccessQuoteId($this->getQuote()->getId());
	//
	//        $this->getQuote()->setIsActive(false);
	//        $this->getQuote()->save();
	//
	//        return $this;
	//    }
	//

	/***
	 * prepare addition order
	 * at this method we separate the orders into multiple order /each order per each warehouse
	 */
	protected function _prepareOrderDropship(Mage_Sales_Model_Quote_Address $address)

	{
		///////////////////////////////////
		///////////////////////////////////
        $pseudo_order_id = $this->getMaximumOrder();
        $pseudo_increment_id = $this->getMaxPseudoId();
		$itemcollection = $address->getAllItems();
		
		
		//hungnamvnjsc debug    
		//fix: some times the carrier_method is not true sometimes it is even null
		Mage::log("====item collecion prepareOrderDropship method====", Zend_Log::INFO);
		//Mage::log($itemcollection, Zend_Log::INFO);
		$orderIds = array();
		$arr_warehouse = array();
		foreach ($itemcollection as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);
			//// in case of product have multiple option
			$tempArr = explode(",", $warehouse);
			if (count($tempArr) > 0) {
				for ($i = 0; $i < count($tempArr) ; $i++) {
					if ( !in_array($tempArr[$i], $arr_warehouse ) ) {
						$arr_warehouse[] = $tempArr[$i];

					}
				}
			}	else {
				if ( !in_array($warehouse, $arr_warehouse ) ) {
					$arr_warehouse[] = $warehouse;

				}
			}
		}

		////////////////////////////////End of get all the warehouse
		//hungnamvnjsc debug
		Mage::log("============ array warehouse====", Zend_Log::INFO);
		Mage::log($arr_warehouse, Zend_Log::INFO);
		$i = 0;
		//foreach ($arr_warehouse as $wareshouse) {
		for ($j = 0; $j < count($arr_warehouse); $j++) {
			$wareshouse = $arr_warehouse[$j];
			//$wareshouse = "CL";
			Mage::log("=======each warehosue =====", Zend_Log::INFO);
			Mage::log($wareshouse, Zend_Log::INFO);
			$i++;
			$allItemInWarehouse = $this->getItemsInWarehouse($wareshouse, $address);

			$quote = $this->getQuote();
			//$quote->unsReservedOrderId();
			// $quote->reserveOrderId();
			//$quote->collectTotals();

			$convertQuote = Mage::getSingleton('sales/convert_quote');
			$order = $convertQuote->addressToOrder($address);
			$order->setBillingAddress(
			$convertQuote->addressToOrderAddress($quote->getBillingAddress())
			);

			if ($address->getAddressType() == 'billing') {
				$order->setIsVirtual(1);
			} else {
				$order->setShippingAddress($convertQuote->addressToOrderAddress($address));
			}

			$order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
			if (Mage::app()->getStore()->roundPrice($address->getGrandTotal()) == 0) {
				$order->getPayment()->setMethod('free');
			}

			$sub_total = 0;
				
			foreach ($allItemInWarehouse as $item) {
				//hungnamvnjsc debug
		//Mage::log("today bug" , Zend_Log::INFO);
		//Mage::log($item->getBasePriceInclTax() , Zend_Log::INFO);
				///if (! $item->getQuoteItem()) {
				// }
				if ($item != null) {
					//echo "=====".$item->getProductId();
					//   ->setProductOptions($item->getQuoteItem()->getProduct()->getTypeInstance(true)->getOrderOptions($item->getQuoteItem()->getProduct()));
					$sub_total += $item->getBasePriceInclTax()* $item->getQty();
					$orderItem = $convertQuote->itemToOrderItem($item);
					if ($item->getParentItem()) {
						$orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
					}
					$order->addItem($orderItem);
				}
			}
			
			
			//hungnamvnjsc debug
			Mage::log("=========Print Session=======", Zend_Log::INFO);
			Mage::log($_SESSION['warehouse'], Zend_Log::INFO);
			if ( isset($_SESSION['warehouse'][$wareshouse]) ) {
				//hungnamvnjsc debug
				Mage::log("+++++++++++++++session in warehouse==============", Zend_Log::INFO);
				Mage::log($_SESSION['warehouse'][$wareshouse], Zend_Log::INFO);
				Mage::log("+++++++++++++++END session in warehouse==============", Zend_Log::INFO);
				if (isset($_SESSION['warehouse'][$wareshouse]['sm_des']) ) {
					//$order->setIncrementId($quote->getReservedOrderId() + $i);
					Mage::log("+++++++++++++++++++", Zend_Log::INFO);
					Mage::log($this->getMaximumOrderIncrementId(), Zend_Log::INFO);
					$order->setIncrementId($this->getMaximumOrderIncrementId() + 1);
					//over write the order
					$order->setShippingDescription($_SESSION['warehouse'][$wareshouse]['sm_des']);
					
					//set the shipping method
					$sh_method = $_SESSION['warehouse'][$wareshouse]['carrier'] ."_".$_SESSION['warehouse'][$wareshouse]['method'];
					$order->setShippingMethod($sh_method);
					$order->setBaseShippingAmount($_SESSION['warehouse'][$wareshouse]['price']);
					$order->setShippingAmount($_SESSION['warehouse'][$wareshouse]['price']);
					$order->setShippingInclTax($_SESSION['warehouse'][$wareshouse]['price']);
					$order->setBaseShippingInclTax($_SESSION['warehouse'][$wareshouse]['price']);
						
					$order->setBaseSubtotal($sub_total);
					$order->setSubtotal($sub_total);
					$order->setBaseSubtotalInclTax($sub_total);
					$order->setSubtotalInclTax($sub_total);
						
					$grand_total = $sub_total + $_SESSION['warehouse'][$wareshouse]['price'];
					$order->setBaseGrandTotal($grand_total);
					$order->setGrandTotal($grand_total);
					$order->setBaseTotalDue($grand_total);
					$order->setTotalDue($grand_total);
						
					$order->setTotalQtyOrdered( count($allItemInWarehouse) );
					$order->setTotalItemCount($allItemInWarehouse);
						
					//save additional data
					$order->setWarehouse($wareshouse);
					$order->setPseudoOrderId($pseudo_order_id + 1);
					$order->setPseudoIncrementId($pseudo_increment_id + 1);
					//$order->setData('pseudo_increment_id', '10000054');
					//Mage::log("pseudo increment id". $pseudo_increment_id , Zend_Log::INFO);
					/////////////
					$order->place();
					$order->save();
					//hungnamvnjsc debug
					Mage::log("*********************Order Save************", Zend_Log::INFO);
					//Mage::log($order,Zend_Log::INFO);
					if ($i == 1) $_SESSION['order'] = $order; 
					$orderIds[$order->getId()] = $order->getIncrementId();
					
					$this->updatePseudoIncrementId($pseudo_increment_id + 1, $order->getId());
				}
			}

		}
		$this->savePseudoOrder($orderIds);
		
		//unset the warehouse session 
		//unset($_SESSION['warehouse']);
		//refactor  some of the code
	}

	/**
	 * get all the warehouse
	 * @author : Luu Thanh Thuy
	 * email : luuthuy205@gmail
	 * return array of warehouse Id
	 */
	public function getAllWarehouses () {
		$itemcollection = $this->getAllItems();

		$arr_warehouse = array();
		foreach ($itemcollection as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);
			//// in case of product have multiple option
			$tempArr = explode(",", $warehouse);
			if (count($tempArr) > 0) {
				for ($i = 0; $i < count($tempArr) ; $i++) {
					if ( !in_array($tempArr[$i], $arr_warehouse ) ) {
						$arr_warehouse[] = $tempArr[$i];

					}
				}
			}	else {
				if ( !in_array($warehouse, $arr_warehouse ) ) {
					$arr_warehouse[] = $warehouse;

				}
			}
		}
		return $arr_warehouse;
	}


	public function getItemsInWarehouse ($warehouseTitle, Mage_Sales_Model_Quote_Address $address) {
		$warehouseTitle = trim($warehouseTitle);
		$out_collection =  new Varien_Data_Collection();
		$itemcollection = $address->getAllItems();

		$arr_item = array();
		foreach ($itemcollection as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);

			//incase the $warehouse is multiple
			//  echo "======warehouse is ".$warehouse . " warehous title.". $warehouseTitle;
			if (strpos($warehouse, $warehouseTitle) || $warehouse == $warehouseTitle ) {

				if ( !in_array($item->getId(), $arr_item) ) {
					$arr_item[] = $item->getProductId();
					$out_collection->addItem($item);
				}
			}

		}
		return $out_collection;
	}

	/**
	 * get the maximum id in sales_flat_order_id
	 * @Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function getMaxOrderId() {
		$query = "select max(`increment_id`) as maximum from `sales_flat_order`";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) > 0) {
			return $rows[0]['maximum'];
		} else {
			return false;
		}
	}

	/***
	 * save the information to a pseudo order ( combine order)
	 */
	public function savePseudoOrder($orderIds) {
		if (count($orderIds) ==0 ) {
			exit('There is exception , humm, no sub order id');
		}
		// $shipping_description = "shipping from multiple warehouse";

		$query = "SELECT max(`entity_id`) AS maximum FROM `sales_flat_order`";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$pseudo_order_id = 0;
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) == 0) {
			$pseudo_order_id = 1;
		} else {
			$pseudo_order_id = $rows[0]['maximum'] + 1;
		}

		$sub_order_id = "";
		foreach ($orderIds as $order_id) {
			$sub_order_id .='_' . $order_id;
		}

		$insert_query = "INSERT INTO `sales_flat_order_hungnam` (`sub_order_id`,`order_id`) VALUES('$sub_order_id', '$pseudo_order_id')";
		$db->query($insert_query);
		//$store_id = Mage->getStoreId();
		//$customer_id = $this->getQuote()->getCustomer()->getId();
		// $base_grand_total = $this->getQuote()->getBaseGrandTotal();
		//$base_shipping_amount = $this->getQuote()->getBaseShippingAmount();
		// $base_subtotal = $this->getQuote() ->getBaseSubtotal();
		// $base_tax_amount = $this->getQuote()->getBaseTaxAmount();
		//

		//$query =	"INSERT INTO `sales_flat_order` ( `state`, `status`, `coupon_code`, `protect_code`, `shipping_description`, `is_virtual`, `store_id`, `customer_id`, `base_discount_amount`, `base_discount_canceled`, `base_discount_invoiced`, `base_discount_refunded`, `base_grand_total`, `base_shipping_amount`, `base_shipping_canceled`, `base_shipping_invoiced`, `base_shipping_refunded`, `base_shipping_tax_amount`, `base_shipping_tax_refunded`, `base_subtotal`, `base_subtotal_canceled`, `base_subtotal_invoiced`, `base_subtotal_refunded`, `base_tax_amount`, `base_tax_canceled`, `base_tax_invoiced`, `base_tax_refunded`, `base_to_global_rate`, `base_to_order_rate`, `base_total_canceled`, `base_total_invoiced`, `base_total_invoiced_cost`, `base_total_offline_refunded`, `base_total_online_refunded`, `base_total_paid`, `base_total_qty_ordered`, `base_total_refunded`, `discount_amount`, `discount_canceled`, `discount_invoiced`, `discount_refunded`, `grand_total`, `shipping_amount`, `shipping_canceled`, `shipping_invoiced`, `shipping_refunded`, `shipping_tax_amount`, `shipping_tax_refunded`, `store_to_base_rate`, `store_to_order_rate`, `subtotal`, `subtotal_canceled`, `subtotal_invoiced`, `subtotal_refunded`, `tax_amount`, `tax_canceled`, `tax_invoiced`, `tax_refunded`, `total_canceled`, `total_invoiced`, `total_offline_refunded`, `total_online_refunded`, `total_paid`, `total_qty_ordered`, `total_refunded`, `can_ship_partially`, `can_ship_partially_item`, `customer_is_guest`, `customer_note_notify`, `billing_address_id`, `customer_group_id`, `edit_increment`, `email_sent`, `forced_shipment_with_invoice`, `gift_message_id`, `payment_auth_expiration`, `paypal_ipn_customer_notified`, `quote_address_id`, `quote_id`, `shipping_address_id`, `adjustment_negative`, `adjustment_positive`, `base_adjustment_negative`, `base_adjustment_positive`, `base_shipping_discount_amount`, `base_subtotal_incl_tax`, `base_total_due`, `payment_authorization_amount`, `shipping_discount_amount`, `subtotal_incl_tax`, `total_due`, `weight`, `customer_dob`, `increment_id`, `applied_rule_ids`, `base_currency_code`, `customer_email`, `customer_firstname`, `customer_lastname`, `customer_middlename`, `customer_prefix`, `customer_suffix`, `customer_taxvat`, `discount_description`, `ext_customer_id`, `ext_order_id`, `global_currency_code`, `hold_before_state`, `hold_before_status`, `order_currency_code`, `original_increment_id`, `relation_child_id`, `relation_child_real_id`, `relation_parent_id`, `relation_parent_real_id`, `remote_ip`, `shipping_method`, `store_currency_code`, `store_name`, `x_forwarded_for`, `customer_note`, `created_at`, `updated_at`, `total_item_count`, `customer_gender`, `base_custbalance_amount`, `currency_base_id`, `currency_code`, `currency_rate`, `custbalance_amount`, `is_hold`, `is_multi_payment`, `real_order_id`, `tax_percent`, `tracking_numbers`, `hidden_tax_amount`, `base_hidden_tax_amount`, `shipping_hidden_tax_amount`, `base_shipping_hidden_tax_amnt`, `hidden_tax_invoiced`, `base_hidden_tax_invoiced`, `hidden_tax_refunded`, `base_hidden_tax_refunded`, `shipping_incl_tax`, `base_shipping_incl_tax`) VALUES
		// ('new', 'pending', NULL, '8a2e7b', '$shipping_description', 0, 1, 2, 0.0000, NULL, NULL, NULL, 450.8300, 0.0000, NULL, NULL, NULL, 0.0000, NULL, 415.9900, NULL, NULL, NULL, 34.8400, NULL, NULL, NULL, 1.0000, 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, 450.8300, 0.0000, NULL, NULL, NULL, 0.0000, NULL, 1.0000, 1.0000, 415.9900, NULL, NULL, NULL, 34.8400, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4.0000, NULL, NULL, NULL, 0, 1, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, NULL, NULL, NULL, 0.0000, 450.8300, NULL, NULL, 0.0000, 450.8300, NULL, 16.7000, NULL, '100000001', NULL, 'USD', 'no_bi_ta711@yahoo.com', 'demo', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'USD', NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'ups_GND', 'USD', 'Main Website\nMain Store\nEnglish', NULL, NULL, '2011-09-26 03:35:44', '2011-09-26 03:35:45', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, 0.0000, 0.0000)";
	}

	/**
	 * check whether the order have the Total order
	 */
	public function getDropshipOrder($increment_order_id) {
		$query = "select * from `sales_flat_order_hungnam`";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$pseudo_order_id = 0;
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) == 0) {
			exit("There is not any records on dropship orders table");
		} else {
			foreach ($rows as $row) {
				$sub_order = $row['sub_order_id'];
				 $temp = explode("_" ,$sub_order );
				 foreach ($temp as $orderid) {
				 	if ($orderid == $increment_order_id) {
				 		$row['entity_id'];
				 	}
				 }
			}
		}
            return false;
	}
	
	/**
	 * description  get the table of item to show in the order managements of 
	 * Account ( need to refactor to the be the method of Helper)
	 * @author Luu Thanh Thuy
	 */
	public function getItemsOrderManagement($order_id) {
		$query = "select * from `sales_flat_order_item` where `order_id` =$order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		//foreac
		return $rows;
	}
	
	/**
	 * 
	 */
	public function getHTMLItemOrderMan($order_id) {
		$rows = $this-> getItemsOrderManagement($order_id);
		$HTML = "";
		foreach ($rows as $row) {
			$HTML .= "<tr>";
			$HTML .= "<td>".$row['name']. "</td>";
			$HTML .= "<td>".$row['sku']. "</td>";
			$HTML .= "<td>".$row['warehouse']. "</td>";
			$HTML .= "<td>".$row['price']. "</td>";
			$HTML .= "<td> Ordered:".$row['qty_ordered']."</td>";
			$HTML .= "<td> ".$row['row_total']."</td>";
			//qty_ordered
		}
	}
	
	/**
	 * get maximum order id
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function getMaximumOrder () {
	$query = "SELECT max(`entity_id`) AS maximum FROM `sales_flat_order`";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$pseudo_order_id = 0;
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		Mage::log($rows, Zend_Log::INFO);
		if ( count($rows) == 0) {
			return  0;
		} else {
			return $rows[0]['maximum'];
		}
	}

	public function getMaximumOrderIncrementId() {
		$entity_id = $this->getMaximumOrder();
		if ($entity_id == 0) return 100000000;
		Mage::log("entity id ", Zend_Log::INFO);
		Mage::log($entity_id, Zend_Log::INFO);
	    $query = "SELECT `increment_id` FROM `sales_flat_order` where `entity_id` = $entity_id";
	    Mage::log($query, Zend_Log::INFO);
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$pseudo_order_id = 0;
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) == 0) {
			return 100000001;
		} else {
			return $rows[0]['increment_id'];
		}
	}
	
	
	public function getMaxPseudoId() {
	    $entity_id = $this->getMaximumOrder();
		if ($entity_id == 0) return 100000000;
		Mage::log("entity id ", Zend_Log::INFO);
		Mage::log($entity_id, Zend_Log::INFO);
	    $query = "SELECT `pseudo_increment_id` FROM `sales_flat_order` where `entity_id` = $entity_id";
	    Mage::log($query, Zend_Log::INFO);
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$pseudo_order_id = 0;
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) == 0) {
			return 100000001;
		} else {
			return $rows[0]['pseudo_increment_id'];
		}
	}
	/**
	 * it is strange that I can not save the pseudo increment id
	 * so I save it by the sql query
	 */
	public function updatePseudoIncrementId($pseudo_increment_id ,$order_id) {
		$query = "update `sales_flat_order` set `pseudo_increment_id` ='$pseudo_increment_id' WHERE `sales_flat_order`.`entity_id` = $order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);

	}
	
	/***
	 * get the newest
	 */
	public function getActiveAction() {
		$product_collection = Mage::getModel('catalog/product')->getCollection();
		$jsonArr = array();
		foreach ($product_collection as $product) {
			 // if ($product->getData('currentdeal') == 1) {
			if ($product->getId() == 51) {
			    $product = Mage::getModel('catalog/product')->load($product->getId() );
				$jsonArr['image'] =  Mage::helper('catalog/image')->init($product, 'small_image');
				$jsonArr['name'] = $product->getName();
				//$jsonArr['description'] = $product->get;
				
			}
		}
		echo Mage::helper('core')->jsonEncode($jsonArr);
	}
}	
/**
 * tackling the order 
 * migrant from turn to international controll terminate employment at anytime without justification 
 * behavior, movement moral conduct 
 * endure abuse 
 * each order have increment_id which is order No. and each of them have entity_order
 * 100076, 100077 correspondent entity 127 , 128, which have sub pseudo and sub increment id
 * 
 * 100076 ---> 127
 * 100077 ---> 128
 * Which 
 * 
 */