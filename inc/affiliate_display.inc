<?
	if(isset($name) && isset($data)){
		if(defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true)
		{
?>
<h1 title="<?=SITE_NAME?>.com - Affiliates - <?=str_replace('"', '&quot;', $name)?>"><?=$name?></h1>
<?
		}
		else
		{
?>
<h1><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		$link_color = (SITE_NAME == 'Print-Forms') ? '#FFFFFF' : '#CC0000';
		print '<a href="' . DOCUMENT_BASE . '/' . SEO_format_url($aff_id, 'affiliate', $name, $seo__url_fname, $seo__url_dname) . '" title="' . str_replace('"', '&quot;', $name) . '" style="text-decoration:none;color:' . $link_color . ';">';
	}
?><?=$name?><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		print '</a>';
	}
?></h1>
<?
		}
		include("../inc/product.inc");
		echo parse_dynamic($data);
		/*
		ob_start();
		eval("?>".$data."<?");
		$output = ob_get_contents();
		ob_end_clean();
		print $output;
		*/
	}else{
?>
<h1<?=((defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true) ? ' title="'.SITE_NAME.'.com - Affiliates"' : ''); ?>>Affiliates</h1>
<ul>
<?
	$results = mysql_query("select * from affiliate where site = '".SITE."' order by name", $dbh);
	if(mysql_num_rows($results)){
		while($row = mysql_fetch_assoc($results)){
			if(defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true)
			{
				print "<li><a href=\"".SEO_format_url($row['id'], 'affiliate', $row['name'], $row['url_filename'], $row['url_foldername'])."\" title=\"" . str_replace('"', '&quot;', $row['name']) . "\">".$row['name']."</a></li>";
			}
			else
			{
				print "<li><a href=\"".$row['urlname']."\">".$row['name']."</a></li>";
			}
		}
	}
?>
</li>
<? } ?>