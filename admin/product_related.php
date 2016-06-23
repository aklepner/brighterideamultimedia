<?
	if(!isset($_GET['category'])){
		header("Location: index.php");
		exit;
	}
	if(isset($_GET['setsite'])){
		setcookie("site_admin",$_GET[setsite],time()+(3600*24*30),"/");
	}
?>
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
if(isset($_GET['add']) || isset($_GET['mod'])){
	if(sizeof($_POST)){
		// Process Form
		if($_POST['category'] == "")
			$error = "Must specify a category!";
		elseif($_POST['description'] == "")
			$error = "Must specify a Description!";
		elseif(!isset($_POST['type']))
			$error = "Must specify a type of Related Product!";
			
		switch($type){
			case "link":
				$type_id = $_POST['link'];
				break;
			case "product":
				$type_id = $_POST['product'];
				break;
			default:
				$type_id = 0;
		}
		if(!is_numeric($type_id) || !$type_id)
			$error = "Please select a valid type!";
		
		if(!$error){
			if(isset($_GET['add'])){
				mysql_query("insert into product_related (id, category, type, type_id, description) values (NULL, '".$_POST['category']."', '".$_POST['type']."', '".$type_id."', '".addslashes($_POST['description'])."')", $dbh);
			}else{
				mysql_query("update product_related set category = '".$_POST['category']."', type = '".$_POST['type']."', type_id = '".$type_id."', description = '".addslashes($_POST['description'])."' where id = '".$_GET['mod']."'", $dbh);
			}
			print "<div align=\"center\" class=\"success\">Related Product updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($_GET['mod'])){
		$results = mysql_query("select * from product_related where id = '".$_GET['mod']."' limit 1", $dbh);
		if(mysql_num_rows($results)){
			$mdb = mysql_fetch_assoc($results);
		}else{
			print "<div align=\"center\">No Related Product with ID of '$mod'!</div>";
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>";
?>
	<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr class="bar"><td>Modify Related Product</td><td align="right"><a href="<?=$_SERVER['PHP_SELF']?>?category=<?=$_GET['category']?>">View All</a></td></tr>
		<tr><td class="field_title">Category</td><td><select name="category"><?
			$results = mysql_query("select * from product_category where site = '".SITE."' order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['id']."\"";
				if((isset($mdb) && $row['id'] == $mdb['category']) || (isset($error) && $row['id'] == $_POST['category']) || (isset($_GET['add']) && $_GET['category'] == $row['id']))
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?></select></td></tr>
		<tr><td class="field_title" valign="top">Type</td><td>
			<table>
				<tr><td><input type="radio" name="type" value="link"<? if((isset($error) && $_POST['type'] == 'link') || (!isset($error) && isset($mdb) && $mdb['type'] == 'link')){ print " checked=\"checked\""; } ?>></td><td>Link <select name="link"><option value=""></option><?
	$results = mysql_query("select * from link where site = '".SITE."' order by name asc");
	while($row = mysql_fetch_assoc($results)){
		print "<option value=\"".$row['id']."\"";
		if((isset($error) && $_POST['type'] == "link" && $row['id'] == $_POST['link'] ) || (!isset($error) && isset($mdb) && $mdb['type'] == "link" && $mdb['type_id'] == $row['id']))
			print " selected=\"selected\"";
		print ">".$row['name']."</option>";
	}
?></select></td></tr>
			<tr><td><input type="radio" name="type" value="product"<? if((isset($error) && $_POST['type'] == 'product') || (!isset($error) && isset($mdb) && $mdb['type'] == 'product')){ print " checked=\"checked\""; } ?>></td><td>Product <select name="product"><option value=""></option><?
	$results = mysql_query("select * from product_category where site = '".SITE."' order by name asc", $dbh);
	while($row = mysql_fetch_assoc($results)){
		print "<option value=\"".$row['id']."\"";
		if((isset($error) && $_POST['type'] == "product" && $row['id'] == $_POST['product'] ) || (!isset($error) && isset($mdb) && $mdb['type'] == "product" && $mdb['type_id'] == $row['id']))
			print " selected=\"selected\"";
		print ">".$row['name']."</option>";
	}
?></select></td></tr>
			</table>
		</td></tr>
		<tr><td class="field_title">Description</td><td><input type="text" name="description" size="50" value="<? if(isset($error)){ print htmlspecialchars($_POST['description']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['description']); }?>"></td></tr>
		<tr class="bar"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($_GET['add']) && !isset($_GET['mod'])) || isset($success)){
	if(isset($_GET['delete'])){
		mysql_query("delete from product_related where id = '".$_GET['delete']."'", $dbh);
		print "<div align=\"center\" class=\"error\">Related Product deleted successfully!</div><br>";
	}
	
	$results = mysql_query("select name from product_category where id = '".$_GET['category']."'", $dbh);
		$bar_title = "Related Products for '".mysql_result($results, 0, "name")."'";
	$results = mysql_query("select * from product_related where category = '".$_GET['category']."' order by description asc", $dbh);

	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr class=\"bar\"><td>$bar_title</td><td align=\"right\"><a href=\"".$_SERVER['PHP_SELF']."?category=".$_GET['category']."&add=1\">Add a Related Product</a></td></tr>";
	if(mysql_num_rows($results)){
		while($row = mysql_fetch_assoc($results)){
			print "<tr><td align=\"left\"><a href=\"".$_SERVER['PHP_SELF']."?category=".$_GET['category']."&mod=".$row['id']."\">".substr($row['description'],0,60)."</a></td><td align=\"right\"><a href=\"".$_SERVER['PHP_SELF']."?category=".$_GET['category']."&delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this item?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
		}
	}else{
		print "<tr><td colspan=\"2\" align=\"center\">No Related Products to this Category!</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
