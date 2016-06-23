<?
	require_once("/home/bocawebgroup/public_html/inc/config.inc");
?>
<table width="900" cellpadding="0" cellspacing="0" align="center" style="background-color:#FFFFFF;">
<tr style="background-color:#000000;"><td colspan="2"><div style="text-align:right;color:#FFFFFF;"><b>Customer Service (800) 778-6247</b><br><a href="<?=DOCUMENT_BASE?>/contact.php" style="color:#FFFFFF;">Contact</a> &middot; <a href="<?=DOCUMENT_BASE?>/privacy.php" style="color:#FFFFFF;">Privacy</a> &middot; <a href="<?=DOCUMENT_BASE?>/returns.php" style="color:#FFFFFF;">Returns</a> &middot; <a href="<?=DOCUMENT_BASE?>/cancellations.php" style="color:#FFFFFF;">Cancellations</a> &middot; <a href="https://www.databusinesssystems.com/account/" style="color:#FFFFFF;">Customer Login</a></div></td></tr>
<tr><td width="162"><a href="http://www.one-write.com/"><img src="<?=DOCUMENT_BASE?>/images/top_left.jpg" width="162" height="132" alt="One-Write Accounting Systems Safeguard Mcbee Deluxe Custom" title="One-Write Accounting Systems Safeguard Mcbee Deluxe Custom" border="0"></a></td><td width="738"><img src="<?=DOCUMENT_BASE?>/images/top_right.jpg" alt="One-Write Accounting Systems Safeguard Mcbee Deluxe Custom" title="One-Write Accounting Systems Safeguard Mcbee Deluxe Custom" width="738" height="132" border="0"></td></tr>
<tr><td rowspan="3" valign="top" width="162" style="background-image:url(<?=DOCUMENT_BASE?>/images/menu_bg.jpg);background-color:#000099;padding-right:9px;">
<script type="text/javascript" src="http://www.medical-forms.com/menu.js"></script>
<?
function get_cats($parent=0){
	global $dbh;
	$results = mysql_query("select id, name, type, type_id from menu where site = '".SITE."' and parent = '$parent' order by sort desc", $dbh);
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
</td><td width="738"><img src="<?=DOCUMENT_BASE?>/images/top_bottom.jpg" width="738" height="64" border="0"></td></tr>
<tr><td width="738" style="padding:15px;" valign="top">
