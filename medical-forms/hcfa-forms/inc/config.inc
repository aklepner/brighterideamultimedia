<?
	require("/home/bocawebgroup/public_html/inc/config.inc");
	// Base Directory
	if(preg_match("/^(www\.)?(hcfa|medical)-forms.com$/",$_SERVER['SERVER_NAME']))
		define("DOCUMENT_BASE","");
	else
		define("DOCUMENT_BASE","/medical-forms");
	// Site
	define("SITE",2);
	define("SITE_NAME","Medical-Forms");
	// SSL Base
	define("SSL_BASE","https://www.databusinesssystems.com/medical-forms");
?>
