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


class Concalma_Dropship_Checkout_OnepageController extends Mage_Checkout_Controller_Action
{
	protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
	);

	/**
	 * @return Mage_Checkout_OnepageController
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		$this->_preDispatchValidateCustomer();

		$checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
		if ($checkoutSessionQuote->getIsMultiShipping()) {
			$checkoutSessionQuote->setIsMultiShipping(false);
			$checkoutSessionQuote->removeAllAddresses();
		}

		return $this;
	}

	protected function _ajaxRedirectResponse()
	{
		$this->getResponse()
		->setHeader('HTTP/1.1', '403 Session Expired')
		->setHeader('Login-Required', 'true')
		->sendResponse();
		return $this;
	}

	/**
	 * Validate ajax request and redirect on failure
	 *
	 * @return bool
	 */
	protected function _expireAjax()
	{
		if (!$this->getOnepage()->getQuote()->hasItems()
		|| $this->getOnepage()->getQuote()->getHasError()
		|| $this->getOnepage()->getQuote()->getIsMultiShipping()) {
			$this->_ajaxRedirectResponse();
			return true;
		}
		$action = $this->getRequest()->getActionName();
		if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
		&& !in_array($action, array('index', 'progress'))) {
			$this->_ajaxRedirectResponse();
			return true;
		}

		return false;
	}

	/**
	 * Get shipping method step html
	 *
	 * @return string
	 */
	protected function _getShippingMethodsHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_shippingmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Get payment method step html
	 *
	 * @return string
	 */
	protected function _getPaymentMethodsHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_paymentmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	protected function _getAdditionalHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_additional');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Get order review step html
	 *
	 * @return string
	 */
	protected function _getReviewHtml()
	{
		return $this->getLayout()->getBlock('root')->toHtml();
	}

	/**
	 * Get one page checkout model
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function getOnepage()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}

	/**
	 * Checkout page
	 */
	public function indexAction()
	{
		if (!Mage::helper('checkout')->canOnepageCheckout()) {
			Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
			$this->_redirect('checkout/cart');
			return;
		}
		$quote = $this->getOnepage()->getQuote();
		if (!$quote->hasItems() || $quote->getHasError()) {
			$this->_redirect('checkout/cart');
			return;
		}
		if (!$quote->validateMinimumAmount()) {
			$error = Mage::getStoreConfig('sales/minimum_order/error_message');
			Mage::getSingleton('checkout/session')->addError($error);
			$this->_redirect('checkout/cart');
			return;
		}
		Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
		Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
		$this->getOnepage()->initCheckout();
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
		$this->renderLayout();
	}

	/**
	 * Checkout status block
	 */
	public function progressAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		$this->loadLayout(false);
		$this->renderLayout();
	}

	public function shippingMethodAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		$this->loadLayout(false);
		$this->renderLayout();
	}

	public function reviewAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		$this->loadLayout(false);
		$this->renderLayout();
	}

	/**
	 * Order success action
	 */
	public function successAction()
	{
		$session = $this->getOnepage()->getCheckout();
		if (!$session->getLastSuccessQuoteId()) {
			Mage::log("1 success controll", Zend_Log::INFO); //thuy debug
			//$this->_redirect('checkout/cart');
			//return;
		}

		$lastQuoteId = $session->getLastQuoteId();
		$lastOrderId = $session->getLastOrderId();
		$lastRecurringProfiles = $session->getLastRecurringProfileIds();
		if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
			//$this->_redirect('checkout/cart');
			//return;
		}

		$session->clear();
		$this->loadLayout();
		$this->_initLayoutMessages('checkout/session');
		Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
		$this->renderLayout();
	}

	public function failureAction()
	{
		$lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
		$lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

		if (!$lastQuoteId || !$lastOrderId) {
			$this->_redirect('checkout/cart');
			return;
		}

		$this->loadLayout();
		$this->renderLayout();
	}


	public function getAdditionalAction()
	{
		$this->getResponse()->setBody($this->_getAdditionalHtml());
	}

	/**
	 * Address JSON
	 */
	public function getAddressAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		$addressId = $this->getRequest()->getParam('address', false);
		if ($addressId) {
			$address = $this->getOnepage()->getAddress($addressId);

			if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
				$this->getResponse()->setHeader('Content-type', 'application/x-json');
				$this->getResponse()->setBody($address->toJson());
			} else {
				$this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
			}
		}
	}

	/**
	 * Save checkout method
	 */
	public function saveMethodAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$method = $this->getRequest()->getPost('method');
			$result = $this->getOnepage()->saveCheckoutMethod($method);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 * save checkout billing address
	 */
	public function saveBillingAction()
	{
		$customer_shipping_account = "<div id=\"customer_own_shipping\"> <strong>Use my own shipping account </strong> <br>
		<input type=\"checkbox\" onclick=\"display_shipping_panel() \" id=\"radio_surcharge\" name=\" customer_shipping_acc\" /> <label> Surcharge <span> $ 0.00</span></label> <br>";
		$table = "<table> 
		<tr> <td> Shipping method </td> 
		<td>
		<select >
		<option>--Please select shipping service--</option>
		 <option> Fedex</option>
		 <option> UPS </option>
		 <option>UPSP </option>
		 <option>DHL </option>
		</select>
		 </td></tr>
		
		 
		</table>
		</div>";
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			//            $postData = $this->getRequest()->getPost('billing', array());
			//            $data = $this->_filterPostData($postData);
			$data = $this->getRequest()->getPost('billing', array());
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

			if (isset($data['email'])) {
				$data['email'] = trim($data['email']);
			}
			$result = $this->getOnepage()->saveBilling($data, $customerAddressId);

			if (!isset($result['error'])) {
				/* check quote for virtual */
				if ($this->getOnepage()->getQuote()->isVirtual()) {
					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
					);
				} elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
					$result['goto_section'] = 'shipping_method';
					$result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml(). "<br>".$customer_shipping_account.$table.$this->getHTMLShippingMethod()
					);

					$result['allow_sections'] = array('shipping');
					$result['duplicateBillingInfo'] = 'true';
				} else {
					$result['goto_section'] = 'shipping';
				}
			}

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 * Shipping address save action
	 */
	public function saveShippingAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost('shipping', array());
			$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
			$result = $this->getOnepage()->saveShipping($data, $customerAddressId);

			if (!isset($result['error'])) {
				$result['goto_section'] = 'shipping_method';
				$result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()."Demo information"."<input type='radio' vaule='1' />"
                    );
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 * Shipping method save action
	 */
	public function saveShippingMethodAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost('shipping_method', '');
			$result = $this->getOnepage()->saveShippingMethod($data);
			/*
			 $result will have erro data if shipping method is empty
			 */
			if(!$result) {
				Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
				array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

				$result['goto_section'] = 'payment';
				$result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
				);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	/**
	 * Save payment ajax action
	 *
	 * Sets either redirect or a JSON response
	 */
	public function savePaymentAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		try {
			if (!$this->getRequest()->isPost()) {
				$this->_ajaxRedirectResponse();
				return;
			}

			// set payment to quote
			$result = array();
			$data = $this->getRequest()->getPost('payment', array());
			$result = $this->getOnepage()->savePayment($data);

			// get section and redirect data
			$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
			if (empty($result['error']) && !$redirectUrl) {
				$this->loadLayout('checkout_onepage_review');
				$result['goto_section'] = 'review';
				$result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
				);
			}
			if ($redirectUrl) {
				$result['redirect'] = $redirectUrl;
			}
		} catch (Mage_Payment_Exception $e) {
			if ($e->getFields()) {
				$result['fields'] = $e->getFields();
			}
			$result['error'] = $e->getMessage();
		} catch (Mage_Core_Exception $e) {
			$result['error'] = $e->getMessage();
		} catch (Exception $e) {
			Mage::logException($e);
			$result['error'] = $this->__('Unable to set Payment Method.');
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	/* @var $_order Mage_Sales_Model_Order */
	protected $_order;

	/**
	 * Get Order by quoteId
	 *
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder()
	{
		if (is_null($this->_order)) {
			$this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
			if (!$this->_order->getId()) {
				throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
			}
		}
		return $this->_order;
	}

	/**
	 * Create invoice
	 *
	 * @return Mage_Sales_Model_Order_Invoice
	 */
	protected function _initInvoice()
	{
		$items = array();
		foreach ($this->getOnepage()->getQuote()->getAllItems() as $item) {
			$items[$item->getId()] = $item->getQty();
		}
		/* @var $invoice Mage_Sales_Model_Service_Order */
		$invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);
		$invoice->setEmailSent(true);

		Mage::register('current_invoice', $invoice);
		return $invoice;
	}

	/**
	 * Create order action
	 */
	public function saveOrderAction()
	{
		if ($this->_expireAjax()) {
			return;
		}

		$result = array();
		try {
			if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
				$postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
				if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
					$result['success'] = false;
					$result['error'] = true;
					$result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
					return;
				}
			}
			if ($data = $this->getRequest()->getPost('payment', false)) {
				$this->getOnepage()->getQuote()->getPayment()->importData($data);
			}
			Mage::log("1st controller", Zend_Log::INFO); //thuydebug
			
			$this->getOnepage()->saveOrder();
			
            Mage::log("2nd controller", Zend_Log::INFO); //thuydebug
            
			$storeId = Mage::app()->getStore()->getId();
			$paymentHelper = Mage::helper("payment");
			$zeroSubTotalPaymentAction = $paymentHelper->getZeroSubTotalPaymentAutomaticInvoice($storeId);
			if ($paymentHelper->isZeroSubTotal($storeId)
			&& $this->_getOrder()->getGrandTotal() == 0
			&& $zeroSubTotalPaymentAction == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE
			&& $paymentHelper->getZeroSubTotalOrderStatus($storeId) == 'pending') {
				$invoice = $this->_initInvoice();
				$invoice->getOrder()->setIsInProcess(true);
				$invoice->save();
				Mage::log("3rd controller after invoice save", Zend_Log::INFO); //thuy debug
			}

			$redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
			Mage::log("4 controller after invoice save", Zend_Log::INFO); //thuy debug
			$result['success'] = true;
			$result['error']   = false;
		} catch (Mage_Payment_Model_Info_Exception $e) {
			$message = $e->getMessage();
			if( !empty($message) ) {
				$result['error_messages'] = $message;
			}
			$result['goto_section'] = 'payment';
			$result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
			);
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
			$result['success'] = false;
			$result['error'] = true;
			$result['error_messages'] = $e->getMessage();

			if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
					Mage::log("5 controller after invoice save", Zend_Log::INFO); //thuy debug
				$result['goto_section'] = $gotoSection;
				$this->getOnepage()->getCheckout()->setGotoSection(null);
			}

			if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
				if (isset($this->_sectionUpdateFunctions[$updateSection])) {
					$updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
					$result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
					);
				}
				$this->getOnepage()->getCheckout()->setUpdateSection(null);
			}
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
			$result['success']  = false;
			$result['error']    = true;
			$result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
		}
		$this->getOnepage()->getQuote()->save();
			Mage::log("6 controller after invoice save", Zend_Log::INFO); //thuy debug
		/**
		 * when there is redirect to third party, we don't want to save order yet.
		 * we will save the order in return action.
		 */
		if (isset($redirectUrl)) {
			$result['redirect'] = $redirectUrl;
		}
        Mage::log("7 controller after invoice save", Zend_Log::INFO); //thuy debug
       // $result['success']  = true;
		//$result['error']    = false;
		//unset($result['error_messages'] );
		echo "success";
		//$this->getResponse()->setBody('{"success": "true", "error": "false"}');
		//$this->getResponse()->setRedirect(Mage::getBaseUrl() . "checkout/onepage/success");
		//Mage::log("8 controller after invoice save", Zend_Log::INFO); //thuy debug    
	}

	/**
	 * Filtering posted data. Converting localized data if needed
	 *
	 * @param array
	 * @return array
	 */
	protected function _filterPostData($data)
	{
		$data = $this->_filterDates($data, array('dob'));
		return $data;
	}

	/**
	 * @author Luu Thanh Thuy
	 * description: get all the items in shopping cart
	 *
	 */
	/**
	 * get All Items in the cart
	 */
	public function getAllItems() {
		$items = Mage::getSingleton('checkout/cart')->getQuote()->getAllItems();

		return $items;
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
			$temp_warehouse = array();
			if (count($tempArr) > 0) {
				for ($i = 0; $i < count($tempArr) ; $i++) {
					$temp_warehouse[] = trim($tempArr[$i]);
						
					//if ( !in_array($tempArr[$i], $arr_warehouse ) ) {
					//$arr_warehouse[] = $tempArr[$i];

					//}
				}///end for

				$nearest_warehouse = $this->getWarehouseWithSmallestDistance($temp_warehouse);

				if ( !in_array($nearest_warehouse, $arr_warehouse ) ) {
					$arr_warehouse[] = $nearest_warehouse;

				}
			}	else { //in case there is only one warehouse assign for the product
				if ( !in_array($warehouse, $arr_warehouse ) ) {
					$arr_warehouse[] = $warehouse;

				}
			}
		}
		return $arr_warehouse;
	}

	/**
	 * @ mailto : luuthuy205@gmail.com
	 * get all the items in warehouse when checkout
	 * @return array of item correspondent with the warehouse information
	 */
	public function getItemsInWarehouse ($warehouseTitle) {
		$warehouseTitle = trim($warehouseTitle);
		$itemcollection = $this->getAllItems();

		$arr_item = array();
		foreach ($itemcollection as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);
			
			//incase the $warehouse is multiple
			//  echo "======warehouse is ".$warehouse . " warehous title.". $warehouseTitle;
			//// in case of product have multiple option
			$tempArr = explode(",", $warehouse);
			$temp_warehouse = array();
			if (count($tempArr) > 0) {
				for ($i = 0; $i < count($tempArr) ; $i++) {
					$temp_warehouse[] = trim($tempArr[$i]);
					//if ( !in_array($tempArr[$i], $arr_warehouse ) ) {
					//$arr_warehouse[] = $tempArr[$i];
					//}
				}///end for

				$nearest_warehouse = $this->getWarehouseWithSmallestDistance($temp_warehouse);
                if ($nearest_warehouse == $warehouseTitle) {     
				if ( !in_array($item->getId(), $arr_item ) ) {
					$arr_item[] = $item->getProductId();
				}
			 }
			}	
			
			elseif ( $warehouse == $warehouseTitle ) {

				if ( !in_array($item->getId(), $arr_item) ) {
					$arr_item[] = $item->getProductId();
				}
			}

		}
			//thuydebug
			if ($warehouseTitle == 'NY') {
				
				Mage::log($arr_item, Zend_Log::ERR);
			}
			//thuydebug
		return $arr_item;
	}

	/**
	 * @author : Luu Thanh Thuy luuthuy205@gmail.com
	 * @param string $warehouseTitle
	 * @return array of information about the warehouse;
	 */
	public function getWarehouseInformation($warehouseTitle) {
		$warehouseTitle = trim($warehouseTitle);
		$query = "select * from `dropship` where `title`= '$warehouseTitle'";
		//echo "query ==========================>".$query;
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) > 0) {
			return $rows[0];
		} else {
			return false;
		}
	}

	/**
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 * return html of the shipping method
	 */
	public function  getHTMLShippingMethod() {
		$html = " <div id=\"normal_shipping_method\" your order is shipped from multiple warehouses <br/>";
		$warehouseArr = $this->getAllWarehouses();
		for ($i = 0; $i < count($warehouseArr) ;$i++) {
			//Mage::log( $warehouseArr, Zend_Log::ERR);
			$html .= $this->getHTMLByWarehouse($warehouseArr[$i]) ;
			//Mage::log( $this->getHTMLByWarehouse('NY') , Zend_Log::ERR);
		}
		$html .= "</div>";
		return $html;
	}

	/**
	 * @atuhor Luu Thanh Thuy luuthuy205@gmail.com
	 * @param warehouse title
	 * return html
	 */
	public function  getHTMLByWarehouse($title) {
		$title = trim($title);

		$warehouseInfo = $this->getWarehouseInformation($title);

		//if product do not have warehouse attribute then return false
		if ($warehouseInfo) {
			$html = "<strong>Shipping from warehouse :".$warehouseInfo['description']." </strong> <br/>";
			$items = $this->getItemsInWarehouse($title);
			//$arr_productNames = array();
			for ($i = 0; $i < count($items) ; $i++) {
				$html .= Mage::getModel('catalog/product')->load($items[$i])->getName()."<br/>";
				//$html .="<br/>End=====".$title;
			
			}
				$w = $this->getWeightByWarehouse($title);
				$html .= $this->test($w, $title);
			return $html;
		} else {
			return "";
		}
	}

	/**
	 *
	 *get total weight of products in warehouse to calculate price
	 * @param unknown_type $title
	 */
	public function getWeightByWarehouse($title) {
		$weight = 0;
		$arrProducts = $this->getItemsInWarehouse($title);
		for ($i = 0; $i < count($arrProducts); $i++) {
			$weight = $weight + Mage::getModel('catalog/product')->load($arrProducts[$i])->getWeight();
		}
		
		//thuydebug
		Mage::log($title,Zend_Log::ERR );
		Mage::log($arrProducts, Zend_Log::ERR );
		Mage::log($weight, Zend_Log::ERR );
		//thuydebug
		return $weight;
	}
	
	/***
	 * hungnamvnjsc important
	 * description 
	 */

	public function test($w, $title) {


		$arrR=  $this->getOnepage()->getQuote()->getShippingAddress()->getDropshipRateResult(null,$w , $title);
		if (!$arrR instanceof Mage_Shipping_Model_Rate_Result) {
			//exit("There is not available warehouse");
			       unset($result);
			        $result['success'] = false;
					$result['error'] = true;
					$result['error_messages'] = $this->__('There is not available warehouse');
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
					return;
					exit();
		}
		//hungnamvnjsc debug
		Mage::log("array of test method", Zend_Log::INFO);
        Mage::log($arrR, Zend_Log::INFO);

		$arrR = $arrR->asArray();
		// print_r ($arrR);
		$out = "<form action='' method= 'post' name='shipping_method_dropship' >" ;

		$out .= "<input type='hidden' name='title' value='$title' />";
		foreach ($arrR as $key=>$value) {
			$carrier_name = $key;
			if (is_array($value)) {
				//echo $value['title'];
				//echo $value['carrier_title'];
				//hungnamvnjsc debug
				Mage::log("=============Current________", Zend_Log::INFO);
				Mage::log($value, Zend_Log::INFO);
				if ( isset($value['title']) ) 	$out .= "<strong>" . $value['title'] . "</strong><br/>";
				// $value['method_title'];
				foreach ($value as $k=>$v) {

					if (is_array($v)) {

						foreach ($v as $keyc=>$vc) {
							// input radio
							$shipping_method_id = "s_method_". $carrier_name . "_".$keyc;
							$shipping_method_value =$carrier_name. "_".$keyc;
							$out .= "<input id='$shipping_method_id' type='radio' class='radio' value='$shipping_method_value' name='shipping_method' />";
							//$out .= $keyc." : ". $vc;
							if (is_array($vc)) {
								//echo $vc['title'];
								//echo $vc['methods'];
								$out .= "<label> ". $vc['title'];
								$out .= "<span> ".$vc['price_formatted']. "</span> </label> <br/>";
								//foreach ($vc as $key_c=>$v_c) {
								//	$out.= $key_c. "   ===".$v_c."<br>";
								//}
									
							}
						}
					}
				}
			}
		}
		$out .= "</form>";
		//$out .="get weight by ware house title " .$this->getWeightByWarehouse('CL');
		return $out;
	}

	/**
	 * CREATE ADDITION ORDER WITH THE WAREHOUSE INFO
	 *
	 */
	public function getMultiple() {
		return Mage::getSingleton('checkout/type_multishipping');
	}

	public function createAdditionalOrder() {
		$this->getMultiple();

	}

	/**
	 * description : save the dropship shipping method  ( invoked by ajax request)
	 * Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function saveDropshipAction() {
		$tit = $this->getRequest()->getParam('tit');
		$method = $this->getRequest()->getParam('method');
		$result = $this->getOnepage()->saveShippingMethod($method);
		$tempArr = explode('_', $method);
		if (!count($tempArr) > 0 || $tit =='') {
			exit("There is an security striction. Sorry for inconvenience");
		}

		$carrier = $tempArr[0];

		$shipping_method = $tempArr[1];

		//echo $tit. " ". $carrier. " ". $shipping_method;
		$this->saveDropship($tit, $carrier, $shipping_method);

		$result['goto_section'] = 'payment';
		$result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
		);

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	/**
	 * new method to save the shipping method
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function saveDropshipMethodAction() {
		$method = $this->getRequest()->getParam('method');
		if ($method != "") {
			$shipping_warehouse = explode(";", $method);
			if ( count($shipping_warehouse) > 0 ) {
				for ($i = 0; $i < count($shipping_warehouse) ; $i++) {
					//the result of each element is NY:flatrate
					$combine = $shipping_warehouse[$i];
					
					$pair = explode(":", $combine);
					//hungnamvnjsc debug
					//Mage::log("==============in save dropship Method Actin======", Zend_Log::INFO);
					//Mage::log($pair, Zend_Log::INFO);
					if ($pair[0] != "" && $pair[0] != null && $pair[1] != "" && $pair[1] != null) {
					$warehouse = $pair[0];
					$carrier_shipping_method = $pair[1];
					//hungnamvnjsc debug
					Mage::log($warehouse);
					Mage::log($carrier_shipping_method);
					$carrier_ship = explode("_", $carrier_shipping_method);
					if ($carrier_ship[0] != null && $carrier_ship[0] != "" && $carrier_ship[1] != null && $carrier_ship[1] != "") {
					$carrier = $carrier_ship[0];
					$shipping_method = $carrier_ship[1];
					$this->saveDropship($warehouse, $carrier, $shipping_method);
					$result = $this->getOnepage()->saveShippingMethod($carrier_shipping_method);
					Mage::log(".........................carrier shipping method is ". $carrier_shipping_method, Zend_Log::INFO);
					//hungnamvnjsc 
					//Mage::log("save sm====>", Zend_Log::INFO);
					Mage::log($carrier_ship, Zend_Log::INFO);
					}
				  }
				}
			}
			$result['goto_section'] = 'payment';
		$result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
		);

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
		
	}
	/**
	 * luu thong tin ve dropship nhu the nao
	 * $_SESSION['dropship']['']
	 */
	public function saveDropship($tit, $carrier,$method ) {


		$inArr = $this->getSMPrice($tit, $carrier,$method);
		$price = $inArr['price'];
		$shipping_description = $inArr['shipping_description'];
		$warehouseArr= array (
		                   'title'=>$tit,
		                    'carrier'=>$carrier,
		                    'method'=>$method,
		                    'price'=>$price,
		                    'sm_des'=>$shipping_description
		);
		// $_SESSION['warehouse'][]= $warehouseArr;
		if ( isset( $_SESSION['warehouse'][$tit]) ) {
			unset($_SESSION['warehouse'][$tit]);
		}
			
		$_SESSION['warehouse'][$tit]= $warehouseArr;
		$_SESSION['rate'] = $this->getTotalDropshipPrice();
	}

	/**
	 * prevent fraud in calculate price
	 *
	 */
	public function getSMPrice($title, $carrier, $method) {
		//hungnamvnjsc debug
		Mage::log("====================title , carrieer, method method getSMPrice" . $title. " ". $carrier. " ". $method, Zend_Log::INFO);
		$weight = $this->getWeightByWarehouse($title);
		$arrR =  $this->getOnepage()->getQuote()->getShippingAddress()->getDropshipRateResult(null,$weight,$title);

		//var_dump($arrR);
		$arrR = $arrR->asArray();
		$price = '';
		$shipping_description = '';
		$carrier_method = '';
		$out = "<form action='' method= 'post' name='shipping_method_dropship' >" ;

		$out .= "<input type='hidden' name='title' value='$title' />";
		foreach ($arrR as $key=>$value) {
			$carrier_method = $value['title'];
			$carrier_name = $key;
			if (is_array($value)) {

				foreach ($value as $k=>$v) {

					if (is_array($v)) {
						foreach ($v as $keyc=>$vc) {
							if ($keyc == $method) {
								// input radio
								$shipping_description = $carrier_method;
								$shipping_method_id = "s_method_". $carrier_name . "_".$keyc;
								$shipping_method_value =$carrier_name. "_".$keyc;
								$out .= "<input id='$shipping_method_id' type='radio' class='radio' value='$shipping_method_value' name='shipping_method' />";
								//$out .= $keyc." : ". $vc;
								if (is_array($vc)) {
									$shipping_description .= ' - '. $vc['title'];
									$out .= "<label> ". $vc['title'];
									$out .= "<span> ".$vc['price_formatted']. "</span> </label> <br/>";
									$price = $vc['price'];
									//foreach ($vc as $key_c=>$v_c) {
									//	$out.= $key_c. "   ===".$v_c."<br>";
									//}
								}
							} //end if
						} //end foreach

					}
				}
			}
		}
		$shipping_description =trim($shipping_description, ' -');
		$arrout = array (
              'price'=>$price,
              'shipping_description'=>$shipping_description
		);
		//hungnamvnjsc debug
		Mage::log("======output of getSMPrice", Zend_Log::INFO);
		Mage::log($arrout, Zend_Log::INFO);
		return $arrout;
	} //end of this function


	/**
	 * description get total price of all shipping on all warehouse
	 * @author : Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function getTotalDropshipPrice () {
		$price  = 0;
		if ( isset($_SESSION['warehouse']) ) {
			foreach ($_SESSION['warehouse'] as $warehousrate) {
				//foreach ($warehousrate as $k=>$value) {
				//echo $k. "end ";
				//echo "bengin print ===";
				//print_r($value);
				//echo "end print=====";
				$price += $warehousrate['price'];
				//}
			} //foreach
		}  //if
		//echo $price;
		return $price;
	}   //function

	public function testAction() {
		if (isset($_SESSION['rate']) ) {
			echo $_SESSION['rate'];
		} else {
			echo "session rate have not been set";
		}
	}

	/**
	 *
	 * test
	 */
	public function test2Action() {
		if (isset($_SESSION['warehouse']) ) {
			print_r ($_SESSION['warehouse']);
		} else {
			echo "session warehouse have not been set";
		}
	}

	/**
	 * function calculate the distance between two point with longtitude and latitude
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function distance($lat1, $lon1, $lat2, $lon2, $unit) {

		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}

	/**
	 *
	 */
	public function distance1Action() {
		echo $this->distance(41.235, -103.658, 42.144, -75.88, "k") . " kilometersis Nebraska to NY  <br> ";
		echo $this->distance(45.498, -122.692, 42.144, -75.88, "k") . " kilometers is Oregon  to NY  <br> ";
		echo $this->distance(41.235, -103.658, 33.973, -118.244, "k") . " kilometers is Nebraska to California <br> ";
		echo $this->distance(45.498, -122.692, 33.973, -118.244, "k") . " kilometers is Oregon  to California <br> ";
		$nebras   = array();
		$nebras[] = $this->distance(41.235, -103.658, 42.144, -75.88, "k");
		$nebras[] =$this->distance(41.235, -103.658, 33.973, -118.244, "k");
		echo "Small distance to Nebraska ".min($nebras);
		$cali = array();
		$cali[] = $this->distance(45.498, -122.692, 42.144, -75.88, "k");
		$cali[] = $this->distance(45.498, -122.692, 33.973, -118.244, "k");
		echo "Smallest distance to Oregon  ".min($cali);

	}

	/**
	 * description : if a product have multiple warehous for it, so it need to calculate which is the
	 * nearest warehouse
	 *
	 * @param $arrWarehouse  = array("NY", "CL");
	 *
	 * angorithm to do that
	 */
	public function getWarehouseWithSmallestDistance( $arrWarehouse) {
		Mage::log("array warehouse ====>", Zend_Log::INFO);
        Mage::log($arrWarehouse, Zend_Log::INFO);
		
		$destination_zipcode = $this->getOnepage()->getQuote()->getShippingAddress()->getPostcode();
		//thuydebug
		Mage::log('des zipcode '. $destination_zipcode, Zend_Log::ERR);
		//thuydebug
		$desLongLatArr = $this->getLongLat($destination_zipcode);
		$distance_arr = array();
		foreach ($arrWarehouse as $warehouse) {
			//if ($warehouse != null && $warehouse != "") {
			$warehouseInfo = $this->getWarehouseInformation($warehouse);
			//thuydebug
			Mage::log('warehouse is ' . $warehouse ,Zend_Log::ERR);
			Mage::log($warehouseInfo, Zend_Log::ERR);
			Mage::log('warehouse  '.$warehouseInfo['title']. $warehouseInfo['zip_code'], Zend_Log::ERR);
			//thuydebug
			if ($warehouseInfo['zip_code'] != "") {
				
			$longlatArr = $this->getLongLat($warehouseInfo['zip_code']);
		
			$dis =  $this->distance($desLongLatArr['lat'], $desLongLatArr['long'], $longlatArr['lat'], $longlatArr['long'], 'k' );
			$distance_arr[] =$dis;
			Mage::log("distance in loop is ". $dis, Zend_Log::ERR );
				}
			//}
		}
			
		$min = min($distance_arr);
		for ($i = 0; $i < count($distance_arr) ; $i++) {
			if ($distance_arr[$i] == $min) {
				return $arrWarehouse[$i];
			}
		}

	}

	/**
	 *
	 * get the longitude and latitude with given zipcode
	 */
	public function getLongLat($zipcode) {
		$query = "select * from `zip_codes` where `zip` ='$zipcode'";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);

		if (count($rows) <= 0) exit('your zipcode in shipping address is not correct!'. $query);
		$output = array('long'=>$rows[0]['longitude'] , 'lat'=>$rows[0]['latitude']);
		return $output;
	}
	/***
	 *
	 *
	 4
	 down vote
	 accepted
	 Having looked under the covers of both of these beasts I can't say that I actually like either of them as they're both rather ugly when you pop the bonnet and something like OpenCart is actually a much nice and easier to work with solution. However, from a feature perspective OpenCart is nowhere near either Magento or PrestaShop and unlike PrestaShop it doesn't have a team of developers behind it. However having said that it is much easier to understand and modify for anyone with a basic knowledge of OO PHP. It is much better structured. PrestaShop is actually a bit ugly under the covers and CSCart (which is an open source, but not free alternative) may also be a viable solution as it only costs like $300 so not that much. I'd have to say I'd personally go for either PrestaShop or CSCart as they do have a lot more features than OpenCart and at the same time also don't cost anywhere near Magento to setup and run. Magento is an absolute nightmare if you're looking to change anything even if you really know what you're doing... Too many layers.

	 Another cart I would suggest to anyone who isn't fussed about technology is nopCommerce. It's a ASP.NET based shopping cart and it's very well architected and full of features. It is also very easy to modify for anyone with ASP.NET experience.
	 */
    public function getActiveAction() {
		$product_collection = Mage::getModel('catalog/product')->getCollection();
		foreach ($product_collection as $product) {
			//if ($product->getData('currentdeal') == 1) {
			if ($product->getId() == 52) {
				$product = Mage::getModel('catalog/product')->load($product->getId());
				//echo 'test';
				echo $product->getImageUrl();
				echo $product->getName();
				echo $product->getId();
				echo "<img src=\" ";
				echo Mage::helper('catalog/image')->init($product, 'small_image');
				echo " \">";
			}
		}
	}

}
