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
		if($_POST['name'] == "")
			$error = "Must specify a vendor name!";
		elseif($_POST['city'] == "")
			$error = "Must specify a city!";
		elseif($_POST['state'] == "")
			$error = "Must specify a state!";
		elseif(strlen($_POST['zip']) != 5)
			$error = "Must specify a 5 digit zip code!";
			
		if(!$error){
			if(isset($_GET['add'])){
				mysql_query("insert into warehouse (id, name, phone, address, city, state, zip) values(NULL, '".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['phone'])."', '".mysql_real_escape_string($_POST['address'])."', '".mysql_real_escape_string($_POST['city'])."', '".mysql_real_escape_string($_POST['state'])."', '".mysql_real_escape_string($_POST['zip'])."')", $dbh);
				print "<div align=\"center\" class=\"success\">Warehouse added successfully!</div><br>";
			}else{
				mysql_query("update warehouse set name = '".mysql_real_escape_string($_POST['name'])."', phone = '".mysql_real_escape_string($_POST['phone'])."', address = '".mysql_real_escape_string($_POST['address'])."', city = '".mysql_real_escape_string($_POST['city'])."', state = '".mysql_real_escape_string($_POST['state'])."', zip = '".mysql_real_escape_string($_POST['zip'])."' where id = '".$_GET['mod']."'", $dbh);
				print "<div align=\"center\" class=\"success\">Warehouse updated successfully!</div><br>";
			}
			
			$success=1;
		}
	}
	if(isset($_GET['mod'])){
		$results = mysql_query("select * from warehouse where id = '".$_GET['mod']."'", $dbh);
		if(mysql_num_rows($results)){
			$mdb = mysql_fetch_assoc($results);
		}else{
			print "<div align=\"center\" class=\"error\">No Warehouse with ID of '".$_GET['mod']."'!</div>";
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div><br>";
?>
	<form action="warehouse.php?<? if(isset($_GET['add'])){ print "add=1"; }elseif(isset($mdb)){ print "mod=".$mdb['id']; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr class="bar"><td><? if(isset($_GET['add'])) print "Add"; else print "Modify"; ?> Warehouse</td><td align="right"><a href="warehouse.php" style="color:white;">View All</a></td></tr>
		<tr><td><b>Name</b></td><td><input type="text" name="name" size="50" value="<? if(isset($error)){ print $_POST['name']; }elseif(isset($mdb)){ print $mdb['name']; } ?>"></td></tr>
		<tr><td><b>Phone</b></td><td><input type="text" name="phone" size="50" value="<? if(isset($error)){ print $_POST['phone']; }elseif(isset($mdb)){ print $mdb['phone']; } ?>"></td></tr>
		<tr><td><b>Address</b></td><td><input type="text" name="address" size="50" value="<? if(isset($error)){ print $_POST['address']; }elseif(isset($mdb)){ print $mdb['address']; } ?>"></td></tr>
		<tr><td><b>City</b></td><td><input type="text" name="city" size="30" value="<? if(isset($error)){ print $_POST['city']; }elseif(isset($mdb)){ print $mdb['city']; } ?>"></td></tr>
		<tr><td class="field_title">State</td><td><select name="state"><option value=""></option><?
			$results = mysql_query("select * from states order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['abbr']."\"";
				if((isset($error) && $_POST['state'] == $row['abbr']) || (isset($mdb) && $mdb['state'] == $row['abbr']))
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?></select></td></tr>
		<tr><td><b>Zip Code</b></td><td><input type="text" name="zip" size="10" maxlength="5" value="<? if(isset($error)){ print $_POST['zip']; }elseif(isset($mdb)){ print $mdb['zip']; } ?>"></td></tr>
		<tr class="bar"><td colspan="2" align="center"><input type="submit" value="<? if(isset($_GET['add'])) print "Add"; else print "Update"; ?> Warehouse"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($_GET['add']) && !isset($_GET['mod'])) || isset($success)){
	if(isset($_GET['delete'])){
		$results = mysql_query("select * from product_warehouse where warehouse = '".$_GET['delete']."'", $dbh);
		if(mysql_num_rows($results)){
			print "<div align=\"center\" class=\"error\">Warehouse still attached to products!</div><br>";
		}else{
			mysql_query("delete from warehouse where id = '".$_GET['delete']."'", $dbh);
			print "<div align=\"center\" class=\"success\">Warehouse deleted!</div><br>";
		}
	}

	$results = mysql_query("select * from warehouse order by name, city asc", $dbh);
	if(mysql_num_rows($results)){
		print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
		print "<tr class=\"bar\"><td>Warehouses</td><td colspan=\"2\" style=\"text-align:right;\"><a href=\"".$_SERVER['PHP_SELF']."?add=1\" style=\"color:#FFFFFF;\">Add a Warehouse</a></td></tr>";
		$i = 0;
		while($row = mysql_fetch_assoc($results)){
			print "<tr class=\"row".($i%2?1:2)."\"><td><a href=\"warehouse.php?mod=".$row['id']."\">".($row['name']?$row['name']:"N/A")."</a></td><td>".$row['city'].", ".$row['state']." ".$row['zip']."</td><td align=\"right\"><a href=\"warehouse.php?delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this Warehouse?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
			$i++;
		}
		print "</table>";
	}
}
include("footer.inc");
?>
</body>
</html>
