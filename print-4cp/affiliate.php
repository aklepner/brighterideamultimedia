<?
require("inc/config.inc");
require("../inc/affiliate_process.inc");

if(isset($name))
{
	$newURL = DOCUMENT_BASE . '/' . SEO_format_url($aff_id, 'affiliate', $name, $seo__url_fname, $seo__url_dname);
	
	//Redirect to new URL
	$newURL = ((DOCUMENT_BASE == '') ? 'http://www.print-4cp.com' : 'http://www.databusinesssystems.com') . $newURL;
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title><? if(isset($name)){ print $name; }else{ print "Affiliate"; } ?> - Print-4cp</title>
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