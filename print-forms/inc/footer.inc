
<table width="500" border="0" cellspacing="5" cellpadding="5">
  <tr>
   
    <td><a href="http://medical-forms.com" target="_blank"><img src="http://www.databusinesssystems.com/buttons/medicalforms_off.gif" width="150" border="0"></a></td>
    <td><a href="http://www.one-write.com/" target="_blank"><img src="http://www.databusinesssystems.com/buttons/onewrite_off.gif" width="150" border="0"></a></td>
    <td><a href="http://print-4cp.com" target="_blank"><img src="http://www.databusinesssystems.com/buttons/print4cp_off.gif" width="150" border="0"></a></td>
  </tr>
  <tr>
    
    <td colspan="3"><table width="160" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td><a href="http://www.facebook.com/pages/data-business-systems-Inc/107138945998394" target="_blank"><img src="http://www.databusinesssystems.com/buttons/facebook.png" width="32" height="32" border="0" /></a></td>
        <td><a href="https://twitter.com/#!/Data_Business" target="_blank"><img src="http://www.databusinesssystems.com/buttons/twitter.png" width="32" height="32" border="0" /></a></td>
        <td><a href="http://databusinesssystemsblog.com/" target="_blank"><img src="http://www.databusinesssystems.com/buttons/wordpress-2.png" width="32" height="32" border="0" /></a></td>
        <td><a href="http://www.linkedin.com/company/data-business-systems-inc-" target="_blank"><img src="http://www.databusinesssystems.com/buttons/linkedin.png" width="32" height="32" border="0" /></a></td><td><a href="http://www.databusinesssystems.com/" target="_blank"><img src="https://www.databusinesssystems.com/images/logos/databusinesssystems.png" alt="Data Business Systems" border="0" style="width:90px;" /></a></td>
	 
      </tr>
    </table></td>
  </tr>
</table>
</div>
</td></tr>
<tr><td colspan="2" style="padding:10px 25px 10px 25px;text-align:center;font-size:10px;"><?
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
			if($i) print " | ";
			print "<a href=\"".$row['url']."\">".$row['name']."</a>";
			$i++;
		}
	}
}
?><?php if(strstr($_SERVER['PHP_SELF'], 'index.php')) { print '| <a href="http://www.print-forms.com/resources.php">Resources</a>'; } ?></td></tr>
</table>
<br>
<!-- Begin Foundeffect code v 2.0 https/html -->
<script type="text/javascript" src="&#104;&#116;&#116;&#112;&#115;&#58;&#47;&#47;&#115;&#101;&#114;&#118;&#101;&#114;&#50;&#46;&#119;&#101;&#98;&#45;&#115;&#116;&#97;&#116;&#46;c&#111;&#109;&#47;&#119;&#116;s&#108;og&#46;&#106;&#115;">
</script><script type="text/javascript">

// BEGIN PARAMETERS 
var page_name = '<? if(isset($_POST['process']) && !$error){ echo '_TRGT1'; } else { echo '#'; } ?>';
var invisible = '#';
var text_counter = '#';
// END PARAMETERS 

wtslog('al150139','3','https',page_name,invisible,text_counter);
</script><noscript><a href="&#104;tt&#112;&#58;&#47;&#47;&#119;&#119;&#119;&#46;&#98;&#117;&#115;&#105;&#110;&#101;&#115;&#115;p&#111;s&#105;ti&#118;e.co&#109;&#47;&#97;na&#108;&#121;&#116;&#105;cs/&#112;&#114;&#102;&#47;">
<img src="&#104;&#116;&#116;&#112;&#115;&#58;&#47;&#47;&#115;&#101;&#114;&#118;&#101;&#114;&#50;&#46;&#119;&#101;&#98;&#45;&#115;&#116;&#97;&#116;.&#99;&#111;&#109;/3&#47;&#97;&#108;&#49;&#53;&#48;&#49;&#51;&#57;&#46;&#103;i&#102;" 
style="border:0px;" alt="SEO Found Effect"></a></noscript>
<!-- End Foundeffect code v 2.0 -->