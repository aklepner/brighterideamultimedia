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
	if(isset($_POST[code])){
		// Process Form
		if($_POST[description] == "")
			$error = "Description must be specified!";
		elseif($_POST[discount] == "")
			$error = "Discount must be specified!";
		elseif($_POST[type] == "")
			$error = "Type must be specified";
		elseif(strtotime($_POST[expire]) == -1)
			$error = "Invalid Expiration Date. Example: YYYY-MM-DD or MM/DD/YYYY";
		if(!$error){
			if($_POST[type] == "all"){
				$type = "all";
				$type_id = 0;
			}else{
				if($_POST[type] == "with")
					$type = "a";
				else
					$type = "";
				if(substr($_POST[type_id],0,1) == "c")
					$type .= "category";
				else
					$type .= "product";
				$type_id = substr($_POST[type_id],1);
			}
			if($_POST[expire] != "")
				$expire = "'".date("Y-m-d",strtotime($_POST[expire]))."'";
			else
				$expire = "NULL";
			if(isset($_POST[oneuse]))
				$setoneuse = "y";
			else
				$setoneuse = "n";
			$updb = new dbi();
			if(isset($add)){
				$updb->query("insert into coupon (site, code, description, type, type_id, discount, discount_type, expire, oneuse) values('".SITE."', '$_POST[code]', '".addslashes($_POST[description])."', '$type', '$type_id', '$_POST[discount]', '$_POST[discount_type]', $expire, '$setoneuse')");
			}else{
				$updb->query("update coupon set description = '".addslashes($_POST[description])."', type = '$type', type_id = '$type_id', discount = '$_POST[discount]', discount_type = '$_POST[discount_type]', expire = $expire, oneuse = '$setoneuse' where id = '$mod'");
			}
			print "<div align=\"center\" class=\"success\">Coupon updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from coupon where site = '".SITE."' and id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Coupon with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form action="coupon.php?<?if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Coupon</td><td align="right"><a href="coupon.php" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">Code</td><td><?
	if(isset($error))
		$c_code = $_POST[code];
	elseif(isset($moddb))
		$c_code = $moddb->result("code");
	else{
		for($i=1;$i<=6;$i++)
			$c_code .= chr(rand(65,90));
	}
	print $c_code;
?><input type="hidden" name="code" size="20" value="<?=$c_code?>"></td></tr>
		<tr><td class="field_title">Description</td><td><input type="text" name="description" size="50" value="<?if(isset($error)){ print $_POST[description]; }elseif(isset($moddb)){ print $moddb->result("description"); }?>"></td></tr>
		<tr><td class="field_title">Discount</td><td><input type="text" name="discount" size="8" value="<?if(isset($error)){ print $_POST[discount]; }elseif(isset($moddb)){ print number_format($moddb->result("discount"),2,".",""); }?>"><select name="discount_type"><option value="dollar"<?if((isset($error) && $_POST[discount_type] == "dollar") || (isset($moddb) && $moddb->result("discount_type") == "dollar")){ print " selected=\"selected\""; }?>>dollars</option><option value="percent"<?if((isset($error) && $_POST[discount_type] == "percent") || (isset($moddb) && $moddb->result("discount_type") == "percent")){ print " selected=\"selected\""; }?>>percent</option></select></td></tr>
		<tr><td class="field_title">Type</td><td><input type="radio" name="type" value="all"<?if((isset($error) && $_POST[type] == "all") || (isset($moddb) && $moddb->result("type") == "all")){ print " checked=\"checked\""; }?>> All<br><input type="radio" name="type" value="for"<?if((isset($error) && $_POST[type] == "for") || (isset($moddb) && ($moddb->result("type") == "product" || $moddb->result("type") == "category"))){ print " checked=\"checked\""; }?>> For<br><input type="radio" name="type" value="with"<?if((isset($error) && $_POST[type] == "with") || (isset($moddb) && ($moddb->result("type") == "aproduct" || $moddb->result("type") == "acategory"))){ print " checked=\"checked\""; }?>> With<br></td></tr>
		<tr><td class="field_title">Type ID</td><td><select name="type_id"><option value=""></option><?
			$cdb = new dbi();
			$pdb = new dbi();
			$cdb->query("select id,name from product_category where site = '".SITE."' order by name");
			while($cdb->loop()){
				print "<option value=\"c".$cdb->result("id")."\"";
				if((isset($error) && $_POST[type_id] == "c".$cdb->result("id")) || (isset($moddb) && preg_match("/^[a]?category$/",$moddb->result("type")) && $moddb->result("type_id") == $cdb->result("id")))
					print " selected=\"selected\"";
				print ">".$cdb->result("name")."</option>";
				$pdb->query("select id,description from product where category = '".$cdb->result("id")."' order by description");
				while($pdb->loop()){
					print "<option value=\"p".$pdb->result("id")."\"";
					if((isset($error) && $_POST[type_id] == "p".$pdb->result("id")) || (isset($moddb) && preg_match("/^[a]?product$/",$moddb->result("type")) && $moddb->result("type_id") == $pdb->result("id")))
						print " selected=\"selected\"";
					print "> - ".substr($pdb->result("description"),0,50)."</option>";
				}
			}
		?></select></td></tr>
		<tr><td class="field_title">Expire</td><td><input type="text" name="expire" size="15" value="<?if(isset($error)){ print $_POST[expires]; }elseif(isset($moddb) && $moddb->result("expire") != NULL){ print date("m/d/Y",strtotime($moddb->result("expire"))); }?>"></td></tr>
		<tr><td class="field_title">One Time Use</td><td><input type="checkbox" name="oneuse"<?if(isset($error) && isset($_POST[oneuse])){ print " checked=\"checked\""; }elseif(isset($moddb) && $moddb->result("oneuse") == 'y'){ print " checked=\"checked\""; }?>></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($add) && !isset($mod)) || isset($success)){
	$db = new dbi();
	if(isset($delete)){
		if(isset($confirm) && $confirm == 'y'){
			$db->query("delete from coupon where site = '".SITE."' and id = '$delete'");
			print "<div align=\"center\" class=\"success\">Coupon deleted!</div><br>";
		}else{
			print "<div align=\"center\" class=\"error\">Are you sure you want to delete this coupon? <a class=\"error\" href=\"$_SERVER[PHP_SELF]?delete=$delete&confirm=y\">Yes</a>  <a class=\"error\" href=\"$_SERVER[PHP_SELF]\">No</a></div><br>";
		}
	}
	$db->query("select * from coupon where site = '".SITE."'");
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr bgcolor=\"#990000\"><td style=\"color:#FFFFFF;\" colspan=\"2\">Coupons</td><td><a href=\"$_SERVER[PHP_SELF]?add=1\" style=\"color:#FFFFFF;\">Add a Coupon</a></td></tr>";
	if($db->numrows()){
		while($db->loop()){
			print "<tr><td valign=\"top\"><a href=\"$_SERVER[PHP_SELF]?mod=".$db->result("id")."\">".$db->result("code")."</a></td><td width=\"300\">".$db->result("description")."<td align=\"right\" valign=\"top\"><a href=\"$_SERVER[PHP_SELF]?delete=".$db->result("id")."\">Delete?</a></td></tr>";
		}
	}else{
		print "<tr><td colspan=\"2\">No Coupons Available</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
