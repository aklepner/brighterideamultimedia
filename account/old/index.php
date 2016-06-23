<?
	if($_SERVER["HTTPS"] != 'on'){
		header("Location: https://www.databusinesssystems.com/account/");
		exit;
	}
	require_once("../inc/dbi.inc");
	require_once("../inc/crypt.inc");
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Data Business Systems - Your Account</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	include("../inc/header.inc"); 
?>
<h1>Your Account</h1>
<?
	$db = new dbi();
	if(isset($_POST[newaccount])){
		if(!preg_match("/^[a-z0-9\.\-_]+\@[a-z0-9\.\-_]+\.[a-z]+$/",$_POST[email])){
			$error = "Create a valid e-mail account.";
		}elseif(strlen($_POST[password]) < 4 || strlen($_POST[password]) > 16){
			$error = "Password must be between 4 and 16 characters.";
		}elseif($_POST[password] != $_POST[cpassword]){
			$error = "Passwords do not match.";
		}else{
			$db->query("select * from account where email = '".$_POST[email]."'");
			if($db->numrows()){
				$error = "Account already exists.  Please try logging in.";
			}else{
				$_SESSION['account_id'] = $db->query("insert into account (email, password, po, created) values('".strtolower($_POST['email'])."', '".base64_encode(encrypto($_POST['password'],strtolower(substr($_POST[email],0,2))))."', 'n',  '".date("Y-m-d H:i:s",time())."')");
				$_SESSION[email] = $_POST[email];
			}
		}
	}
	if(isset($_POST['login'])){
		if($_POST[email] == "" || $_POST[password] == ""){
			$error = "Please specify an E-mail and Password to login!";
		}else{
			# echo "select * from account where lower(email) = '".strtolower($_POST['email'])."' and (password = '".base64_encode(encrypto($_POST['password'],strtolower(substr($_POST['email'],0,2))))."' or password = '".base64_encode(encrypto(strtolower($_POST['password']),strtolower(substr($_POST['email'],0,2))))."' or password = '".base64_encode(encrypto(strtoupper($_POST['password']),strtolower(substr($_POST['email'],0,2))))."')";
			$db->query("select * from account where lower(email) = '".strtolower($_POST['email'])."' and (password = '".base64_encode(encrypto($_POST['password'],strtolower(substr($_POST['email'],0,2))))."' or password = '".base64_encode(encrypto(strtolower($_POST['password']),strtolower(substr($_POST['email'],0,2))))."' or password = '".base64_encode(encrypto(strtoupper($_POST['password']),strtolower(substr($_POST['email'],0,2))))."')");
			if($db->numrows()){
				$_SESSION[account_id] = $db->result("account.id");
				$_SESSION[email] = $db->result("account.email");
				$_SESSION[account_po] = $db->result("account.po");
				$_SESSION[account_taxrate] = $db->result("account.taxrate");
			}else{
				$error = "Invalid Email/Password.  Please try again.";
			}
		}
	}
	if(!isset($_SESSION['account_id']) || $_SESSION['account_id'] < 1){
?>
<form method="post" action="<?=$PHP_SELF?>"><table width="95%" border="0" cellspacing="0" cellpadding="4" align="center"><tr><td class="bar" align="left" colspan="2">Log Into an Existing Account</td></tr>
<tr><td colspan="2" align="center">If you already have an account with Data Business Systems, you can login here with your e-mail address and password. <a href="remind.html">Lost your password?</a></td></tr>
<? if(isset($_POST[login]) && $error){?>
	<tr><td colspan="2" align="center" style="font-weight:bold;color:red;"><?=$error?></td></tr>
<?}?>
<tr><td align="right" width="40%" style="font-weight:bold;">E-mail</td><td align="left" width="60%"><input type="text" name="email" size="35" value=""></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;" valign="top">Password</td><td align="left" width="60%"><input type="password" name="password" size="35" value=""><br><i>Passwords are case senstiive.</i></td></tr>
<tr><td class="bar" align="center" colspan="2"><input type="hidden" name="login" value="1"><input type="submit" value="Log In"></td></tr>
</table></form>
<br>
<form method="post" action="<?=$PHP_SELF?>"><table width="95%" border="0" cellspacing="0" cellpadding="4" align="center"><tr><td class="bar" align="left" style="font-weight:bold;" colspan="2">Create a New Account</td></tr>
<tr><td colspan="2" align="center">If you do not have an account with Data Business Systems, you can create one below.</td></tr>
<? if(isset($_POST[newaccount]) && $error){?>
	<tr><td colspan="2" align="center" style="font-weight:bold;color:red;"><?=$error?></td></tr>
<?}?>
<tr><td align="right" width="40%" style="font-weight:bold;">E-mail</td><td align="left" width="60%"><input type="text" name="email" size="35" value="<?if(isset($_POST[newaccount]) && $error){ print $_POST[email]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;" valign="top">Password</td><td align="left" width="60%"><input type="password" name="password" size="25" value="<? if(isset($_POST[newaccount]) && $error){ print $_POST[password]; } ?>"><br><i>Passwords are case senstiive.</i></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Retype Password</td><td align="left" width="60%"><input type="password" name="cpassword" size="25" value="<?if(isset($_POST[newaccount]) && $error){ print $_POST[cpassword]; } ?>"></td></tr>
<tr><td align="center" colspan="2" class="bar"><input type="hidden" name="newaccount" value="1"><input type="submit" value="Create Account"></td></tr>
</table></form>
<?
	}else{
?>
<table width="95%" border="0" cellspacing="0" cellpadding="2" align="center">
<tr><td align="left">Here you can change your email address and password, delete stored addresses, and access your order history. For security, any updates of Credit Card Information must be done during the checkout process.<br><br></td></tr>
<tr><td align="left">
<ul>
<li><a href="info.html">Change Email and/or Password</a></li>
<li><a href="address.html">Modify Stored Addresses</a></li>
<li><a href="custom.html">Custom Products</a></li>
<li><a href="history.html"><b>Complete Order History</a></b></li>
<?
	$results = mysql_query("select * from affiliate where account = '".$_SESSION['account_id']."'", $dbh);
	if(mysql_num_rows($results))
		print "<li><a href=\"affiliate-report.html\">Affiliate Report</a></li>";
?>
</ul>
</td></tr>
<?
	$results = mysql_query("select orders.*, site.document_base from orders join site ON site.id = orders.site where account = '".$_SESSION['account_id']."' order by datetime desc limit 1", $dbh);
	if(mysql_num_rows($results)){
		$order = mysql_fetch_assoc($results);
?>
<tr><td align="left" style="font-weight:bold;font-size:14px;">Most Recent Order</td></tr>
<tr><td>
<table width="100%" cellpadding="4" cellspacing="0" style="border:2px solid #000000;">
<tr class="bar"><td align="left" style="font-weight:bold;font-size:14px;">Order #<?=$order['id']?></td><td align="right"><a href="order.html?<?=$order['id']?>" style="font-size:14px;font-weight:bold;color:#FFFFFF;">View Invoice</a></td></tr>
<tr><td align="left">
<span style="font-weight:bold;">Date:</span> <?=date("F d, Y", $order['datetime'])?><br>
<span style="font-weight:bold;">Time:</span> <?=date("g:i A T", $order['datetime'])?><br>
</td><td align="right">
<span style="font-weight:bold;">Status:</span> <?=ucwords($order['status'])?><br>
<span style="font-weight:bold;">Total:</span> $<?=sprintf("%0.2f",$order['total'])?><br>
</td></tr>
<tr><td colspan="2" align="left">
<br><div style="font-weight:bold;">Items in this Order:</div>
<table width="100%" cellpadding="2">
<?
	$results = mysql_query("select * from order_items where order_id = '".$order['id']."'", $dbh);
	while($row = mysql_fetch_assoc($results)){
		print "<tr><td align=\"left\"> - ".$row['description'];
		if($row['quantity'])
			 print "<i>(QTY: ".$row['quantity'].")</i>";
		print "</td><td align=\"right\">";
		if(preg_match("/^Iscribe/i",$row['item'])){ 
			print "<form><input type=\"button\" value=\"Reorder\" onclick=\"location.href='https://www.databusinesssystems.com/medical-forms/product/118/';\" ></form>";
		}elseif($row['item_id'] > 0){
			print "<form method=\"post\" action=\"https://www.databusinesssystems.com".$order['document_base']."/cart/\" style=\"margin:0;\">";
			print "<input type=\"hidden\" name=\"option\" value=\"".$row["item_option"]."\"><input type=\"hidden\" name=\"quantity\" value=\"".$row["quantity"]."\"><input type=\"hidden\" name=\"product\" value=\"".$row["item_id"]."\"><input type=\"hidden\" name=\"reorder\" value=\"1\" /><input type=\"submit\" value=\"Reorder\">";
			print "</form>";
		}
		print "</td></tr>";
	}
?>
</table>
</td></tr>
</table>
</td></tr>
<?
	}
?>
</table>
<div style="text-align:center;font-size:14px;margin:10px 0;"><a href="history.html"><b>View your Complete Order History</a></b></li>
<?
	}
?>
<? include("../inc/footer.inc"); ?>

</body>
</html>
