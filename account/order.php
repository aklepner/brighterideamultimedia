<?
	if($_SERVER["HTTPS"] != 'on'){
		header("Location: https://www.databusinesssystems.com/account/");
		exit;
	}
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time()));
	header("Pragma: no-cache");
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()-3600));
	header("Cache-Control: no-cache");
	require_once("../inc/dbi.inc");
	require_once("../inc/crypt.inc");
	session_start();
	function print_address($id){
		global $dbh;
		$results = mysql_query("select * from order_address where id = '$id'", $dbh);
		if(mysql_num_rows($results)){
			$row = mysql_fetch_assoc($results);
			print "<table align=\"left\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
			print "<tr><td align=\"left\" class=\"order_header\">Name</td><td>:</td><td align=\"left\">".$row['name']."</td></tr>";
			print "<tr><td align=\"left\" class=\"order_header\">Company</td><td>:</td><td align=\"left\">".$row['company']."</td></tr>";
			print "<tr><td align=\"left\" class=\"order_header\">Address 1</td><td>:</td><td align=\"left\">".$row['address1']." ".$row['address2']."</td></tr>";
			print "<tr><td align=\"left\" class=\"order_header\">City, State Zip</td><td>:</td><td align=\"left\">".$row['city'].", ".$row['state']." ".$row['zip_code']."</td></tr>";
			print "<tr><td align=\"left\" class=\"order_header\">Phone</td><td>:</td><td align=\"left\">".$row['phone']."</td></tr>";
			print "<tr><td align=\"left\" class=\"order_header\">Fax</td><td>:</td><td align=\"left\">".$row['fax']."</td></tr>";
			print "</table>";
		}else{
			print "<p>Error retrieving address</p>";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Data Business Systems - Order</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	include("../inc/header.inc");
	$db = new dbi();
	$tdb = new dbi();
	$db->query("select * from orders where account = '".$_SESSION['account_id']."' and orders.id = '$_SERVER[QUERY_STRING]' limit 0,1");
	if($db->numrows()){
		$order_id = $db->result("id");
?>
<h1>Invoice for Order #<?=$db->result("id")?></h1>
<div style="text-align:center;margin:10px;"><a href="invoice.php?<?=$db->result("id")?>" target="_blank"><b>PRINT A COPY OF THIS INVOICE</b></a></div>
<table width="95%" align="center" cellpadding="4" cellspacing="0" style="border:2px solid #000000;">
<tr class="bar"><td align="left" style="font-weight:bold;font-size:14px;" colspan="2">Shipping Information</td></tr>
<tr><td colspan="2">
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr><td align="left" class="order_header">Item</td><td align="right" class="order_header">Quantity</td><td align="right" class="order_header">Price</td></tr>
<?
	$tdb->query("select description,quantity,price from order_items where order_id = '$order_id'");
	while($tdb->loop()){
		print "<tr><td align=\"left\">".$tdb->result("description")."</td><td align=\"right\">".$tdb->result("quantity")."</td><td align=\"right\">$".(sprintf("%0.2f",$tdb->result("price")))."</td></tr>";
	}
?>
</table></td>
<tr><td valign="top"><? print_address($db->result("ship_address")); ?></td><td valign="top">
<table align="right" cellpadding="2" cellspacing="0" border="0">
<tr><td align="right" class="order_header">SubTotal:</td><td align="right">$<?=sprintf("%0.2f",$db->result("subtotal"));?></td></tr>
<tr><td align="right" class="order_header">Shipping &amp; Handling(<?=$ship_type[$db->result("ship_method")];?>):</td><td align="right">$<?=sprintf("%0.2f",$db->result("shipping"));?></td></tr>
<tr><td align="right" class="order_header">Tax:</td><td align="right">$<?=sprintf("%0.2f",$db->result("tax"));?></td></tr>
<tr><td align="right" class="order_header">Total:</td><td align="right">$<?=sprintf("%0.2f",$db->result("total"));?></td></tr>
</table>
</td></tr>
</table><br>

<table width="95%" align="center" cellpadding="4" cellspacing="0" style="border:2px solid #000000;">
<tr class="bar"><td colspan="2" align="left" style="font-weight:bold;font-size:14px;">Billing Information</td></tr>
<tr><td valign="top"><? print_address($db->result("bill_address")); ?></td><td valign="top">
<table align="right" cellpadding="2" cellspacing="0" border="0">
<tr><td align="right" class="order_header">Payment Type:</td><td align="left"><? if($db->result("payment_method") == 'cc') print "Credit Card"; else print "Purchase Order"; ?></td></tr>
<?
if($db->result("payment_method") == 'cc'){
	$tdb->query("select * from cc_charges where order_id = '$order_id' order by datetime desc limit 0,1");
	if($tdb->numrows()){
?>
<tr><td align="right" class="order_header">Name on Card:</td><td align="left"><?=$tdb->result("first_name")." ".$tdb->result("last_name");?></td></tr>
<tr><td align="right" class="order_header">Card Number:</td><td align="left"><?
if($tdb->result("card_number")){
	$card_number = trim(decrypto(base64_decode($tdb->result("card_number")), substr($tdb->result("order_id"),strlen($tdb->result("order_id"))-2,2)));
	for($i=0;$i<strlen($card_number)-4;$i++)
		print "x";
	print substr($card_number,strlen($card_number)-4,4);
}else{
	print "Removed for Security";
}
?></td></tr>
<tr><td align="right" class="order_header">Expiration Date:</td><td align="left"><?=$tdb->result("exp_date");?></td></tr>
<tr><td align="right" class="order_header">Message:</td><td align="left"><?=$tdb->result("message");?></td></tr>
<? 
	}
}else{
	$tdb->query("select * from order_po where id = '$order_id' limit 0,1");
	print "<tr><td align=\"right\" class=\"order_header\">PO Number:</td><td align=\"left\">".$tdb->result("po_number")."</td></tr>";
}
?>
</table>
</td></tr>
</table><br>
<?
	}else{
		print "<div align=\"center\" class=\"header\">Error</div>We were unable to retrieve the order you requested.";
	}
?>
<br>
<table width="60%" cellpadding="2" cellspacing="1" border="0" align="center">
<tr><td align="center"><a href="history.php">Go to Order History</a></td><td align="center"><a href="index.php">Go to Main Account</a></td></tr>
</table>
<?
	include("../inc/footer.inc");
?>
</body>
</html>
