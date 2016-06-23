<? 
session_start();
$_SESSION['admin'] = "true";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>dbs - customer search</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
require_once("../inc/crypt.inc");
include("header.inc");
$db = new dbi();

if($_GET['delete_account']){
	$results = mysql_query("SELECT id, email FROM account WHERE id = '".mysql_real_escape_string($_GET['delete_account'])."'", $dbh);
	if(mysql_num_rows($results)){
		$account = mysql_fetch_assoc($results);
		
		$results = mysql_query("SELECT id FROM orders WHERE account = '".$account['id']."'", $dbh);
		if(mysql_num_rows($results)){
			while($row = mysql_fetch_assoc($results)){
				mysql_query("DELETE FROM order_po WHERE id = '".$row['id']."'", $dbh);
				mysql_query("DELETE FROM order_items WHERE order_id = '".$row['id']."'", $dbh);	
			}
			mysql_query("DELETE FROM order_address WHERE account = '".$account['id']."'", $dbh);
			mysql_query("DELETE FROM orders WHERE account = '".$account['id']."'", $dbh);
		}
		mysql_query("DELETE FROM cc_charges WHERE account = '".$account['id']."'", $dbh);
		mysql_query("DELETE FROM account WHERE id = '".$account['id']."'", $dbh);
		echo '<div class="success" align="center">Account #',$account['id'],' (',$account['email'],') has been deleted!</div><br>';
	}
}
?>
<table width="100%" cellpadding="2" cellspacing="0" border="0" style="font-size:11px;margin-bottom:10px;">
<tr><td align="left" valign="top"><a href="<?=$_SERVER[PHP_SELF]."?add=1";?>">Add Account</a></td>
<form action="<?=$_SERVER['PHP_SELF']?>"><td align="right"><b>Search:</b> <input type="text" name="search" size="20" value="<? if(isset($_GET[search])) print $_GET[search]; ?>" style="font-size:11px;border:1px solid #000000;"> <input type="submit" value="Find" style="font-size:11px;color:#fff;border:1px solid #000;background-color:#399;"></form></td></tr>
</table>
<?
if(isset($_GET['add']) || isset($_GET['mod'])){
	if(isset($_POST['email'])){
		if($_POST['email'] == "" || !preg_match("/^\S+@\S+\.\S+$/i",$_POST['email'])){
			$error = "Enter a valid email.";
		}elseif($_POST[password] != "" && (strlen($_POST[password]) < 4 || strlen($_POST[password]) > 16)){
			$error = "Password must be between 4 and 16 characters.";
		}elseif($_POST['taxrate'] && !is_numeric($_POST['taxrate'])){
			$error = "Enter a valid Tax Rate.";
		}
		if(!$error){
			if(isset($_GET['add'])){
				$results = mysql_query("select * from account where email = '".$_POST['email']."' and id != '".$_GET['mod']."'", $dbh);
				if(mysql_num_rows($results))
					$error = "Email account already exists.";
			}
			if(!$error){
				if(isset($_POST['po']))
					$po = 'y';
				else
					$po = 'n';

				if(isset($_GET['mod'])){
					$query = "update account set email = '".$_POST['email']."', password = ";
					if($_POST[password] != ""){
						$query .= "'".base64_encode(encrypto($_POST['password'],strtolower(substr($_POST['email'],0,2))))."'";
					}else{
						$db->query("select email,password from account where id = '".$_GET['mod']."'");
						$oldpassword = trim(decrypto(base64_decode($db->result("password")), substr($db->result("email"),0,2)));
						$query .= "'".base64_encode(encrypto($oldpassword,strtolower(substr($_POST['email'],0,2))))."'";
					}
					$query .= ", po = '$po', taxrate = ".(is_numeric($_POST['taxrate'])?"'".$_POST['taxrate']."'":"null")." where id = '".$_GET['mod']."'";
					$db->query($query);
					$id = $mod;
				}else{
					#$error = 'success';
					$id = $db->query("insert into account (email, password, po, taxrate, created) values('$_POST[email]', '".base64_encode(encrypto($_POST[password],strtolower(substr($_POST['email'],0,2))))."', '$po', ".(is_numeric($_POST['taxrate'])?"'".$_POST['taxrate']."'":"null").",  '".date("Y-m-d H:i:s",time())."')");
				}
				print "<div align=\"center\" class=\"success\">Account updated successfully!</div><br>";
			}
		}
	}

	if(!isset($_POST[email]) || $error){
		if(isset($_GET['mod']) && !$error){
			$results = mysql_query("select * from account where id = '".$_GET['mod']."' LIMIT 1", $dbh);
			if(mysql_num_rows($results))
				$mdb = mysql_fetch_assoc($results);
		}
		if($error)
			echo '<div align="center" class="error">',$error,'</div><br />';
?>
<form action="<?=$_SERVER[PHP_SELF]."?".(isset($mdb)?"mod=".$mdb['id']:"add=1")?>" method="post"><table align="center" cellpadding="3" cellspacing="0" border="0">
	<tr bgcolor="#990000"><td style="color:#FFFFFF;" colspan="2">Update Account</td></td></tr>
	<tr><td class="field_title">Email</td><td><input type="text" name="email" size="50" value="<? if($error) print $_POST[email]; elseif(isset($mdb)) echo $mdb['email']; ?>"></td></tr>
	<tr><td class="field_title">Password</td><td><input type="password" name="password" size="20" value="<? if($error) print $_POST['password'];?>"><br><span class="help">Leave password blank for no change.</span></td></tr>
	<tr><td class="field_title">Purchase Order?</td><td><input type="checkbox" name="po"<? if(($error && isset($_POST[po])) || (isset($mdb) && $mdb['po'] == 'y')) print " checked=\"checked\""; ?>></td></tr>
	<tr><td class="field_title">Custom Tax Rate</td><td><input type="text" name="taxrate" size="3" value="<? if($error) print $_POST['taxrate']; elseif(isset($mdb)) echo $mdb['taxrate']; ?>">% <i>Overrides default tax table for ALL orders</i></td></tr>
	<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
	</table>
</form>
<?
	}
}
if(isset($_GET['address'])){
	$results = mysql_query("select * from order_address where id = '".$_GET['address']."' and disable = 'n' limit 1", $dbh);
	if(mysql_num_rows($results)){ 
		$mdb = mysql_fetch_assoc($results);
		if(sizeof($_POST)){
			if($_POST['name'] == "")
				$error = "Please specify a Full Name!";
			elseif($_POST['address1'] == "")
				$error = "Please specify an Address!";
			elseif($_POST['city'] == "")
				$error = "Please specify a City!";
			elseif($_POST['state'] == "")
				$error = "Please specify a State!";
			elseif($_POST['zip_code'] == "")
				$error = "Please specify a Zip Code!";
			elseif($_POST['phone'] == "")
				$error = "Please specify a Phone Number!";
					
			if(!$error){
				mysql_query("update order_address set name = '".mysql_real_escape_string($_POST['name'])."', company = '".mysql_real_escape_string($_POST['company'])."', address1 = '".mysql_real_escape_string($_POST['address1'])."', address2 = '".mysql_real_escape_string($_POST['address2'])."', city = '".mysql_real_escape_string($_POST['city'])."', state = '".mysql_real_escape_string($_POST['state'])."', zip_code = '".mysql_real_escape_string($_POST['zip_code'])."', phone = '".mysql_real_escape_string($_POST['phone'])."', fax = '".mysql_real_escape_string($_POST['fax'])."' where id = '".$_GET['address']."'", $dbh);
				echo "<div align=\"center\" class=\"success\">Address updated successfully!</div><br/>";
				$id = $mdb['account'];
			}
		}
		if(isset($error) || !sizeof($_POST)){
			if($error)
				print "<div align=\"center\" class=\"error\">$error</div><br>";
		?>
		<form action="<?=$_SERVER['PHP_SELF']?>?address=<?=$mdb['id']?>" method="post">
		<table border="0" cellspacing="1" cellpadding="4" align="center">
		<tr class="bar"><td align="left" colspan="2">Modify Address</td></tr>
		<tr><td><b>Full Name</b></td><td><input type="text" name="name" size="35" value="<? if($error){ print $_POST['name']; }else{ print $mdb['name']; } ?>"></td></tr>
		<tr><td><b>Company</b></td><td align="left" width="60%"><input type="text" name="company" size="35" value="<? if($error){ print $_POST['company']; }else{ echo $mdb['company']; } ?>"></td></tr>
	<tr><td><b>Address 1</b></td><td><input type="text" name="address1" size="35" value="<? if($error){ print $_POST['address1']; }else{ print $mdb["address1"]; } ?>"></td></tr>
	<tr><td><b>Address 2</b></td><td><input type="text" name="address2" size="35" value="<? if($error){ print $_POST['address2']; }else{ print $mdb["address2"]; } ?>"></td></tr>
	<tr><td><b>City</b></td><td><input type="text" name="city" size="35" value="<? if($error){ print $_POST['city']; }else{ print $mdb["city"]; } ?>"></td></tr>
	<tr><td><b>State</b></td><td><select name="state"><option value=""></option><?
			
			$results = mysql_query("select * from states order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['abbr']."\"";
				if((isset($error) && $_POST['state'] == $row['abbr']) || (!isset($error) && $mdb['state'] == $row['abbr']))
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?></select></td></tr>
	<tr><td><b>Zip Code</b></td><td><input type="text" name="zip_code" size="15" value="<? if($error){ print $_POST['zip_code']; }else{ print $mdb['zip_code']; }  ?>"></td></tr>
	<tr><td><b>Phone Number</b></td><td><input type="text" name="phone" size="20" value="<? if($error){ print $_POST['phone']; }else{ print $mdb["phone"]; }  ?>"></td></tr>
	<tr><td><b>Fax Number</b></td><td><input type="text" name="fax" size="20" value="<? if($error){ print $_POST['fax']; }else{ print $mdb["fax"]; }  ?>"></td></tr>
			<tr class="bar"><td align="center" colspan="2"><input type="submit" value="Submit"></td></tr>
			</table>
			</form>
		<?
			}
		}else{
			print "<div align=\"center\" class=\"error\">No Such Address</div>";
		}
	}
if(isset($_GET['id']))
	$id = $_GET['id'];
elseif(isset($_GET['search']) && is_numeric($_GET['search']))
	$id = $_GET['search'];

if(isset($id) && is_numeric($id)){
	if(isset($_GET['delete'])){
		mysql_query("UPDATE order_address SET disable = 'y' WHERE account = '$id' AND id = '".$_GET['delete']."'", $dbh);
		print "<div align=\"center\" class=\"success\">Address deleted successfully!</div><br>";
	}
	$results = mysql_query("select * from account where id = '$id' limit 0,1", $dbh);
	if(mysql_num_rows($results)){
		$row = mysql_fetch_assoc($results);
		$account_id = $row['id']; ?>
<table align="center" width="94%">
<tr><td valign="top">
<table align="center" cellpadding="2" cellspacing="0" border="0">
<tr class="bar"><td align="left" style="font-weight:bold;">Account #<?=$row['id']?></td><td align="right" colspan="2"><a style="color:#fff;" href="<?=$_SERVER['PHP_SELF']."?mod=".$row['id'];?>">Modify Account</a></td></tr>
<tr><td class="field_title">Email</td><td><?=$row['email']?></td></tr>
<tr><td class="field_title">Password</td><td><?=trim(decrypto(base64_decode($row['password']), strtolower(substr($row['email'],0,2))))?></td></tr>
<tr><td class="field_title">Created</td><td><?=date("n/j/y g:i a",strtotime($row['created']))?></td></tr>
<tr><td class="field_title">Purchase Order?</td><td><?=($row['po']=='y'?"Yes":"No")?></td></tr>
<tr><td class="field_title">Tax Rate</td><td><?=(is_numeric($row['taxrate'])?$row['taxrate']."%":"n/a")?></td></tr>
<tr><td colspan="2" style="text-align:center;padding:5px 0 15px 0;"><a href="<?=$_SERVER['PHP_SELF']?>?delete_account=<?=$row['id']?>" onClick="if(confirm('Delete customer?')){ return true; }else{ return false; }" style="color:#F00;">Delete customer?</a></td></tr>
<tr class="bar"><td colspan="2" align="center"><a style="font-weight:bold;color:#FFFFFF;" href="product.php?user=<?=$account_id?>">Custom Products</a></td></tr>
</table>
</td><td valign="top">
<table align="center" cellpadding="2" cellspacing="0" border="0">
<?
	$results = mysql_query("select * from order_address where account = '$account_id' AND disable = 'n' ORDER BY type", $dbh);
	if(mysql_num_rows($results)){
		$type = "0";
		$i = 0;
		while($row = mysql_fetch_assoc($results)){ 
			if($row['type'] != $type){
				echo "<tr class=\"bar\"><td colspan=\"4\" style=\"text-align:center;\"><b>",ucwords($row['type'])," Address</b></td></tr>";
				$type = $row['type'];
			}
		?>
			<tr class="row<?=($i%2?1:2)?>"><td><?=$row['name']?><br/><?=$row['company']?></td><td><?=$row['address1']." ".$row['address2']?><br /><?=$row['city'].", ".$row['state']." ".$row['zip_code']?></td><td><a href="<?=$_SERVER['PHP_SELF']?>?address=<?=$row['id']?>">Edit</a><br/><a href="<?=$_SERVER['PHP_SELF']?>?id=<?=$id?>&amp;delete=<?=$row['id']?>" onClick="if(confirm('Are you sure you want to delete this address?')){ return true; }else{ return false; }">Delete</a></tr>
<?
			$i++;
		}
	}else{
		echo "<tr><td colspan=\"3\">No Address Information Available.</td></tr>";
	}
?>
</td></tr>
</table>
</td></tr>
</table>
<br>
<?
		if(isset($_GET['item'])){
			$results = mysql_query("select order_items.item, order_items.item_id, orders.id,  description,  datetime,status,  order_items.item_option, order_items.quantity, orders.site, site.document_base from order_items inner join orders on orders.id = order_items.order_id JOIN site ON site.id = orders.site where orders.status <> 'canceled' and account = '".$account_id."' and order_items.item = '".urldecode($_GET['item'])."' order by datetime desc");
			if(mysql_num_rows($results))
				echo "<div align=\"center\"><a href=\"".$_SERVER['PHP_SELF']."?id=".$account_id."\">View all Items</a></div><br>";
		}else{
			// $db->query("select order_items.item, order_items.item_id, max(orders.id) as id, description, max(datetime) as datetime,status,  order_items.item_option, order_items.quantity, orders.site from order_items inner join orders on orders.id = order_items.order_id where orders.status <> 'canceled' and account = '".$account_id."'  group by order_items.item order by datetime desc");
			$results = mysql_query("select a.*, site.document_base FROM (SELECT i.item, i.description, i.item_id, i.item_option, i.quantity, o.id, o.datetime, o.site, o.status from order_items i JOIN orders o ON o.id = i.order_id WHERE o.status != 'canceled' AND o.account = '".$account_id."' order by datetime desc) as a JOIN site ON site.id = a.site group by item order by datetime desc");
		}
		if(mysql_num_rows($results)){ ?>
<table width="95%" align="center" cellpadding="3" cellspacing="0" style="vertical-align:middle;">
<tr class="bar"><td align="center" nowrap="nowrap">Order ID</td><td align="center">Date</td><td align="left" nowrap="nowrap">Item #</td><td align="left">Description</td><td align="left">Status</td><td>Reorder</td></tr>
<?
			$i = 0;
			while($row = mysql_fetch_assoc($results)){
?>
<tr<? if($i%2) print " class=\"shade\""; ?>><td align="center"><a href="order.php?id=<?=$row['id']?>"><?=$row['id']?></a></td><td align="center"><?=date("n/d/Y",$row['datetime'])?></td><td nowrap="nowrap"><a href="<?=$_SERVER['PHP_SELF']."?id=".$account_id."&amp;item=".urlencode($row['item']) ?>"><?=($row['item']?$row['item']:"N/A")?></a></td><td align="left"><?=$row['description']?></td><td align="left"><?=ucwords($row['status'])?></td><td><? if($row['item_id'] > 0){ ?><form method="post" action="https://www.databusinesssystems.com<?=$row['document_base']?>/cart/" style="margin:0;"><input type="hidden" name="admin_order" value="<?=$account_id?>"><input type="hidden" name="option" value="<?=$row['item_option']?>"><input type="hidden" name="quantity" value="<?=$row['quantity']?>"><input type="hidden" name="product" value="<?=$row['item_id']?>"><input type="submit" value="Reorder" style="font-size:10px;"></form><? } ?></td></tr>
<?
				$i++;
			}
?>
			</table>
<?
		}else{
			print "<br><div align=\"center\">This customer has not placed any orders.</div>";
		}
	}else{
		print "<div align=\"center\" class=\"error\">No account with id '".$id."'.</div>";
	}
}
if(!isset($id) && isset($_GET['search']) && $_GET['search'] != ""){
	if(preg_match("/^[a-z0-9\-_\.]+\@[a-z0-9\-_\.]+\.[a-z]{2,6}$/i",$_GET['search']))
		$results = mysql_query("select a.id, a.email, oa.name, oa.company from account a left join order_address oa on a.id = oa.account where a.email = '".mysql_real_escape_string($_GET['search'])."'", $dbh)
			or error_log("MySQL Error: ".mysql_error($dbh)." in ".__FILE__." on line ".__LINE__);
	else
		$results = mysql_query("select account.id, account.email, order_address.name, order_address.company from order_address inner join account on account.id = order_address.account where match(company, name) against ('".mysql_real_escape_string($_GET['search'])."')  group by account.id UNION select account.id, account.email, order_address.name, order_address.company from account LEFT JOIN order_address on order_address.account = account.id where email LIKE '%".mysql_real_escape_string($_GET['search'])."%' GROUP BY account.id", $dbh)
			or error_log("MySQL Error: ".mysql_error($dbh)." in ".__FILE__." on line ".__LINE__);
	if(mysql_num_rows($results)){
		print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
		print "<tr class=\"bar\"><td style=\"font-weight:bold\">Account #</td><td style=\"font-weight:bold\">Company</td><td style=\"font-weight:bold\">Name</td><td style=\"font-weight:bold\">Email</td></tr>";
		$account_numbers = array();
		while($row = mysql_fetch_assoc($results)){
			if(!in_array($row['id'], $account_numbers)){
				print "<tr><td align=\"center\"><a href=\"customer.php?id=".$row['id']."\">".$row['id']."</a></td><td>".$row['company']."</td><td>".$row['name']."</td><td>".$row['email']."</td></tr>";
				array_push($account_numbers, $row['id']);
			}
		}
		print "<tr class=\"bar\"><td colspan=\"5\">&nbsp;</td></tr></table>";
	}else{
		print "<div align=\"center\" class=\"error\">No accounts found</div>";
	}
}
include("footer.inc");
?>

</body>
</html>
