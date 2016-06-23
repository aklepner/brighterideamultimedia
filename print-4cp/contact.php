<?
session_start();
include("inc/config.inc");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Contact Print-4cp.com</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
	session_start();
	$states = array('','AK','AL','AR','AZ','CA','CO','CT','DC','DE','FL','GA','HI','IA','ID','IL','IN','KS','KY','LA','MA','MD','ME','MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VA','VT','WA','WI','WV','WY');
?>
<h1>Contact Print-4cp.com</h1>
<?
	if(isset($_POST['first_name'])){
		if($_POST[first_name] == "")
			$error = "Must specify a First Name!";
		elseif($_POST[last_name] == "")
			$error = "Must specify a Last Name!";
		elseif($_POST[title] == "")
			$error = "Must specify a Title!";
		elseif($_POST[company] == "")
			$error = "Must specify a Company!";
		elseif($_POST[street] == "")
			$error = "Must specify a Street!";
		elseif($_POST[city] == "")
			$error = "Must specify a City!";
		elseif($_POST[state] == "")
			$error = "Must specify a State!";
		elseif($_POST[zipcode] == "")
			$error = "Must specify a Zip Code!";
		elseif($_POST[phone] == "")
			$error = "Must specify a Phone!";
		elseif(!preg_match("/^[A-z0-9\-_\.]+\@[A-z0-9\-_\.]+\.[A-z]+$/",$_POST[email])){
			$error = "Must specify a Valid Email!";
		}
		
		foreach($_POST as $x){
			if(preg_match("/\[url=http/i", $x) || preg_match("/(content\-type|multipart\/message)/i", $x)){
				$error = "Invalid Content!";
				break;
			}
		}
		
		if(!isset($error)){
			if(substr_count(strtolower($_POST['message']),"http") > 2)
				$error = "Invalid Content!";
		}
		
		if(!isset($error) && (strtolower($_SESSION['security_code']) != strtolower($_POST['security_code']) || empty($_SESSION['security_code']))){
			$error = "Invalid Security Code! ";
	   	}
		
		if(!isset($error)){
			$from = "$_POST[first_name] $_POST[last_name] <$_POST[email]>";
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
			$message .= "Street.........: $_POST[ship_street]\n";
			$message .= "City...........: $_POST[ship_city]\n";
			$message .= "State..........: $_POST[ship_state]\n";
			$message .= "Zip Code.......: $_POST[ship_zipcode]\n";
			$message .= "\nContact Info\n";
			$message .= "Phone..........: $_POST[phone]\n";
			$message .= "Fax............: $_POST[fax]\n";
			$message .= "Email..........: $_POST[email]\n";
			$message .= "Web Site.......: $_POST[website]\n";
			$message .= "Message........:\n";
			$message .= $_POST[message];
			$message .= "\n\nHost.....: ".gethostbyaddr($_SERVER[REMOTE_ADDR])."\n";
			$message .= "Agent....: $_SERVER[HTTP_USER_AGENT]\n";

			mail("jk@databusinesssystems.com","Print-4cp Web Contact",$message,$header);
?>
<div align="center">Your message has been sent successfully!  You should receive a response within one business day.</div>
<?
		}
	}
?>
<? if(!isset($_POST[first_name]) || isset($error)){ ?>
<? if(isset($error)){ ?>
	<div align="center" style="font-weight:bold;color:red;"><?=$error?></div>
<? }else{ ?>
	<div style="text-align:center;">Fields with an * are required</div>
<? } ?>
<br>
<form method="POST" action="contact.php"  name="contact">
<table border="0" cellpadding="2" cellspacing="0" align="center">
<tr><td><font face="arial" size="-1">First name</font> *</td><td><input name="first_name" size="40" value="<?if(isset($error)){ print $_POST[first_name]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">Last name*</font></td><td><input name="last_name" size="40" value="<?if(isset($error)){ print $_POST[last_name]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">Title*</font></td><td><input name="title" size="40" value="<?if(isset($error)){ print $_POST[title]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">Company*</font></td><td><input name="company" size="40" value="<?if(isset($error)){ print $_POST[company]; }?>"></td></tr>
<tr><tr><td colspan="2" class="bar">Mailing Address</td></tr>
<td><font face="arial" size="-1">Street*</font></td><td><input name="street" size="40" value="<?if(isset($error)){ print $_POST[street]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">City*</font></td><td><input name="city" size="40" value="<?if(isset($error)){ print $_POST[city]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">State*</font></td><td><select name="state" ><?
	foreach($states as $s){
		print "<option value=\"$s\"";
		if(isset($error) && $_POST[state] == $s)
			print " selected=\"selected\"";
		print ">$s</option>";
	}
?></select>&nbsp;&nbsp;&nbsp;&nbsp;<font face="arial" size="-1">Zip Code</font>*&nbsp;&nbsp;<input name="zipcode" size="10" value="<?if(isset($error)){ print $_POST[zipcode]; }?>"></td></tr>

<tr><td colspan="2" class="bar">Shipping Address - if different from Mailing Address</td></tr>
<td><font face="arial" size="-1">Street</font></td><td><input name="ship_street" size="40" value="<?if(isset($error)){ print $_POST[ship_street]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">City</font></td><td><input name="ship_city" size="40" value="<?if(isset($error)){ print $_POST[ship_city]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">State</font></td><td><select name="ship_state"><?
	foreach($states as $s){
		print "<option value=\"$s\"";
		if(isset($error) && $_POST[ship_state] == $s)
			print " selected=\"selected\"";
		print ">$s</option>";
	}
	?></select>&nbsp;&nbsp;&nbsp;&nbsp;<font face="arial" size="-1">Zip Code</font>&nbsp;&nbsp;<input name="ship_zipcode" size="10" value="<?if(isset($error)){ print $_POST[ship_zipcode]; }?>"></td></tr>
<tr><td colspan="2" class="bar">Contact Info</td></tr>
<tr><td><font face="arial" size="-1">Phone*</font></td><td><input name="phone" size="40" value="<?if(isset($error)){ print $_POST[phone]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">Fax</font></td><td><input name="fax" size="40" value="<?if(isset($error)){ print $_POST[fax]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">E-Mail Address*</font></td><td><input name="email" size="40" value="<?if(isset($error)){ print $_POST[email]; }?>"></td></tr>
<tr><td><font face="arial" size="-1">Website Address</font></td><td><input name="website" size="40" value="<?if(isset($error)){ print $_POST[website]; }?>"></td></tr>
<tr><td></td></tr>
<tr><td><font face="arial" size="-1">I'm interested in...<br>
Please provide as many<br> details as possible.</font></td><td><textarea name="message" rows=10 cols=40><? if(isset($error)){ print $_POST['message']; }?></textarea>
</td><tr>
<tr><td><font face="arial" size="-1">Security Code</font></td><td><table>
	<tr><td><img src="images/captcha.jpg" alt="Security Code"/></td><td>Enter the Security Code Below<br /><input type="text" size="4" name="security_code" /></td></tr>
</table></td></tr>
<tr><td align="center" colspan="2" class="bar"><input type="submit" value="Submit"><input type="reset" value="Reset"></td></tr>

</table>

</form>
<? } ?>

<?
	include("inc/footer.inc");
?>



</body>
</html>
