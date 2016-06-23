<? 
	require_once(dirname(__FILE__)."/../../inc/dbi.inc");
	require_once(dirname(__FILE__)."/../inc/config.inc");
?>
<table cellpadding="0" cellspacing="5" align="center" width="860" style="background-color:#fff;">
<tr><td colspan="2"><a href="<?=DOCUMENT_BASE?>"><img src="<?=DOCUMENT_BASE?>/images/header.jpg" /></a></td></tr>
<tr><td colspan="2" id="topnav">
	<span><a href="<?=DOCUMENT_BASE?>">Home</a></span> | <span><a href="<?=DOCUMENT_BASE?>/contact.php">Contact</a></span> | <span><a href="<?=DOCUMENT_BASE?>/privacy.php" title="Print-Forms.com Privacy Policy">Privacy</a></span> | <span><a href="<?=DOCUMENT_BASE?>/returns.php">Returns</a></span> | <span><a href="<?=DOCUMENT_BASE?>/cancellations.php">Cancellations</a></span>
</td></tr>
<tr><td colspan="2" style="background:#810041;color:white;padding:5px;text-align:center;font-size:16px;line-height:30px;vertical-align:bottom;">
	<form method="post" action="https://www.databusinesssystems.com/account/" style="margin:0;display:inline;">
		Email <input type="text" name="email" style="width:120px;margin-right:30px;"> Password  <input type="password" name="password" style="width:120px;margin-right:30px;"> <input type="hidden" name="login" value="1"> <input type="submit" value="Login">
	</form>
</td></tr>
<tr><td width="180" valign="top">
<script type="text/javascript" src="<?=DOCUMENT_BASE?>/menu.js"></script>
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
				print '<div><a href="#" onClick="P7_TMenu(this);return false;" title="View more '.$rowName.' products and resources.">'.$rowName.'</a>' . "\n";
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

</td><td width="680" valign="top" style="margin:10px;">
