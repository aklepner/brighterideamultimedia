<?
	if(isset($_GET['setsite'])){
		setcookie("site_admin",$_GET['setsite'],time()+(3600*24*30),"/");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>dbs - administration</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");

	
print "<table cellpadding=\"4\" cellspacing=\"0\" border=\"0\" align=\"center\">";
print "<tr><td colspan=\"5\" align=\"center\" style=\"font-weight:bold;font-size:14px;\">New Orders</td></tr>";
print "<tr class=\"bar\"><td style=\"font-weight:bold\">Order #</td><td style=\"font-weight:bold\">Name</td><td style=\"font-weight:bold\">Email</td><td style=\"font-weight:bold;\">Response</td><td style=\"font-weight:bold\" align=\"right\">Date/Time</td></tr>";
$results = mysql_query("select orders.id, orders.datetime, orders.payment_method, order_address.name, account.email from orders inner join order_address on order_address.id = orders.bill_address inner join account on account.id = orders.account  where orders.status = 'new' order by orders.datetime desc", $dbh);
if(mysql_num_rows($results)){
	$i = 1;
	while($row = mysql_fetch_assoc($results)){
		print "<tr class=\"row".($i%2?1:2)."\"><td><a href=\"order.php?id=".$row['id']."\">".$row['id']."</a></td><td>".$row['name']."</td><td>".$row['email']."</td>";
		if($row['payment_method'] == 'cc'){
			$pay = mysql_query("select response from cc_charges where order_id = '".$row['id']."' order by cc_charges.datetime desc limit 1", $dbh);
			if(mysql_num_rows($pay)){
				$response = mysql_result($pay, 0, "response");
				if($response == 1)
					print "<td align=\"center\" class=\"success\">Approved</td>";
				elseif($response == 2)
					print "<td align=\"center\" class=\"error\">Declined</td>";
				elseif($response == 3)
					print "<td align=\"center\" class=\"error\">Error</td>";
			}else{
				print "<td></td>";
			}
		}else{
			print "<td align=\"center\" class=\"success\">Purchase Order</td>";
		}
		print "<td align=\"right\">".date("n/j/y g:i a",$row['datetime'])."</td></tr>";
		$i++;
	}
}else{
	print "<tr><td align=\"center\" colspan=\"5\">All orders have been processed.</td></tr>";
}
print "<tr class=\"bar\"><td colspan=\"5\" align=\"right\">Total New Orders: ".mysql_num_rows($results)."</td></tr>";
print "</table>";

include("footer.inc");
?>
</body>
</html>
