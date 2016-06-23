<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>dbs - report</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
if(isset($_GET['start_date'])){
	if(strtotime($_GET['start_date']) == -1){
		$error = "Invalid Start Date!";
	}elseif(strtotime($_GET['end_date']) == -1){
		$error = "Invalid End Date!";
	}else{
		if($_GET['type'] == "iscribe"){
			$query = "select orders.id, orders.account,order_items.quantity,order_address.name, order_address.address1, order_address.address2, order_address.city, order_address.state, order_address.zip_code, order_items.item, order_items.description, order_items.price, order_items.discount, order_items.ship_cost from order_items inner join orders on orders.id = order_items.order_id inner join order_address on order_address.id = orders.ship_address where orders.datetime >= '".strtotime($_GET['start_date'])."'";
			if($_GET['end_date'] != "")
				$query .= " and orders.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
			if($_GET['status'] != "")
				$query .= " and orders.status = '".$_GET['status']."'";
			$query .= " AND (order_items.item IN ('Iscribe', 'IscribeNJ', 'IscribeOutsideNJ') or order_items.item_id = '491') ORDER BY orders.datetime DESC";
			# echo $query;
		}elseif($_GET['type'] == "allscripts"){
			$query = "select orders.id, orders.account,order_items.quantity,order_address.name, order_address.address1, order_address.address2, order_address.city, order_address.state, order_address.zip_code, order_items.item, order_items.description, order_items.price, order_items.discount, order_items.ship_cost from order_items inner join orders on orders.id = order_items.order_id inner join order_address on order_address.id = orders.ship_address where orders.datetime >= '".strtotime($_GET['start_date'])."'";
			if($_GET['end_date'] != "")
				$query .= " and orders.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
			if($_GET['status'] != "")
				$query .= " and orders.status = '".$_GET['status']."'";
			$query .= " AND order_items.item IN ('EP-1UP', 'EP-4UP', 'TW-1UP') ORDER BY orders.datetime DESC";
		}elseif($_GET['type'] == "mailing"){
			if(trim($_GET['skus']) != ''){
				$skus = explode(",",$_GET['skus']);
				foreach($skus as $idx => $val)
					$skus[$idx] = "'".trim($val)."'";
			
				$query = "SELECT oa.name, oa.address1, oa.address2, oa.city, oa.state, oa.zip_code, a.email, FROM_UNIXTIME(o.datetime) as created, oi.item, oi.description FROM order_items oi JOIN orders o ON o.id = oi.order_id JOIN order_address oa ON oa.id = o.ship_address JOIN account a ON a.id = o.account WHERE o.datetime >= '".strtotime($_GET['start_date'])."' AND oi.item IN (".join(", ",$skus).")";
				if($_GET['end_date'] != "")
					$query .= " and o.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
				if($_GET['site'] != '')
					$query .= " AND o.site = '".$_GET['site']."'";
				if($_GET['status'] != "")
					$query .= " and o.status = '".$_GET['status']."'";
				else
					$query .= " and o.status != 'canceled'";
				$query .= " GROUP BY a.email";
			}else{ # Get Everyone
				$query = "SELECT oa.name, oa.address1, oa.address2, oa.city, oa.state, oa.zip_code, a.email, FROM_UNIXTIME(o.datetime) as created FROM orders o JOIN order_address oa ON oa.id = o.ship_address JOIN account a ON a.id = o.account WHERE o.datetime >= '".strtotime($_GET['start_date'])."'";
				if($_GET['end_date'] != "")
					$query .= " and o.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
				if($_GET['site'] != '')
					$query .= " AND o.site = '".$_GET['site']."'";
				if($_GET['status'] != "")
					$query .= " and o.status = '".$_GET['status']."'";
				else
					$query .= " and o.status != 'canceled'";
				$query .= " GROUP BY a.email";

			}
		}elseif($_GET['type'] == "reminder"){
			$query = "select orders.id, order_items.reminder, order_address.name, account.email, site.name as site_name from orders inner join order_items on order_items.order_id = orders.id inner join order_address on order_address.id = orders.bill_address inner join account on account.id = orders.account inner join site on site.id = orders.site where order_items.reminder >= '".date("Y-m-d",strtotime($_GET['start_date']))."'";
			if($_GET['end_date'] != "")
				$query .= " and order_items.reminder <= '".date("Y-m-d",strtotime($_GET['end_date']." + 1 day"))."'";
			if($_GET[status] != "")
				$query .= " and orders.status = '".$_GET['status']."'";
			$query .= " order by site.name, order_items.reminder asc";
		}elseif($_GET['type'] == "monthly"){
			$query = "select sum(orders.total) as total, sum(oi.itemcount) as cnt, date_format(from_unixtime(datetime), '%Y-%m') as month, site.id, site.name from `orders` join (select order_id, count(id) as itemcount from `order_items` group by order_id) as oi on oi.order_id = orders.id join site on site.id = orders.site where datetime >= '".strtotime($_GET['start_date'])."'";
			if($_GET['end_date'] != "")
				$query .= " and orders.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
			if($_GET['status'] != "")
				$query .= " and orders.status = '".$_GET['status']."'";
			else
				$query .= " and orders.status != 'canceled'";
			$query .= " GROUP BY date_format(from_unixtime(datetime), '%Y-%m'), site.id order by month";
			// New Orders
			$querynew = "select sum(orders.total) as total, sum(oi.itemcount) as cnt, date_format(from_unixtime(datetime), '%Y-%m') as month, site.id, site.name from `orders` join (select order_id, count(id) as itemcount from `order_items` group by order_id) as oi on oi.order_id = orders.id join site on site.id = orders.site JOIN account AS a ON orders.account = a.id where datetime >= '".strtotime($_GET['start_date'])."' AND date_format(from_unixtime(orders.datetime), '%Y-%m-%d') = date_format(a.created, '%Y-%m-%d')";
			if($_GET['end_date'] != "")
				$querynew .= " and orders.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
			if($_GET['status'] != "")
				$querynew .= " and orders.status = '".$_GET['status']."'";
			else
				$querynew .= " and orders.status != 'canceled'";
			$querynew .= " GROUP BY date_format(from_unixtime(datetime), '%Y-%m'), site.id order by month";
		}elseif($_GET['type'] == "product"){
			$query = "select product.category, site.name as site_name, orders.site, product_category.name, count(product.category) as cnt, sum(order_items.price) as total from order_items join orders on order_items.order_id = orders.id join product on product.id = order_items.item_id join product_category on product_category.id = product.category join site on site.id = orders.site where orders.datetime >= '".strtotime($_GET['start_date'])."'";
			if($_GET['end_date'] != "")
				$query .= " and orders.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
			if($_GET['status'] != "")
				$query .= " and orders.status = '".$_GET['status']."'";
			else
				$query .= " and orders.status != 'canceled'";
			$query .= "group by product.category order by orders.site, product_category.name";
		}else{
			$query ="select orders.id, orders.datetime, orders.total, order_address.name, account.email, site.name as site_name from orders inner join order_address on order_address.id = orders.bill_address inner join account on account.id = orders.account inner join site on site.id = orders.site where orders.datetime >= '".strtotime($_GET['start_date'])."'";
			if($_GET[end_date] != "")
				$query .= " and orders.datetime <= '".strtotime($_GET['end_date']." + 1 day")."'";
			if($_GET[status] != "")
				$query .= " and orders.status = '".$_GET['status']."'";
			else
				$query .= " and orders.status != 'canceled'";
			$query .= " order by site.name, orders.datetime desc";
		}
		$results = mysql_query($query, $dbh);
		if(!mysql_num_rows($results))
			$error = "Report returned 0 records!";
	}
}
if(($_GET['type'] == 'iscribe' || $_GET['type'] == 'allscripts') && !$error){
	$fp = fopen('report.csv','w');
	fwrite($fp,"Batch Number,SO No.,DBS No.,Item Number,Item Description,QTY,Customer Name,Address 1,Address 2,Address 3,City,State,Zip Code,Retail,Freight\n");
	while($row = mysql_fetch_assoc($results)){
		fwrite($fp,"PAPER-".date("mdy",time()).",".$row['id'].",".$row['account'].",".$row['item'].",\"".trim($row['description'])."\",".$row['quantity'].",".preg_replace("/\,/","",$row['name']).",".$row['address1'].",".$row['address2'].",,".$row['city'].",".$row['state'].",".$row['zip_code'].",$".number_format($row['price']-$row['discount'],2).",$".number_format($row['ship_cost'],"2")."\n");
	}
	fclose($fp);
	echo '<p align="center"><a href="report.csv">Download CSV Report</a><br><br>A new report has been created.  You can click above to access it, or Right Click and choose Save As to save it to your computer.</p>';
}elseif($_GET['type'] == "mailing" && !$error){
	$output = "Name, Address1, Address2, City, State, Zip, Email, Created";
	if($skus)
		$output .= ", SKU, Description";
	$output .= "\n";
	while($row = mysql_fetch_assoc($results)){
		$i = 0;
		foreach($row as $idx => $val){
			$val = preg_replace("/(\r|\n)/", '', $val);
			if($i)
				$output .= ",";
			if($idx == "created")
				$output .= date("m/d/Y",strtotime($val));
			elseif(preg_match("/,/",$val))
				$output .= "\"".trim($val)."\"";
			else
				$output .= trim($val);
			$i++;
		}
		$output .= "\n";
	}
	$fp = fopen('report.csv','w');
	fwrite($fp,$output);
	fclose($fp);
	echo '<p align="center"><a href="report.csv">Download CSV Report</a><br><br>A new report has been created.  You can click above to access it, or Right Click and choose Save As to save it to your computer.</p>';
}elseif($_GET['type'] == "monthly" && !$error){
	// echo $query;
	print "<div align=\"center\" style=\"line-height:24px;\"><b>Monthly Report</b><br /><a href=\"report.php\">Create New Report</a><br />Top Number: All Orders - Bottom Number: New Account Orders</div><br />";
	$data = array();
	while($row = mysql_fetch_assoc($results)){
		$sites[$row['id']] = $row['name'];
		$data[$row['month']][$row['id']]['total'] = $row['total']; 
		$data[$row['month']][$row['id']]['cnt'] = $row['cnt'];
	}
	$results = mysql_query($querynew, $dbh);
	if(mysql_num_rows($results)){
		while($row = mysql_fetch_assoc($results)){
			$data[$row['month']][$row['id']]['totalnew'] = $row['total']; 
			$data[$row['month']][$row['id']]['cntnew'] = $row['cnt'];
		}
	}
	print "<table cellpadding=\"4\" cellspacing=\"0\" border=\"0\" align=\"center\" style=\"line-height:18px;\">";
	print "<tr class=\"bar\"><td></td>";
	foreach($sites as $id => $val){
		print "<td align=\"center\">".$val."</td>";
	}
	print "<td align=\"center\">Total</td></tr>";
	$total = $sub = 0;
	$x = 0;
	foreach($data as $month => $s){
		print "<tr class=\"row".($x%2?1:2)."\"><td><b>".date("F y", strtotime($month."-01"))."</b></td>";
		$subtotal = $subcnt = $subtotalnew = $subcntnew = 0;
		for($i=1;$i<=3;$i++){
			print "<td align=\"center\"><b>".$s[$i]['cnt']."</b> ";
			if($s[$i]['total'] > 0)
				print "$".number_format($s[$i]['total'],2);
			print "<br /><span style=\"color:#333;\">".$s[$i]['cntnew']." $".number_format($s[$i]['totalnew'],2)."</span></td>";
			$subcnt += $s[$i]['cnt'];
			$subcntnew += $s[$i]['cntnew'];
			$subtotal += $s[$i]['total'];
			$subtotalnew += $s[$i]['totalnew'];
		}
		print "<td align=\"center\"><b>".$subcnt."</b> $".number_format($subtotal,2)."<br />".$subcntnew." $".number_format($subtotalnew,2)."</td></tr>";
		$cnt += $subcnt;
		$cntnew += $subcntnew;
		$total += $subtotal;
		$totalnew += $subtotalnew;
		$x++;
	}
	print "<tr class=\"bar\"><td align=\"right\" colspan=\"".(sizeof($sites))."\">Total:</td><td align=\"right\">".$cnt."<br /><span style=\"font-weight:normal;\">".$cntnew."</span></td><td align=\"right\">$".number_format($total,2)."<br /><span style=\"font-weight:normal;\">$".number_format($totalnew,2)."</span></td></tr>";
	print "</table>";	
}elseif($_GET['type'] == "product" && !$error){
	print "<div align=\"center\" style=\"font-weight:bold\">Total Products Ordered from ".$_GET['start_date'];
	if($_GET['end_date'] != "")
		print " to ".$_GET['end_date']; 
	print "</div><br>";
	print "<div align=\"center\"><a href=\"report.php\">Create New Report</a></div><br>";
	print "<table cellpadding=\"4\" cellspacing=\"0\" border=\"0\" align=\"center\">";
	$site = $total = $subtotal = $subcnt = $count = 0;
	while($row = mysql_fetch_assoc($results)){
		if($site != $row['site']){
			if($site != 0){
				print "<tr><td style=\"padding-left:20px;\"><i>Subtotal</i></td><td><i>".$subcnt."</i></td><td style=\"text-align:right;\"><i>$".number_format($subtotal,2)."</i></td></tr>";
				$subtotal = $subcnt = 0;
			}
			print "<tr><td colspan=\"3\"><b>".$row['site_name']."</b></td></tr>";
			$site = $row['site'];
		}
		print "<tr><td style=\"padding-left:20px;\">".$row['name']."</td><td>".$row['cnt']."</td><td style=\"text-align:right;\">$".number_format($row['total'], 2)."</td></tr>";
		$subtotal += $row['total'];
		$total += $row['total'];
		$subcnt += $row['cnt'];
		$count += $row['cnt'];
	}
	print "<tr><td style=\"padding-left:20px;\"><i>Subtotal</i></td><td><i>".$subcnt."</i></td><td style=\"text-align:right;\"><i>$".number_format($subtotal,2)."</i></td></tr>";
	print "<tr><td><b>Total</b></td><td><b>".$count."</td><td style=\"text-align:right;\"><b>$".number_format($total, 2)."</b></td></tr>";
	print "</table>";
}elseif(isset($_GET['start_date']) && !$error){
	print "<div align=\"center\"><b>";
	if($_GET['type'] == "reminder")
		print "Reminder ";
	print "Report from ".$_GET['start_date'];
	if($_GET['end_date'] != "")
		print " to ".$_GET['end_date'];
	if($_GET['status'] != "")
		print " (".strtoupper($_GET['status']).")";
	print "</b></div>";
	print "<div align=\"center\" style=\"margin-bottom:20px;\"><a href=\"report.php\">Create New Report</a></div>";
	print "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" align=\"center\">";
	print "<tr class=\"bar\"><td style=\"font-weight:bold\">Order #</td><td style=\"font-weight:bold\">Name</td><td style=\"font-weight:bold\">Email</td>";
	if($_GET['type'] == "normal")
		print "<td style=\"font-weight:bold\">Total</td>";
	print "<td style=\"font-weight:bold\" align=\"right\">";
	if($_GET['type'] == "reminder")
		print "Reminder";
	else
		print "Date/Time";
	print "</td></tr>";
	$prev_id = 0;
	$site = "";
	$total = 0;
	while($row = mysql_fetch_assoc($results)){
		if($row['id'] != $prev_id){
			if($site != $row['site_name']){
				print "<tr><td colspan=\"4\" align=\"center\"><b>".$row['site_name']."</b></td></tr>";
				$site = $row['site_name'];
			}
			print "<tr><td><a href=\"order.php?id=".$row['id']."&".$_SERVER['QUERY_STRING']."\">".$row['id']."</a></td><td>".$row['name']."</td><td>".$row['email']."</td>";
			if($_GET['type'] == "normal"){
				print "<td align=\"right\">$".number_format($row['total'],2)."</td>";
				$total += $row['total'];
			}
			print "<td align=\"right\">";
			if($_GET['type'] == "reminder")
				print date("n/j/y",strtotime($row['reminder']));
			else
				print date("n/j/y g:i a",$row['datetime']);
			print "</td></tr>";
			if($_GET['type'] == "normal"){
				$items = mysql_query("select product_category.name, order_items.description, order_items.price from order_items left join product on product.id = order_items.item_id left join product_category on product_category.id = product.category where order_id = '".$row['id']."'", $dbh);
				if(mysql_num_rows($items)){
					while($i = mysql_fetch_assoc($items)){
						print "<tr><td></td><td colspan=\"2\" style=\"font-size:10px;\">";
						if($i['name'] == "")
							print $i['description'];
						else
							print $i['name'];
						print "</td><td style=\"font-size:10px;\" align=\"right\">$".number_format($i['price'],2)."</td><td></td></tr>";
					}
				}
			}	
			$prev_id = $row['id'];
		}
	}
	if($_GET['type'] == "normal")
		print "<tr class=\"bar\"><td colspan=\"3\" align=\"right\">Total:</td><td align=\"right\">$".number_format($total,2)."</td><td align=\"right\">Orders: ".mysql_num_rows($results)."</td></tr>";
	else
		print "<tr class=\"bar\"><td colspan=\"4\" align=\"right\">Total Orders: ".mysql_num_rows($results)."</td></tr>";
	print "</table>";
}else{
	if($error)
		print "<div align=\"center\" class=\"error\">$error</div>";
?>
	<form action="<?=$_SERVER[PHP_SELF]?>" method="get">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr class="bar"><td colspan="2">View Report</td></td></tr>
		<tr><td class="field_title">Start Date</td><td><input type="text" name="start_date" size="15" value="<? if(isset($_GET['start_date'])) print $_GET['start_date']; else print date("n/j/Y",mktime (0,0,0,date("m"),1, date("Y")));?>"></td></tr>
		<tr><td class="field_title">End Date</td><td><input type="text" name="end_date" size="15" value="<? if(isset($_GET['end_date'])) print $_GET['end_date']; ?>"></td></tr>
		<tr><td class="field_title">Status</td><td><select name="status"><?
			foreach(array("","new", "processed", "canceled") as $val){
				print "<option value=\"$val\"";
				if(isset($_GET[status]) && $_GET[status] == $val)
					print " selected=\"selected\"";
				print ">".strtoupper($val)."</option>";
			}
		?></select></td></tr>
		<tr><td class="field_title">Type</td><td><select name="type"><?
			foreach(array("normal" => "Normal", "iscribe" => "iScribe", "allscripts" => "Allscripts", "monthly" => "Monthly Totals", "product" => "Product Totals", "reminder" => "Reminder", "mailing" => "Mailing List") as $idx => $val){
				echo '<option value="',$idx,'"';
				if($_GET['type'] == $idx)
					echo ' selected="selected"';
				echo '>',$val,'</option>';
			}
		?></select></td></tr>
		<tr><td class="field_title">Site</td><td><select name="site"><option></option><?
		$results = mysql_query("select * from site", $dbh);
		while($row = mysql_fetch_assoc($results)){
			echo '<option value="',$row['id'],'">',$row['name'],'</option>';
		}
		?></select>
		<tr id="skus"><td class="field_title">SKU(s)</td><td><input type="text" name="skus" size="35" value="<? if(isset($_GET['skus'])) print $_GET['skus']; ?>"><br /><span style="font-size:0.8em;">Only used for Mailing List Report</span></td></tr>
		<tr class="bar"><td colspan="2" align="center"><input type="submit" value="View Report"></td></tr>
		</table>
	</form>
<?
}

include("footer.inc");
?>
</body>
</html>
