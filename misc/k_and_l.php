#!/usr/local/bin/php
<?
require_once(dirname(__FILE__)."/../inc/config.inc");

$c_code = "";

while($c_code == ""){
	for($i=1;$i<=6;$i++)
		$c_code .= chr(rand(65,90));
	$results = mysql_query("select * from coupon where code = '$c_code'", $dbh);
	if(mysql_num_rows($results))
		$c_code = "";
}
	
mysql_query("update coupon set code = '$c_code' where id = '5'", $dbh);

$from = "DBS System <noreply@databusinesssystems.com>";
$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from";
$message = "The coupon for K&L has been successfully changed to:\n\n  $c_code\n\nPlease make the appropriate adjustments.";
mail("jk@databusinesssystems.com,linda@billerswebsite.com","[DBS] K&L Coupon",$message,$header);

?>
