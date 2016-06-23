<?
	require_once("/home/bocawebgroup/public_html/inc/config.inc");
?>
<table cellpadding="0" id="medhead" cellspacing="0" border="0" width="900" align="center">
<tr>
  <td class="top_menu"><a href="http://www.medical-forms.com"><img src="<?=DOCUMENT_BASE?>/themeimages/logo.gif" width="900" height="125" alt="Medical Forms, Health Insurance Claim Forms, CMS1500, UB92, Hospital Claim Forms" title="Medical Forms, Health Insurance Claim Forms, CMS1500, UB92, Hospital Claim Forms" border="0"></a>
    <ul id="topmenulist">
      <li><a href="<?=DOCUMENT_BASE?>/contact.php" class="top_menu" title="Contact Medical-Forms.com for your Medical Forms, HCFA 1500, HIPAA Forms, Prescription Pads, and Health Insurance Claim Forms needs.">Contact</a></li>
      <li><a href="<?=DOCUMENT_BASE?>/privacy.php" class="top_menu" title="Medical-Forms.com Privacy Policy">Privacy</a></li>
      <li><a href="<?=DOCUMENT_BASE?>/returns.php" class="top_menu" title="Medical-Forms.com Return Policy">Returns</a></li>
      <li><a href="<?=DOCUMENT_BASE?>/cancellations.php" class="top_menu" title="Medical-Forms.com Cancellation Policy">Cancellations</a></li>
      <li id="top_menu_last"><a href="<?=DOCUMENT_BASE?>/affiliate_links.php" class="top_menu" title="Medical-Forms.com Affiliates, Partners and Resources">Affiliates</a></li>
    </ul></td>
</tr>

</table>


<table cellpadding="0" cellspacing="0" border="0" width="900" style="background-color:#FFFFFF;" align="center">
<tr>
<? if(!defined("NO_MENU")){ ?>
	<td width="205" valign="top" style="background-color:#FFFFFF;">
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
	', $dbh)
		or error_log("MySQL Error: ".mysql_error($dbh)." in ".__FILE___." on line ".__LINE__);

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
<? if($_SERVER["HTTPS"] == 'on'){ ?>
<br><div align="center">
<script language="JavaScript" src='https://secure.comodo.net/trustlogo/javascript/trustlogo.js'></script>
<script type="text/javascript">TrustLogo("https://www.databusinesssystems.com/images/secure_safe.gif", "PAIR", "none");</script>
</div>
<? } ?>
	</td>
<? } ?>
	<td valign="top" style="padding:0 2px 4px 2px;">
