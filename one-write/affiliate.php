<?
require("inc/config.inc");
include("../inc/dbi.inc");
include("../inc/affiliate_process.inc");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>One-Write - Affiliate</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script language="JavaScript" src="<?=DOCUMENT_BASE?>/viewphoto.js"></script>
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
	include("../inc/affiliate_display.inc");
	include("inc/footer.inc");
?>
</body>
</html>
