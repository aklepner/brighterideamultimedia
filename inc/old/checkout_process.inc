<?
	require_once("../../inc/dbi.inc");
	require_once("../../inc/crypt.inc");
	require_once("../../inc/cc.inc");

	$db = new dbi;
	$tempdb = new dbi;

	if(isset($_POST['process'])){
		if(isset($_POST['card_number'])){
			if($_POST['card_number'] == ""){
				$error = "Please specify a Credit Card Number.";
			}elseif($_POST['year'].sprintf("%02d",$_POST['month']) < date("Ym",time())){
				$error = "Expiration Date on Credit Card has passed.";
			}elseif(!unserialize($_SESSION['cart']) || !sizeof(unserialize($_SESSION['cart']))){
				$error = "Shopping Cart is empty!";
			}
		}else{
			if($_POST['po_number'] == "")
				$error = "Please specify a Purchase Order Number";
		}

		if(!$error){
			if(isset($_POST['po_number']))
				$payment_method = 'po';
			else
				$payment_method = 'cc';
			if(!isset($_POST['order_id']) || $_POST['order_id'] < 1){
				$disc = unserialize($_SESSION['discount']);
				$shipping = unserialize($_SESSION['ship']);
				$cart_array = unserialize($_SESSION['cart']);
				if(!sizeof($cart_array) || $_SESSION['account_id'] < 1){
					$error = "There was an error completing your order!  Please contact Data Business Systems.";
					$fatal = 1;
				}else{
					$results = mysql_query("select id from orders where account = '".$_SESSION['account_id']."' and datetime > '".(time()-(10*60))."'", $dbh);
					if(mysql_num_rows($results)){
						$error = "This order was already placed.  Please check your email for confirmation, and if you have any questions, please contact Data Business Systems.";
						$fatal = 1;
					}else{
						// Store Addresses if New
						if($_SESSION['rebill'] > 0){
							$bill_address = $_SESSION['rebill'];
						}else{
							 mysql_query("insert into order_address(account, type, name, company, address1, address2, city, state, zip_code, phone, fax) values ('".$_SESSION['account_id']."', 'bill', '".addslashes($_SESSION[name])."', '".addslashes($_SESSION[company])."', '".addslashes($_SESSION[address1])."', '".addslashes($_SESSION[address2])."', '".addslashes($_SESSION[city])."', '$_SESSION[state]', '".$_SESSION[zip_code]."', '".addslashes($_SESSION[phone])."', '".addslashes($_SESSION[fax])."')");
							 $bill_address = mysql_insert_id($dbh);
						}
						if($_SESSION['reship'] > 0){
							$ship_address = $_SESSION['reship'];
						}else{
							mysql_query("insert into order_address(account, type, name, company, address1, address2, city, state, zip_code, phone, fax) values ('".$_SESSION['account_id']."', 'ship', '".addslashes($_SESSION['sname'])."', '".addslashes($_SESSION['scompany'])."', '".addslashes($_SESSION['saddress1'])."', '".addslashes($_SESSION['saddress2'])."', '".addslashes($_SESSION['scity'])."', '".$_SESSION['sstate']."', '$_SESSION[szip_code]', '".addslashes($_SESSION['sphone'])."', '".addslashes($_SESSION['sfax'])."')");
							$ship_address = mysql_insert_id($dbh);
						}
						// Store Order
						if(isset($_SESSION['coupon'])){
							$results = mysql_query("select * from coupon where code = '".$_SESSION['coupon']."'", $dbh);
							if(mysql_num_rows($results)){
								$discount_reason = addslashes(mysql_result($results, 0, "code")." - ".mysql_result($results, 0, "description"));
								if(mysql_result($results, 0, "oneuse") == 'y'){
									mysql_query("update coupon set expire = '".date("Y-m-d",time()-(48*60*60))."' where code = '".$_SESSION['coupon']."'", $dbh);
								}
							}
						}
						mysql_query("insert into orders (account, site, datetime, bill_address, ship_address, ship_method, payment_method, subtotal, shipping, tax, total, coupon, status, special_instructions) values ('".$_SESSION['account_id']."', '".SITE."', '".time()."', '$bill_address', '$ship_address', '".$_SESSION['service']."', '$payment_method', '".$_SESSION['sub_total']."', '".$_SESSION['ship_total']."', '".$_SESSION['tax']."', '".$_SESSION['total']."', '$discount_reason', 'new', '".mysql_real_escape_string($_SESSION['special_instructions'])."')", $dbh);
						$order_id = mysql_insert_id($dbh);
						// Store Items
						$_SESSION['cc_description'] = "";
						foreach($cart_array as $key => $val){
							list($product, $quantity, $option) = split(",", $val);
							$db->query("select * from product left join product_category on product_category.id = product.category left join product_quantity on product_quantity.product = product.id left join product_option on product_option.product = product.id and product_option.id = '$option' where product_quantity.product = '$product' and product_quantity.quantity = '$quantity'");
							if($db->numrows()){
								if(is_file("../form/".$db->result("product_category.id").".htm")){
									$form_print[] = "<a href=\"../form/".$db->result("product_category.id").".htm\" target=\"_blank\">".$db->result("product_category.name")."</a>";
								}
								$item_id = $db->result("product.id");
								if($db->result("product_option.sku") != "")
									$item = $db->result("product_option.sku");
								else
									$item = $db->result("product.sku");
	
								$desc = $db->result("product_category.name");
								if($db->result("product_option.id") != ""){
									$item_option = $db->result("product_option.id");
									$desc .= "/".$db->result("product_option.description");
								}else{
									$item_option = 0;
								}
								$desc .= "/".$db->result("product.description");
								$_SESSION['cc_description'] .= strip_tags($desc)." ";
								$weight = ($db->result("product_quantity.quantity")/$db->result("unit_quantity"))*$db->result("unit_weight");
								$price = round(($db->result("product_quantity.quantity")/$db->result("unit_quantity"))*$db->result("unit_price"), 2);
								if($db->result("unit_cost"))
									$cost = "'".round(($db->result("product_quantity.quantity")/$db->result("unit_quantity"))*$db->result("unit_cost"), 2)."'";
								else 
									$cost = "NULL";
								list($ship_center,$ship_cost) = split(",", $shipping[$key]);
								$results = mysql_query("select * from warehouse where id = '".$ship_center."'", $dbh);
								if(mysql_num_rows($results)){
									$row = mysql_fetch_assoc($results);
									$vendor_warehouse = "";
									if($row['name'])
										$vendor_warehouse .= $row['name']." ";
									if($row['address'])
										$vendor_warehouse .= $row['address']." ";
									$vendor_warehouse .= $row['city'].", ".$row['state']." ".$row['zip'];
									if($row['phone'])
										$vendor_warehouse .= " ".$row['phone'];
								}else{
									$vendor_warehouse = "N/A";
								}
								mysql_query("insert into order_items (order_id, item, item_id, item_option, description, comment, quantity, weight, cost, price, discount, ship_cost, ship_center) values ('$order_id', '$item', '$item_id', '$item_option', '".mysql_real_escape_string(strip_tags($desc))."', '".mysql_real_escape_string($db->result("product.comment"))."', '$quantity', '$weight', ".$cost.", '$price', '".$disc[$key]."', '$ship_cost', '".mysql_real_escape_string($vendor_warehouse)."')", $dbh);
							}
						}
					}
				}
			}else{
				$roi_order_total = $_SESSION['total'];
				$order_id = $_POST['order_id'];
			}
			if(!$error){
				if(is_numeric($order_id) && $order_id > 0){
					// Process Credit Card
					if($payment_method == 'po'){
						mysql_query("replace into order_po (id, po_number) values ('$order_id', '".addslashes(strip_tags($_POST['po_number']))."')");
					}else{
						$results = mysql_query("select id from cc_charges where response = '1' and order_id = '$order_id'", $dbh);
						if(mysql_num_rows($results)){
							$error = "This order has already been placed.  Check your e-mail for confirmation, and if you have questions, please contact Data Business Systems.";
							$fatal = 1;
						}else{
							// Get Billing Address to Send to Authorize.net
							$results = mysql_query("select order_address.* from orders inner join order_address on order_address.id = orders.bill_address where orders.id = '$order_id' limit 0,1", $dbh);
							$bill = mysql_fetch_assoc($results);
							$results = mysql_query("select order_address.* from orders inner join order_address on order_address.id = orders.ship_address where orders.id = '$order_id' limit 0,1", $dbh);
							$ship = mysql_fetch_assoc($results);
							$ship['first_name'] = preg_replace("/^([^ ]+) .*$/","\\1", $ship['name']);
							$ship['last_name'] = preg_replace("/^[^ ]+ (.*)$/","\\1", $ship['name']);
							$error = cc_proc($_POST['first_name'], $_POST['last_name'], $_SESSION['account_id'],$_SESSION['email'], $order_id, $_POST['card_number'], $_POST['ccid'], $_POST['month']."/".$_POST['year'], $_SESSION['total'], $_SESSION['cc_description'], $bill['company'], $bill['address1']." ".$bill['address2'], $bill['city'], $bill['state'], $bill['zip_code'], $bill['phone'], $bill['fax'], $ship['first_name'], $ship['last_name'], $ship['company'], $ship['address1']." ".$ship['address2'], $ship['city'], $ship['state'], $ship['zip_code']);							
							if($error == "") unset($error);
						}
					}
					if(!$error){
						$roi_order_total = $_SESSION['total'];
						// Send Admin Email
						$from = "DBS Order <orders@databusinesssystems.com>";
						$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from";
						$message = "A new order has been placed.  Go Here to view it:\n\n";
						$message .= "https://www.databusinesssystems.com/admin/order.php?id=$order_id\n\n";
						// User Information
						$results = mysql_query("SELECT account.email, count(orders.id) as cnt FROM account LEFT JOIN orders ON orders.account = account.id WHERE account.id = '".$_SESSION['account_id']."' GROUP BY account.id", $dbh);
						if(mysql_num_rows($results))
							$message .= "User: ".mysql_result($results, 0, "email")." (".$_SESSION['account_id'].") Orders: ".mysql_result($results, 0, "cnt")."\n\n";
						// Products
						$results = mysql_query("SELECT item, description, quantity FROM order_items WHERE order_id = '".$order_id."'", $dbh);
						if(mysql_num_rows($results)){
							while($row = mysql_fetch_assoc($results)){
								$message .= $row['item']." - ".$row['description']." QTY: ".$row['quantity']."\n\n";
							}
						}
						// Billing Address
						$results = mysql_query("select order_address.* from orders inner join order_address on order_address.id = orders.bill_address where orders.id = '".$order_id."' limit 0,1", $dbh);
						if(mysql_num_rows($results)){
							$bill = mysql_fetch_assoc($results);
							$message .= "\n\nBilling Information\n";
							$message .= "\t".$bill['name']."\n";
							if($bill['company'])
								$message .= "\t".$bill['company']."\n";
							$message .= "\t".$bill['address1']." ".$bill['address2']."\n";
							$message .= "\t".$bill['city'].", ".$bill['state']." ".$bill['zip_code']."\n";
							$message .= "\tPhone: ".$bill['phone']."\n\n";
						}
						// Shipping Address
						$results = mysql_query("select order_address.* from orders inner join order_address on order_address.id = orders.ship_address where orders.id = '".$order_id."' limit 0,1", $dbh);
						if(mysql_num_rows($results)){
							$ship = mysql_fetch_assoc($results);
							$message .= "Shipping Information\n";
							$message .= "\t".$ship['name']."\n";
							if($ship['company'])
								$message .= "\t".$ship['company']."\n";
							$message .= "\t".$ship['address1']." ".$ship['address2']."\n";
							$message .= "\t".$ship['city'].", ".$ship['state']." ".$ship['zip_code']."\n";
						}
						// mail("customercare@databusinesssystems.com", "[DBS] New Order #".$order_id, wordwrap($message), $header);
						// Send Customer Email
						$from = "DBS Order <orders@databusinesssystems.com>";
						$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from";
						$message = "Thank you for your order ".$order_id." placed on ".date("F j, Y",time()).".  Stock products usually ship within 24-72 hours.  Custom printed products ship within 7 to 10 business days.\n\n";
						$message .= "If you would like to check the status of your order,  please wait 24 hours for the order to be entered into our system, please click on the following:\n\n";
						$message .= "http://www.databusinesssystems.com/status/".$order_id."\n\n";
						$message .= "If you would like to view your invoice, order history or re-order you can log in to the customer account area and enter your email address and password.\n\n";
						$message .= "The direct link to the account area is: https://www.databusinesssystems.com/account/\n\n";
						$message .= "If you have any questions please call 800-778-6247 to speak to a customer service representative.  Please have your order number available so we can better assist you.\n\n";
						$message .= "Thank you for choosing Data Business Systems.\n\n";
						$message .= "Sincerely,\n\n";
						$message .= "Customer Service Department\nsales@databusinesssystems.com\n";
						mail($_SESSION['email'], "Your order with ".SITE_NAME." (#$order_id)", wordwrap($message),$header);
						$cart = array();
						$cart_items = 0;
						unset($bill, $ship);
						// Order has been placed, kill cart cookies and kill session
						session_destroy();
						setcookie("cart", "", time()-3600, "/", ".databusinesssystems.com", 1);
						setcookie("coupon", "", time()-3600, "/", ".databusinesssystems.com", 1);
						setcookie("po", "", time()-3600, "/", ".databusinesssystems.com", 1);
					}
				}else{
					$error = "Please check your information and try again.  If you still experience difficulties, please contact Data Business Systems.";
				}
			}
		}
	}
?>
