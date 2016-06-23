<?
	require_once("ad_track.inc");
	include("inc/config.inc");
	$seo__page_title = '';
	$seo__meta_kws = '';
	$seo__meta_desc = '';
	$seo__url_fname = '';
	$seo__url_dname = '';
	
	$results = mysql_query("select * from link where id = '".$_GET['id']."' and site = '".SITE."'", $dbh);
	if(mysql_num_rows($results)){
		$link = mysql_fetch_assoc($results);
		if($link['samewindow'] == 'y'){
			header("Location: ".$link['url']);
			exit;
		}
		
		$name = $link['name'];
		$seo__page_title = $link['page_title'];
		$seo__meta_kws = $link['meta_keywords'];
		$seo__meta_desc = $link['meta_description'];
		$seo__url_fname = $link['url_filename'];
		$seo__url_dname = $link['url_foldername'];
	}else{
		header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
		include("404.php");
		exit;
	}
	
	$newURL = DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'link', $name, $seo__url_fname, $seo__url_dname);
	
	//Redirect to new URL
	$newURL = ((DOCUMENT_BASE == '') ? 'http://www.medical-forms.com' : 'http://www.databusinesssystems.com') . $newURL;
	header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
	header('Location: ' . $newURL);
	// Output Standard HTML note of redirect.
	print '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\n";
	print '<html><head>' . "\n";
	print '<title>301 Moved Permanently</title>' . "\n";
	print '</head><body>' . "\n";
	print '<h1>Moved Permanently</h1>' . "\n";
	print '<p>The document has moved <a href="' . $newURL . '">here</a></p>' . "\n";
	print '</body></html>';
	exit();

?>
<html>
<head>
	<title><?=$link['name']?> -  Medical Forms</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script type="text/javascript" src="<?=DOCUMENT_BASE?>/menu.js"></script>
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
<? 
	if(SITE_NAME == 'Medical-Forms')
	{
		print '<center><a href="' . DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'link', $name, $seo__url_fname, $seo__url_dname) . '" title="' . str_replace('"', '&quot;', $name) . '" style="text-decoration:none;font-weight:normal;">';
		print $name;
		print '</a></center>';
	}
?>

</body>
</html>