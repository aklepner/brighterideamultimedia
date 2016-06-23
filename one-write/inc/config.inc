<?
	require_once("/home/databiz/public_html/inc/config.inc");
	// Base Directory
	if(preg_match("/^(www\.)?one-write\.com$/",$_SERVER['SERVER_NAME']))
		define("DOCUMENT_BASE","");
	else
		define("DOCUMENT_BASE","/one-write");
	// Site
	define("SITE",3);
	define("SITE_NAME","One-Write");
	// SSL Base
	define("SSL_BASE","https://www.databusinesssystems.com/one-write");
?>
