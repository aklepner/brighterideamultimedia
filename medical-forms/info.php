<?
	require_once("/home/bocawebgroup/public_html/inc/ad_track.inc");
	require_once("inc/config.inc");
	require_once("../inc/dbi.inc");
	$seo__page_title = '';
	$seo__meta_kws = '';
	$seo__meta_desc = '';
	$seo__url_fname = '';
	$seo__url_dname = '';

	if(isset($_POST['name']) && isset($_POST['data'])){
		$name = $_POST['name'];
		$date = $_POST['data'];
	}else{
		$db = new dbi();

		$db->query("select * from info where id = '".$_GET[id]."' and site = '".SITE."'");
		if($db->numrows()){
			$name = $db->result("info.name");
			$data = $db->result("info.data");

			$seo__page_title = $db->result("info.page_title");
			$seo__meta_kws = $db->result("info.meta_keywords");
			$seo__meta_desc = $db->result("info.meta_description");
			$seo__url_fname = $db->result("info.url_filename");
			$seo__url_dname = $db->result("info.url_foldername");
		}else{
			header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
			include("404.php");
			exit;
		}
	}

	$newURL = DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'info', $name, $seo__url_fname, $seo__url_dname);

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
	<title>Medical Forms - <?=$name?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script language="JavaScript" src="<?=DOCUMENT_BASE?>/viewphoto.js"></script>
	<script type="text/javascript" src="<?=DOCUMENT_BASE?>/menu.js"></script>
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
	include("../inc/info_page.inc");
	include("inc/footer.inc");
?>



</body>
</html>
