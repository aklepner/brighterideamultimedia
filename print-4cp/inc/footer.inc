
	<table border="0" cellspacing="0" cellpadding="5" style="margin:10px auto;">
      <tr>
        <td><a href="http://www.facebook.com/pages/data-business-systems-Inc/107138945998394" target="_blank"><img src="http://www.databusinesssystems.com/buttons/facebook.png" width="32" height="32" border="0" /></a></td>
        <td><a href="https://twitter.com/#!/Data_Business" target="_blank"><img src="http://www.databusinesssystems.com/buttons/twitter.png" width="32" height="32" border="0" /></a></td>
        <td><a href="http://databusinesssystemsblog.com/" target="_blank"><img src="http://www.databusinesssystems.com/buttons/wordpress-2.png" width="32" height="32" border="0" /></a></td>
        <td><a href="http://www.linkedin.com/company/data-business-systems-inc-" target="_blank"><img src="http://www.databusinesssystems.com/buttons/linkedin.png" width="32" height="32" border="0" /></a></td><td><a href="http://www.databusinesssystems.com/" target="_blank"><img src="https://www.databusinesssystems.com/images/logos/databusinesssystems.png" alt="Data Business Systems" border="0" style="width:90px;" /></a></td>
         <td><a href="http://medical-forms.com" target="_blank"><img src="http://www.databusinesssystems.com/images/logos/medical-forms.png" width="90" border="0"></a></td>
    <td><a href="http://www.one-write.com/" target="_blank"><img src="http://www.databusinesssystems.com/images/logos/one-write.png" width="90" border="0"></a></td>
    <td><a href="http://www.print-forms.com" target="_blank"><img src="http://www.databusinesssystems.com/images/logos/print-forms.png" width="90" border="0"></a></td>
	 
      </tr>
    </table>
</td></tr>
<tr><td colspan="2" style="background-color:#122B57;color:white;padding:12px;text-align:center;">&copy; <?=date('Y')?> Data Business Systems, Inc. &middot; 230 Route 206 South #301 &middot; Flanders, NJ 07836 &middot; (973) 252-1236 &middot; Fax (973) 252-1885</td></tr>
</table>
<div id="footerlinks"><?
require_once(dirname(__FILE__)."/config.inc");
$thisIsOldPage = false;
$tmpPhpBase = basename($_SERVER['PHP_SELF']);
if($tmpPhpBase == 'product.php' || $tmpPhpBase == 'info.php' || $tmpPhpBase == 'link.php' || $tmpPhpBase == 'affiliate.php' || $tmpPhpBase == 'sample.php')
{
	$thisIsOldPage = true;
}
$results = mysql_query("select * from bottom_link order by sort, name", $dbh);
$tmpSiteName = 'www.' . strtolower(SITE_NAME) . '.com';
if(mysql_num_rows($results)){
	$i = 0;
	while($row = mysql_fetch_assoc($results)){
		if(!$thisIsOldPage || strstr($row['url'], $tmpSiteName)) //hide external footer links on old templates
		{
			if($i) echo ' | ';
			print "<a href=\"".$row['url']."\">".$row['name']."</a>";
			$i++;
		}
	}
}
?>
</div>