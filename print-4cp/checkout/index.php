<?
	require_once("../inc/config.inc");
	if($_SERVER["HTTPS"] != 'on'){
		header("Location: ".SSL_BASE.DOCUMENT_BASE."/cart/");
		exit;
	}
	session_start();
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time()));
	header("Pragma: no-cache");
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()-3600));
	header("Cache-Control: no-cache");
	require("../../inc/checkout_process.inc");
?>
<html>
<head>
	<title>Checkout - Print-4cp</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
</head>

<body>
<?
	include("../inc/header.inc");
	include("../../inc/checkout.inc");
	include("../inc/footer.inc");
?>
</body>
</html>
