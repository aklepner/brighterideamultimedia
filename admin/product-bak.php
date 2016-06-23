<?
	if(!isset($_GET['category']) && !isset($_GET['user'])){
		header("Location: index.php");
		exit;
	}
	if(isset($_GET['setsite'])){
		setcookie("site_admin",$_GET[setsite],time()+(3600*24*30),"/");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - administration</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script language="JavaScript" type="text/javascript">
	function more_sku(){
		 //Create Clone of Record and Append to Table
		 table = document.getElementById("product_sku_box");
		 tr = table.tBodies[0].rows[1];
		 clone = tr.cloneNode(true);
		 document.getElementById("product_sku_tbody").appendChild(clone);
	}
 //Set All New Fields To Blank
 //var names = new Array("cusprodqty[]", "cusprodinum[]", "cusproddesc[]", "cusprodprice[]", "cusprodrep[]", "cusprodcomm[]");
 
 //for (var n=0; n < names.length; n++){
   //  x = document.getElementsByName(names[n]);
   //  len = x.length-1;
     //x[len].value='';
 // }
	</script>
</head>

<body>
<?
include("header.inc");
define("BASE_IMAGE_DIR", $_SERVER['DOCUMENT_ROOT'].DOCUMENT_BASE."/images/product/");
define("BASE_PDF_DIR", $_SERVER['DOCUMENT_ROOT'].DOCUMENT_BASE."/pdf/product/");
// Following variable is deprecated, and the above constants should be used instead.
$base_dir = $_SERVER[DOCUMENT_ROOT].DOCUMENT_BASE."/images/product/";
if(isset($_GET['add']) || isset($_GET['mod'])){
	if(isset($_POST['description'])){
		// Process Form
		if($_POST['category'] == ""){
			$error = "Must specify a category!";
		}elseif($_POST[description] == ""){
			$error = "Must specify a Description!";
		}elseif($_POST[unit_quantity] == ""){
			$error = "Must specify a Unit Quantity!";
		}elseif($_POST[unit_weight] == ""){
			$error = "Must specify a Unit Weight!";
		}elseif($_POST[unit_weight] != 0 && $_POST[warehouse][0] == ""){
			$error = "Must select at least 1 Warehouse!";
		}elseif(strtotime($_POST[expire]) == -1){
			$error = "Invalid Expiration Date. Example: YYYY-MM-DD or MM/DD/YYYY";
		}
		// Check for JPEG
		if(!$error && $_FILES[image][name] != ""){
			if(!preg_match("/jpeg/",$_FILES['image']['type'])){
				$error = "Wrong File Type! Image must be JPEG!";
			}
		}
		// Check for PDF
		if(!$error && $_FILES[pdf][name] != ""){
			if(!preg_match("/pdf/",$_FILES['pdf']['type'])){
				$error = "Wrong File Type! File must be a PDF!";
			}
		}
		
		if(!$error){
			for($i=1;$i<=10;$i++){
				if($_POST["quantity_$i"] != "" && $_POST["price_$i"]){
					$i=12;
				}
			}
			if($i==12)
				$error = "Must specify at least one Quantity/Price combination!";
		}
		if(!$error){
			if($_POST['expire'] != "")
				$expire = "'".date("Y-m-d",strtotime($_POST['expire']))."'";
			else
				$expire = "NULL";
			if(isset($_GET['user']))
				$set_user = $_GET['user'];
			else
				$set_user = 0;
			if(isset($_POST['dropdown']))
				$dd = 'y';
			else
				$dd = 'n';
			if(isset($_GET['add'])){
				 mysql_query("insert into product (category, user, sku, dropdown, sort, description, comment, unit_quantity, unit_weight, status, expire) values ('".$_POST['category']."', '$set_user', '$_POST[sku]', '$dd', '".$_POST['sort']."', '".mysql_real_escape_string($_POST['description'])."', '".mysql_real_escape_string($_POST['comment'])."', '$_POST[unit_quantity]', '$_POST[unit_weight]', '$_POST[status]', $expire)", $dbh)
				 	or die("Error: ".mysql_error());
				 $proid = mysql_insert_id($dbh);
			}else{
				mysql_query("update product set category = '".$_POST['category']."', sku = '".$_POST['sku']."', dropdown = '$dd', sort = '".$_POST['sort']."', description = '".mysql_real_escape_string($_POST['description'])."', comment = '".mysql_real_escape_string($_POST['comment'])."', unit_quantity = '$_POST[unit_quantity]', unit_weight = '$_POST[unit_weight]', status = '$_POST[status]', expire = $expire where id = '".$_GET['mod']."'", $dbh)
					or die("Error: ".mysql_error());
				$proid = $_GET['mod'];
			}
			if($proid < 1){
				$error = "Error updating product!";
			}else{
				mysql_query("delete from product_warehouse where product = '$proid'", $dbh);
				if($_POST['warehouse'][0] != ""){
					foreach($_POST['warehouse'] as $id)
						mysql_query("replace into product_warehouse (product, warehouse) values ('$proid','$id')", $dbh);
				}
				mysql_query("delete from product_quantity where product = '$proid'", $dbh);
				$i=1;
				while(isset($_POST["quantity_$i"])){
					if($_POST["ship_override_$i"] != "")
						$override = "'".preg_replace("/[^0-9\.]/","",$_POST["ship_override_$i"])."'";
					else
						$override = "NULL";
					if($_POST["quantity_$i"] != "" && $_POST["price_$i"] != "")
						mysql_query("insert into product_quantity (product, quantity, unit_cost, unit_price, ship_override) values ('$proid', '".$_POST["quantity_$i"]."', ".($_POST["cost_$i"]?"'".preg_replace("/[^0-9\.]/","",$_POST["cost_$i"])."'":"NULL").", '".preg_replace("/[^0-9\.]/","",$_POST["price_$i"])."', ".$override.")", $dbh);
					$i++;
				}

				$i=1;
				while(isset($_POST["sku_$i"])){
					if(isset($_POST["option_$i"]))
						if($_POST["sku_$i"] == "" && $_POST["description_$i"] == "")
							mysql_query("delete from product_option where id = '".$_POST["option_$i"]."'", $dbh);
						else
							mysql_query("update product_option set sku = '".$_POST["sku_$i"]."', description = '".addslashes($_POST["description_$i"])."', sort = '".$_POST["sort_$i"]."' where id = '".$_POST["option_$i"]."'", $dbh);
					elseif($_POST["sku_$i"] != "" && $_POST["description_$i"] != "")
						mysql_query("insert into product_option (id, product, sku, sort, description) values (NULL, '$proid', '".$_POST["sku_$i"]."', '".$_POST["sort_$i"]."', '".addslashes($_POST["description_$i"])."')", $dbh);
					$i++;
				}
				if(isset($_POST['delete_image']))
					unlink(BASE_IMAGE_DIR.$proid.".jpg");
				if($_FILES[image][name] != ""){
					if(move_uploaded_file($_FILES['image']['tmp_name'], BASE_IMAGE_DIR.$proid.".jpg")){
						chmod(BASE_IMAGE_DIR.$proid.".jpg", 0644);
					}
				}
				if(isset($_POST[delete_pdf]))
					unlink(BASE_PDF_DIR.$proid.".pdf");
				if($_FILES[pdf][name] != ""){
					if(move_uploaded_file($_FILES['pdf']['tmp_name'], BASE_PDF_DIR.$proid.".pdf")){
						chmod(BASE_PDF_DIR.$proid.".pdf", 0644);
					}
				}
				print "<div align=\"center\" class=\"success\">Product updated successfully!</div><br>";
				$success=1;
			}
		}
	}
	if(isset($_GET['mod'])){
		$moddb = new dbi();
		$moddb->query("select * from product where id = '".$_GET['mod']."'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Product with ID of '".$_GET['mod']."'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>";
	$tempdb = new dbi();
?>
	<form action="product.php?<?=$_SERVER['QUERY_STRING']?>" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Product</td><td align="right"><a href="product.php?<?=(isset($_GET[category])?"category=".$_GET['category']:"user=".$_GET['user'])?>" style="color:white;">View All</a></td></tr>
		<? if(isset($_GET[user])){ ?>
		<tr><td class="field_title">User</td><td><? 
			$tempdb->query("select email from account where account.id = '".$_GET['user']."'");
			print $tempdb->result("email")." (".$_GET['user'].")";
		?></td></tr>
		<? } ?>
		<tr><td class="field_title">Category</td><td><select name="category"><?
			$tempdb->query("select * from product_category where site = '".SITE."' order by name asc");
			while($tempdb->loop()){
				print "<option value=\"".$tempdb->result("id")."\"";
				if((isset($moddb) && $tempdb->result("id") == $moddb->result("category")) || (isset($error) && $tempdb->result("id") == $_POST[category]) || (isset($add) && $_GET[category] == $tempdb->result("id")))
					print " selected=\"selected\"";
				print ">".$tempdb->result("name")."</option>";
			}
		?></select></td></tr>
		<tr><td class="field_title">Drop Down Display</td><td><input type="checkbox" name="dropdown" value="1"<? if((isset($error) && isset($_POST['dropdown'])) || (!isset($error) && isset($moddb) && $moddb->result("dropdown") == 'y')){ print " checked=\"checked\""; } ?>></td></tr>
		<tr><td class="field_title">Display Order</td><td><input type="text" name="sort" size="5" value="<? if(isset($error)){ print $_POST['sort']; }elseif(isset($moddb)){ print $moddb->result("sort"); }else{ print "0"; } ?>"></td></tr>
		<tr><td class="field_title">SKU</td><td><input type="text" name="sku" size="25" value="<? if(isset($error)){ print $_POST[sku]; }elseif(isset($moddb)){ print $moddb->result("sku"); }?>"></td></tr>
		<tr><td class="field_title">Description</td><td><textarea cols="50" rows="4" name="description"><? if(isset($error)){ print $_POST['description']; }elseif(isset($moddb)){ print $moddb->result("description"); }?></textarea></td></tr>
		<tr><td class="field_title">Comment</td><td><textarea cols="50" rows="4" name="comment"><? if(isset($error)){ print $_POST['comment']; }elseif(isset($moddb)){ print $moddb->result("comment"); }?></textarea></td></tr>
		<tr><td class="field_title">Image</td><td><input type="file" name="image"><br>
		<? if(isset($moddb) && is_file(BASE_IMAGE_DIR.$moddb->result("id").".jpg")){ ?>
		<input type="checkbox" name="delete_image"> Delete Current Image? Image Size: <?=round(filesize(BASE_IMAGE_DIR.$moddb->result("id").".jpg")/1000,1)?> K
		<? } ?>
		</td></tr>
		<tr><td class="field_title">PDF</td><td><input type="file" name="pdf"><br>
		<? if(isset($moddb) && is_file(BASE_PDF_DIR.$moddb->result("id").".pdf")){ ?>
		<input type="checkbox" name="delete_pdf"> Delete Current PDF? PDF Size: <?=round(filesize(BASE_PDF_DIR.$moddb->result("id").".pdf")/1000,1)?> K
		<? } ?>
		</td></tr>
	<tr><td class="field_title">Unit Quantity</td><td><input type="text" name="unit_quantity" size="25" value="<?if(isset($error)){ print $_POST[unit_quantity]; }elseif(isset($moddb)){ print $moddb->result("unit_quantity"); }?>"></td></tr>
	<tr><td class="field_title">Unit Weight (lbs)</td><td><input type="text" name="unit_weight" size="25" value="<?if(isset($error)){ print $_POST[unit_weight]; }elseif(isset($moddb)){ print $moddb->result("unit_weight"); }?>"></td></tr>
	<tr><td class="field_title">Warehouses</td><td><select name="warehouse[]" multiple="multiple" size="8"><?
		if(isset($moddb))
			$tempdb->query("select * from warehouse left join product_warehouse on product_warehouse.warehouse = warehouse.id and product_warehouse.product = '".$moddb->result("id")."' order by name, city asc");
		else
			$tempdb->query("select * from warehouse order by name, city asc");
		while($tempdb->loop()){
			print "<option value=\"".$tempdb->result("id")."\"";
			if(!isset($error) && isset($moddb) && $tempdb->result("product") == $moddb->result("id"))
				print " selected=\"selected\"";
			elseif(isset($error))
				if(isset($_POST['warehouse'][0])){
					foreach($_POST[warehouse] as $x)
						if($x == $tempdb->result("id"))
							print " selected=\"selected\"";
				}
			print ">".($tempdb->result("name")?$tempdb->result("name"):"N/A")." - ".$tempdb->result("city").", ".$tempdb->result("state")."</option>";
		}
	?></select></td></tr>
	<tr><td class="field_title">Status</td><td><select name="status"><?
		$s = array("in" => "In Stock", "out" => "Out of Sotck", "discontinued" => "Discontinued", "disable" => "Disable");
		foreach($s as $key => $val){
			print "<option value=\"$key\"";
			if((isset($error) && $_POST['status'] == $key) || (isset($moddb) && $moddb->result("status") == $key))
				print " selected=\"selected\"";
			print ">$val</option>";
		}
	?></select></td></tr>
	<tr><td class="field_title">Expire</td><td><input type="text" name="expire" size="15" value="<? if(isset($error)){ print $_POST['expires']; }elseif(isset($moddb) && $moddb->result("expire") != NULL){ print date("m/d/Y",strtotime($moddb->result("expire"))); }?>"></td></tr>
	<tr><td colspan="2">
	<table cellpadding="1" cellspacing="2" border="0" align="center">
	<tr><td valign="top">
	<table cellpadding="2" cellspacing="0" style="border:1px solid #990000;">
	<tr class="bar"><td>Quantity</td><td>Unit List</td><td>Unit Cost</td><td nowrap="nowrap">Shipping</td></tr>
	<? 
		$start = 1;
		if(isset($moddb) && !isset($error)){
			$results = mysql_query("select * from product_quantity where product = '".$moddb->result("id")."' order by quantity");
			while($row = mysql_fetch_assoc($results)){?>
	<tr<? if($start%2) print " class=\"shade\""; ?>><td><input type="text" size="8" name="quantity_<?=$start?>" value="<?=$row['quantity']?>" style="font-size:10px;"></td><td><input type="text" size="8" name="price_<?=$start?>" value="<?=number_format($row['unit_price'],2)?>" style="font-size:10px;"></td><td><input type="text" size="8" name="cost_<?=$start?>" value="<? if($row['unit_cost']) print number_format($row['unit_cost'],2) ?>" style="font-size:10px;"></td><td><input type="text" size="8" name="ship_override_<?=$start?>" value="<?=$row['ship_override']?>" style="font-size:10px;"></td></tr>
			<?
				$start++;
			}
		}
		for($i=$start;$i<=20;$i++){ ?>
	<tr<? if($i%2) print " class=\"shade\""; ?>><td><input type="text" size="8" name="quantity_<?=$i?>" value="<? if(isset($error)){ print $_POST["quantity_$i"]; }?>" style="font-size:10px;"></td><td><input type="text" size="8" name="price_<?=$i?>" value="<? if(isset($error) && $_POST["price_$i"] != ""){ print number_format($_POST["price_$i"],2); }?>" style="font-size:10px;"></td><td><input type="text" size="8" name="cost_<?=$i?>" value="<? if(isset($error) && $_POST["price_$i"] != ""){ print number_format($_POST["cost_$i"],2); }?>" style="font-size:10px;"></td><td><input type="text" size="8" name="ship_override_<?=$i?>" value="<? if(isset($error) && $_POST["ship_override_$i"] != ""){ print $_POST["ship_override_$i"]; }?>" style="font-size:10px;"></td></tr>
	<? } ?>	
	</table></td><td valign="top">
	<table cellpadding="2" cellspacing="0" id="product_sku_box">
	<tbody id="product_sku_tbody">
	<tr class="bar"><td>SKU</td><td>Description</td><td>Sort</td></tr>
	<? 
		$start = 1;
		if(isset($moddb) && !isset($error)){
			$tempdb->query("select product_option.*, if(sort=0,1,0) as zero from product_option where product = '".$moddb->result("id")."' order by zero asc, sort asc, sku");
			while($tempdb->loop()){?>
	<tr<? if($start%2) print " class=\"shade\""; ?>><td><input type="hidden" name="option_<?=$tempdb->currentrow()+1?>" value="<?=$tempdb->result("id")?>"><input type="text" size="12" name="sku_<?=$tempdb->currentrow()+1?>" value="<?=$tempdb->result("sku")?>"></td><td><input type="text" size="35" name="description_<?=$tempdb->currentrow()+1?>" value="<?=$tempdb->result("description")?>"></td><td><input type="text" size="2" name="sort_<?=$tempdb->currentrow()+1?>" value="<?=$tempdb->result("sort")?>"></td></tr>
			<?
				$start++;
			}
		}
		for($i=$start;$i<=20;$i++){ ?>
	<tr<? if($i%2) print " class=\"shade\""; ?>><td><? if(isset($error) && isset($_POST["option_$i"])){ ?><input type="hidden" name="option_<?=$i?>" value="<?=$_POST["option_$i"]?>"><? } ?><input type="text" size="12" name="sku_<?=$i?>" value="<? if(isset($error)){ print $_POST["sku_$i"]; }?>"></td><td><input type="text" size="35" name="description_<?=$i?>" value="<? if(isset($error)){ print $_POST["description_$i"]; }?>"></td><td><input type="text" size="2" name="sort_<?=$i?>" value="<? if(isset($error)){ print $_POST["sort_$i"]; }?>"></td></tr>
	<? } ?>	
	</tbody>
	</table>
	</td></tr></table>
	</td></tr>
	<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
	</table>
	</form>
<?
	}
}
if((!isset($_GET['add']) && !isset($_GET['mod'])) || isset($success)){
	if(isset($_GET['delete'])){
		if(isset($_GET['confirm']) && $_GET['confirm'] == 'y'){
			@unlink(BASE_IMAGE_DIR.$delete.".jpg");
			@unlink(BASE_PDF_DIR.$delete.".pdf");
			mysql_query("delete from product_quantity where product = '".$_GET['delete']."'", $dbh);
			mysql_query("delete from product_warehouse where product = '".$_GET['delete']."'", $dbh);
			mysql_query("delete from product_option where product = '".$_GET['delete']."'", $dbh);
			mysql_query("delete from product where id = '".$_GET['delete']."'", $dbh);
			print "<div align=\"center\" class=\"error\">Product deleted successfully!</div><br>";
		}else{
			print "<div align=\"center\" class=\"error\">Are you sure you want to delete this product? <a class=\"error\" href=\"product.php?".(isset($_GET['category'])?"category=$_GET[category]":"user=$_GET[user]")."&delete=".$_GET['delete']."&confirm=y\">Yes</a>  <a class=\"error\" href=\"product.php?".(isset($_GET[category])?"category=$_GET[category]":"user=$_GET[user]")."\">No</a></div><br>";
		}
	}
	
	$db = new dbi();
	if(isset($_GET['category'])){
		$results = mysql_query("select name from product_category where id = '".$_GET['category']."'", $dbh);
		$bar_title = "Products for '".mysql_result($results, 0, "name")."'";
		$db->query("select * from product where category = '$_GET[category]' and user = '0' order by sort, description asc");
		
	}else{
		$results = mysql_query("select email from account where id = '".$_GET['user']."'", $dbh);
		$bar_title = "Products for '".mysql_result($results, 0, "email")."'";
		$db->query("select product_category.site,product.* from product inner join product_category on product_category.id = product.category where user = '$_GET[user]' order by sort, description asc");
		
	}
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr class=\"bar\"><td>$bar_title</td><td align=\"right\"><a href=\"product.php?".(isset($_GET[category])?"category=".$_GET['category']:"user=".$_GET['user'])."&add=1\">Add a Product</a></td></tr>";
	if($db->numrows()){
		while($db->loop()){
			print "<tr><td align=\"left\"><a href=\"product.php?".(isset($_GET[category])?"category=$_GET[category]":"user=$_GET[user]")."&mod=".$db->result("id");
			if(isset($_GET[user]))
				print "&setsite=".$db->result("product_category.site");
			print "\">".substr($db->result("description"),0,60)."</td><td align=\"right\"><a href=\"product.php?".(isset($_GET[category])?"category=".$_GET['category']:"user=$_GET[user]")."&delete=".$db->result("id")."\">Delete?</a></td></tr>";
		}
	}else{
		print "<tr><td colspan=\"2\" align=\"center\">No Products for that Category/User!</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
