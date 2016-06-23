<? include("inc/config.inc"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Form-N-Envelope Sample Request - Print Forms</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
?>
<h1>Request Form-N-Envelope Sample</h1>
<?
	if(isset($_POST['first_name'])){
		foreach($_POST as $i => $val){
			if(!is_array($_POST[$i]))
				$_POST[$i] = strip_tags($val);
		}

		if($_POST['company'] == "")
			$error = "Must specify a Company!";
		elseif($_POST[first_name] == "")
			$error = "Must specify a First Name!";
		elseif($_POST[last_name] == "")
			$error = "Must specify a Last Name!";
		elseif($_POST[street] == "")
			$error = "Must specify a Street Address!";
		elseif($_POST[city] == "")
			$error = "Must specify a City!";
		elseif($_POST[state] == "")
			$error = "Must specify a State!";
		elseif($_POST[zipcode] == "")
			$error = "Must specify a Zip Code!";
		elseif($_POST[phone] == "")
			$error = "Must specify a Phone!";
		elseif(!preg_match("/^[A-z0-9\-_\.]+\@[A-z0-9\-_\.]+\.[A-z]+$/",$_POST[email]))
			$error = "Must specify a Valid Email!";
		elseif($_POST[make_model] == "")
			$error = "Must specify a Printer Make/Model!";
		elseif($_POST['type'] == "")
			$error = "Must specify a Printer Type!";
		elseif($_POST['size'] == "")
			$error = "Must specify a Paper Size!";	
		else{
			$from = "'$_POST[first_name] $_POST[last_name]' <$_POST[email]>";
			$header = "Return-Path: $from\r\nFrom: $from\r\nReply-To: $from";

			$message  = "Company........: $_POST[company]\n";
			$message .= "First Name.....: $_POST[first_name]\n";
			$message .= "Last  Name.....: $_POST[last_name]\n";
			$message .= "Street.........: $_POST[street]\n";
			$message .= "City...........: $_POST[city]\n";
			$message .= "State..........: $_POST[state]\n";
			$message .= "Zip Code.......: $_POST[zipcode]\n";
			$message .= "Phone..........: $_POST[phone]\n";
			$message .= "Fax............: $_POST[fax]\n";
			$message .= "Email..........: $_POST[email]\n";
			$message .= "\nPrinter Information:\n";
			$message .= "Make/Model.....: $_POST[make_model]\n";
			$message .= "Type...........: $_POST[type]\n";
			$message .= "Size...........: $_POST[size]\n";

			mail("customercare@databusinesssystems.com","Form-N-Envelope Sample Request ".$_POST['company'], $message,$header);
?>
<div style="text-align:center;font-size:16px;"><b>Your request has been sent successfully!<br><br>Your samples will be sent within 24 to 48 hours.</b></div>
<?
		}
	}
?>
<? if(!isset($_POST[first_name]) || isset($error)){ ?>
<p>Fill out the following form to request a sample of our <a href="http://www.print-forms.com/FormNEnvelope/Form-N-Envelope-p192.htm">Form-N-Envelope</a> to make sure it is compatible with your printer.</p>
<? if(isset($error)){ ?>
	<div align="center" style="color:red;"><b><?=$error?></b></div>
<? } ?>
<br>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<table border="0" cellpadding="2" cellspacing="0" align="center">
<tr><td colspan="2" class="bar"><b>Mailing Address</b></td></tr>
<tr><td><b>Company</b> *</td><td><input name="company" size="40" value="<? if(isset($error)){ print $_POST['company']; }?>"></td></tr>
<tr><td><b>First Name</b> *</td><td><input name="first_name" size="40" value="<? if(isset($error)){ print $_POST['first_name']; }?>"></td></tr>
<tr><td><b>Last Name</b> *</td><td><input name="last_name" size="40" value="<? if(isset($error)){ print $_POST['last_name']; }?>"></td></tr>
<tr><td><b>Street Address</b> *</td><td><input name="street" size="40" value="<? if(isset($error)){ print $_POST['street']; }?>"></td></tr>
<tr><td><b>City</b> *</td><td><input name="city" size="40" value="<? if(isset($error)){ print $_POST['city']; }?>"></td></tr>
<tr><td><b>State</b> *</td><td><input name="state" size="2" value="<? if(isset($error)){ print $_POST['state']; }?>"></td></tr>
<tr><td><b>Zip Code</b> *</td><td><input name="zipcode" size="10" value="<? if(isset($error)){ print $_POST['zipcode']; }?>"></td></tr>
<tr><td><b>Phone</font> *</td><td><input name="phone" size="40" value="<? if(isset($error)){ print $_POST['phone']; }?>"></td></tr>
<tr><td><b>Fax</b></td><td><input name="fax" size="40" value="<? if(isset($error)){ print $_POST[fax]; }?>"></td></tr>
<tr><td><b>E-Mail Address</b> *</td><td><input name="email" size="40" value="<? if(isset($error)){ print $_POST[email]; }?>"></td></tr>
<tr><td colspan="2" class="bar"><b>Printer Information</b></td></tr>
<tr><td><b>Printer Make and Model</b> *</td><td><input name="make_model" size="40" value="<? if(isset($error)){ print $_POST['make_model']; }?>"></td></tr>
<tr><td><b>Type</b> *</td><td><input type="radio" name="type" value="Laser"<? if(isset($error) && $_POST['type'] == "Laser") print " checked=\"checked\""; ?>> Laser <input type="radio" name="type" value="Inkjet"<? if(isset($error) && $_POST['type'] == "Inkjet") print " checked=\"checked\""; ?>> Inkjet</td></tr>
<tr><td><b>Size</b> *</td><td><input type="radio" name="size" value='Memo Size (8.5" x 11")'<? if(isset($error) && $_POST['size'] == 'Memo Size (8.5" x 11")') print " checked=\"checked\""; ?>> Memo Size (8.5" x 11")<br/>
<input type="radio" name="size" value='Letter Size(8.5" x 14")'<? if(isset($error) && $_POST['size'] == 'Letter Size(8.5" x 14")') print " checked=\"checked\""; ?>>  Letter Size(8.5" x 14")</td></tr>
<tr><td align="center" colspan="2" class="bar"><input type="submit" value="Submit"></td></tr>
</table>
</form>
<div style="text-align:center;">Fields with an * are required</div>
<? } ?>

<?
	include("inc/footer.inc");
?>
</body>
</html>
