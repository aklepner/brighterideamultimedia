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
	<title>Data Business Systems - Order History</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	include("../inc/header.inc"); 
?>
<h1>Order History</h1>
<div align="center"><a href="index.php">Back to Your Account</a></div>
<br>
<?
	if(isset($_GET['item'])){
		$results = mysql_query("select order_items.item, order_items.item_id, orders.id,  description,  datetime,status,  order_items.item_option, order_items.quantity, orders.site, site.document_base from order_items inner join orders on orders.id = order_items.order_id inner join site on site.id = orders.site where orders.status <> 'canceled' and account = '".$_SESSION['account_id']."' and order_items.item = '".urldecode($_GET['item'])."' order by datetime desc", $dbh);
		if(mysql_num_rows($results)){
			print "<div align=\"center\"><a href=\"".$_SERVER['PHP_SELF']."\">View all Items</a></div><br>";
		}
	}
	if(!isset($_GET['item']) || !mysql_num_rows($results)){
		//$results = mysql_query("select order_items.item, order_items.item_id, order_items.quantity, max(orders.id) as id, description, max(datetime) as datetime,status,  order_items.item_option, order_items.quantity, site.document_base from order_items inner join orders on orders.id = order_items.order_id inner join site on site.id = orders.site where orders.status <> 'canceled' and account = '".$_SESSION['account_id']."'  group by order_items.item order by datetime desc");
		$results = mysql_query("select a.item, a.item_id, a.item_option, a.quantity, a.description, orders.id, orders.datetime, orders.site, orders.status, site.document_base FROM (SELECT i.item, i.description, i.item_id, i.item_option, i.quantity, max(i.order_id) as order_id FROM order_items i JOIN orders o ON o.id = i.order_id WHERE o.status != 'canceled' AND o.account = '".$_SESSION['account_id']."' GROUP BY item) as a JOIN orders ON orders.id = a.order_id JOIN site ON site.id = orders.site order by datetime desc");
	}
	if(mysql_num_rows($results)){ ?>
<table width="95%" align="center" cellpadding="2" cellspacing="0">
<tr class="bar"><td align="center" nowrap="nowrap">Order ID</td><td align="center">Date</td><td align="center">Item</td><td align="left">Description</td><td align="left">Status</td><td align="left">Reorder</td></tr>
<?
		$i=0;
		while($row = mysql_fetch_assoc($results)){
?>
<tr<? if($i%2) print " style=\"background-color:#e6e6e6;\""; ?>><td align="center" valign="top"><a href="order.php?<?=$row['id']?>"><?=$row['id']?></a></td><td align="center" valign="top"><?=date("n/d/Y",$row['datetime'])?></td><td align="center" nowrap="nowrap" valign="top"><a href="<?=$_SERVER['PHP_SELF']."?item=".urlencode($row['item']) ?>">View All</a></td><td align="left" valign="top"><?=$row['description']?><? if($row['quantity']) print "<i>(QTY: ".$row['quantity'].")</i>"; ?></td><td align="left"><?=ucwords($row['status'])?></td><td align="center" valign="top"><? if(preg_match("/^Iscribe/i",$row['item'])){ ?><form><input type="button" value="Reorder" onclick="location.href='https://www.databusinesssystems.com/medical-forms/product/118/';" style="font-size:10px;"></form><? }elseif($row['item_id'] > 0){ ?><form method="post" action="https://www.databusinesssystems.com<?=$row['document_base']?>/cart/" style="margin:0;"><input type="hidden" name="option" value="<?=$row['item_option']?>"><input type="hidden" name="quantity" value="<?=$row['quantity']?>"><input type="hidden" name="product" value="<?=$row['item_id']?>"><input type="submit" value="Reorder" style="font-size:10px;"></form><? }else{ ?>Not Available<? } ?></td></tr>
<?
			$i++;
		}
?>
</table>
<?
	}else{
		print "You have no items in your order history.";
	}
?>
<? include("../inc/footer.inc"); ?>

</body>
</html>
