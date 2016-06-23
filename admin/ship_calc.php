<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - administration</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
// 85.0,66701,97302
include("header.inc");
require("../inc/ups_xml_lib.inc");
$country = array("US" => "United States", "CA" => "Canada", "PR" => "Puerto Rico");
$ship_type['11'] = "Canada Standard";
$ship_type['XPR'] = "WorldWide";
?>

<form action="<?=$PHP_SELF?>" method="post">
<?
if($_POST['origin_zip'])
print "<div align=\"center\" style=\"font-weight:bold;\">Response: ".trim(getUPSQuote($_POST['service'],$_POST['weight'],$_POST['origin_zip'],$_POST['origin_country'],$_POST['destination_zip'],$_POST['destinition_country']))."</div>";
?>
<table align="center">
<tr><td>Origin Zip</td><td><input type="text" name="origin_zip" size="10" value="<?=$_POST['origin_zip']?>"> <select name="origin_country"><?
	foreach($country as $key => $val){
		print "<option value=\"$key\"";
		if(isset($_POST['origin_country']) && $_POST['origin_country'] == $key)
			print " selected=\"selected\"";
		print ">$val</option>";
	}
?></select></td></tr>
<tr><td>Destination Zip</td><td><input type="text" name="destination_zip" size="10" value="<?=$_POST['destination_zip']?>"> <select name="destinition_country"><?
	foreach($country as $key => $val){
		print "<option value=\"$key\"";
		if(isset($_POST['destinition_country']) && $_POST['destinition_country'] == $key)
			print " selected=\"selected\"";
		print ">$val</option>";
	}
?></select></td></tr>
<tr><td>Weight</td><td><input type="text" name="weight" size="10" value="<?=$_POST['weight']?>"></td></tr>
<tr><td>Service</td><td><select name="service">
<?
	foreach($ship_type as $key => $val){
		print "<option value=\"$key\"";
		if(isset($_POST['service']) && $_POST['service'] == $key)
			print " selected=\"selected\"";
		print ">$val</option>";
	}
?>
</select></td></tr>
<tr><td colspan="2" align="center"><input type="submit"></td></tr>
</table>
</form>


<?

include("footer.inc");

?>
</body>
</html>
