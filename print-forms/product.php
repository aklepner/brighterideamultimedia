<?
	require_once("../inc/ad_track.inc");
	require_once("inc/config.inc");
	$seo__page_title = '';
	$seo__meta_kws = '';
	$seo__meta_desc = '';
	$seo__url_fname = '';
	$seo__url_dname = '';
	
	$results = mysql_query("select * from product_category where id = '".$_GET['id']."' and site = '".SITE."' limit 1", $dbh);
	
	if(mysql_num_rows($results)){
		$category = mysql_fetch_assoc($results);
		
		$name = $category['name'];
		$seo__page_title = $category['page_title'];
		$seo__meta_kws = $category['meta_keywords'];
		$seo__meta_desc = $category['meta_description'];
		$seo__url_fname = $category['url_filename'];
		$seo__url_dname = $category['url_foldername'];
	}else{
		header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
		include("404.php");
		exit;
	}
	
	if(!$_GET['old']){
		$newURL = DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'product', $name, $seo__url_fname, $seo__url_dname);
		
		//Redirect to new URL
		$newURL = ((DOCUMENT_BASE == '') ? 'http://www.print-forms.com' : 'http://www.databusinesssystems.com') . $newURL;
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
	}
?>
<html>
<head>
	<title><?=$category['name']?> - Print Forms</title>
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