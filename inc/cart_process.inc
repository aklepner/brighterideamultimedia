<?
	session_start();
	if($_SESSION['admin'] == "true" && $_POST['admin_order']){
		$results = mysql_query("select * from account where id = '".$_POST['admin_order']."' limit 1", $dbh);
		if(mysql_num_rows($results)){
			$row = mysql_fetch_assoc($results);
			$_SESSION['account_id'] = $row['id'];
			$_SESSION['email'] = $row['email'];
			$_SESSION['account_po'] = $row['po'];
			$_SESSION['account_taxrate'] = $row['taxrate'];
		}
	}
	
	if(isset($_COOKIE['cart']) && $_COOKIE['cart'] != "")
		$cart = unserialize($_COOKIE['cart']);
	else
		$cart = array();
		
	if(isset($_POST['po']))
		setcookie("po",1,0,"/",".databusinesssystems.com",0);

	// Delete Item from Cart
	if(isset($_POST['delete']))
		unset($cart[$_POST['delete']]);

	// Add Item to Cart
	if(isset($_POST['product']) && isset($_POST['quantity'])){
		$results = mysql_query("SELECT expire FROM product left join product_category on product_category.id = product.category left join product_quantity pq on pq.product = product.id left join product_option on product_option.product = product.id and product_option.id = '".(isset($_POST['option'])?$_POST['option']:0)."' where pq.product = '".$_POST['product']."' and pq.quantity = '".$_POST['quantity']."'", $dbh)
			or error_log("MySQL Error: ".mysql_error($dbh)." in ".__FILE__." on line ".__LINE__);
		if(mysql_num_rows($results)){
			$expire = mysql_result($results, 0, "expire");
			if(!$expire || strtotime($expire) >= time()){
				if(isset($_POST['option']))
					array_push($cart,$_POST['product'].",".$_POST['quantity'].",".$_POST['option']);
				else
					array_push($cart,$_POST['product'].",".$_POST['quantity'].",0");
			}else{
				$cerror = "This items pricing has expired.  Please contact us for updated pricing information.";
			}
		}else{
			$cerror = "This item is not available for purchase at the moment.  Please use the menu on the left to find a similar product or contact us for more information.";
		}
	}

	$cart_items = sizeof($cart);
	setcookie("cart",serialize($cart),time()+60*60*24*7,"/",".databusinesssystems.com",0);

	if($_POST['coupon']){
		$coupon = $_POST['coupon'];
	}elseif(isset($_COOKIE['coupon']) && $_COOKIE['coupon']){
		$coupon = $_COOKIE['coupon'];
	}

	if($coupon && $cart_items > 0){
		$coupon = strtoupper($coupon);
		$cdb = new dbi();
		$cdb->query("select * from coupon where code = '$coupon' and (expire IS NULL or expire >= NOW())");
		if($cdb->numrows()){
			if($cdb->result("type") == 'acategory' || $cdb->result("type") == 'aproduct'){
				$db = new dbi();
				foreach($cart as $val){
					list($product,$quantity,$option) = split(",",$val);
					if($cdb->result("type") == "aproduct" && $product == $cdb->result("type_id")){
						$found = 1;
					}else{
						$db->query("select product.category as cat from product where id = '$product'");
						if($cdb->result("type") == "acategory" && $db->result("cat") == $cdb->result("type_id"))
							$found = 1;
					}
				}
				if(!$found)
					$cerror = "Invalid Coupon: $coupon";
			}
		}else{
			$cerror = "Coupon Not Available: $coupon";
		}
		if($cerror){
			unset($cdb);
			unset($coupon);
			setcookie("coupon","",time()-3600,"/",".databusinesssystems.com",0);
		}else{
			setcookie("coupon",$coupon,time()+60*60*24*7,"/",".databusinesssystems.com",0);
		}
	}elseif($cart_items == 0){
		setcookie("coupon","",time()-3600,"/",".databusinesssystems.com",0);
	}
?>
