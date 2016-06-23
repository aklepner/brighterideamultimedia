<?
	require_once("/usr/www/users/sysice/clients/dbs/inc/config.inc");
	// Base Directory
	if(preg_match("/^(www\.)?print-forms.com$/",$_SERVER['SERVER_NAME']))
		define("DOCUMENT_BASE","");
	else
		define("DOCUMENT_BASE","/print-forms");
	// Site
	define("SITE",1);
	define("SITE_NAME","Print-Forms");
	// SSL Base
	define("SSL_BASE","https://www.databusinesssystems.com/print-forms");
?>
