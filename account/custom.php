<?
	session_start();
	if($_SERVER["HTTPS"] != 'on' || $_SESSION[account_id] < 1){
		header("Location: https://www.databusinesssystems.com/account/");
		exit;
	}
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time()));
	header("Pragma: no-cache");
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()-3600));
	header("Cache-Control: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
   
<html>
<head>
	<title>Data Business Systems - Change Account Information</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
	<script language="JavaScript" src="../viewphoto.js"></script>
</head>

<body>
<?
	require_once("../inc/dbi.inc");
	include("../inc/header.inc");
?>
<h1>Custom Products</h1>
<div align="center" width="95%" style="text-align:right;"><a href="index.php">Back to Your Account</a></div><br>
<?
	include("../inc/product.inc");
	if(!print_product_cart(0,$_SESSION['account_id']))
		print "<div align=\"center\"><b>No Custom Products Avaiable</b></div>";
	include("../inc/footer.inc");
?>
</body>
</html>