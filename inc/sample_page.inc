<?
	if(defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true)
	{
		?>
<h1 title="Samples for <?=$category['name']?>"><?=$category['name']?></h1>
<table width="95%" cellpadding="2" cellspacing="2" align="center" summary="A collection of sample files and images for <?=str_replace('"', '&quot;', $category['name'])?> from <?=SITE_NAME?>.com">
		<?
	}
	else
	{
		?>
<h1><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		$link_color = (SITE_NAME == 'Print-Forms') ? '#FFFFFF' : '#CC0000';
		print '<a href="' . DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'sample', $name, $seo__url_fname, $seo__url_dname) . '" title="' . str_replace('"', '&quot;', $category['name']) . '" style="text-decoration:none;color:' . $link_color . ';">';
	}
?><?=$category['name']?><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		print '</a>';
	}
?></h1>
<table width="95%" cellpadding="2" cellspacing="2" align="center">
		<?
	}
?>
<? if($category['description']){ ?>
<tr><td colspan="2"><?=parse_dynamic(nl2br($category['description']))?></td></tr>
<? } ?>
<?
	$results = mysql_query("select * from sample where category = '".$category['id']."' ORDER BY description");
	if(mysql_num_rows($results)){
		$i=1;
		while($row = mysql_fetch_assoc($results)){
			if($i==1)
				print "<tr>";
			print "<td width=\"50%\" valign=\"middle\">";
			if($row['type'] == "pdf")
				print "<a target=\"_blank\" href=\"".DOCUMENT_BASE."/pdf/sample/".$row['id'].".pdf\"" . ((defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true) ? " title=\"" . $category['name'] . "\"" : '') . "><img src=\"".DOCUMENT_BASE."/images/pdf_logo.jpg\" alt=\"".$row['description']."\" title=\"".$row['description']."\" height=\"50\" width=\"50\" align=\"left\" hspace=\"2\" vspace=\"2\" valign=\"absmiddle\"></a>";
			else
				print "<a href=\"".DOCUMENT_BASE."/images/sample/".$row['id'].".jpg\"" . ((defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true) ? " title=\"" . $category['name'] . "\"" : '') . " target=\"_blank\"><img width=\"100\" src=\"".DOCUMENT_BASE."/images/sample/".$row['id'].".jpg\" alt=\"".$category['name']."\" title=\"".$category['name']."\" border=\"1\" align=\"left\" valign=\"middle\" hspace=\"2\" vspace=\"2\"></a></br>";
			print $row['description']."</td>";
			if($i==2){
				print "</tr>";
				$i=1;
			}else
				$i++;
		}
		if($i==2)
			print "<td></td></tr>";
	}else{
		print "<tr><td colspan=\"2\" align=\"center\">No samples for this category!</td></tr>";
	}
?>
</table>