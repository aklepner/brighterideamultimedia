<h1>Shopping Cart</h1>
<?
	if(isset($_POST['continue']))
		$continue = $_POST['continue'];
	elseif(preg_match("/databusinesssystems\.com\/account/",$_SERVER['HTTP_REFERER']))
		$continue = $_SERVER['HTTP_REFERER'];
	elseif(isset($_POST['affiliate']))
		$continue = DOCUMENT_BASE."/affiliate/".$_POST['affiliate'];
	if(isset($_POST['product']) && !isset($_POST['quantity']))
		print "<div align=\"center\" style=\"font-weight:bold;\">You must select a quantity! Please go <a href=\"javascript:history.back()\">back</a> and fix your selection.</div><br>";
	
	if(isset($cerror))
		print "<div class=\"error\" style=\"margin:10px auto;text-align:center;\"><b>$cerror</b></div>";

	if(sizeof($cart)){
		function discount($price,$discount,$type){
			if($type == "dollar")
				return ($discount);
			else
				return ($price*$discount/100);
		}
		$db = new dbi();
		$tempdb = new dbi();
		$total = $price = 0;
		$proarr = array(); // Used later for coupon check.
		print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\" width=\"90%\">";
		print "<tr class=\"bar\"><td style=\"font-weight:bold\">Product</td><td style=\"font-weight:bold\" width=\"80\" align=\"right\">Quantity</td><td align=\"right\" style=\"font-weight:bold\" width=\"80\">Price</td><td>&nbsp;</td></tr>";
		foreach($cart as $key => $val){
			$discount = 0.0;
			list($product,$quantity,$option) = split(",",$val);
			$db->query("select * from product left join product_category on product_category.id = product.category left join product_quantity on product_quantity.product = product.id left join product_option on product_option.product = product.id and product_option.id = '$option' where product_quantity.product = '$product' and product_quantity.quantity = '$quantity' and (expire IS NULL or expire >= NOW())");
			if($db->numrows()){
				$price = ($db->result("product_quantity.quantity")/$db->result("unit_quantity")) * $db->result("unit_price");
				if(isset($cdb)){
					if($cdb->result("type") == "all"){
						$discount = discount($price,$cdb->result("discount"),$cdb->result("discount_type"));
					}elseif(($cdb->result("type") == "product" && $db->result("product.id") == $cdb->result("type_id")) || ($cdb->result("type") == "category" && $db->result("product.category") == $cdb->result("type_id"))){
						$discount = discount($price,$cdb->result("discount"),$cdb->result("discount_type"));
					}elseif(($cdb->result("type") == "aproduct" && $db->result("product.id") != $cdb->result("type_id")) || ($cdb->result("type") == "acategory" && $db->result("product.category") != $cdb->result("type_id"))){
						$discount = discount($price,$cdb->result("discount"),$cdb->result("discount_type"));
					}
				}
				if($price < $discount)
					$price = 0.0;
				else
					$price = $price - $discount;
				$total += $price;
				print "<tr><td>".$db->result("product_category.name")."<br>".$db->result("product.description");
				if($db->result("product_option.id") != "")
					print "<br>".$db->result("product_option.description");
				print "</td><td align=\"right\" valign=\"top\">";
				$tempdb->query("select * from product_quantity where product = '$product' order by quantity");
				if($tempdb->numrows() == 1){
					print $quantity;
				}else{
					print "<form method=\"post\" action=\"$PHP_SELF\"><input type=\"hidden\" name=\"delete\" value=\"$key\"><input type=\"hidden\" name=\"product\" value=\"$product\"><input type=\"hidden\" name=\"option\" value=\"$option\">";
					if(isset($continue))
						print "<input type=\"hidden\" name=\"continue\" value=\"".$continue."\">";
					print "<select name=\"quantity\" onchange=\"this.form.submit()\">";
					while($tempdb->loop()){
						print "<option value=\"".$tempdb->result("quantity")."\"";
						if($quantity == $tempdb->result("quantity"))
							print " selected=\"selected\"";
						print ">".$tempdb->result("quantity")."</option>";
					}
					print "</select></form>";
				}
				print "</td><td valign=\"top\" align=\"right\">$".number_format($price,2)."<br>";
				if($discount > 0)
					print "Save: $".number_format($discount,2);
				print "</td><td align=\"center\" valign=\"top\"><form method=\"post\" action=\"$PHP_SELF\">";
				if(isset($continue))
					print "<input type=\"hidden\" name=\"continue\" value=\"".$continue."\">";
				print "<input type=\"hidden\" name=\"delete\" value=\"$key\"><input type=\"submit\" value=\"Remove\"></form></td></tr>";
				$last_category = $db->result("product_category.id");
			}else{
				unset($cart[$key]);
			}
		}
		print "<tr class=\"bar\"><td>&nbsp;</td><td align=\"right\"><b>Sub Total:</b></td><td align=\"right\"><b>$".number_format($total,2)."</b></td><td>&nbsp;</td></tr>";
		print "<tr><td colspan=\"4\" align=\"center\"><form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">Coupon Code: <input type=\"text\" name=\"coupon\" value=\"\"><input type=\"submit\" value=\"Enter Coupon\"></form></td></tr>";
		print "</table>";
		if(is_array($cart) && sizeof($cart)){
			$cdb = new dbi();
			$carr = array();
			foreach($cart as $val){
				list($product,$quantity,$option) = split(",",$val);
				$cdb->query("select product.category as cat from product where id = '$val'");
				$category = $cdb->result("cat");
				$cdb->query("select * from coupon where (type = 'aproduct' and type_id = '$product') or (type = 'acategory' and type_id = '$category')");
				while($cdb->loop()){
					$carr[$cdb->result("code")] = $cdb->result("description");
				}
			}
			if(sizeof($carr)){
?>
<table width="90%" cellspacing="0" cellpadding="2" align="center" style="border: 2 solid red;">
<tr><td colspan="2" style="color:blue;font-weight:bold;font-size:14px;">The following special offers are available with this purchase:</td></tr>
<tr><td nowrap="nowrap" style="color:blue;font-size:10px;">Coupon Code</td><td style="color:blue;;font-size:10px;">Description</td></tr>
<?
			foreach($carr as $key => $val)
				print "<tr><td valign=\"top\" style=\"font-weight:bold;\">$key</td><td valign=\"top\">$val</td></tr>";
?>
</table>
<?	
			}
		}
	}else{
		print "<div align=\"center\">You currently have no items in your cart.</div>";
	}
?>
<br>
<form method="post" action="<?=DOCUMENT_BASE?>/checkout/">
<table width="90%" align="center">
<tr><td colspan="2">
<b>Special Instructions for custom printed forms</b><br>
<div align="center">(Use the space below to provide us with any new or revised information)<br>
<textarea name="special_instructions" cols="60" rows="3"></textarea><br />
<input type="checkbox" name="fax_special" id="fax_special" /> <label for="fax_special"><b>I will fax information to you at (973) 252-1885.</b></label></div>
<br>
</td></tr>
<tr><td align="center" width="50%" valign="top">
<? if(isset($continue)){ ?>
<input type="button" value="Continue Shopping" onClick="location.href = '<?=$continue?>'">
<? }else{ ?>
<input type="button" value="Continue Shopping" onClick="alert('To make an additional purchase, select from the product menu on the left and add to the cart.')">
<? } ?></td><td width="50%" align="center" valign="top">
<? if(sizeof($cart)){ ?>
	<input type="hidden" name="cart" value="<?=urlencode(serialize($cart))?>">
	<input type="hidden" name="coupon" value="<?=$coupon?>">
	<input type="hidden" name="checkout" value="1">
	<? if(isset($_COOKIE['po']) || isset($_POST['po'])){ ?>
		<input type="hidden" name="po" value="1">
	<? } ?>
	<input type="submit" value="Checkout">
<? }?>
</td></tr>
</table>
</form>
<br>