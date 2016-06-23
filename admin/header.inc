<?
	require_once("../inc/config.inc"); 
	include("../inc/dbi.inc");
	if(isset($_GET['setsite']))
		$results = mysql_query("select * from site where id = '".$_GET['setsite']."'");
	elseif(isset($_COOKIE['site_admin']))
		$results = mysql_query("select * from site where id = '".$_COOKIE['site_admin']."'");
	else
		$results = mysql_query("select * from site where id = '1'");
		
	$row = mysql_fetch_assoc($results);
	define("SITE",$row['id']);
	define("DOCUMENT_BASE",$row['document_base']);
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" style="background-color:#339999;">
<tr><td><a href="/admin/" style="text-decoration:none;font-size:24px;font-family:Century Gothic;color:#990000;font-weight:bold;margin:5px 0px 5px 20px;">data business systems, inc.</a></td><td align="right"><div style="margin-right:10px;font-size:10px;font-weight:bold;color:#FFF;"><?=date("F j, Y",time())?><br />Welcome <?=$_SERVER['REMOTE_USER']?>!<br /><a href="password.php" style="color:white;">password</a> | <a href="http://www.databusinesssystems.com" style="color:#FFF;">log out</a></div></td></tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="#339999">
<tr><td bgcolor="#339999" width="160" valign="top">
<a href="affiliate.php" class="menu">Affiliates</a><br><br>
<a href="bottom_link.php" class="menu">Bottom Links</a><br><br>
<a href="coupon.php" class="menu">Coupons</a><br><br>
<a href="customer.php" class="menu">Customer</a><br><br>
<a href="info.php" class="menu">Information</a><br><br>
<a href="links.php" class="menu">Links</a><br><br>
<a href="menu.php" class="menu">Menu</a><br><br>
<a href="product_category.php" class="menu">Products</a><br><br>
<a href="report.php" class="menu">Reports</a><br><br>
<a href="sample_category.php" class="menu">Samples</a><br><br>
<!-- <a href="state_tax.php" class="menu">State Taxes</a><br><br> -->
<a href="warehouse.php" class="menu">Warehouses</a><br><br>
<br><div align="center" style="font-weight:bold;color:white;">Site:<br><select name="site" onChange="location.href='<?
	if($_SERVER['PHP_SELF'] == "/admin/product.php" && !isset($_GET['setsite'])){
		print "product.php?";  
		if($_SERVER['QUERY_STRING'] != "") 
			print $_SERVER['QUERY_STRING']."&"; 
	}else 
		print "index.php?"; ?>setsite='+this.value;"><?
	$results = mysql_query("select * from site", $dbh);
	while($row = mysql_fetch_assoc($results)){
		print "<option value=\"".$row['id']."\"";
		if($row['id'] == SITE)
			print " selected=\"selected\"";
		print ">".$row['name']."</option>";
	}
?></select></div><br>
<a href="ad_track.php" class="menu">Ad Track</a><br>
<a href="ship_calc.php" class="menu">Shipping Calc</a><br>
<a href="system.php" class="menu">System Info</a><br>
<a href="/stats/" class="menu">Statistics</a><br>
<br>
</td><td valign="top" bgcolor="#FFFFFF">
<? if(!preg_match("/^\/admin\/customer\.php/",$_SERVER['REQUEST_URI'])) print "<br>"; ?>
