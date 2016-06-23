<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - system information</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
$db = new dbi();

print "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" align=\"center\">";
print "<tr><td colspan=\"4\" align=\"center\" style=\"font-weight:bold;font-size:14px;\">Web Server Status</td></tr>";
print "<tr><td colspan=\"4\" align=\"center\">";
system("uptime");
print "</td></tr>";
print "<tr><td colspan=\"4\" align=\"center\" style=\"font-weight:bold;font-size:14px;\">Database Information</td></tr>";
$db->query("show status");
while($db->loop()){
	if($db->result("Variable_name") == "Threads_connected"){
		$threads_connected = $db->result("Value");
	}elseif($db->result("Variable_name") == "Threads_running"){
		$threads_running = $db->result("Value");
	}elseif($db->result("Variable_name") == "Uptime"){
		$uptime = $db->result("Value");
	}
}
$days = floor($uptime/(60*60*24));
$uptime = $uptime - $days*(60*60*24);
$hour = floor($uptime/(60*60));
$uptime = $uptime - $hour*60*60;
$min = floor($uptime/60);
$uptime = $uptime - $min*60;
$uptime = sprintf("%02d:%02d:%02d",$hour,$min,$uptime);
print "<tr><td colspan=\"2\" align=\"left\">Threads: $threads_running/$threads_connected</td><td colspan=\"2\" align=\"right\">Uptime: $days days, $uptime</td></tr>";
print "<tr class=\"bar\"><td style=\"font-weight:bold\">Name</td><td style=\"font-weight:bold\">Rows</td><td style=\"font-weight:bold\">Last Updated</td><td style=\"font-weight:bold\" align=\"right\">Size</td></tr>";
$total = $rows = 0;
$db->query("show table status");
$i = 1;
while($db->loop()){
	print "<tr class=\"row".($i%2?1:2)."\"><td>".$db->result("Name")."</td><td>".$db->result("Rows")."</td><td>".date("n/j/y g:i a",strtotime($db->result("Update_time")))."</td><td align=\"right\">".number_format((($db->result("Data_length")+$db->result("Index_length"))/1024),2)." KB</td></tr>";
	$total += $db->result("Data_length")+$db->result("Index_length");
	$rows += $db->result("Rows");
	$i++;
}
print "<tr class=\"bar\"><td></td><td>".number_format($rows,0)."</td><td align=\"right\">Total:</td><td align=\"right\">".number_format($total/1024,2)." KB</td></tr>";
print "</table>";
include("footer.inc");
?>
</body>
</html>
