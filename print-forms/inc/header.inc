<? 
	require_once(dirname(__FILE__)."/../../inc/dbi.inc");
	require_once(dirname(__FILE__)."/../inc/config.inc");
?>
<table cellpadding="0" cellspacing="0" align="center" width="787">
<tr><td colspan="2" class="top_menu"><b>Customer Service 1-800-778-6247</b><br><a href="<?=DOCUMENT_BASE?>/contact.php" title="Contact Print-Forms.com for your Print Forms, Tax Forms, Check Stock, and Digital Printing needs.">Contact</a> . <a href="<?=DOCUMENT_BASE?>/privacy.php" title="Print-Forms.com Privacy Policy">Privacy</a> . <a href="<?=DOCUMENT_BASE?>/returns.php" title="Print-Forms.com Return Policy">Returns</a> . <a href="<?=DOCUMENT_BASE?>/cancellations.php" title="Print-Forms.com Cancellation Policy">Cancellations</a> . <a href="http://www.databusinesssystems.com/affiliates.html" title="Print-Forms.com Affiliates, Partners and Resources">Affiliates</a></td></tr>
<tr><td width="180" valign="top"><a href="http://www.print-forms.com/" target="_top" title="Print-Forms.com - Print Forms, Software Compatible forms, International Trade, Tax Forms, and Digital Printing"><img src="<?=DOCUMENT_BASE?>/images/logo.gif" width="156" height="85" alt="Print Forms, Software Compatible forms, International Trade, Tax Forms, and Digital Printing" title="Print Forms, Software Compatible forms, International Trade, Tax Forms, and Digital Printing" border="0" style="margin:10px;"></a><br />
<script type="text/javascript" src="http://www.medical-forms.com/menu.js"></script>
<?

function has_cat_kids($parent=0)
{
	global $dbh;
	$num = 0;
	$results = mysql_query('
		SELECT COUNT(*) as num_kids
		FROM menu AS m
		WHERE m.site = \'' . SITE . '\'
		AND m.parent = \'' . $parent . '\'
	', $dbh);
	if(mysql_num_rows($results))
	{
		$row = mysql_fetch_assoc($results);
		$num = ($row['num_kids'] + 0);
	}
	
	return (($num > 0) ? true : false);
}

function get_cats($parent=0){
	global $dbh;
	$results = mysql_query('
		SELECT
			m.id,
			m.name,
			
			c.name AS product_name,
			c.url_filename AS product_fname,
			c.url_foldername AS product_dname,
			i.name AS info_name,
			i.url_filename AS info_fname,
			i.url_foldername AS info_dname,
			s.name AS sample_name,
			s.url_filename AS sample_fname,
			s.url_foldername AS sample_dname,
			a.name AS affiliate_name,
			a.url_filename AS affiliate_fname,
			a.url_foldername AS affiliate_dname,
			l.name AS link_name,
			l.url_filename AS link_fname,
			l.url_foldername AS link_dname,
			
			m.type,
			m.type_id
		FROM
			menu AS m
			LEFT JOIN product_category AS c
				ON (c.id = m.type_id AND m.site = c.site AND m.type = \'product\')
			LEFT JOIN info AS i
				ON (i.id = m.type_id AND m.site = i.site AND m.type = \'info\')
			LEFT JOIN sample_category AS s
				ON (s.id = m.type_id AND m.site = s.site AND m.type = \'sample\')
			LEFT JOIN affiliate AS a
				ON (a.id = m.type_id AND m.site = a.site AND m.type = \'affiliate\')
			LEFT JOIN link AS l
				ON (l.id = m.type_id AND m.site = l.site AND m.type = \'link\')
		WHERE m.site = \'' . SITE . '\'
		AND m.parent = \'' . $parent . '\'
		ORDER BY m.sort DESC
	', $dbh);
	
	if(mysql_num_rows($results)){
		while($row = mysql_fetch_assoc($results)){
			$typeTxt = '';
			$tmp_seo_name = '';
			$tmp_seo_fname = '';
			$tmp_seo_dname = '';
			$rowName = htmlentities($row['name']);
			if($row['type'] != 'none' && $row['type'] != ''){
				switch($row['type'])
				{
					case 'product':
						$typeTxt = 'Buy ';
						break;
					case 'sample':
						$typeTxt = 'Samples for ';
						break;
					default:
						$typeTxt = 'More info on ';
						break;
				}
				$tmp_seo_name = $row[$row['type'] . '_name'];
				$tmp_seo_fname = $row[$row['type'] . '_fname'];
				$tmp_seo_dname = $row[$row['type'] . '_dname'];
				
				$urlPath = SEO_format_url($row['type_id'], $row['type'], $tmp_seo_name, $tmp_seo_fname, $tmp_seo_dname);
				$isParent = has_cat_kids($row['id']);
				if($isParent)
				{
					print '<div><a href="'.DOCUMENT_BASE.'/'.$urlPath.'" onClick="return true;P7_TMenu(this);" title="Click to see more '.$rowName.' products and resources.">'.$rowName.'</a>' . "\n";
					get_cats($row['id']);
					print '</div>' . "\n";
				}
				else
				{
					print '<div><a href="'.DOCUMENT_BASE.'/'.$urlPath.'" title="'.$typeTxt.$rowName.'.">'.$rowName.'</a></div>' . "\n";
				}
			}else{
				print '<div><a href="#" onClick="P7_TMenu(this);return false;" title="Click to see more '.$rowName.' products and resources.">'.$rowName.'</a>' . "\n";
				get_cats($row['id']);
				print '</div>' . "\n";
			}
		}
		return true;
	}
	return false;
}
?>
<div id="p7TMnav">
<? 	get_cats(0); ?>
</div>
<img src="<?=DOCUMENT_BASE?>/images/left_forms.gif" alt="Print-Forms.com - Print Forms, Check Printing, Software Compatible forms, International Trade, Tax Forms, and Digital Printing" title="Print-Forms.com - Print Forms, Software Compatible forms, International Trade, Tax Forms, and Digital Printing">
</td><td width="607" style="background-color:#FFFFFF;background-image:url('<?=DOCUMENT_BASE?>/images/bg_main.gif');" valign="top">
<? if(defined("SHOW_HEADER")){ ?>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="607" height="220">
    	<param name="movie" value="<?=DOCUMENT_BASE?>/intro.swf">
    	<param name="quality" value="high">
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		if (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]){
		   document.write('<embed src="<?=DOCUMENT_BASE?>/intro.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="607" height="220"></embed>');
		} else {
		   document.write('<img src="<?=DOCUMENT_BASE?>/images/noflash.gif" width="607" height="225" alt="Welcome to Print-Forms.com" title="Welcome to Print-Forms.com - Check Printing, Tax Forms, International Trade Forms, Graphic Design, Mailing Services, and Commercial Printing" />');
		}
		// --> 
		</SCRIPT>
   	</object>
<? } ?>
<div style="margin:15px;width:577px;">