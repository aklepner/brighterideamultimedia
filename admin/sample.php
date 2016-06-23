<?
	if(!isset($_GET[category])){
		header("Location: sample_category.php");
		exit;
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
define("BASE_IMAGE_DIR",$_SERVER[DOCUMENT_ROOT].DOCUMENT_BASE."/images/sample/");
define("BASE_PDF_DIR",$_SERVER[DOCUMENT_ROOT].DOCUMENT_BASE."/pdf/sample/");
$base_dir = $_SERVER[DOCUMENT_ROOT].DOCUMENT_BASE."/images/sample/";
if(isset($add) || isset($mod)){
	if(isset($_POST[description])){
		// Process Form
		if(!$error && $_FILES['file'][name] != ""){
			if(!preg_match("/(jpeg|pdf)/",$_FILES['file']['type'])){
				$error = "Wrong File Type! Must be JPEG or PDF!";
			}elseif($_FILES['image']['size'] > 500000){
				$error = "File too big! Must be less then 500 KB";
			}
		}
		if(!$error){
			$updb = new dbi();
			if($_FILES['file'][name] != "" && preg_match("/pdf/",$_FILES['file']['type'])){
				$file_type = "pdf";
				$base_dir = BASE_PDF_DIR;
			}else{
				$file_type = "jpg";
				$base_dir = BASE_IMAGE_DIR;
			}
			if(isset($add)){
				$imageid = $updb->query("insert into sample (category, description, type) values ('$_GET[category]', '".addslashes($_POST[description])."', '$file_type')");
			}else{
				$query = "update sample set category = '$_GET[category]', description = '".addslashes($_POST[description])."'";
				if($_FILES['file'][name] != "")
					$query .= ", type = '$file_type'";
				$query .= " where id = '$mod'";
				$updb->query($query);
				$imageid = $mod;
			}
			if($_FILES['file'][name] != ""){
				if(move_uploaded_file($_FILES['file']['tmp_name'], $base_dir.$imageid.".".$file_type)){
					chmod($base_dir.$imageid.".".$file_type, 0644);
				}
			}
			print "<div align=\"center\" class=\"success\">Sample updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from sample where id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Sample with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form action="sample.php?category=<?=$_GET[category]?>&<?if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Sample</td><td align="right"><a href="sample.php?category=<?=$_GET[category]?>" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">Description</td><td><textarea cols="50" rows="10" name="description"><?if(isset($error)){ print $_POST[description]; }elseif(isset($moddb)){ print $moddb->result("description"); }?></textarea></td></tr>
		<tr><td class="field_title">File</td><td><input type="file" name="file"><br>JPEG or PDF</td></tr>
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
			@unlink(BASE_IMAGE_DIR.$delete.".jpg");
			@unlink(BASE_PDF_DIR.$delete.".jpg");
			$deldb->query("delete from sample where id = '$delete'");
			print "<div align=\"center\" class=\"success\">Sample deleted!</div><br>";
		}else{
			print "<div align=\"center\" class=\"error\">Are you sure you want to delete this Sample? <a class=\"error\" href=\"sample.php?category=$_GET[category]&delete=$delete&confirm=y\">Yes</a>  <a class=\"error\" href=\"sample.php?category=$_GET[category]\">No</a></div><br>";
		}
	}
	
	$db = new dbi();
	$db->query("select * from sample where category = '$_GET[category]' order by description asc");
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr bgcolor=\"#990000\"><td style=\"color:#FFFFFF;\">Samples</td><td align=\"right\"><a href=\"sample.php?category=$_GET[category]&add=1\" style=\"color:#FFFFFF;\">Add a Sample</a></td></tr>";
	if($db->numrows()){
		while($db->loop()){
			print "<tr><td><a href=\"sample.php?category=$_GET[category]&mod=".$db->result("id")."\">".substr($db->result("description"),0,100)."</a></td><td align=\"right\"><a href=\"sample.php?category=$_GET[category]&delete=".$db->result("id")."\">Delete?</a></td></tr>";
		}
	}else{
		print "<tr><td colspan=\"2\" align=\"center\">No Samples for that Category!</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
