<? 
include("inc/config.inc"); 
$results = mysql_query("select * from sample_category where site = '".SITE."' and id = '".$_GET['id']."' limit 1", $dbh);
if(mysql_num_rows($results)){
	$category = mysql_fetch_assoc($results);
}else{
	header("HTTP/1.1 404 Not Found");
	include("404.php");
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
	include("../inc/dbi.inc");
	include("inc/header.inc");
	include("../inc/sample_page.inc");
	include("inc/footer.inc");
?>
</body>
</html>