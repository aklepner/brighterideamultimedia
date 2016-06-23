#!/usr/local/bin/php
<?
require_once(dirname(__FILE__)."/../inc/config.inc");
// Get Order Items that need reminders
$results = mysql_query("select account.id, email, description, reminder, name, company from order_items inner join orders on orders.id = order_items.order_id inner join account on account.id = orders.account inner join order_address on order_address.id = orders.bill_address where reminder <= now() and reminder_sent IS NULL order by reminder asc", $dbh)
	or die("Failed Query: ".mysql_error());

if(mysql_num_rows($results)){
	$r = array();
	while($row = mysql_fetch_assoc($results)){
		$r[$row['id']][] = array("name" => $row['name'], "company" => $row['company'], "email" => $row['email'], "reminder" => date("m/d/Y",strtotime($row['reminder'])), "description" => $row['description']);
	}
}

// Make sure some items have been selected
if(sizeof($r)){
	$from = "\"Data Business Systems, Inc.\" <info@databusinesssystems.com>";
	$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from\r\n";

	$summary = "The following reminders have been sent out:\n\n";

	foreach($r as $x){
		$message = "Dear ".$x[0]['name'];

		$summary .= $x[0]['name'];
		
		if($x[0]['company'] != "")
			$summary .= " of ".$x[0]['company'];
			
		$summary .= " <".$x[0]['email'].">\n";
		
		$message .= ":\n\nAccording to our records the following item(s) may need to be reordered:\n\n";
		foreach($x as $i){
			$message .= "\t".$i['reminder']." - ".strip_tags($i['description'])."\n";
			$summary .= "\t".$i['reminder']." - ".strip_tags($i['description'])."\n";
		}
		$summary .= "\n";
		$message .= "\nYou can visit your account area at the following location to reorder them:\n\n";
		$message .= "\thttps://www.databusinesssystems.com/account/\n\n";
		$message .= "If you have any questions please feel free to email or call us.\n\n";
		$message .= "Note: If you have already placed your reorder on this item, please disregard this reminder.\n\n";
		$message .= "Sincerely,\n\nCustomer Service\nData Business Systems\ninfo@databusinesssystems.com\n(800) 778-6247\n";
		// Send Email to Customer
		mail($x[0]['email'], "Reorder Reminder from Data Business Systems", $message, $header);
	}
	// Send Summary Email to Admin
	mail("jk@databusinesssystems.com", "[DBS] Reminder Summary", $summary, "Return-Path: $from\r\nFrom: $from\r\n");

	$results = mysql_query("update order_items set reminder_sent = now() where reminder <= now() and reminder_sent IS NULL", $dbh) 
		or die("Failed Query: ".mysql_error());
}

if($dbh) mysql_close($dbh);
?>
