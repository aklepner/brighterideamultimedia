<?
	require_once("../inc/ad_track.inc");
	require_once("inc/config.inc");
	
	$results = mysql_query("select * from product_category where id = '".$_GET['id']."' and site = '".SITE."' limit 1", $dbh);
	
	if(mysql_num_rows($results)){
		$category = mysql_fetch_assoc($results);
	}else{
		header("HTTP/1.0 404 Not Found");
		exit;
	}
?>
<html>
<head>
	<title><?=$category['name']?> - One-Write</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script language="JavaScript" src="<?=DOCUMENT_BASE?>/viewphoto.js"></script>
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
	include("../inc/product_page.inc");
	include("inc/footer.inc");
?>
</body>
</html>
