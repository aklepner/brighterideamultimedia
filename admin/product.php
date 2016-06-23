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
	function more_rows(box){
		 //Create Clone of Record and Append to Table
		 var table = document.getElementById(box);
		 var tr = table.rows;
		 var clone = tr[1].cloneNode(true);
 		 var inputs = clone.getElementsByTagName('input');
		 for(var i = 0; i < inputs.length; i++)
		 	inputs[i].value = '';
		 table.getElementsByTagName('tbody')[0].appendChild(clone);
	}
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
		}elseif($_POST['description'] == ""){
			$error = "Must specify a Description!";
		}elseif($_POST['unit_quantity'] == ""){
			$error = "Must specify a Unit Quantity!";
		}elseif($_POST['unit_weight'] == ""){
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
			$qty_chk = false;
			foreach($_POST['p_quantity'] as $i => $qty){
				if($qty != "" && $_POST['p_list'][$i] != "")
					$qty_chk = true;
			}
			if(!$qty_chk)
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
				 mysql_query("insert into product (category, user, sku, dropdown, sort, description, comment, unit_quantity, unit_weight, status, expire) values ('".$_POST['category']."', '$set_user', '".$_POST['product_sku']."', '$dd', '".$_POST['sort_product']."', '".mysql_real_escape_string($_POST['product_description'])."', '".mysql_real_escape_string($_POST['comment'])."', '$_POST[unit_quantity]', '$_POST[unit_weight]', '$_POST[status]', $expire)", $dbh)
				 	or die("Error: ".mysql_error());
				 $proid = mysql_insert_id($dbh);
			}else{
				mysql_query("update product set category = '".$_POST['category']."', sku = '".$_POST['product_sku']."', dropdown = '$dd', sort = '".$_POST['sort_product']."', description = '".mysql_real_escape_string($_POST['product_description'])."', comment = '".mysql_real_escape_string($_POST['comment'])."', unit_quantity = '$_POST[unit_quantity]', unit_weight = '".$_POST['unit_weight']."', status = '".$_POST['status']."', expire = $expire where id = '".$_GET['mod']."'", $dbh)
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
				
				foreach($_POST['p_quantity'] as $i => $qty){
					if($qty != "" && $_POST['p_list'][$i] != ""){
						mysql_query("insert into product_quantity (product, quantity, unit_cost, unit_price, ship_override) values ('$proid', '".$qty."', ".($_POST['p_cost'][$i]?"'".preg_replace("/[^0-9\.]/","",$_POST['p_cost'][$i])."'":"NULL").", '".preg_replace("/[^0-9\.]/","",$_POST['p_list'][$i])."', ".($_POST["p_shipping"][$i]?"'".preg_replace("/[^0-9\.]/","",$_POST["p_shipping"][$i])."'":"NULL").")", $dbh);
					}
				}

				foreach($_POST['option'] as $i => $opt){
					if($opt)
						if($_POST['sku'][$i] == "" && $_POST['description'][$i] == "")
							mysql_query("delete from product_option where id = '".$opt."'", $dbh);
						else
							mysql_query("update product_option set sku = '".$_POST['sku'][$i]."', description = '".mysql_real_escape_string($_POST['description'][$i])."', sort = '".$_POST['sort'][$i]."' where id = '".$opt."'", $dbh);
					elseif($_POST['sku'][$i] != "" && $_POST['description'][$i] != "")
						mysql_query("insert into product_option (id, product, sku, sort, description) values (NULL, '$proid', '".$_POST['sku'][$i]."', '".$_POST['sort'][$i]."', '".mysql_real_escape_string($_POST['description'][$i])."')", $dbh);
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
	<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr class="bar"><td>Modify Product</td><td align="right"><a href="<?=$_SERVER['PHP_SELF']?>?<?=(isset($_GET[category])?"category=".$_GET['category']:"user=".$_GET['user'])?>" style="color:white;">View All</a></td></tr>
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
		<tr><td class="field_title">Display Order</td><td><input type="text" name="sort_product" size="5" value="<? if(isset($error)){ print $_POST['sort_product']; }elseif(isset($moddb)){ print $moddb->result("sort"); }else{ print "0"; } ?>"></td></tr>
		<tr><td class="field_title">SKU</td><td><input type="text" name="product_sku" size="25" value="<? if(isset($error)){ print $_POST['product_sku']; }elseif(isset($moddb)){ print $moddb->result("sku"); }?>"></td></tr>
		<tr><td class="field_title">Description</td><td><textarea cols="50" rows="4" name="product_description"><? if(isset($error)){ print $_POST['product_description']; }elseif(isset($moddb)){ print $moddb->result("description"); }?></textarea></td></tr>
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
	<table cellpadding="0" cellspacing="3" border="0" align="center">
	<tr><td valign="top">
	<table cellpadding="1" cellspacing="0" id="product_qty_box">
	<tbody>
	<tr class="bar"><td>Quantity</td><td>Unit List</td><td>Unit Cost</td><td nowrap="nowrap">Shipping</td></tr>
	<? 
		function print_product_qty_row($quantity = "", $list = "", $cost = "", $shipping = ""){ ?>
		<tr><td><input type="text" size="8" name="p_quantity[]" value="<?=$quantity?>"></td><td><input type="text" size="8" name="p_list[]" value="<? if($list) print number_format($list,2); ?>"></td><td><input type="text" size="8" name="p_cost[]" value="<? if($cost) print number_format($cost,2) ?>"></td><td><input type="text" size="8" name="p_shipping[]" value="<?=$shipping?>"></td></tr>
		<? }
		if(isset($error)){
			foreach($_POST['p_quantity'] as $x => $qty){ 
				print_product_qty_row($opt, $_POST['p_list'][$x], $_POST['p_cost'][$x], $_POST["p_shipping"][$x]);
			}
		}elseif(isset($moddb)){
			$results = mysql_query("select * from product_quantity where product = '".$moddb->result("id")."' order by quantity");
			$i = 0;
			if(mysql_num_rows($results)){
				while($row = mysql_fetch_assoc($results)){
					print_product_qty_row($row['quantity'], $row['unit_price'], $row['unit_cost'], $row["ship_override"]);
					$i++;
				}
			}
			for($i;$i<mysql_num_rows($results)+3;$i++) print_product_qty_row();
		}else{
			for($i=0;$i<5;$i++) print_product_qty_row();
		}
?>
	</tbody>
	</table>
	<div style="text-align:right;margin:4px;"><input type="button" onclick="more_rows('product_qty_box');" value="Add More"></div>
	</td><td valign="top">
	<table cellpadding="1" cellspacing="0" id="product_sku_box">
	<tbody>
	<tr class="bar"><td>SKU</td><td>Description</td><td>Sort</td></tr>
	<? 
		function print_product_sku_row($option = "", $sku = "", $description = "", $sort = ""){ ?>
		<tr><td><input type="hidden" name="option[]" value="<?=$option?>"><input type="text" size="12" name="sku[]" value="<?=$sku?>"></td><td><input type="text" size="35" name="description[]" value="<?=$description?>"></td><td><input type="text" size="2" name="sort[]" value="<?=$sort?>"></td></tr>
		<? }
		if(isset($error)){
			foreach($_POST['option'] as $x => $opt){ 
				print_product_sku_row($opt, $_POST['sku'][$x], $_POST['description'][$x], $_POST["sort"][$x]);
			}
		}elseif(isset($moddb)){
			$results = mysql_query("select product_option.*, if(sort=0,1,0) as zero from product_option where product = '".$moddb->result("id")."' order by zero asc, sort asc, sku");
			$i = 0;
			if(mysql_num_rows($results)){
				while($row = mysql_fetch_assoc($results)){
					print_product_sku_row($row['id'], $row['sku'], $row['description'], $row["sort"]);
					$i++;
				}
			}
			for($i;$i<mysql_num_rows($results)+3;$i++) print_product_sku_row();
		}else{
			for($i=0;$i<5;$i++) print_product_sku_row();
		}
?>
	</tbody>
	</table>
	<div style="text-align:right;margin:4px;"><input type="button" onclick="more_rows('product_sku_box');" value="Add More"></div>
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
			if(isset($_GET['user']))
				print "&setsite=".$db->result("product_category.site");
			print "\">".substr(strip_tags($db->result("description")),0,60)."</td><td align=\"right\"><a href=\"product.php?".(isset($_GET[category])?"category=".$_GET['category']:"user=$_GET[user]")."&delete=".$db->result("id")."\">Delete?</a></td></tr>";
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
