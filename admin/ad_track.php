<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>dbs - ad track</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
$db = new dbi();
$db->query("select * from ad_track order by timestamp desc limit 0,25");
?>
<table width="98%" align="center" cellpadding="3" cellspacing="0">
<tr class="bar"><td>Date/Time</td><td>Campaign</td><td>IP/HOST</td></tr>
<? while($db->loop()){ ?>
<tr bgcolor="#<?=(($db->currentrow()%2)?"DDDDDD":"FFFFFF")?>"><td valign="top"><?=date("n/d/Y",strtotime($db->result("timestamp")))?> <?=date("h:i a",strtotime($db->result("timestamp")))?></td><td style="font-weight:bold;"><?=$db->result("campaign")?></td><td><?=gethostbyaddr($db->result("ip"))?></td></tr>
<tr bgcolor="#<?=(($db->currentrow()%2)?"DDDDDD":"FFFFFF")?>"><td colspan="3" style="font-weight:bold;">File: <?=$db->result("server_name").$db->result("file")?></td>
<tr bgcolor="#<?=(($db->currentrow()%2)?"DDDDDD":"FFFFFF")?>"><td colspan="3" align="left">Referer: <?=$db->result("referer")?></td></tr>
<tr bgcolor="#<?=(($db->currentrow()%2)?"DDDDDD":"FFFFFF")?>"><td colspan="3" align="left">User Agent: <?=$db->result("user_agent")?></td></tr>
<? } ?>
</table>
<?
include("footer.inc");
?>

</body>
</html>