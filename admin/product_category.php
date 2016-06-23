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
define("FORM_DIR",$_SERVER['DOCUMENT_ROOT'].DOCUMENT_BASE."/form/");
$base_dir = $_SERVER['DOCUMENT_ROOT'].DOCUMENT_BASE."/images/product_category/";
if(isset($add) || isset($mod)){
	if(isset($_POST['name'])){
		// Process Form
		if($_POST['name'] == "")
			$error = "Must specify a name!";
		if(!$error && $_FILES[image][name] != ""){
			if(!preg_match("/jpeg/",$_FILES['image']['type'])){
				$error = "Wrong File Type! Image must be JPEG!";
			}elseif($_FILES['image']['size'] > 4*1024*1024){
				$error = "Image too big! Must be less then 4 MB";
			}
		}
		if(!$error && $_FILES[logo][name] != ""){
			if(!preg_match("/jpeg/",$_FILES['logo']['type'])){
				$error = "Wrong File Type! Logo must be JPEG!";
			}elseif($_FILES['logo']['size'] > 4*1024*1024){
				$error = "Logo too big! Must be less then 4 MB";
			}
		}
		if(!$error && $_FILES['form']['name'] != ""){
			if($_FILES['form']['type'] != "text/html"){
				$error = "Wrong File Type! Info Form must be HTML! Type: ".$_FILES['form']['type'];
			}
		}
		if(!$error)
		{
			if($_POST['page_title'] != '')
			{
				$_POST['page_title'] = str_replace('"', '&quot;', trim($_POST['page_title']));
			}
			if($_POST['meta_keywords'] != '')
			{
				$_POST['meta_keywords'] = str_replace('"', '&quot;', trim($_POST['meta_keywords']));
			}
			if($_POST['meta_description'] != '')
			{
				$_POST['meta_description'] = str_replace('"', '&quot;', trim($_POST['meta_description']));
			}
			if($_POST['url_filename'] != '')
			{
				$_POST['url_filename'] = cleanforurl2(trim($_POST['url_filename']));
			}
			if($_POST['url_foldername'] != '')
			{
				$_POST['url_foldername'] = cleanforurl2(trim($_POST['url_foldername']));
			}
		}

		if(!$error){
			if(isset($_GET['add'])){
				mysql_query("
					INSERT
					INTO product_category
					(
						site,
						name,
						description,
						company_url,
						family,
						page_title,
						meta_keywords,
						meta_description,
						url_filename,
						url_foldername
					) VALUES (
						'".SITE."',
						'".mysql_real_escape_string($_POST['name'])."',
						'".mysql_real_escape_string($_POST['description'])."',
						'".$_POST['company_url']."',
						".(trim($_POST['family'])==""?"NULL":"'".mysql_real_escape_string($_POST['family'])."'").",
						'".mysql_real_escape_string($_POST['page_title'])."',
						'".mysql_real_escape_string($_POST['meta_keywords'])."',
						'".mysql_real_escape_string($_POST['meta_description'])."',
						'".mysql_real_escape_string($_POST['url_filename'])."',
						'".mysql_real_escape_string($_POST['url_foldername'])."'
					)
				", $dbh);
				$catid = mysql_insert_id($dbh);
			}else{
				mysql_query("
					UPDATE product_category
					SET
						name = '".mysql_real_escape_string($_POST['name'])."',
						description = '".mysql_real_escape_string($_POST['description'])."',
						company_url = '".$_POST['company_url']."',
						family = ".(trim($_POST['family'])==""?"NULL":"'".mysql_real_escape_string($_POST['family'])."'").",
						page_title = '".mysql_real_escape_string($_POST['page_title'])."',
						meta_keywords = '".mysql_real_escape_string($_POST['meta_keywords'])."',
						meta_description = '".mysql_real_escape_string($_POST['meta_description'])."',
						url_filename = '".mysql_real_escape_string($_POST['url_filename'])."',
						url_foldername = '".mysql_real_escape_string($_POST['url_foldername'])."'
					WHERE id = '".$_GET['mod']."'
				", $dbh);
				$catid = $_GET['mod'];
			}
			if(isset($_POST['delete_image']))
				unlink($base_dir.$catid.".jpg");
			if(isset($_POST['delete_logo']))
				unlink($base_dir."logo/".$catid.".jpg");
			if($_FILES[image][name] != ""){
				if(move_uploaded_file($_FILES['image']['tmp_name'], $base_dir.$catid.".jpg")){
					chmod($base_dir.$catid.".jpg", 0644);
				}
			}
			if($_FILES['logo']['name'] != ""){
				if(move_uploaded_file($_FILES['logo']['tmp_name'], $base_dir."logo/".$catid.".jpg")){
					chmod($base_dir."logo/".$catid.".jpg", 0644);
				}
			}

			if(isset($_POST['delete_form']))
				unlink(FORM_DIR.$catid.".htm");
			if($_FILES['form']['name'] != ""){
				if(move_uploaded_file($_FILES['form']['tmp_name'], FORM_DIR.$catid.".htm")){
					chmod(FORM_DIR.$catid.".htm", 0644);
				}
			}
			print "<div align=\"center\" class=\"success\">Product Category updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($_GET['mod'])){
		$results = mysql_query("select * from product_category where id = '".$_GET['mod']."'", $dbh);
		if(mysql_num_rows($results)){
			$mdb = mysql_fetch_assoc($results);
		}else{
			print "<div align=\"center\" class=\"error\">No Product Category with ID of '".$_GET['mod']."'!</div>";
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div><br>";
?>
	<form action="product_category.php?<? if(isset($_GET['add'])){ print "add=1"; }elseif(isset($mdb)){ print "mod=".$mdb['id']; }?>" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr class="bar"><td>Modify Product Category</td><td align="right"><a href="product_category.php">View All</a></td></tr>
		<tr><td class="field_title">Name</td><td><input type="text" name="name" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['name']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['name']); }?>'></td></tr>
		<tr><td class="field_title">Product Family</td><td><input type="text" name="family" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['family']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['family']); }?>'></td></tr>
		<tr><td class="field_title">Description</td><td><textarea cols="50" rows="10" name="description"><? if(isset($error)){ print htmlspecialchars($_POST[description]); }elseif(isset($mdb)){ print htmlspecialchars($mdb['description']); }?></textarea></td></tr>
		<tr><td class="field_title">Image</td><td><?
		if(isset($mdb) && is_file($base_dir.$mdb['id'].".jpg")){
			print "<img src=\"".DOCUMENT_BASE."/images/thumb/product_category/".$mdb['id'].".jpg\" align=\"right\">";
		}
		?><input type="file" name="image" size="40"><br><?
		if(isset($mdb) && is_file($base_dir.$mdb['id'].".jpg")){
			$img_size = getimagesize($base_dir.$mdb['id'].".jpg");
			print "Type: ".$img_size['mime']." Size:  ".number_format((filesize($base_dir.$mdb['id'].".jpg")/1024),2)." KB<br><input type=\"checkbox\" name=\"delete_image\" value=\"1\" /> Delete current Image?";
				}
		?></td></tr>
		<tr><td class="field_title">Logo</td><td><?
		if(isset($mdb) && is_file($base_dir."logo/".$mdb['id'].".jpg")){
			print "<img src=\"".DOCUMENT_BASE."/images/thumb/product_category/logo/".$mdb['id'].".jpg\" align=\"right\">";
		}
		?><input type="file" name="logo" size="40"><br><?
		if(isset($mdb) && is_file($base_dir."logo/".$mdb['id'].".jpg")){
			$img_size = getimagesize($base_dir."logo/".$mdb['id'].".jpg");
			print " Type: ".$img_size['mime']." Size:  ".number_format((filesize($base_dir."logo/".$mdb['id'].".jpg")/1024),2)." KB<br><input type=\"checkbox\" name=\"delete_logo\" value=\"1\" /> Delete current Logo?";
		}
		?></td></tr>
		<tr><td class="field_title">Info Form</td><td><input type="file" name="form" size="40"><br><?
		if(isset($mdb) && is_file(FORM_DIR.$mdb['id'].".htm")){
			print "<input type=\"checkbox\" name=\"delete_form\"> Delete Form? <a href=\"".DOCUMENT_BASE."/form/".$mdb['id'].".htm\" target=\"_blank\">View Form</a>";
		}
		?></td></tr>
		<tr><td class="field_title">Company URL</td><td><input type="text" name="company_url" size="40" value="<? if(isset($error)){ print $_POST[company_url]; }elseif(isset($mdb)){ print $mdb['company_url']; }?>"></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		<tr><td colspan="2" style="border-bottom: 2px solid #000000; font-weight: bold;">Meta Settings</td></tr>
		<tr><td class="field_title">Title Tag</td><td><input type="text" name="page_title" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['page_title']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['page_title']); }?>'></td></tr>
		<tr><td class="field_title">Meta Keywords</td><td><input type="text" name="meta_keywords" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_keywords']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['meta_keywords']); }?>'></td></tr>
		<tr><td class="field_title">Meta Description</td><td><input type="text" name="meta_description" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_description']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['meta_description']); }?>'></td></tr>
		<tr><td class="field_title">URL Filename</td><td><input type="text" name="url_filename" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_filename']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['url_filename']); }?>'>-p<? if(isset($mdb)){ print $mdb['id']; }else{ print '#'; } ?>.htm</td></tr>
		<tr><td class="field_title">URL Foldername</td><td><input type="text" name="url_foldername" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_foldername']); }elseif(isset($mdb)){ print htmlspecialchars($mdb['url_foldername']); }?>'></td></tr>
		<tr><td class="field_title">Current URL</td><td><? if(!isset($mdb)){ print 'N/A'; }else{ print '/' . SEO_format_url($mdb['id'], 'product', $mdb['name'], $mdb['url_filename'], $mdb['url_foldername']); } ?></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($_GET['add']) && !isset($_GET['mod'])) || isset($success)){
	if(isset($_GET['delete'])){
		$results = mysql_query("select * from menu where site = '".SITE."' and type = 'product' and type_id = '".$_GET['delete']."'");
		if(mysql_num_rows($results)){
			print "<div align=\"center\" class=\"error\">Product Category attached to menu!  Can not delete!</div><br>";
		}else{
			$results = mysql_query("select * from product where category = '".$_GET['delete']."'");
			if(mysql_num_rows($results)){
				print "<div align=\"center\" class=\"error\">Products attached to category!  Please move or delete products first!</div><br>";
			}else{
				@unlink($base_dir.$_GET['delete'].".jpg");
				@unlink($base_dir."logo/".$_GET['delete'].".jpg");
				mysql_query("delete from product_category where id = '".$_GET['delete']."'");
				print "<div align=\"center\" class=\"success\">Product Category deleted!</div><br>";
			}
		}
	}

	$results = mysql_query("select * from product_category where site = '".SITE."' order by family desc, name asc", $dbh);
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr class=\"bar\"><td colspan=\"2\">Product Category</td><td colspan=\"3\" align=\"right\"><a href=\"product_category.php?add=1\">Add a Category</a></td></tr>";
	if(mysql_num_rows($results)){
		print "<tr class=\"row1\"><td align=\"left\"><b>Name</b></td><td align=\"left\"><b>View</b></td><td align=\"center\"><b>Products</b></td><td align=\"left\"><b>Delete?</b></td></tr>";
		$i = 0;
		$family = NULL;
		while($row = mysql_fetch_assoc($results)){
			if($family != $row['family']){
				print "<tr class=\"row".($i%2?1:2)."\"><td colspan=\"4\"><b>";
				if($row['family'])
					print $row['family'];
				else
					print "<i>Individual Products</i>";
				print "</b></td></tr>";
				$family = $row['family'];
				$i++;
			}
			print "<tr class=\"row".($i%2?1:2)."\"><td style=\"padding-left:25px;\"><a href=\"product_category.php?mod=".$row['id']."\">".$row['name']."</a> (".$row['id'].")</td><td align=\"center\"><a target=\"_blank\" href=\"".DOCUMENT_BASE."/product/".$row['id']."\">View</a></td><td align=\"center\"><a href=\"product.php?category=".$row['id']."\">Edit</a> - <a href=\"product_related.php?category=".$row['id']."\">Related</a></td><td align=\"right\"><a href=\"product_category.php?delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this Product Category?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
			$i++;
		}
	}else{
		print "<tr><td colspan=\"5\">No Product Categories Available!</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
