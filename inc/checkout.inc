<h1>Checkout</h1>
<?
	echo $_COOKIE['po']."<br />";
	if(isset($_POST['newaccount'])){
		if(!preg_match("/^[a-z0-9\.\-_]+\@[a-z0-9\.\-_]+\.[a-z]+$/",$_POST['email'])){
			$error = "Create a valid e-mail account.";
		}elseif(strlen($_POST['password']) < 4 || strlen($_POST['password']) > 16){
			$error = "Password must be between 4 and 16 characters.";
		}elseif($_POST['password'] != $_POST['cpassword']){
			$error = "Passwords do not match.";
		}else{
			$results = mysql_query("select id from account where email = '".$_POST['email']."'", $dbh);
			if(mysql_num_rows($results)){
				$error = "Account already exists.  Please try logging in.";
			}else{
				if(isset($_COOKIE['po']) && $_COOKIE['po'] == 1)
					$_SESSION['account_po'] = 'y';
				else
					$_SESSION['account_po'] = 'n';
				 mysql_query("insert into account (email, password, po, created) values('".strtolower($_POST['email'])."', '".base64_encode(encrypto($_POST['password'],strtolower(substr($_POST['email'],0,2))))."', '".$_SESSION['account_po']."',  '".date("Y-m-d H:i:s",time())."')", $dbh);
				$_SESSION['account_id'] = mysql_insert_id($dbh);
				$_SESSION['email'] = $_POST['email'];
			}
		}
	}
	if(isset($_POST['login'])){
		if($_POST['email'] == "" || $_POST['password'] == ""){
			$error = "Please specify an E-mail and Password to login!";
		}else{
			$results = mysql_query("select * from account where lower(email) = '".strtolower($_POST['email'])."' and (password = '".base64_encode(encrypto($_POST['password'],strtolower(substr($_POST['email'],0,2))))."' or password = '".base64_encode(encrypto(strtolower($_POST['password']),strtolower(substr($_POST['email'],0,2))))."' or password = '".base64_encode(encrypto(strtoupper($_POST['password']),strtolower(substr($_POST['email'],0,2))))."') limit 1", $dbh);
			if(mysql_num_rows($results)){
				$row = mysql_fetch_assoc($results);
				$_SESSION['account_id'] = $row['id'];
				$_SESSION['email'] = $row['email'];
				if(isset($_COOKIE['po']) && $_COOKIE['po'] == 1){
					mysql_query("update account set po = 'y' where account.id = '".$_SESSION['account_id']."'", $dbh);
					$_SESSION['account_po'] = 'y';
				}else{
					$_SESSION['account_po'] = $row['po'];
				}
				$_SESSION['account_taxrate'] = $row['taxrate'];
			}else{
				$error = "Invalid Email/Password.  Please try again.";
			}
		}
	}
	if(isset($_POST['address1'])){
		if(!isset($_POST[address]) && !isset($_SESSION[address1])){
			if($_POST[rebill] < 1 && $_POST[name] == ""){
				$error = "Please specify a Billing Name.";
			}elseif($_POST[rebill] < 1 && $_POST[address1] == ""){
				$error = "Please specify a Billing Address.";
			}elseif($_POST[rebill] < 1 && $_POST[city] == ""){
				$error = "Please specify a Billing City.";
			}elseif($_POST[rebill] < 1 && $_POST[state] == ""){
				$error = "Please specify a Billing State.";
			}elseif($_POST[rebill] < 1 && $_POST['zip_code'] == ""){
				$error = "Please specify a valid Billing Zip Code!";
			}elseif($_POST[rebill] < 1 && $_POST[phone] == ""){
				$error = "Please specify a Billing Phone Number.";
			}elseif($_POST[reship] < 1 && !isset($_POST[sameasbill]) && $_POST[sname] == ""){
				$error = "Please specify a Shipping Name.";
			}elseif($_POST[reship] < 1 && !isset($_POST[sameasbill]) && $_POST[saddress1] == ""){
				$error = "Please specify a Shipping Address.";
			}elseif($_POST[reship] < 1 && !isset($_POST[sameasbill]) && $_POST[scity] == ""){
				$error = "Please specify a Shipping City.";
			}elseif($_POST[reship] < 1 && !isset($_POST[sameasbill]) && $_POST[sstate] == ""){
				$error = "Please specify a Shipping State.";
			}elseif($_POST[reship] < 1 && !isset($_POST[sameasbill]) && $_POST['szip_code'] == ""){
				$error = "Please specify a valid Shipping Zip Code!";
			}elseif($_POST[reship] < 1 && !isset($_POST[sameasbill]) && $_POST[sphone] == ""){
				$error = "Please specify a Shipping Phone Number.";
			}else{
				$fields = array("name","company","address1","address2","city","state","zip_code","phone","fax");
				if($_POST[rebill] > 0){
					$_SESSION[rebill] = $_POST[rebill];
				}else{
					foreach($fields as $field){
						$_SESSION["$field"] = $_POST["$field"];
					}
				}
				if($_POST[reship] > 0){
					$_SESSION[reship] = $_POST[reship];
				}elseif(isset($_POST[sameasbill]) && $_POST[rebill] > 0){
					$_SESSION[reship] = $_POST[rebill];
				}elseif(isset($_POST[sameasbill]) && $_POST[rebill] < 1){
					foreach($fields as $field){
						$_SESSION["s$field"] = $_POST["$field"];
					}
				}else{
					foreach($fields as $field){
						$_SESSION["s$field"] = $_POST["s$field"];
					}
				}
			}
		}
	}
	if((!$error && (isset($_POST[newaccount]) || isset($_POST[login]))) || ($error && isset($_POST[address1])) || (isset($_POST[checkout]) && isset($_SESSION[account_id]) && isset($_SESSION[email]))){
	if(isset($_POST['checkout'])){
		if(isset($_POST['cart']))
			$_SESSION['cart'] = urldecode($_POST['cart']);
		if(isset($_POST['coupon']))
			$_SESSION['coupon'] = $_POST['coupon'];
		if(isset($_POST['fax_special']))
			$_SESSION['special_instructions'] = trim(strip_tags($_POST['special_instructions'])."\nWill Fax More Info");
		elseif(isset($_POST['special_instructions']))
			$_SESSION['special_instructions'] = trim(strip_tags($_POST['special_instructions']));

	}
	if(isset($_POST[newaccount])){?>
	<div align="center"><b>Your account has been created!</b></div>
<?
	}
	if(isset($_POST[address1]) && $error){?>
	<div align="center" style="color:red;"><b><?=$error?></b></div>
<? } ?>
<script language="JavaScript">
	function billtoship(){
		if(document.addressinfo.sameasbill.checked){
			document.addressinfo.sname.value = document.addressinfo.name.value;
			document.addressinfo.scompany.value = document.addressinfo.company.value;
			document.addressinfo.saddress1.value = document.addressinfo.address1.value;
			document.addressinfo.saddress2.value = document.addressinfo.address2.value;
			document.addressinfo.scity.value = document.addressinfo.city.value;
			document.addressinfo.sstate.value = document.addressinfo.state.value;
			document.addressinfo.szip_code.value = document.addressinfo.zip_code.value;
			document.addressinfo.sphone.value = document.addressinfo.phone.value;
			document.addressinfo.sfax.value = document.addressinfo.fax.value;
		}
	}
</script>
	<form name="addressinfo" method="post" action="<?=$PHP_SELF?>"><table width="95%" border="0" cellspacing="0" cellpadding="4" align="center"><tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Billing Address</td></tr>
<?
	$results = mysql_query("select * from order_address where account = '$_SESSION[account_id]' and type = 'bill' and disable = 'n'", $dbh);
	if(mysql_num_rows($results)){
?>
<tr><td align="right" width="40%" style="font-weight:bold;">Choose your Billing Address:</td><td align="left" width="60%" style="font-weight:bold;"><select name="rebill"><option value="">Enter Below or choose from list</option><?
	while($row = mysql_fetch_assoc($results)){
		print "<option value=\"".$row['id']."\"";
		if(isset($error) && $_POST['rebill'] == $row['id'])
			print " selected=\"selected\"";
		print ">".$row['name'].", ".$row['address1']." ".$row['address2']." ".$row['city'].", ".$row['state']."</option>";
	}
?></select></td></tr>
<tr><td align="center" class="bar" style="font-weight:bold;" colspan="2">... or enter a New Billing address below.</td></tr>
<? } ?>
<tr><td align="right" width="40%" style="font-weight:bold;">Name</td><td align="left" width="60%"><input type="text" name="name" size="35" value="<? if($error){ print $_POST['name']; }elseif(isset($_SESSION[icustomer][name])){ print $_SESSION[icustomer][name]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Company</td><td align="left" width="60%"><input type="text" name="company" size="35" value="<? if($error){ print $_POST['company']; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Address 1</td><td align="left" width="60%"><input type="text" name="address1" size="35" value="<? if($error){ print $_POST[address1]; }elseif(isset($_SESSION[icustomer][address1])){ print $_SESSION[icustomer][address1]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Address 2</td><td align="left" width="60%"><input type="text" name="address2" size="35" value="<? if($error){ print $_POST[address2]; }elseif(isset($_SESSION[icustomer][addess2])){ print $_SESSION[icustomer][address2]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">City</td><td align="left" width="60%"><input type="text" name="city" size="35" value="<? if($error){ print $_POST[city]; }elseif(isset($_SESSION[icustomer][city])){ print $_SESSION[icustomer][city]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">State</td><td align="left" width="60%"><select name="state"><option value=""></option><?
			$results = mysql_query("select * from states order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['abbr']."\"";
				if((isset($error) && $_POST['state'] == $row['abbr']) || (isset($_SESSION[icustomer][state]) && $_SESSION[icustomer][state] == $row['abbr']))
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?><option value="">--</option><option value="CANADA"<? if((isset($error) && $_POST['state'] == "CANADA") || (isset($_SESSION['icustomer']['state']) && $_SESSION['icustomer']['state'] == "CANADA")) print " selected=\"selected\""; ?>>Canada</option></select></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Zip Code</td><td align="left" width="60%"><input type="text" name="zip_code" size="15" value="<? if($error){ print $_POST[zip_code]; }elseif(isset($_SESSION[icustomer][zip_code])){ print $_SESSION[icustomer][zip_code]; }  ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Phone Number</td><td align="left" width="60%"><input type="text" name="phone" size="20" value="<? if($error){ print $_POST[phone]; }elseif(isset($_SESSION[icustomer][phone])){ print $_SESSION[icustomer][phone]; }  ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Fax Number</td><td align="left" width="60%"><input type="text" name="fax" size="20" value="<? if($error){ print $_POST['fax']; }elseif(isset($_SESSION['icustomer']['fax'])){ print $_SESSION['icustomer']['fax']; }  ?>"></td></tr>
<tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Shipping Address</td></tr>
<?
	$results = mysql_query("select * from order_address where account = '".$_SESSION['account_id']."' and type = 'ship' and disable = 'n'", $dbh);
	if(mysql_num_rows($results)){
?>
<tr><td align="right" width="40%"><b>Choose your Shipping Address:</b></td><td align="left" width="60%" style="font-weight:bold;"><select name="reship"><option value="">Enter Below or choose from list</option><?
	while($row = mysql_fetch_assoc($results)){
		print "<option value=\"".$row['id']."\"";
		if(isset($error) && $_POST[rebill] == $row['id'])
			print " selected=\"selected\"";
		print ">".$row['name'].", ".$row['address1']." ".$row['address2']." ".$row['city'].", ".$row['state']."</option>";
	}
?></select></td></tr>
<tr><td align="center" class="bar" colspan="2"><b>... or enter a New Shipping address below.</b></td></tr>
<? } ?>
<tr><td align="center" colspan="2"><input type="checkbox" name="sameasbill" id="sameasbill" value="1" onClick="billtoship()"> <label for="sameasbill">Check here if the Shipping Address is the same as the Billing Address.</label></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Name</td><td align="left" width="60%"><input type="text" name="sname" size="35" value="<? if($error){ print $_POST[sname]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Company</td><td align="left" width="60%"><input type="text" name="scompany" size="35" value="<? if($error){ print $_POST[scompany]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Address 1</td><td align="left" width="60%"><input type="text" name="saddress1" size="35" value="<? if($error){ print $_POST[saddress1]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Address 2</td><td align="left" width="60%"><input type="text" name="saddress2" size="35" value="<? if($error){ print $_POST[saddress2]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">City</td><td align="left" width="60%"><input type="text" name="scity" size="35" value="<? if($error){ print $_POST[scity]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">State</td><td align="left" width="60%"><select name="sstate"><option value=""></option><?
			$results = mysql_query("select * from states order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['abbr']."\"";
				if(isset($error) && $_POST['sstate'] == $row['abbr'])
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?><option value="">--</option><option value="CANADA"<? if(isset($error) && $_POST['sstate'] == "CANADA") print " selected=\"selected\""; ?>>Canada</option></select></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Zip Code</td><td align="left" width="60%"><input type="text" name="szip_code" size="15" value="<? if($error){ print $_POST[szip_code]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Phone Number</td><td align="left" width="60%"><input type="text" name="sphone" size="20" value="<? if($error){ print $_POST[sphone]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Fax Number</td><td align="left" width="60%"><input type="text" name="sfax" size="20" value="<? if($error){ print $_POST['sfax']; } ?>"></td></tr>
<tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Type of Shipping</td></tr>
<tr><td colspan="2" align="center"><select name="service">
<?
	foreach($ship_type as $key => $val)
		print "<option value=\"$key\">$val</option>";
?>
</select></td></tr>
<tr><td class="bar" align="center" colspan="2"><input type="submit" value="Calculate Shipping"></td></tr>
</table></form>
<?
	}elseif(isset($_POST['checkout']) || ((isset($_POST[newaccount]) || isset($_POST[login])) && $error)){
		if(isset($_POST['cart']))
			$_SESSION['cart'] = urldecode($_POST['cart']);
		if(isset($_POST['coupon']))
			$_SESSION['coupon'] = $_POST['coupon'];
		if(isset($_POST['fax_special']))
			$_SESSION['special_instructions'] = trim(strip_tags($_POST['special_instructions'])."\n\nWill Fax More Info");
		elseif(isset($_POST['special_instructions']))
			$_SESSION['special_instructions'] = trim(strip_tags($_POST['special_instructions']));
		
		if(!sizeof($_SESSION[cart])){
			print "<div align=\"center\">Your shopping cart is empty.  You must add something to your shopping cart before you can check out.</div>";
		}else{
		//print $_SESSION[cart];
?>
<form method="post" action="<?=$PHP_SELF?>"><table width="95%" border="0" cellspacing="0" cellpadding="4" align="center"><tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Log Into an Existing Account</td></tr>
<tr><td colspan="2" align="center">If you already have an account with <?=SITE_NAME?> or any Data Business Systems web site, you can login here with your e-mail address and password. <a href="/account/remind.html">Lost your password?</a></td></tr>
<? if(isset($_POST[login]) && $error){?>
	<tr><td colspan="2" align="center" style="font-weight:bold;color:red;"><?=$error?></td></tr>
<?}?>
<tr><td align="right" width="40%" style="font-weight:bold;">E-mail</td><td align="left" width="60%"><input type="text" name="email" size="35" value="<? if(isset($_SESSION['icustomer']['email'])) print  $_SESSION['icustomer']['email']; ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;" valign="top">Password</td><td align="left" width="60%"><input type="password" name="password" size="35" value=""><br><i>Passwords are case senstiive.</i></td></tr>
<tr><td class="bar" align="center" colspan="2"><input type="hidden" name="login" value="1"><input type="submit" value="Log In"></td></tr>
</table></form>
<br>
<form method="post" action="<?=$PHP_SELF?>"><table width="95%" border="0" cellspacing="0" cellpadding="4" align="center"><tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Create a New Account</td></tr>
<tr><td colspan="2" align="center">If you do not have an account with <?=SITE_NAME?>, you can create one below.</td></tr>
<? if(isset($_POST[newaccount]) && $error){?>
	<tr><td colspan="2" align="center" style="font-weight:bold;color:red;"><?=$error?></td></tr>
<?}?>
<tr><td align="right" width="40%" style="font-weight:bold;">E-mail</td><td align="left" width="60%"><input type="text" name="email" size="35" value="<? if(isset($_POST[newaccount]) && $error){ print $_POST[email]; }elseif(isset($_SESSION[icustomer][email])){ print  $_SESSION[icustomer][email]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Password</td><td align="left" width="60%"><input type="password" name="password" size="25" value="<? if(isset($_POST[newaccount]) && $error){ print $_POST[password]; } ?>"><br />Must be between 4 and 16 characters <b>Passwords are case senstiive.</b></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Retype Password</td><td align="left" width="60%"><input type="password" name="cpassword" size="25" value="<? if(isset($_POST[newaccount]) && $error){ print $_POST[cpassword]; } ?>"></td></tr>
<tr><td align="center" colspan="2" class="bar"><input type="hidden" name="newaccount" value="1"><? if(isset($_POST[po])) print "<input type=\"hidden\" name=\"po\" value=\"1\">"; ?><input type="submit" value="Create Account"></td></tr>
</table></form>
<?	
		}
	}
	if((!$error && isset($_POST['address1'])) || (isset($_POST['process']) && $error) || (isset($_POST['shipping']))){
		if(isset($_POST['service']))
			$_SESSION["service"] = $_POST['service'];
		elseif(strlen($_SESSION["service"]) != 3)
			$_SESSION["service"] = "GND";
		if($error)
			print "<div align=\"center\" style=\"font-weight:bold;color:red;\">$error</div><br>";
		if(!$fatal){
		require("../../inc/ups_lib.inc");
		if(isset($_POST['shipping']))
			$_SESSION['service'] = $_POST['service'];
		if(isset($_SESSION['coupon']) && $_SESSION['coupon'] != ""){
			$cdb = new dbi();
			$cdb->query("select * from coupon where code = '$_SESSION[coupon]'");
			if(!$cdb->numrows()){
				unset($cdb);
				unset($_SESSION['coupon']);
			}
		}
		$sub_total = $price = $ship_total = $tax = 0;
		$ship = array();
		$discount = array();
		if($_SESSION['rebill']){
			$results = mysql_query("select state from order_address where id = '".$_SESSION['rebill']."'", $dbh);
			$bill_state = mysql_result($results, 0, "state");
		}else{
			$bill_state = $_SESSION['state'];
		}
		if($_SESSION['reship']){
			$results = mysql_query("select zip_code from order_address where id = '".$_SESSION['reship']."'", $dbh);
			$ship_zip_code = mysql_result($results, 0, "zip_code");
		}else{
			$ship_zip_code = $_SESSION['szip_code'];
		}
		function discount($price,$discount,$type){
			if($type == "dollar")
				return round($discount,2);
			else
				return round($price*$discount/100,2);
		}
		print "<table align=\"center\" cellpadding=\"4\" cellspacing=\"0\" width=\"90%\">";
		print "<tr class=\"bar\"><td align=\"left\"><b>Product</b></td><td width=\"80\" align=\"right\"><b>Quantity</b></td><td align=\"right\" width=\"80\"><b>Price</b></td></tr>";
		$cart_data = unserialize($_SESSION['cart']);
		if(is_array($cart_data) && sizeof($cart_data)){
		foreach($cart_data as $key => $val){
			list($product,$quantity,$option) = split(",",$val);
			$db->query("select * from product left join product_category on product_category.id = product.category left join product_quantity on product_quantity.product = product.id left join product_option on product_option.product = product.id and product_option.id = '$option' where product_quantity.product = '$product' and product_quantity.quantity = '$quantity'");
			if($db->numrows()){
				$price = ($db->result("product_quantity.quantity")/$db->result("unit_quantity"))*$db->result("unit_price");
				if(isset($cdb)){
					if($cdb->result("type") == "all"){
						$discount[$key] = discount($price,$cdb->result("discount"),$cdb->result("discount_type"));
					}elseif(($cdb->result("type") == "product" && $db->result("product.id") == $cdb->result("type_id")) || ($cdb->result("type") == "category" && $db->result("product.category") == $cdb->result("type_id"))){
						$discount[$key] = discount($price,$cdb->result("discount"),$cdb->result("discount_type"));
					}elseif(($cdb->result("type") == "aproduct" && $db->result("product.id") != $cdb->result("type_id")) || ($cdb->result("type") == "acategory" && $db->result("product.category") != $cdb->result("type_id"))){
						$discount[$key] = discount($price,$cdb->result("discount"),$cdb->result("discount_type"));
					}
				}
				if($price < $discount[$key])
					$price = 0.0;
				else
					$price = round($price,2) - $discount[$key];
				
				$sub_total += $price;
				print "<tr><td>".$db->result("product_category.name")."<br>".$db->result("product.description");
				if($db->result("product_option.id") != "")
					print "<br>".$db->result("product_option.description");
				print "</td><td align=\"right\" width=\"80\">$quantity</td><td align=\"right\" width=\"80\" nowrap=\"nowarp\">$".number_format($price,2);
				if($discount[$key] > 0)
					print "<br>Save: $".number_format($discount[$key],2);
				print "</td></tr>";

				if($db->result("ship_override") > 0){
					$ship[$key] = "0,".$db->result("ship_override");
					$ship_total += $db->result("ship_override");
				}elseif($db->result("unit_weight") == 0){
					$ship[$key] = "0,0";
				}else{
					$weight = ($db->result("product_quantity.quantity")/$db->result("unit_quantity"))*$db->result("unit_weight");
					$results = mysql_query("select warehouse.id, warehouse.zip from product_warehouse inner join warehouse on warehouse.id = product_warehouse.warehouse where product_warehouse.product = '".$db->result("product.id")."'", $dbh);
					$ship_cost=0;
					if(mysql_num_rows($results)){
						$i = 0;
						while($row = mysql_fetch_assoc($results)){
							$ups_quote = 1;
							if($weight >= 150)
								$ups_quote = trim(getUPSQuote($_SESSION['service'],150, $row['zip'],'US',substr($ship_zip_code,0,5),'US'));
							if(is_numeric($ups_quote)){
								$ups_quote = floor($weight/150) * $ups_quote;
								$ups_quote += trim(getUPSQuote($_SESSION['service'], ($weight-(floor($weight/150) * 150)), $row['zip'],'US',substr($ship_zip_code,0,5),'US'));
							}
							if(is_numeric($ups_quote) && ($i == 0 || $ups_quote < $ship_cost)){
								$ship_cost = round($ups_quote,2);
								$ship_warehouse = $row['id'];
							}
							$i++;
						}
						if($ship_cost != 0){
							$ship_total += $ship_cost;
							$ship[$key] = $ship_warehouse.",".$ship_cost;
						}else{
							$calc_error = "We apologize for the inconvenience. UPS is experiencing technical difficulties. <br><h3> CLICK YOUR BACK BUTTON AND CACULATE SHIPPING AGAIN</h3><br>If after multiple tries UPS continues to experience difficulties, call <br>Data Business Systems Directly at 1-800-778-6247";
							break;
						}
					}
				}
			}
		}
		}else{
			print "<div align=\"center\" style=\"font-weight:bold;color:red;\">Invalid Items in Cart.  Try clearing your cookies before proceeding.</div>";
		}
		$_SESSION['sub_total'] = $sub_total;
		print "<tr class=\"bar\"><td>&nbsp;</td><td style=\"font-weight:bold;\" align=\"right\">Sub Total:</td><td align=\"right\">$".number_format($_SESSION[sub_total],2)."</td></tr>";
		if(is_numeric($_SESSION['account_taxrate'])){
			if($_SESSION['account_taxrate']){
				$_SESSION['tax'] = round(($_SESSION['account_taxrate']/100) * ($sub_total + $ship_total),2);
			}else{
				$_SESSION['tax'] = 0;
			}
		}else{
			$results = mysql_query("select * from state_tax where state = '$bill_state'", $dbh);
			if(mysql_num_rows($results)){
				if(mysql_result($results, 0, "tax") >= 0)
					$_SESSION['tax'] = round((mysql_result($results, 0, "tax")/100) * ($sub_total + $ship_total),2);
				elseif($_SESSION['account_po'] == 'y')
					$_SESSION['tax'] = 0;
				else
					$calc_error = "You are located in a State that has certain Tax Requirements.<br>Please contact Data Business Systems at 1-800-778-6247 to complete your order.<br>We apologize for any inconvenience.";
			}else{
				$_SESSION['tax'] = 0;
			}
		}
		if($calc_error){
			print "<tr><td colspan=\"3\" align=\"center\" style=\"font-weight:bold;color:blue;\">$calc_error</td></tr>";
		}else{
			print "<tr><td rowspan=\"3\">";
	if(!isset($order_id)){
?><form method="post" action="<?=$PHP_SELF?>"><select name="service">
<?
	foreach($ship_type as $key => $val){
		print "<option value=\"$key\"";
		if($_SESSION[service] == $key)
			print " selected=\"selected\"";
		print ">$val</option>";
	}
?>
</select><input type="hidden" name="shipping" value="1"><input type="submit" value="Change Shipping"></form>
<?
	}
	$_SESSION[discount] = serialize($discount);
	$_SESSION[ship] = serialize($ship);
	$_SESSION[ship_total] = $ship_total;
	$_SESSION[total] = $_SESSION[sub_total]+$_SESSION[ship_total]+$_SESSION[tax];
			print "</td><td style=\"font-weight:bold;\" align=\"right\">Shipping ".$ship_type["$_SESSION[service]"].":</td><td  valign=\"top\" align=\"right\">$".number_format($_SESSION[ship_total],2)."</td></tr>";
			print "<tr><td style=\"font-weight:bold;\" align=\"right\">Tax:</td><td align=\"right\">$".number_format($_SESSION[tax],2)."</td></tr>";
			print "<tr><td style=\"font-weight:bold;\" align=\"right\">Total:</td><td align=\"right\">$".number_format($_SESSION[total],2)."</td></tr>";
		}
		print "</table>";
		if(!$calc_error){
?>
<br>

<table align="center" cellpadding="4" cellspacing="0" border="0">
<tr class="bar"><td align="left"><b>Billing Information</b></td><td align="left"><b>Shipping Information</b></td></tr>
<tr><td valign="top">
<?
$full_bill_name = "";
function print_address($id, $type){
	global $full_bill_name, $dbh;
	$results = mysql_query("select * from order_address where id = '$id'", $dbh);
	if(mysql_num_rows($results)){
		$row = mysql_fetch_assoc($results);
		echo $row['name'],'<br>';
		echo $row['company'],'<br>';
		echo $row['address1'],' ',$row['address2'],'<br>';
		echo $row['city'],', ',$row['state'],' ',$row['zip_code'],'<br>';
		echo 'Phone: ',$row['phone'],'<br>';
		if($row['fax'])
			echo 'Fax: ',$row['fax'],'<br>';
		if($type == 'bill')
			$_SESSION['state'] = $row['state'];		
	}
	return $row['name'];
}
if($_SESSION[rebill] > 0){
	$full_bill_name = print_address($_SESSION[rebill], 'bill');
}else{
	$full_bill_name = $_SESSION[name];
?>
<?=$_SESSION[name]?><br>
<? if($_SESSION['company'] != "") print $_SESSION['company']."<br>"; ?>
<?=$_SESSION[address1]." ".$_SESSION[address2]?><br>
<?=$_SESSION['city'].", ".$_SESSION['state']." ".$_SESSION['zip_code']?><br>
Phone: <?=$_SESSION['phone']?><br>
Fax: <?=$_SESSION['fax']?>
<? } ?>
</td><td valign="top">
<?
if($_SESSION[reship] > 0){
	print_address($_SESSION[reship], 'ship');
}else{
?>
<?=$_SESSION[sname]?><br>
<? if($_SESSION['scompany'] != "") print $_SESSION['scompany']."<br>"; ?>
<?=$_SESSION[saddress1]." ".$_SESSION[saddress2]?><br>
<?=$_SESSION[scity].", ".$_SESSION[sstate]." ".$_SESSION[szip_code]?><br>
Phone: <?=$_SESSION[sphone]?><br>
Fax: <?=$_SESSION['sfax']?>
<? } ?>
</td></tr>
</table>
<br><div align="center" style="font-weight:bold;">Please select a method of payment below.</div><br>
<table align="center"><tr>
<? if((isset($payment_method) && $payment_method == 'po') || (!isset($payment_method) && $_SESSION['account_po'] == 'y')){ ?>
<td valign="top">
<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<?
	if(isset($order_id)){
		print "<input type=\"hidden\" name=\"order_id\" value=\"$order_id\">";
	}
?>
<table align="center" cellpadding="4" cellspacing="0" border="0">
<tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Purchase Order</td></tr>
<tr><td align="right" nowrap="nowrap"><b>PO Number</b></td><td align="left"><input type="text" name="po_number" size="25" value=""></td></tr>
<tr><td colspan="2" align="center" style="font-style:italic;">A hard copy of original Purchase Order needs to be faxed to (973) 252-1885 to complete the order process.</td></tr>
<tr><td class="bar" align="center" colspan="2"><input type="hidden" name="process" value="1"><input type="submit" value="Process Order"><br><span style="font-weight:bold;">Press Only ONCE!</span></td></tr>
</table>
</form>
</td>
<? } ?>
<? 
$results = mysql_query("select * from state_tax where state = '".$_SESSION['state']."' and tax < 0", $dbh);
if(((isset($payment_method) && $payment_method != 'po') || !isset($payment_method)) && (!mysql_num_rows($results) || is_numeric($_SESSION['account_taxrate']))){ ?>
<td valign="top">
<form method="post" action="<?=$PHP_SELF?>">
<?
	if(!isset($_POST[first_name])){
		$first_name = preg_replace("/^([^ ]+) .*$/","\\1",$full_bill_name);
		$last_name = preg_replace("/^[^ ]+ (.*)$/","\\1",$full_bill_name);
	}
	if(isset($order_id)){
		print "<input type=\"hidden\" name=\"order_id\" value=\"$order_id\">";
	}
?>
<script language="javascript">
function open_cc() {
	window.open("https://www.databusinesssystems.com/cc_security.html", "openpopup_cc", "resizable=no, toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1,copyhistory=0, width=500,height=360");
}
</script>
<table align="center" cellpadding="4" cellspacing="0" border="0">
<tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Credit Card Information</td></tr>
<tr><td align="right" style="font-weight:bold;">First Name</td><td align="left"><input type="text" name="first_name" value="<? if(isset($_POST[first_name])){ print $_POST[first_name]; }else{ print $first_name; } ?>"></td></tr>
<tr><td align="right" style="font-weight:bold;">Last Name</td><td align="left"><input type="text" name="last_name" value="<? if(isset($_POST[last_name])){ print $_POST[last_name]; }else{ print $last_name; } ?>"></td></tr>
<tr><td align="right" style="font-weight:bold;">Card Type</td><td align="left"><select name="type"><option value="American Express">American Express</option><option value="MasterCard">MasterCard</option><option value="Visa">Visa</option></select></td></tr>
<tr><td align="right" style="font-weight:bold;">Card Number</td><td align="left"><input type="text" name="card_number" size="30" value=""></td></tr>
<tr><td align="right" style="font-weight:bold;">CVV2/CVC2/CID</td><td align="left"><input type="text" name="ccid" size="5" value=""> <a href="javascript:open_cc()">What is this?</a></td></tr>
<tr><td align="right" style="font-weight:bold;">Expiration Date</td><td align="left"><select name="month"><? for($i=1;$i<=12;$i++) print "<option value=\"".sprintf("%02d",$i)."\">".sprintf("%02d",$i)."</option>"; ?></select><select name="year"><? for($i=0;$i<=10;$i++) print "<option value=\"".(date("Y",time())+$i)."\">".(date("Y",time())+$i)."</option>"; ?></select></td></tr>
<tr><td class="bar" align="center" colspan="2"><input type="hidden" name="process" value="1"><input type="submit" value="Process Order"><br><span style="font-weight:bold;">Press Only ONCE!</span></td></tr>
</table>
</form>
</td>
<?
}
?>
</tr></table>
<?
		}
		}
	}
	if(isset($_POST['process']) && !$error){
	?>
	<p>Thank you for your order. Your order number is: <b><?=$order_id?></b></p>
	
	<p>If you need to fax additional information, our fax number is (973) 252-1885.</p>
	
	<p>You should receive a confirmation e-mail soon.</p>
	<? 
	if(isset($form_print) && sizeof($form_print)){
		print "<p><b>Please fill out, print, and fax the following forms to (973) 252-1885.</b></p>";
		print "<ul>";
		foreach($form_print as $x)
			print "<li>$x</li>";
		print "</ul>";
	} 
	?>
	<br>
<?
	}
?>