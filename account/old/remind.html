<?
	require_once("../inc/dbi.inc");
	require_once("../inc/crypt.inc");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Data Business Systems - Password Reminder</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	include("../inc/header.inc"); 
?>
<h1>Password Reminder</h1>
<table align="center" cellpadding="4" cellspacing="0" align="center">
<? 
	if(isset($_POST[email])){
		if($_POST[email] == "")
			$error = "Please specify a valid email address!";
		if(!$error){
			$edb = new dbi();
			$edb->query("select email,password from account where email = '$_POST[email]' limit 0,1");
			if($edb->numrows()){
					$from = "DBS Support <support@databusinesssystems.com>";
					$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from";

					$message  = "This is your requested password reminder from a Data Business Systems Site. Below is your account email address  and password.\n\n  Email Address: ".$edb->result("email")."\n       Password: ".trim(decrypto(base64_decode($edb->result("password")), substr($edb->result("email"),0,2)))."\n\nThis information was requested from  (".$_SERVER[REMOTE_ADDR].").\n\nRegards,\nCustomer Support\nsupport@databusinesssystems.com";
				mail($edb->result("email"),"Password Reminder",$message,$header);

				print "<tr><td align=\"left\">Your password has been sent!  If you do not receive the email or if you are still having difficulties using your account, please email us at <a href=\"mailto:support@databusinesssystems.com\">support@databusinesssystems.com</a> or call us at 1-800-778-6247.</td></tr>";
			}else{
				$error = "No account with that email address!";
			}
		}
	}
	if(!isset($_POST[email]) || $error){
?>
<tr><td>If you have lost your password, enter your email address below and your information will be sent to you.<br><br></td></tr>
<? if($error){ ?>
<tr><td align="center" class="error"><?=$error?><br><br></td></tr>
<? } ?>
<form action="<?=$_SERVER[PHP_SELF]?>" method="post">
<tr><td align="center"><input type="text" name="email" size="25">&nbsp;&nbsp;<input type="submit" value="Send Password"></td></tr>
</form>
<?
	}
?>
</table>
<?
	include("../inc/footer.inc"); 
?>
</body>
</html>
