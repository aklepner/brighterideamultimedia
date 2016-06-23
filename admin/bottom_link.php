<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - bottom links</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
if(isset($_GET['add']) || isset($_GET['mod'])){
	if(isset($_POST['name'])){
		// Process Form
		if($_POST['name'] == "")
			$error = "Name must be specified!";
		elseif($_POST['url'] == "")
			$error = "URL must be specified!";

		if(!$error){
			if(isset($_GET['add'])){
				mysql_query("insert into bottom_link (id, name, url, sort) values(NULL, '".$_POST['name']."', '".$_POST['url']."', '".$_POST['sort']."')", $dbh);
				print "<div align=\"center\" class=\"success\">Link added successfully!</div><br>";
			}else{
				mysql_query("update bottom_link set name = '".$_POST['name']."', url = '".$_POST['url']."', sort = '".$_POST['sort']."' where id = '".$_GET['mod']."'", $dbh);
				print "<div align=\"center\" class=\"success\">Link updated successfully!</div><br>";
			}
			$success=1;
		}
	}
	if(isset($_GET['mod'])){
		$results = mysql_query("select * from bottom_link where id = '".$_GET['mod']."'",$dbh);
		if(mysql_num_rows($results)){
			$mdb = mysql_fetch_assoc($results);
		}else{
			print "<div align=\"center\">No Bottom Link with ID of '".$_GET['mod']."'!</div><br />";
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>";
?>
	<form action="<?=$_SERVER['PHP_SELF']?>?<? if(isset($mdb)){ print "mod=".$mdb['id']; }else{ print "add=1"; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" class="box">
		<tr class="bar"><td><? if(isset($mdb)) print "Modify"; else print "Add"; ?> Bottom Link</td><td style="text-align:right;" colspan="2"><a href="<?=$_SERVER['PHP_SELF']?>">View All</a></td></tr>
		<tr><td class="field_title">Name</td><td><input type="text" name="name" size="40" value="<? if(isset($error)){ print stripslashes($_POST['name']); }elseif(isset($mdb)){ print $mdb['name']; } ?>"></td></tr>
		<tr><td class="field_title">URL</td><td><input type="text" name="url" size="60" value="<? if(isset($error)){ print stripslashes($_POST['url']); }elseif(isset($mdb)){ print $mdb['url']; } ?>"></td></tr>
		<tr><td class="field_title">Sort</td><td><input type="text" name="sort" size="5" value="<? if(isset($error)){ print stripslashes($_POST['sort']); }elseif(isset($mdb)){ print $mdb['sort']; } ?>"></td></tr>
		<tr class="bar"><td colspan="2" style="text-align:center;"><input type="submit" value="<? if(isset($mdb)) print "Modify"; else print "Add"; ?> Bottom Link"></td></tr>
	</table>
	</form>
<?
	}
}
if((!isset($_GET['add']) && !isset($_GET['mod'])) || isset($success)){
	if(isset($_GET['delete'])){
		mysql_query("delete from bottom_link where id = '".$_GET['delete']."'");
		print "<div align=\"center\" class=\"success\">Bottom Link deleted!</div><br>";
	}
	
	$results = mysql_query("select * from bottom_link order by sort, name", $dbh);
	print "<table align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
	print "<tr class=\"bar\"><td>Bottom Links</td><td><a href=\"".$_SERVER['PHP_SELF']."?add=1\">Add a Link</a></td></tr>";
	if(mysql_num_rows($results)){
		$i = 0;
		while($row = mysql_fetch_assoc($results)){
			echo "<tr";
			if($i%2) 
				echo " class=\"shade\"";			
			echo "><td><a href=\"".$_SERVER['PHP_SELF']."?mod=".$row['id']."\">".$row['name']."</a></td><td align=\"right\"><a href=\"".$_SERVER['PHP_SELF']."?delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this link?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
			$i++;
		}
	}else{
		print "<tr><td colspan=\"2\">No Links Available</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>