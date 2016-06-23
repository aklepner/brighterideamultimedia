<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>dbs - Change Password</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? 
	include("header.inc");
	if(isset($_POST['password'])){
		// Process Form
		if(strlen($_POST['password']) < 4)
			$error = "Password must be 4 or more characters!";
		elseif($_POST['password'] != $_POST['cpassword'])
			$error = "Passwords do not match!";
		if(!$error){
			system("/usr/local/bin/htpasswd -b ".dirname(__FILE__)."/.htpasswd ".$_SERVER['REMOTE_USER']." ".escapeshellarg($_POST['password']));
			print "<div align=\"center\" class=\"success\">Password Updated!</div><br>";
		}
	}
	if($error)
        	print "<div align=\"center\" class=\"error\">$error</div><br>";
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr class="bar"><td colspan="2">Change Password for <?=$_SERVER['REMOTE_USER']?></td></tr>
		<tr><td width="150"><b>Password</b></td><td><input type="password" name="password" size="20" value=""></td></tr>
		<tr><td width="150"><b>Confirm Password</b></td><td><input type="password" name="cpassword" size="20" value=""></td></tr>
		<tr class="bar"><td colspan="2" align="center"><input type="submit" value="Change Password"></td></tr>
	</table>
	</form>
<? include("footer.inc"); ?>
</body>
</html>
