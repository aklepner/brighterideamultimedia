<?
	if($_SERVER["HTTPS"] != 'on'){
		header("Location: https://www.databusinesssystems.com".$_SERVER['REQUEST_URI']);
		exit;
	}
	require_once("../inc/dbi.inc");
	require_once("../inc/crypt.inc");
	$employee = array("J. Kirschner", "L. Higgs", "A. Picillo", "K. Hou");
	$db = new dbi();
	if(isset($_POST['status'])){
		mysql_query("update orders set status = '".$_POST['status']."', placed_by = ".($_POST['placed_by']?"'".$_POST['placed_by']."'":"NULL")." where id = '".$_GET['id']."'", $dbh);
		if($_POST['status'] == "processed" || $_POST['status'] == "canceled"){
			header("Location: index.php");
			exit;
		}
	}
	if(isset($_POST['history']) && strtotime($_POST['history']) > 0){
		mysql_query("update orders set datetime = '".strtotime($_POST['history'])."' where id = '".$_GET['id']."'", $dbh);
	}
	if(isset($_POST['oitem'])){
		$query = "update order_items set vendor = '".addslashes($_POST['vendor'])."', job = '".addslashes($_POST['job'])."'";
		if($_POST['reminder'] != $_POST['reminder_prev']){
			if($_POST['reminder'] != "")
				$query .= ", reminder = '".date("Y-m-d",strtotime($_POST['reminder']))."'";
			else
				$query .= ", reminder = NULL";
			$query .= ", reminder_sent = NULL";
		}
		$query .= " where id = '$_POST[oitem]'";
		mysql_query($query, $dbh);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>DBS - Order #<? if(isset($_GET['id'])) print $_GET['id']; ?></title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
	function dollar($num){
		return "$".number_format($num,2,".",",");
	}
	if(isset($_GET[id])){
		$db->query("select orders.*,account.email from orders inner join account on account.id = orders.account where orders.id = '".$_GET['id']."'");
		if(!$db->numrows()){
			print "No Such Order!";
		}else{
?>
<table align="center" width="98%" cellpadding="2" cellspacing="0" border="0" style="border-bottom:2px solid black;">
<tr><td align="left" style="font-size:16px;"><b>Data Business Systems, Inc.</b></td><td align="right" style="font-size:16px;"><b>Order #: <?=$db->result("id")?></b></td></tr>
</table>
<table bgcolor="#FFFFFF" width="96%" cellpadding="0" cellspacing="0" style="margin:2px auto 2px auto;">
<tr><td colspan="2">
<table width="100%" cellpadding="5" cellspacing="0">
<tr><td align="left" nowrap="nowrap"><? if($_GET['history']){ ?><form style="margin:0;" method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$db->result("id")?>"><span class="order_header">Order Placed:</span> <input type="text" name="history" value="<?=date("m/d/Y h:i a",$db->result("datetime"))?>" style="font-size:10px;"><input type="submit" value="Update" style="font-size:10px;font-weight:bold;"></form><? }else{ ?><span class="order_header">Order Placed:</span> <?=date("m/d/Y h:i a",$db->result("datetime"))?> <span style="font-size:10px;"><a href="<?=$_SERVER[PHP_SELF]?>?id=<?=$db->result("id")?>&history=1" style="font-size:10px;">Update</a></span><? } ?></td><td align="right" rowspan="2"><form method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$_GET['id']?>"><b>Placed By:</b> <select name="placed_by" style="font-size:12px;"><option value="">Customer</option><?
	foreach($employee as $x){
		echo "<option value=\"$x\"";
		if($db->result("placed_by") == $x)
			echo ' selected="selected"';
		echo ">$x</option>";
	}
	if($db->result("placed_by") && !in_array($db->result("placed_by"), $employee))
		echo '<option value="',$db->result("placed_by"),'">',$db->result("placed_by"),'</option>';
?></select> <b>Status:</b> <select name="status" style="font-size:12px;"><?
	$status_option = array("new", "processed", "canceled");
	foreach($status_option as $val){
		print "<option value=\"$val\"";
		if($val == $db->result("status"))
			echo ' selected="selected"';
		echo '>',strtoupper($val),'</option>';
	}
?></select><input type="submit" value="Update" style="font-size:10px;font-weight:bold;"></form></td></tr>
<tr><td align="left"><span class="order_header">Account:</span> <?=$db->result("account.email")?> (<a href="/admin/customer.php?id=<?=$db->result("orders.account")?>"><?=$db->result("orders.account")?></a>)</td></tr>
</table>
</td></tr>
<tr><td colspan="2">
<table align="center" width="100%" bgcolor="#000000" cellpadding="3" cellspacing="1" border="0">
<tr><td class="order_item_header" nowrap="nowrap">Item</td><td class="order_item_header">Description</td><td class="order_item_header">Quantity</td><td class="order_item_header">Weight</td><td class="order_item_header">Cost</td><td class="order_item_header">Price</td><td class="order_item_header">Discount</td><td class="order_item_header">Shipping</td></tr>
<?
	$results = mysql_query("select * from order_items where order_id = '".$db->result("id")."'", $dbh);
	while($item = mysql_fetch_assoc($results)){ ?>
		<tr><td class="order_item" align="center"><?=$item['item']?></td><td class="order_item"><?=strip_tags($item['description'])?><br><br><b>Vendor:</b> <?=$item['ship_center']?><br><br>
		<? if($item['comment']){ ?><b>Comment:</b> <?=nl2br($item['comment'])?><br><br><? } ?>
<? if($_GET['oitem'] == $item['id']){ ?>
<br>
<div align="center">
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$db->result("id")?>"><input type="hidden" name="oitem" value="<?=$item['id']?>"><input type="hidden" name="reminder_prev" value="<? if($item['reminder'] != "") print date("m/d/Y",strtotime($item['reminder'])); ?>"><span class="order_item_header">Vendor</span>&nbsp;<input type="text" name="vendor" size="10" value="<?=$item['vendor']?>" class="order_item">&nbsp;<span class="order_item_header">Job #</span>&nbsp;<input type="text" name="job" class="order_item" size="10" value="<?=$item['job']?>">&nbsp;<span class="order_item_header">Reminder</span>&nbsp;<input type="text" name="reminder" class="order_item" size="10" value="<? if($item['reminder'] != "") print date("m/d/Y",strtotime($item['reminder'])); ?>">&nbsp;<input type="submit" value="Update" style="font-size:10px;font-weight:bold;"></form>
</div>
<? }else { ?><table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td class="order_item"><? 
	if($item['vendor'] != "")
		print "<span class=\"order_item_header\">Vendor:</span> ".$item['vendor']." ";
	if($item['job'] != "")
		print "<span class=\"order_item_header\">Job #:</span> ".$item['job']." ";
	if($item['reminder'] != "")
		print "<span class=\"order_item_header\">Reminder:</span> ".date("m/d/Y",strtotime($item['reminder']))." ";
	if($item['reminder_sent'] != "")
		print "<span class=\"order_item_header\">Sent:</span> ".date("m/d/Y",strtotime($item['reminder_sent']));
?></td><td align="right"><a class="order_item" href="<?=$_SERVER['PHP_SELF']?>?id=<?=$db->result("id")?>&oitem=<?=$item['id']?>">Options</a></td></tr></table><? } ?></td>
		<? print "<td class=\"order_item\" align=\"center\">".$item['quantity']."</td><td class=\"order_item\" align=\"center\" nowrap=\"nowrap\">".$item['weight']." lbs.</td><td class=\"order_item\" align=\"center\">".($item['cost']?dollar($item['cost']):"N/A")."</td><td class=\"order_item\" align=\"center\">".dollar($item['price'])."</td><td class=\"order_item\" align=\"center\">".dollar($item['discount'])."</td><td class=\"order_item\" align=\"center\" nowrap=\"nowrap\">".dollar($item['ship_cost'])."</td></tr>";
	}
?>
</table>
</td></tr>
<tr><td valign="top"><br>
<span class="order_header">Coupon:</span> <? if($db->result("coupon") != "") print " ".$db->result("coupon"); else print "N/A"; ?><br><br>
<span class="order_header">Shipping Method:</span> <?=$ship_type[$db->result("ship_method")]?><br><br>
<span class="order_header">Special Instructions:</span><br><? if($db->result("special_instructions") != "") print nl2br($db->result("special_instructions")); else print "N/A"; ?><br></td>
<td>
<table align="right" cellpadding="5" cellspacing="0" border="0">
<tr><td class="order_header">Subtotal</td><td>:</td><td align="right"><?=dollar($db->result("subtotal"))?></td></tr>
<tr><td class="order_header">Shipping</td><td>:</td><td align="right"><?=dollar($db->result("shipping"))?></td></tr>
<tr><td class="order_header">Tax</td><td>:</td><td align="right"><?=dollar($db->result("tax"))?></td></tr>
<tr><td class="order_header">Total</td><td>:</td><td align="right"><?=dollar($db->result("total"))?></td></tr>
</table>
</td></tr>
</table>
<table bgcolor="#FFFFFF" width="96%" cellpadding="4" cellspacing="0" style="border-top:1px solid #000000;margin:5px auto 5px auto;">
<tr><td>
<?
	function print_address($id){
		global $dbh;
		$results = mysql_query("select * from order_address where id = '$id'", $dbh);
		if(mysql_num_rows($results)){
			$row = mysql_fetch_assoc($results);
			print "<table align=\"left\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
			print "<tr><td class=\"order_header\">Name</td><td>:</td><td align=\"left\">".$row['name']."</td></tr>";
			print "<tr><td class=\"order_header\">Company</td><td>:</td><td align=\"left\">".$row['company']."</td></tr>";
			print "<tr><td class=\"order_header\">Address 1</td><td>:</td><td align=\"left\">".$row['address1']."</td></tr>";
			print "<tr><td class=\"order_header\">Address 2</td><td>:</td><td align=\"left\">".$row['address2']."</td></tr>";
			print "<tr><td class=\"order_header\">City, State Zip</td><td>:</td><td align=\"left\">".$row['city'].", ".$row['state']." ".$row['zip_code']."</td></tr>";
			print "<tr><td class=\"order_header\">Phone</td><td>:</td><td align=\"left\">".$row['phone']."</td></tr>";
			print "<tr><td class=\"order_header\">Fax</td><td>:</td><td align=\"left\">".$row['fax']."</td></tr>";
			print "</table>";
		}else{
			print "Error getting address ($id)";
		}
	}
?>
<div class="order_header">Billing Information</div>
<? print_address($db->result("bill_address")); ?>
</td><td rowspan="2" valign="top">
<?	if($db->result("payment_method") == 'cc'){ ?>
<div class="order_header">Credit Card Information</div>
<table align="left" cellpadding="3" cellspacing="0" border="0">
<?
	$results = mysql_query("select * from cc_charges where order_id = '".$db->result("id")."' order by datetime desc", $dbh);
	$i = 0;
	while($row = mysql_fetch_assoc($results)){
		if(!$i){
			print "<tr><td class=\"order_header\">Name</td><td>:</td><td align=\"left\">".$row['first_name']." ".$row['last_name']."</td></tr>";
			if($row['card_number']){
				print "<tr><td class=\"order_header\">Card Number</td><td>:</td><td align=\"left\">";
				if($row['card_number']){
					$card = trim(decrypto(base64_decode($row['card_number']), substr($row['order_id'], strlen($row['order_id'])-2,2)));
					echo "<div id=\"order_card_num\">",$card,"</div>";
					echo "<div id=\"order_card_last\">";
					for($i=0;$i<strlen($card)-4;$i++) echo "X";
					echo substr($card,strlen($card)-4,4),"</div>";
				}else
					print "Removed";
				print "</td></tr>";
				print "<tr><td class=\"order_header\">CC ID</td><td>:</td><td align=\"left\">".$row['card_code']."</td></tr>";
			}
			print "<tr><td class=\"order_header\">Expiration Date</td><td>:</td><td align=\"left\">".$row['exp_date']."</td></tr>";
			print "<tr><td class=\"order_header\">Message</td><td>:</td><td align=\"left\" class=\"".($row['response']==1?"success":"error")."\">".$row['message']." R: ".$row['response']." C: ".$row['code']."</td></tr>";
		}else{
			if($i == 1)
				print "<tr><td colspan=\"3\">Previous Failed Attempts</td></tr>";
			print "<tr><td class=\"order_header\">Message</td><td>:</td><td align=\"left\" class=\"".($row['response']==1?"success":"error")."\">".$row['message']." R: ".$row['response']." C: ".$row['code']."</td></tr>";
		}
		$i++;
	}
?>
</table>
<? }else{ ?>
<div class="order_header">Purchase Order Information</div><br>
<?
		$results = mysql_query("select * from order_po where id = '".$db->result("id")."' limit 0,1", $dbh);
		echo '<span class="order_header">PO Number</span>: ',mysql_result($results, 0, "po_number");
   }
?>
</td></tr>
<tr><td>
<div class="order_header">Shipping Information</div>
<? print_address($db->result("ship_address")); ?>
</td></tr>
</table>
<?
		}
	}
?>
<div id="footer"><? if(isset($_GET[start_date])){ print "<a href=\"report.php?start_date=$_GET[start_date]&end_date=$_GET[end_date]&status=$_GET[status]\">Back to Report</a>"; }else{ ?><a href="/admin/">Main Menu</a><? } ?></div>
</body>
</html>
