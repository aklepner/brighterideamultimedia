<?
	if($_SERVER["HTTPS"] != 'on'){
		header("Location: https://www.databusinesssystems.com/account/");
		exit;
	}
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time()));
	header("Pragma: no-cache");
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()-3600));
	header("Cache-Control: no-cache");
	require_once("../inc/config.inc");
	require_once("../inc/crypt.inc");
	session_start();
	function print_address($id){
		global $dbh;
		$results = mysql_query("select * from order_address where id = '$id'", $dbh);
		if(mysql_num_rows($results)){
			$row = mysql_fetch_assoc($results);
			if($row['company'])
				print $row['company']."<br>";
			print $row['name']."<br>".$row['address1']." ".$row['address2']."<br>".$row['city'].", ".$row['state']." ".$row['zip_code']."<br>";
		}else{
			print "<p>Error retrieving address</p>";
		}
	}
	$results = mysql_query("select * from orders where account = '".$_SESSION['account_id']."' and orders.id = '".$_SERVER['QUERY_STRING']."' limit 0,1", $dbh);
	if(mysql_num_rows($results)){
		$order = mysql_fetch_assoc($results);
	}else{
		print "<script>window.close();</script>";
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Data Business Systems - Invoice #<?=$order['id']?></title>
	<style type="text/css">
		body {
			font-family:Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
		}
		th { text-align:left; }
	</style>	
</head>

<body>
<div style="font-size:14px;text-align:center;">
<img src="../images/dbs_logo_print.gif" width="155" height="65" alt="Data Business Systems" /><br>
<span style="font-size:18px;"><b>Data Business Systems</b></span><br />
PO Box 780<br>
Flanders, NJ 07836<br>
+1(800)778-6247<br>
</div>
<div style="font-size:14px;"><b>Invoice #<?=$order['id']?></b></div>
<br clear="all">
<br><br>
<div><b>Placed on <?=date("F j, Y", $order['datetime'])?> at <?=date("h:i A", $order['datetime'])?></b></div>
<br>
<table width="95%" align="center" cellpadding="4" cellspacing="0">
<tr><td colspan="2">
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr><th>Item</th><th style="text-align:right;">Quantity</th><th style="text-align:right;">Price</th></tr>
<?
	$results = mysql_query("select description,quantity,price from order_items where order_id = '".$order['id']."'", $dbh);
	while($row = mysql_fetch_assoc($results)){
		print "<tr><td align=\"left\">".$row['description']."</td><td align=\"right\">".$row['quantity']."</td><td align=\"right\">$".(sprintf("%0.2f",$row['price']))."</td></tr>";
	}
?>
</table></td>
<tr><td valign="top"><b>Shipping Address</b><br><? print_address($order['ship_address']); ?></td><td valign="top">
<table align="right" cellpadding="2" cellspacing="0" border="0">
<tr><td align="right"><b>SubTotal:</b></td><td align="right">$<?=sprintf("%0.2f",$order['subtotal']);?></td></tr>
<tr><td align="right"><b>Shipping &amp; Handling(<?=$ship_type[$order['ship_method']];?>):</b></td><td align="right">$<?=sprintf("%0.2f",$order['shipping']);?></td></tr>
<tr><td align="right"><b>Tax:</b></td><td align="right">$<?=sprintf("%0.2f",$order['tax']);?></td></tr>
<tr><td align="right"><b>Total:</b></td><td align="right">$<?=sprintf("%0.2f",$order['total']);?></td></tr>
</table>
</td></tr>
</table><br>
<br>
<table width="95%" align="center" cellpadding="4" cellspacing="0">
<tr><td valign="top"><b>Billing Address</b><br><? print_address($order['bill_address']); ?></td><td valign="top">
<table align="right" cellpadding="2" cellspacing="0" border="0">
<tr><td align="right" class="order_header">Payment Type:</td><td align="left"><? if($order['payment_method'] == 'cc') print "Credit Card"; else print "Purchase Order"; ?></td></tr>
<?
if($order['payment_method'] == 'cc'){
	$results = mysql_query("select * from cc_charges where order_id = '".$order['id']."' order by datetime desc limit 0,1");
	if(mysql_num_rows($results)){
		$cc = mysql_fetch_assoc($results);
?>
<tr><td align="right">Name on Card:</td><td align="left"><?=$cc['first_name']." ".$cc['last_name'];?></td></tr>
<tr><td align="right">Card Number:</td><td align="left"><?
if($cc['card_number']){
	$card_number = trim(decrypto(base64_decode($cc['card_number']), substr($cc['order_id'],strlen($cc['order_id'])-2,2)));
	for($i=0;$i<strlen($card_number)-4;$i++)
		print "x";
	print substr($card_number,strlen($card_number)-4,4);
}else{
	print "Removed for Security";
}
?></td></tr>
<tr><td align="right" class="order_header">Expiration Date:</td><td align="left"><?=$cc['exp_date']?></td></tr>
<? 
	}
}else{
	$results = mysql_query("select * from order_po where id = '".$order['id']."' limit 0,1",$dbh);
	if(mysql_num_rows($results)){
		print "<tr><td align=\"right\" class=\"order_header\">PO Number:</td><td align=\"left\">".mysql_result($results, 0,"po_number")."</td></tr>";
	}
}
?>
</table>
</td></tr>
</table><br>
<script>window.print();</script>
</body>
</html>
