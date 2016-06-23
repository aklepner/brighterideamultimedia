<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - affiliate</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
if(isset($_GET['add']) || isset($_GET['mod'])){
	if(isset($_POST[name])){
		// Process Form
		if($_POST['name'] == "")
			$error = "Name must be specified!";
		elseif((strlen($_POST['urlname']) < 3 || strlen($_POST['urlname']) > 20) || !preg_match("/^[A-z0-9_]+$/",$_POST[urlname]))
			$error = "Please specify a valid URL Name!";
		elseif($_POST['data'] == "")
			$error = "Data must be specified!";
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
			if(isset($add)){
				mysql_query("
					INSERT
					INTO affiliate
					(
						site,
						name,
						urlname,
						product,
						account,
						data,
						page_title,
						meta_keywords,
						meta_description,
						url_filename,
						url_foldername
					) VALUES (
						'".SITE."',
						'".mysql_real_escape_string($_POST['name'])."',
						'".$_POST['urlname']."',
						'".$_POST['product']."',
						'".$_POST['account']."',
						'".mysql_real_escape_string($_POST['data'])."',
						'".mysql_real_escape_string($_POST['page_title'])."',
						'".mysql_real_escape_string($_POST['meta_keywords'])."',
						'".mysql_real_escape_string($_POST['meta_description'])."',
						'".mysql_real_escape_string($_POST['url_filename'])."',
						'".mysql_real_escape_string($_POST['url_foldername'])."'
					)
				", $dbh);
			}else{
				mysql_query("
					UPDATE affiliate
					SET
						name = '".mysql_real_escape_string($_POST['name'])."',
						urlname = '".$_POST['urlname']."',
						product = '".$_POST['product']."',
						account = '".$_POST['account']."',
						data = '".mysql_real_escape_string($_POST['data'])."',
						page_title = '".mysql_real_escape_string($_POST['page_title'])."',
						meta_keywords = '".mysql_real_escape_string($_POST['meta_keywords'])."',
						meta_description = '".mysql_real_escape_string($_POST['meta_description'])."',
						url_filename = '".mysql_real_escape_string($_POST['url_filename'])."',
						url_foldername = '".mysql_real_escape_string($_POST['url_foldername'])."'
					WHERE id = '$mod'
				", $dbh);
			}
			print "<div align=\"center\" class=\"success\">Affiliate updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from affiliate where site = '".SITE."' and id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Affiliate with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form name="information" action="<?=$_SERVER[PHP_SELF]?>?<?if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post" target="_self">
		<table align="center" cellpadding="3" cellspacing="0" border="0" width="90%">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Affiliate</td><td align="right"><a href="<?=$_SERVER[PHP_SELF]?>" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">Name</td><td><input type="text" name="name" size="50" value="<? if(isset($error)){ print htmlspecialchars($_POST[name]); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result("name")); }?>"></td></tr>
		<tr><td class="field_title">URL Name</td><td><input type="text" name="urlname" size="20" value="<? if(isset($error)){ print $_POST[urlname]; }elseif(isset($moddb)){ print $moddb->result("urlname"); }?>"></td></tr>
		<tr><td class="field_title">Report Product</td><td><select name="product"><option value=""></option><?
			$results = mysql_query("select id, name from product_category where site = '".SITE."' order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['id']."\"";
				if((isset($error) && $_POST['product'] == $row['id']) || (!isset($error) && isset($moddb) && $moddb->result("product") == $row['id']))
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?></select></td></tr>
		<tr><td class="field_title">Report Account</td><td><input type="text" name="account" size="5" value="<? if(isset($error)){ print $_POST['account']; }elseif(isset($moddb)){ print $moddb->result("account"); }?>"></td></tr>
		<tr><td class="field_title" colspan="2">Data</td></tr>
		<tr><td colspan="2" align="center"><textarea cols="50" rows="10" name="data" style="width:90%; height:400px;"><? if(isset($error)){ print htmlentities($_POST['data']); }elseif(isset($moddb)){ print htmlentities($moddb->result("data")); }?></textarea></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update" onClick="document.information.action='<?=$_SERVER[PHP_SELF]?>?<? if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>'; document.information.target='_self'; document.information.submit()">&nbsp;&nbsp;<input type="button" value="Preview" onClick="document.information.action='<?=DOCUMENT_BASE?>/affiliate.php'; document.information.target='_blank'; document.information.submit()"></td></tr>
		<tr><td colspan="2" style="border-bottom: 2px solid #000000; font-weight: bold;">Meta Settings</td></tr>
		<tr><td class="field_title">Title Tag</td><td><input type="text" name="page_title" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['page_title']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('page_title')); }?>'></td></tr>
		<tr><td class="field_title">Meta Keywords</td><td><input type="text" name="meta_keywords" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_keywords']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('meta_keywords')); }?>'></td></tr>
		<tr><td class="field_title">Meta Description</td><td><input type="text" name="meta_description" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_description']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('meta_description')); }?>'></td></tr>
		<tr><td class="field_title">URL Filename</td><td><input type="text" name="url_filename" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_filename']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('url_filename')); }?>'>-a<? if(isset($moddb)){ print $moddb->result('id'); }else{ print '#'; } ?>.htm</td></tr>
		<tr><td class="field_title">URL Foldername</td><td><input type="text" name="url_foldername" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_foldername']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('url_foldername')); }?>'></td></tr>
		<tr><td class="field_title">Current URL</td><td><? if(!isset($moddb)){ print 'N/A'; }else{ print '/' . SEO_format_url($moddb->result('id'), 'affiliate', $moddb->result('name'), $moddb->result('url_filename'), $moddb->result('url_foldername')); } ?></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update" onClick="document.information.action='<?=$_SERVER[PHP_SELF]?>?<? if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>'; document.information.target='_self'; document.information.submit()">&nbsp;&nbsp;<input type="button" value="Preview" onClick="document.information.action='<?=DOCUMENT_BASE?>/affiliate.php'; document.information.target='_blank'; document.information.submit()"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($add) && !isset($mod)) || isset($success)){
	if(isset($_GET['delete'])){
		mysql_query("delete from affiliate where site = '".SITE."' and id = '".$_GET['delete']."'", $dbh);
		print "<div align=\"center\" class=\"success\">Affiliate deleted!</div><br>";
	}

	$results = mysql_query("select * from affiliate where site = '".SITE."' order by name", $dbh);
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr bgcolor=\"#900\"><td style=\"color:#fff;\">Affiliate</td><td><a href=\"$_SERVER[PHP_SELF]?add=1\" style=\"color:#fff;\">Add Affiliate</a></td></tr>";
	if(mysql_num_rows($results)){
		$i = 0;
		while($row = mysql_fetch_assoc($results)){
			print "<tr".($i%2?" class=\"shade\"":"")."><td><a href=\"$_SERVER[PHP_SELF]?mod=".$row['id']."\">".$row['name']."</a></td><td align=\"right\"><a href=\"$_SERVER[PHP_SELF]?delete=".$row['id']."\" onclick=\"if(confirm('Are you sure you want to delete this affiliate?')){ return true; }else{ return false; }\">Delete?</a></td></tr>";
			$i++;
		}
	}else{
		print "<tr><td colspan=\"2\" align=\"center\">No Affiliates available!</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
