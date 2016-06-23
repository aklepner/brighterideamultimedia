<?
	if(defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true)
	{
		?>
<h1 title="<?=SITE_NAME?>.com - Products and Information for <?=str_replace('"', '&quot;', $name)?>"><?=$name?></h1>
		<?
	}
	else
	{
		?>
<h1><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		$link_color = (SITE_NAME == 'Print-Forms') ? '#FFFFFF' : '#CC0000';
		print '<a href="' . DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'info', $name, $seo__url_fname, $seo__url_dname) . '" title="' . str_replace('"', '&quot;', $name) . '" style="text-decoration:none;color:' . $link_color . ';">';
	}
?><?=$name?><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		print '</a>';
	}
?></h1>
		<?
	}
?>
<br>
<?
	function get_menu($parent=0,$level=0){
		global $dbh;
		$results = mysql_query("select * from menu where site = '".SITE."' and parent = '$parent'", $dbh);
		if(mysql_num_rows($results)){
			while($row = mysql_fetch_assoc($results)){
				print "<div style=\"margin-left:".($level*25)."px;\"><a href=\"\">".$row['name']."</a></div>";
				get_menu($row['id'], $level+1);
			}
		}

	}

	// Parse Info for PHP
	include("../inc/product.inc");
	echo parse_dynamic($data);
	/*
	ob_start();
	eval("?>".$data."<?");
	ob_end_flush();
	*/
?>
