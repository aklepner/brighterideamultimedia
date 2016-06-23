<?
require_once("config.inc");
if(isset($_GET['ad']) && $_GET['ad'] != ""){
	// Track Ad Hit
	mysql_query("insert into ad_track (campaign, timestamp, ip, file, referer, server_name, user_agent) values ('".$_GET['ad']."', now(), '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['REQUEST_URI']."', '".$_SERVER['HTTP_REFERER']."', '".$_SERVER['SERVER_NAME']."', '".$_SERVER['HTTP_USER_AGENT']."')", $dbh);
	mysql_query("delete from ad_track where timestamp < DATE_ADD(now(), INTERVAL -14 DAY)", $dbh);
}
?>
