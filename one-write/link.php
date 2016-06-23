<?
	require_once("ad_track.inc");
	include("inc/config.inc");
	$results = mysql_query("select * from link where id = '".$_GET['id']."' and site = '".SITE."'", $dbh);
	if(mysql_num_rows($results)){
		$link = mysql_fetch_assoc($results);
		if($link['samewindow'] == 'y'){
			header("Location: ".$link['url']);
			exit;
		}
	}else{
		header("HTTP/1.1 404 Not Found");
		include("404.php");
		exit;
	}
?>
<html>
<head>
	<title><?=$link['name']?> - One-Write</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
?>
<br>
<div align="center"><iframe width="95%" height="400" src="<?=$link['url']?>" name="extlink" class="iframe" frameborder="1"></iframe></div>
<?
	include("inc/footer.inc");
?>


</body>
</html>
