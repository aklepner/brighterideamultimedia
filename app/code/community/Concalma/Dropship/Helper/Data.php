<?php

class Concalma_Dropship_Helper_Data extends Mage_Core_Helper_Abstract
{
	/***
	 * description: send the email to warehouse
	 */
	public function sendMail($checkout, $text_email) {
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		$mailTemplate = Mage::getModel('core/email_template');
		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		$template = "checkout_payment_success_template";
		$sender = array('email'=>'kontakt@play2day.dk','name'=>'Play2day I/SDenmark');
		$recipient['email'] = $checkout->getCustomerEmail();
		$recipient['name']= $checkout->getCustomerFirstname() . ' ' . $checkout->getCustomerLastname();
		$mailTemplate->setTemplateSubject("Information about game licenses");
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$checkout->getStoreId()))
		->sendTransactional(
		$template,
		$sender,
		$recipient['email'],
		$recipient['name'],
		array(
          'customer' => $checkout->getCustomerFirstname() . ' ' . $checkout->getCustomerLastname(),
          'textmail' => $text_email     
		)
		);
		$translate->setTranslateInline(false);
		return $this;

	}

	/**
	 * send winning email
	 */
	public function sendWinMail($recipient_email, $recipient_name, $text_email) {
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		$mailTemplate = Mage::getModel('core/email_template');
		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		$template = "member_experience_win_template";
		$sender = array('email'=>'sales@coopown.com','name'=>'Coopown');
		$recipient['email'] = $recipient_email;
		$recipient['name']= $recipient_name;
		$mailTemplate->setTemplateSubject("Congratulation!You won the vote");
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
		->sendTransactional(
		$template,
		$sender,
		$recipient['email'],
		$recipient['name'],
		array(
          'customer' => $recipient_name,
          'textmail' => $text_email     
		)
		);
		$translate->setTranslateInline(false);

		return $this;

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
		$orders = $this-> getBrothersOrder($order_id);
		$inf= $this->getOrderInfo($order_id);
		$currency_code = $inf['base_currency_code'];
		if ($currency_code == "USD") $currency_code = "$";
		$subtotal = 0;
		$shipping_handling = 0;
		$grand_total = 0;
		
		$rows = array();
		foreach ($orders as $order) {
			$order_info = $this->getOrderInfo($order['entity_id']);
			$subtotal += $order_info['subtotal'];
		    $shipping_handling += $order_info['shipping_amount'];
			$grand_total  += $order_info['grand_total'];
			$raws = $this->getItemsOrder($order['entity_id']);
			foreach ($raws as $r) {
				$r['warehouse'] = $this->getWarehouseByOrderId($order['entity_id']);
				$rows[] = $r;
			}
		}

		$HTML = "<table class=\"data-table\">";
		$HTML  .= "<thead><tr>
		          <th> Product Name </th>
		          <th> SKU </th>
		          <th>Warehouse</th>
		          <th>Price</th>
		          <th>Qty</th>
		           <th>SubTotal</th>
		</tr></thead>";
		foreach ($rows as $row) {
			$HTML .= "<tr>";
			$HTML .= "<td>".$row['name']. "</td>";
			$HTML .= "<td>".$row['sku']. "</td>";
			$HTML .= "<td>".$row['warehouse']. "</td>";
			$HTML .= "<td>".$currency_code.$row['price']. "</td>";
			$HTML .= "<td> Ordered:".$row['qty_ordered']."</td>";
			$HTML .= "<td> ".$row['row_total']."</td>";
			Mage::log("hard bug", Zend_Log::INFO);
			Mage::log($row, Zend_Log::INFO);
			
			//if ($row['subtotal']) 
			//$subtotal += $row['subtotal'];
			//$shipping_handling += $row['shipping_amount'];
			//$grand_total  += $row['grand_total'];
			
			//qty_ordered
		}
		$HTML .= "<tfoot> <tr> <td class=\"a-right\" colspan= \"5\"> Subtotal</td> <td> " .$currency_code.$subtotal."</td></tr>";
		$HTML .= "<tr> <td class=\"a-right\" colspan= \"5\"> Shipping & Handling </td> <td> " .$currency_code.$shipping_handling."</td></tr>";
		$HTML .= "<tr> <td class=\"a-right\" colspan= \"5\"> <strong> Grand Total </strong> </td> <td> <strong> " .$currency_code.$grand_total." </strong> </td></tr> </tfoot>";
		$HTML .= "</table>";
		return $HTML;
	}

	public function getBrothersOrder($order_id) {
		$query = "select * from `sales_flat_order` where `pseudo_order_id` =$order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		//foreac
		//return $rows;
		return $rows;
	}
	public function getItemsOrder($order_id) {
		$query = "select * from `sales_flat_order_item` where `order_id` =$order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);

		return $rows;
	}
	public function getWarehouseByOrderId($order_id) {
		$query = "select * from `sales_flat_order` where `entity_id` =$order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) == 0) return false;
		return $rows[0]['warehouse'];
	}

	public function renderOnAccountOrderPage($customer_id , $order_id ) {
		$query = "select * from `sales_flat_order` where `customer_id` =$customer_id and `entity_id` = $order_id";

		Mage::log($query, Zend_Log::INFO) ; //thuy debug
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ( count($rows) == 0 )  exit("Customer have not bought any product");

		if ($rows[0]['pseudo_order_id']) {
			if ($rows[0]['entity_id'] != $rows[0]['pseudo_order_id']) {
				return false;
			}
				
			//			if ($row[0][]) {
			//
			//			}
		}
		return true;
	}

	/***
	 * get the warehouse title if given the warehouse
	 * @author Luu Thanh Thuy luuthuy205@gmail.com
	 */
	public function getWarehouseTitleByOrderId($order_id) {
		$query = "select * from `sales_flat_order_id where `entity_id` = $order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if ($count($rows) == 0) return false;
		return $rows[0]['warehouse'];
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
		return $rows[0];
	}
	
	/**
	 * description get all available carrier by warehouse
	 */
	public function getAvailableCarrier($warehouse) {
	 $info = $this->getWarehouseInformation($warehouseTitle);
	 
	}
	
	/**
	 * description get the sub orders of the real order
	 */
	public function getSubOrders($order_id) {
		$query = "select * from `sales_flat_order` where `entity_id` =$order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if (count($rows) == 0) {
			exit("the order id  is not correct");
		}
		$pseudo_cr = $rows[0]['pseudo_increment_id'];
		$query = "select * from `sales_flat_order` where `pseudo_increment_id` = '$pseudo_cr'";
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	/**
	 * description get order information
	 */
public function getOrderInfo($order_id) {
		$query = "select * from `sales_flat_order` where `entity_id` =$order_id";
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		if (count($rows) == 0) {
			exit("the order id  is not correct");
		}
		return $rows[0];
	}
	
	/**
	 * description gell all the product on last Order Id
	 */
	public function getLastedBuyPd() {
		$out = array();
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tPrefix = (string) Mage::getConfig()->getTablePrefix();
		$quoteItemTable = $tPrefix.'sales_flat_quote_item';
		$session = Mage::getSingleton('customer/session');
		$custId = $session->getId();
		$query = "select max(created_at)  as max from `".$tPrefix. "sales_flat_order` where `customer_id`=".$custId;
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		$max = $rows[0]['max'];

	   $query = "select max(created_at)  as max from `".$tPrefix. "sales_flat_order` where `created_at`='".$max."'";
       $rs = $db->query($query);
	   $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
	   $order_id = $rows[0]['entity_id'];
	   //select the order - id
	   	$query = "select max(created_at)  as max from `".$tPrefix. "sales_flat_order_item` where `order_id`=".$order_id;
	   	$rs = $db->query($query);
	   $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
	   foreach ($rows as $row) {
	   	$out[] = $row['product_id'];
	   }
	   return $out;
	}
	public function getCurrentHit($product_id) {
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tPrefix = (string) Mage::getConfig()->getTablePrefix();
		$quoteItemTable = $tPrefix.'first_deal_exp';
		$query = "select * from $quoteItemTable where product_id= ".$product_id;
		 $rs = $db->query($query);
	    $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
	    return $rows[0]['hit_number'];
	}
}