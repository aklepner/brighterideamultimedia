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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Data Business Systems - Affiliate Report</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	include("../inc/header.inc"); 
?>
<h1>Affiliate Report</h1>
<div align="center"><a href="index.php">Back to Your Account</a></div>
<br>
<?
$results = mysql_query("select * from affiliate where account = '".$_SESSION['account_id']."'", $dbh);
if(mysql_num_rows($results)){
	$affiliate = mysql_fetch_assoc($results);
	$results = mysql_query("select orders.id, orders.datetime, orders.status, order_items.description, order_items.quantity, order_address.name, order_address.address1, order_address.address2, order_address.city, order_address.state,  order_address.zip_code  from order_items inner join product on product.id= order_items.item_id inner join product_category on product.category = product_category.id inner join orders on orders.id = order_items.order_id inner join order_address on order_address.id = orders.ship_address where product_category.id = '".$affiliate['product']."' order by datetime desc");
	if(mysql_num_rows($results)){ ?>
<table width="95%" align="center" cellpadding="2" cellspacing="0">
<tr class="bar"><td align="center" nowrap="nowrap">Order ID</td><td align="center">Date</td><td align="left">Description</td><td align="left">Quantity</td><td align="left">Status</td></tr>
<?
		$i = 0;
		while($row = mysql_fetch_assoc($results)){
			if($i){ ?>
				<tr><td colspan="5" align="center" style="padding:0;"><hr size="1" noshade="noshade"></td></tr>
			<?  } ?>
<tr><td align="center" valign="top"><?=$row['id']?></a></td><td align="center" valign="top"><?=date("n/d/Y",$row['datetime'])?></td><td align="left" valign="top"><?=$row['description']?></td><td align="left" valign="top"><?=$row['quantity']?></td><td align="left" valign="top"><?=ucwords($row['status'])?></td></tr>
<tr><td colspan="2"> </td><td colspan="3" align="left"><b>Ship To:</b> <?=$row['name']?> <?=$row['address1']?> <?=$row['address2']?> <?=$row['city']?>, <?=$row['state']?> <?=$row['zip_code']?></td></tr>
	<?	
			$i++;
		} ?>
</table>
<?
	}else{
		print "There have been no items ordered for this affiliate.";
	}
}
?>
<? include("../inc/footer.inc"); ?>

</body>
</html>
