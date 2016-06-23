<?
	require_once("/home/databiz/public_html/inc/config.inc");
	define('USE_SEO_URL_REWRITES', true);
	// Base Directory
	if(preg_match("/^(www\.)?print-4cp.com$/",$_SERVER['SERVER_NAME']))
		define("DOCUMENT_BASE","");
	else
		define("DOCUMENT_BASE","/print-4cp");
	// Site
	define('SITE',4);
	define("SITE_NAME","Print-4cp");
	// SSL Base
	define("SSL_BASE","https://www.databusinesssystems.com/print-4cp");
?>