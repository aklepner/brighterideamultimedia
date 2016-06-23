#!/usr/local/bin/php
<?
include(dirname(__FILE__)."/../inc/config.inc");
mysql_query("UPDATE cc_charges JOIN orders ON orders.id = cc_charges.order_id SET card_number = NULL, card_code = NULL WHERE (orders.status = 'canceled' OR orders.status = 'processed') AND cc_charges.`datetime` < UNIX_TIMESTAMP()-(60*60*24*3)", $dbh);
?>
