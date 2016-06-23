<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - administration</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
if(isset($add) || isset($mod)){
	if(isset($_POST[state])){
		// Process Form
		if(strlen($_POST[state]) != 2)
			$error = "Must be 2 Letter Abbreviation!";
		elseif($_POST[tax] == "")
			$error = "Must specify Tax Percentage!";
		if(!$error){
			$updb = new dbi();
			if(isset($add)){
				$updb->query("insert into state_tax (state, tax) values('".strtoupper($_POST[state])."', '$_POST[tax]')");
			}else{
				$updb->query("update state_tax set state = '".strtoupper($_POST[state])."', tax = '$_POST[tax]' where id = '$mod'");
			}
			print "<div align=\"center\" class=\"success\">State Tax updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from state_tax where id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No State Tax Entry with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form action="state_tax.php?<?if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify State Tax</td><td align="right"><a href="state_tax.php" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">State</td><td><input type="text" name="state" size="5" maxlength="2" value="<?if(isset($error)){ print $_POST[state]; }elseif(isset($moddb)){ print $moddb->result("state"); }?>"></td></tr>
		<tr><td class="field_title">Tax (%)</td><td><input type="text" name="tax" size="10" value="<?if(isset($error)){ print $_POST[tax]; }elseif(isset($moddb)){ print $moddb->result("tax"); }?>"></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($add) && !isset($mod)) || isset($success)){
	if(isset($delete)){
		$deldb = new dbi();
		if(isset($confirm) && $confirm == 'y'){
			$deldb->query("delete from state_tax where id = '$delete'");
			print "<div align=\"center\" class=\"success\">State Tax Entry deleted!</div><br>";
		}else{
			print "<div align=\"center\" class=\"error\">Are you sure you want to delete this State Tax Entry? <a class=\"error\" href=\"state_tax.php?delete=$delete&confirm=y\">Yes</a>  <a class=\"error\" href=\"state_tax.php\">No</a></div><br>";
		}
	}
	$db = new dbi();
	$db->query("select * from state_tax");
	if($db->numrows()){
		print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
		print "<tr bgcolor=\"#990000\"><td style=\"color:#FFFFFF;\" colspan=\"2\">State Tax</td><td><a href=\"state_tax.php?add=1\" style=\"color:#FFFFFF;\">Add an Entry</a></td></tr>";
		while($db->loop()){
			print "<tr><td width=\"50\"><a href=\"state_tax.php?mod=".$db->result("id")."\">".$db->result("state")."</a></td><td align=\"right\">".$db->result("tax")." %</td><td align=\"right\"><a href=\"state_tax.php?delete=".$db->result("id")."\">Delete?</a></td></tr>";
		}
		print "</table>";
	}
}
include("footer.inc");
?>
</body>
</html>
