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
		elseif($_POST[url] == "")
			$error = "URL must be specified!";
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
			if(isset($_POST['samewindow']))
				$samewindow = 'y';
			else
				$samewindow = 'n';
			$updb = new dbi();
			if(isset($add)){
				$updb->query("
					INSERT
					INTO link
					(
						site,
						name,
						url,
						samewindow,
						page_title,
						meta_keywords,
						meta_description,
						url_filename,
						url_foldername
					) VALUES (
						'".SITE."',
						'".mysql_real_escape_string($_POST['name'])."',
						'$_POST[url]',
						'$samewindow',
						'".mysql_real_escape_string($_POST['page_title'])."',
						'".mysql_real_escape_string($_POST['meta_keywords'])."',
						'".mysql_real_escape_string($_POST['meta_description'])."',
						'".mysql_real_escape_string($_POST['url_filename'])."',
						'".mysql_real_escape_string($_POST['url_foldername'])."'
					)
				");
			}else{
				$updb->query("
					UPDATE link
					SET
						name = '".mysql_real_escape_string($_POST['name'])."',
						url = '$_POST[url]',
						samewindow = '$samewindow',
						page_title = '".mysql_real_escape_string($_POST['page_title'])."',
						meta_keywords = '".mysql_real_escape_string($_POST['meta_keywords'])."',
						meta_description = '".mysql_real_escape_string($_POST['meta_description'])."',
						url_filename = '".mysql_real_escape_string($_POST['url_filename'])."',
						url_foldername = '".mysql_real_escape_string($_POST['url_foldername'])."'
					WHERE id = '$mod'
				");
			}
			print "<div align=\"center\" class=\"success\">Link updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from link where site = '".SITE."' and id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Link Entry with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form action="links.php?<?if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Link</td><td align="right"><a href="links.php" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">Name</td><td><input type="text" name="name" size="50" value="<? if(isset($error)){ print $_POST[name]; }elseif(isset($moddb)){ print $moddb->result("name"); }?>"></td></tr>
		<tr><td class="field_title">URL</td><td><input type="text" name="url" size="50" value="<? if(isset($error)){ print $_POST[url]; }elseif(isset($moddb)){ print $moddb->result("url"); }?>"></td></tr>
		<tr><td class="field_title">Same Window</td><td><input type="checkbox" name="samewindow"<? if(isset($error) && isset($_POST[samewindow])){ print " checked=\"checked\""; }elseif(isset($moddb) && ($moddb->result("samewindow") == 'y' || $moddb->result("samewindow") == 'Y')){ print " checked=\"checked\""; }?>></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		<tr><td colspan="2" style="border-bottom: 2px solid #000000; font-weight: bold;">Meta Settings</td></tr>
		<tr><td class="field_title">Title Tag</td><td><input type="text" name="page_title" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['page_title']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('page_title')); }?>'></td></tr>
		<tr><td class="field_title">Meta Keywords</td><td><input type="text" name="meta_keywords" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_keywords']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('meta_keywords')); }?>'></td></tr>
		<tr><td class="field_title">Meta Description</td><td><input type="text" name="meta_description" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_description']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('meta_description')); }?>'></td></tr>
		<tr><td class="field_title">URL Filename</td><td><input type="text" name="url_filename" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_filename']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('url_filename')); }?>'>-l<? if(isset($moddb)){ print $moddb->result('id'); }else{ print '#'; } ?>.htm</td></tr>
		<tr><td class="field_title">URL Foldername</td><td><input type="text" name="url_foldername" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_foldername']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('url_foldername')); }?>'></td></tr>
		<tr><td class="field_title">Current URL</td><td><? if(!isset($moddb)){ print 'N/A'; }else{ print '/' . SEO_format_url($moddb->result('id'), 'link', $moddb->result('name'), $moddb->result('url_filename'), $moddb->result('url_foldername')); } ?></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($add) && !isset($mod)) || isset($success)){
	if(isset($_GET['delete'])){
		$results = mysql_query("select * from menu where site = '".SITE."' and type = 'link' and type_id = '".$_GET['delete']."'");
		if(mysql_num_rows($results)){
			print "<div align=\"center\" class=\"error\">Link attached to Menu!  Please remove from Menu first.</div><br>";
		}else{
			mysql_query("delete from link where site = '".SITE."' and id = '".$_GET['delete']."'");
			print "<div align=\"center\" class=\"success\">Link Item deleted!</div><br>";
		}
	}

	$results = mysql_query("select * from link where site = '".SITE."' order by name");
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr bgcolor=\"#990000\"><td style=\"color:#FFFFFF;\">Links</td><td><a href=\"links.php?add=1\" style=\"color:#FFFFFF;\">Add a Link</a></td></tr>";
	if(mysql_num_rows($results)){
		$i = 0;
		while($row = mysql_fetch_assoc($results)){
			print "<tr".(!($i%2)?" class=\"shade\"":"")."><td><a href=\"links.php?mod=".$row['id']."\">".$row['name']."</a></td><td align=\"right\"><a href=\"links.php?delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this Link?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
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
