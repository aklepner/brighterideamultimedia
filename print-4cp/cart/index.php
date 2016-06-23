<?
	require_once("../inc/config.inc");
	require_once("../../inc/dbi.inc");
	require_once("../../inc/cart_process.inc");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Shopping Cart - Print-4cp</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
</head>

<body>
<?
	include("../inc/header.inc");
	require_once("../../inc/cart_display.inc");
	include("../inc/footer.inc");
?>
</body>
</html>
