<? 
	require_once("/home/databiz/public_html/inc/dbi.inc");
	require_once("/home/databiz/public_html/inc/config.inc");
?>
<table cellpadding="0" cellspacing="0" border="0" width="800" style="background-color:#FFFFFF;" align="center">
<tr><td rowspan="2"><a href="http://www.medical-forms.com" target="_top"><img src="<?=DOCUMENT_BASE?>/images/logo.gif" width="366" height="78" alt="At Medical-Forms.com, we supply a full line of Medical Forms, Computer Compatible Forms, Health Insurance Claim Forms (CMS1500) and UB92 Hospital Claim Forms for the medical practice" border="0"></a></td><td align="right"><table cellpadding="0" cellspacing="0" border="0"><td><img src="<?=DOCUMENT_BASE?>/images/spacer.gif" alt="" height="37" width="1" border="0"></td><td align="right" valign="bottom" class="top_menu"><a href="<?=DOCUMENT_BASE?>/contact.php" class="top_menu">Contact</a> . <a href="<?=DOCUMENT_BASE?>/privacy.php" class="top_menu">Privacy</a> . <a href="<?=DOCUMENT_BASE?>/returns.php" class="top_menu">Returns</a> . <a href="<?=DOCUMENT_BASE?>/affiliate_links.php" class="top_menu">Affiliates</a></td></tr></table></td></tr>
<tr><td><img src="<?=DOCUMENT_BASE?>/images/top_bar.gif" alt="" width="434" height="41" border="0"></td></tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="800" style="background-color:#FFFFFF;" align="center">
<tr>
	<td width="205" valign="top" style="background-color:#FFFFFF;"><br><br><img src="<?=DOCUMENT_BASE?>/images/buyonlinenow.gif" alt="Buy Online Now" width="127" height="22" border="0"><br>
<?
function get_cats($parent=0){
	global $dbh;
	$results = mysql_query("select * from menu where site = '".SITE."' and parent = '$parent' order by sort desc", $dbh);
	if(mysql_num_rows($results)){
		while($row = mysql_fetch_assoc($results)){
			if($row['type'] != 'none' && $row['type'] != ''){
				print "<div><a href=\"".DOCUMENT_BASE."/".$row['type']."/".$row['type_id']."/\">".$row['name']."</a></div>\n";
			}else{
				print "<div><a href=\"#\" onClick=\"P7_TMenu(this);return false\">".$row['name']."</a>\n";
				get_cats($row['id']);
				print "</div>\n";
			}		
		}
	}
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
	<td width="595" valign="top" style="padding:0 2px 4px 2px;">
