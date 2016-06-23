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
	if(isset($_POST[name])){
		// Process Form
		if($_POST[name] == "")
			$error = "Name must be specified!";
		elseif(!isset($_POST[type]))
			$error = "Must specify a type of menu item!";
		switch($type){
			case "info":
				$type_id = $_POST['info'];
				break;
			case "link":
				$type_id = $_POST['link'];
				break;
			case "sample":
				$type_id = $_POST['sample'];
				break;
			case "product":
				$type_id = $_POST['product'];
				break;
			default:
				$type_id = 0;
		}
		if(!is_numeric($type_id))
			$error = "Please select a valid type! $type_id";
		if(!$error){
			if(isset($_GET['add'])){
				mysql_query("insert into menu (site, name, parent, sort, type, type_id) values('".SITE."', '".$_POST['name']."', '".$_POST['parent']."', '".$_POST['sort']."', '".$_POST['type']."', '$type_id')");
			}else{
				mysql_query("update menu set name = '".$_POST['name']."', parent = '".$_POST['parent']."', sort = '".$_POST['sort']."', type = '".$_POST['type']."', type_id = '$type_id' where id = '$mod'");
			}
			print "<div align=\"center\" class=\"success\">Menu updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from menu where site = '".SITE."' and id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Menu Entry with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form action="menu.php?<?if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
			<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Menu</td><td align="right"><a href="menu.php" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">Name</td><td><input type="text" name="name" size="50" value="<? if(isset($error)){ print htmlspecialchars($_POST[name]); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result("name")); }?>"></td></tr>
		<tr><td class="field_title">Parent Menu</td><td><select name="parent"><option value="0">(None)</option><?
			function get_menu($parent=0,$level=0){
				global $moddb, $dbh;
				$results = mysql_query("select * from menu where site = '".SITE."' and parent = '$parent' order by sort desc");
				if(mysql_num_rows($results)){
					while($row = mysql_fetch_assoc($results)){
						if(isset($moddb) && $row['id'] == $moddb->result("id"))
							continue;
						print "<option value=\"".$row['id']."\"";
						if((isset($error) && $_POST['parent'] == $row['id']) || (isset($moddb) && $moddb->result("parent") == $row['id'])){
							print " selected=\"selected\"";
						}
						print ">";
						for($i=1;$i<=$level;$i++)
							print "&nbsp;&nbsp;&nbsp;";
						print $row['name']."</option>";
						get_menu($row['id'],$level+1);
					}
				}
			}
			get_menu();
		?></select></td></tr>
		<tr><td class="field_title">Sort</td><td><input type="text" name="sort" size="5" value="<? if(isset($error)){ print $_POST['sort']; }elseif(isset($moddb)){ print $moddb->result("sort"); } ?>"> (Higher to Lower on Menu)</td></tr>
		<tr><td class="field_title" valign="top">Type</td><td>
			<table>
				<tr><td><input type="radio" name="type" value="none"<? if(isset($error) && $_POST[type] == 'none'){ print " checked=\"checked\""; }elseif(isset($moddb) && $moddb->result("type") == 'none'){ print " checked=\"checked\""; }?>></td><td>None</td></tr>
				<tr><td><input type="radio" name="type" value="info"<? if(isset($error) && $_POST[type] == 'info'){ print " checked=\"checked\""; }elseif(isset($moddb) && $moddb->result("type") == 'info'){ print " checked=\"checked\""; }?>></td><td>Info <select name="info"><option value=""></option><?
	$infodb = new dbi();
	$infodb->query("select * from info where site = '".SITE."' order by name asc");
	while($infodb->loop()){
			print "<option value=\"".$infodb->result("id")."\"";
		if((isset($error) && $_POST[type] == "info" && $infodb->result("id") == $_POST[info] ) || (!isset($error) && isset($moddb) && $moddb->result("type") == "info" && $moddb->result("type_id") == $infodb->result("id")))
			print " selected=\"selected\"";
		print ">".$infodb->result("name")."</option>";
	}
?></select></td></tr>
				<tr><td><input type="radio" name="type" value="link"<? if(isset($error) && $_POST[type] == 'link'){ print " checked=\"checked\""; }elseif(isset($moddb) && $moddb->result("type") == 'link'){ print " checked=\"checked\""; }?>></td><td>Link <select name="link"><option value=""></option><?
	$linkdb = new dbi();
	$linkdb->query("select * from link where site = '".SITE."' order by name asc");
	while($linkdb->loop()){
		print "<option value=\"".$linkdb->result("id")."\"";
		if((isset($error) && $_POST[type] == "link" && $linkdb->result("id") == $_POST[link] ) || (!isset($error) && isset($moddb) && $moddb->result("type") == "link" && $moddb->result("type_id") == $linkdb->result("id")))
			print " selected=\"selected\"";
		print ">".$linkdb->result("name")."</option>";
	}
?></select></td></tr>
			<tr><td><input type="radio" name="type" value="sample"<?if(isset($error) && $_POST[type] == 'sample'){ print " checked=\"checked\""; }elseif(isset($moddb) && $moddb->result("type") == 'sample'){ print " checked=\"checked\""; }?>></td><td>Sample <select name="sample"><option value=""></option><?
	$prodb = new dbi();
	$prodb->query("select * from sample_category where site = '".SITE."' order by name asc");
	while($prodb->loop()){
		print "<option value=\"".$prodb->result("id")."\"";
		if((isset($error) && $_POST[type] == "sample" && $prodb->result("id") == $_POST[sample] ) || (!isset($error) && isset($moddb) && $moddb->result("type") == "sample" && $moddb->result("type_id") == $prodb->result("id")))
			print " selected=\"selected\"";
		print ">".$prodb->result("name")."</option>";
	}
?></select></td></tr>
			<tr><td><input type="radio" name="type" value="product"<?if(isset($error) && $_POST[type] == 'product'){ print " checked=\"checked\""; }elseif(isset($moddb) && $moddb->result("type") == 'product'){ print " checked=\"checked\""; }?>></td><td>Product <select name="product"><option value=""></option><?
	$prodb = new dbi();
	$prodb->query("select * from product_category where site = '".SITE."' order by name asc");
	while($prodb->loop()){
		print "<option value=\"".$prodb->result("id")."\"";
		if((isset($error) && $_POST[type] == "product" && $prodb->result("id") == $_POST[product] ) || (!isset($error) && isset($moddb) && $moddb->result("type") == "product" && $moddb->result("type_id") == $prodb->result("id")))
			print " selected=\"selected\"";
		print ">".$prodb->result("name")."</option>";
	}
?></select></td></tr>
			</table>
		</td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($add) && !isset($mod)) || isset($success)){
	if(isset($_GET['delete'])){
		$results = mysql_query("select * from menu where site = '".SITE."' and parent = '".$_GET['delete']."'", $dbh);
		if(mysql_num_rows($results)){
			print "<div align=\"center\" class=\"error\">Sub Menu Items exist!  Delete or Move them first!</div><br>";
		}else{
			mysql_query("delete from menu where site = '".SITE."' and id = '".$_GET['delete']."'");
			print "<div align=\"center\" class=\"success\">Menu Item deleted!</div><br>";
		}
	}
	function get_menu($parent=0,$level=0){
		global $i, $dbh;
		$results = mysql_query("select * from menu where site = '".SITE."' and parent = '$parent' order by sort desc");
		if($parent==0){
			print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
			print "<tr class=\"bar\"><td colspan=\"2\">Menu Items</td><td><a href=\"menu.php?add=1\">Add an Item</a></td></tr>";
		}
		if(mysql_num_rows($results)){
			while($row = mysql_fetch_assoc($results)){
				print "<tr".(!($i%2)?" class=\"shade\"":"")."><td><div style=\"margin-left:".($level*20)."px;\"><a href=\"menu.php?mod=".$row['id']."\">".$row['name']."</a></div></td><td align=\"center\">".ucfirst($row['type'])."</td><td align=\"right\"><a href=\"menu.php?delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this Menu Item?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
				$i++;
				get_menu($row['id'],$level+1);
			}

		}elseif($parent==0){
			print "<tr><td colspan=\"3\" align=\"center\">No Menu Items available!</td></tr>";
		}
		if($parent==0)
			print "</table>";
	}
	$i = 0;
	get_menu();
}
include("footer.inc");
?>
</body>
</html>
