<? session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>Contact Data Business Systems</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? include("inc/header.inc"); ?>
<h1>Contact Data Business Systems</h1>
<? 
$states = array('','AK','AL','AR','AZ','CA','CO','CT','DC','DE','FL','GA','HI','IA','ID','IL','IN','KS','KY','LA','MA','MD','ME','MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VA','VT','WA','WI','WV','WY');
if(isset($_POST['first_name'])){
	foreach($_POST as $idx => $val){
		if(preg_match("/(content\-type|multipart\/message)/i", $val)){
			$error = "Invalid Content!";
			break;
		}else{
			$_POST[$idx] = trim(strip_tags($val));
		}
	}
	if($_POST['first_name'] == "")
		$error = "Must specify a First Name!";
	elseif($_POST['last_name'] == "")
		$error = "Must specify a Last Name!";
	elseif($_POST['company'] == "")
		$error = "Must specify a Company!";
	elseif($_POST['street'] == "")
		$error = "Must specify a Street!";
	elseif($_POST['city'] == "")
		$error = "Must specify a City!";
	elseif($_POST['state'] == "")
		$error = "Must specify a State!";
	elseif($_POST['zipcode'] == "")
		$error = "Must specify a Zip Code!";
	elseif($_POST['phone'] == "")
		$error = "Must specify a Phone!";
	elseif($_POST['email'] == "" || !preg_match("/^[A-z0-9\-_\.]+\@[A-z0-9\-_\.]+\.[A-z]+$/",$_POST['email']))
		$error = "Must specify a Valid Email!";

   	if(!isset($error) && (strtolower($_SESSION['security_code']) != strtolower($_POST['security_code']) || empty($_SESSION['security_code']))){
		$error = "Invalid Security Code! ";
   	}
	if(!isset($error)){
		$from = preg_replace("/(\n|\r)/", "", $_POST['first_name']." ".$_POST['last_name']." <".$_POST['email'].">");
		$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from";

		$message  = "First Name.....: $_POST[first_name]\n";
		$message .= "Last  Name.....: $_POST[last_name]\n";
		$message .= "Title..........: $_POST[title]\n";
		$message .= "Company........: $_POST[company]\n";
		$message .= "\nMailing Address\n";
		$message .= "Street.........: $_POST[street]\n";
		$message .= "City...........: $_POST[city]\n";
		$message .= "State..........: $_POST[state]\n";
		$message .= "Zip Code.......: $_POST[zipcode]\n";
		$message .= "\nShipping Address\n";
		if($_POST[ship_street] == "")
			$message .= "Street.........: $_POST[street]\n";
		else
			$message .= "Street.........: $_POST[ship_street]\n";
		if($_POST[ship_city] == "")
			$message .= "City...........: $_POST[city]\n";
		else
			$message .= "City...........: $_POST[ship_city]\n";
		if($_POST[ship_state] == "")
			$message .= "State..........: $_POST[state]\n";
		else
			$message .= "State..........: $_POST[ship_state]\n";
		if($_POST[ship_zip] == "")
			$message .= "Zip Code.......: $_POST[zipcode]\n";
		else
			$message .= "Zip Code.......: $_POST[ship_zipcode]\n";
		
		$message .= "\nContact Info\n";
		$message .= "Phone..........: $_POST[phone]\n";
		$message .= "Fax............: $_POST[fax]\n";
		$message .= "Email..........: $_POST[email]\n";
		if($_POST[web_url] != "")
			$message .= "Web Site.......: $_POST[website]\n";
		if($_POST[message] != ""){
			$message .= "Message........:\n";
		$message .= $_POST[message];
		}
		$message .= "\n-- \n\nHost.....: ".gethostbyaddr($_SERVER[REMOTE_ADDR])."\n";
		$message .= "Agent....: $_SERVER[HTTP_USER_AGENT]\n";

		mail("jk@databusinesssystems.com","DBS Web Contact",$message,$header);
	print "<p align=\"center\">Thank you for your interest in Data Business Systems.<br />One of our representitives will contact you shortly.</p>";
	}
}
if(!isset($_POST['first_name']) || isset($error)){

if(isset($error))
	echo "<div style=\"text-align:center;font-size:14px;margin:0 auto 20px auto;color:red;\"><b>",$error,"</div>";
?>

<form method="POST" action="<?=$_SERVER['PHP_SELF']?>"  name="contact">
<table border="0" align="center" cellpadding="2" cellspacing="0">
<tr><td colspan="2" class="bar">Fields with an * are required</td></tr>
<tr><td>First name *</td><td><input type="text" name="first_name" size="40" value="<?=$_POST['first_name']?>" /></td></tr>
<tr><td>Last name *</td><td><input type="text" name="last_name" size="40" value="<?=$_POST['last_name']?>" /></td></tr>
<tr><td>Title</td><td><input type="text" name="title" size="40" value="<?=$_POST['title']?>" /></td></tr>
<tr><td>Company*</td><td><input type="text" name="company" size="40" value="<?=$_POST['company']?>" /></td></tr>
<tr><td colspan="2" class="bar">Mailing Address</td></tr>
<td>Street*</td><td><input type="text" name="street" size="40" value="<?=$_POST['street']?>" /></td></tr>
<tr><td>City*</td><td><input type="text" name="city" size="40" value="<?=$_POST['city']?>" /></td></tr>
<tr><td>State*</td><td><select name="state" ><?
	foreach($states as $s){
		print "<option value=\"$s\"";
		if(isset($error) && $_POST['state'] == $s)
			print " selected=\"selected\"";
		print ">$s</option>";
	}
?></select>&nbsp;&nbsp;&nbsp;&nbsp;Zip Code*&nbsp;&nbsp;<input name="zipcode" size="10" value="<?=$_POST['zipcode']?>"></td></tr>

<tr><td colspan="2" class="bar">Shipping Address - if different from Mailing Address</td></tr>
<tr><td>Street</td><td><input name="ship_street" size="40" value="<?=$_POST['ship_street']?>" /></td></tr>
<tr><td>City</td><td><input name="ship_city" size="40" value="<?=$_POST['ship_city']?>" /></td></tr>
<tr><td>State</td><td><select name="ship_state" ><?
	foreach($states as $s){
		print "<option value=\"$s\"";
		if(isset($error) && $_POST['ship_state'] == $s)
			print " selected=\"selected\"";
		print ">$s</option>";
	}
?></select>&nbsp;&nbsp;&nbsp;&nbsp;Zip Code&nbsp;&nbsp;<input name="ship_zipcode" size="10" value="<?=$_POST['ship_zipcode']?>" /></td></tr>
<tr><td colspan="2" class="bar">Contact Info</td></tr>
<tr><td>Phone*</td><td><input name="phone" size="40" value="<?=$_POST['phone']?>" /></td></tr>
<tr><td>Fax</td><td><input name="fax" size="40" value="<?=$_POST['fax']?>" /></td></tr>
<tr><td>E-Mail Address*</td><td><input name="email" size="40" value="<?=$_POST['email']?>"></td></tr>
<tr><td>Website Address</td><td><input name="web_url" size="40" value="<?=$_POST['web_url']?>"></td></tr>
<tr><td></td></tr>
<tr><td>I'm interested in...<br />Please provide as many<br /> details as possible.</td><td><textarea name="message" rows="10" cols="40"><?=$_POST['message']?></textarea></td>
<tr><td>Security Code</td><td>
<table>
	<tr><td><img src="images/captcha.jpg" alt="Security Code"/></td><td>Enter the Security Code Below<br /><input type="text" size="4" name="security_code" /></td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" class="bar" align="center"><input type="submit" value="Submit"> <input type="reset" value="Reset"></td></tr>
</table>
</form>
<? 
}
include("inc/footer.inc");
?>

</body>
</html>








