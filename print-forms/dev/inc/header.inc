<? 
	require_once(dirname(__FILE__)."/../../../inc/dbi.inc");
	require_once(dirname(__FILE__)."/../../../inc/config.inc");
?>
<table cellpadding="0" cellspacing="0" align="center" width="787">
<tr><td colspan="2" class="top_menu"><b>Customer Service 1-800-778-6247</b><br><a href="<?=DOCUMENT_BASE?>/contact.php">Contact</a> . <a href="<?=DOCUMENT_BASE?>/privacy.php">Privacy</a> . <a href="<?=DOCUMENT_BASE?>/returns.php">Returns</a> . <a href="<?=DOCUMENT_BASE?>/cancellations.php">Cancellations</a> . <a href="<?=DOCUMENT_BASE?>/affiliate_links.php">Affiliates</a></td></tr>
<tr><td width="180" valign="top"><a href="http://www.print-forms.com/" target="_top"><img src="images/logo.gif" width="156" height="85" alt="Print-Forms - Software Compatible forms, International Trade, Tax Forms, and Digital Printing." border="0" style="margin:10px;"></a><br />
<script type="text/javascript" src="<?=DOCUMENT_BASE?>/menu.js"></script>
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
<img src="images/left_forms.gif">
</td><td width="607" style="background-color:#FFFFFF;background-image:url('images/bg_main.gif');" valign="top">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="607" height="220">
    	<param name="movie" value="intro.swf">
    	<param name="quality" value="high">
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		if (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]){
		   document.write('<embed src="intro.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="607" height="220"></embed>');
		} else {
		   document.write('<img src="images/noflash.gif" width="607" height="225" alt="Non-Shockwave Welcome">');
		}
		// --> 
		</SCRIPT>
   	</object>
<div style="margin:15px;">
