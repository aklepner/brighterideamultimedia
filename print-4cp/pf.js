<? 	
	require_once("inc/config.inc");
	require_once("../inc/dbi.inc");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time()));
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()+(60*15)));
	header("Content-Type: application/x-javascript");
	//header("X-Pad: avoid browser bug");
	//ob_start();
?>

	var LowBgColor='#B7CFD0';			// Background color when mouse is not over
	var LowSubBgColor='white';			// Background color when mouse is not over on subs
	var HighBgColor='#44696A';			// Background color when mouse is over
	var HighSubBgColor='#FFEBD7';			// Background color when mouse is over on subs
	var FontLowColor='#44696A';			// Font color when mouse is not over
	var FontSubLowColor='#44696A';			// Font color subs when mouse is not over
	var FontHighColor='#B7CFD0';			// Font color when mouse is over
	var FontSubHighColor='#44696A';			// Font color subs when mouse is over
	var BorderColor='#FF9933';			// Border color
	var BorderSubColor='#FF9933';			// Border color for subs
	var BorderWidth=2;				// Border width
	var BorderBtwnElmnts=1;			// Border between elements 1 or 0
	var FontFamily="Arial"	// Font family menu items
	var FontSize=9;				// Font size menu items
	var FontBold=1;				// Bold menu items 1 or 0
	var FontItalic=0;				// Italic menu items 1 or 0
	var MenuTextCentered='center';			// Item text position 'left', 'center' or 'right'
	var MenuCentered='left';			// Menu horizontal position 'left', 'center' or 'right'
	var MenuVerticalCentered='top';		// Menu vertical position 'top', 'middle','bottom' or static
	var ChildOverlap=0;				// horizontal overlap child/ parent
	var ChildVerticalOverlap=0;			// vertical overlap child/ parent
	var StartTop=135;				// Menu offset x coordinate
	var StartLeft=0;				// Menu offset y coordinate
	var VerCorrect=0;				// Multiple frames y correction
	var HorCorrect=0;				// Multiple frames x correction
	var LeftPaddng=1;				// Left padding
	var TopPaddng=0;				// Top padding
	var FirstLineHorizontal=0;			// SET TO 1 FOR HORIZONTAL MENU, 0 FOR VERTICAL
	var MenuFramesVertical=0;			// Frames in cols or rows 1 or 0
	var DissapearDelay=2000;			// delay before menu folds in
	var TakeOverBgColor=1;			// Menu frame takes over background color subitem frame
	var FirstLineFrame='navig';			// Frame where first level appears
	var SecLineFrame='space';			// Frame where sub levels appear
	var DocTargetFrame='space';			// Frame where target documents appear
	var TargetLoc='';				// span id for relative positioning
	var HideTop=0;				// Hide first level when loading new document 1 or 0
	var MenuWrap=1;				// enables/ disables menu wrap 1 or 0
	var RightToLeft=0;				// enables/ disables right to left unfold 1 or 0
	var UnfoldsOnClick=0;			// Level 1 unfolds onclick/ onmouseover
	var WebMasterCheck=0;			// menu tree checking on or off 1 or 0
	var ShowArrow=1;				// Uses arrow gifs when 1
	var KeepHilite=1;				// Keep selected path highligthed
	var Arrws=['<?=DOCUMENT_BASE?>/images/tri.gif',5,10,'<?=DOCUMENT_BASE?>/images/tridown.gif',10,5,'<?=DOCUMENT_BASE?>/images/trileft.gif',5,10];



<?
function get_cats($parent=0,$width=180,$prefix="",$title="",$link="",$cart=0){
	$db = new dbi();
	// Get the width of this set of menus
	if($parent != 0){
		$db->query("select length(name) as strlen from menu where site = '".SITE."' and parent = '$parent' order by strlen desc limit 0,1");
		if($db->numrows())
			if($db->result("strlen") < 10)
				$maxwidth = ($db->result("strlen")+2) * 9;
			else
				$maxwidth = ($db->result("strlen")+1) * 7;
	}else{
		$maxwidth = 180;		
	}
	$db->query("select * from menu where site = '".SITE."' and parent = '$parent'");

	if($parent == 0){
			// Only run this the first time to set the number of total First Set's
	?>
		var NoOffFirstLineMenus=<?=$db->numrows()?>;
		function BeforeStart(){return}
		function AfterBuild(){return}
		function BeforeFirstOpen(){return}
		function AfterCloseAll(){return}
	<?
	}else{
		// Otherwise we are in submenus print, other stuff.
		if($link != "")
			$link = DOCUMENT_BASE."/$link";
		if($cart)
			print "Menu$prefix=new Array(\"<div align=\\\"left\\\" style=\\\"margin-left:6px;\\\"><img src=\\\"".DOCUMENT_BASE."/images/cart.gif\\\" alt=\\\"\\\" border=\\\"0\\\">&nbsp;&nbsp;<span style=\\\"position:relative;top:-4px;\\\">".addslashes($title)."</span></div>\",\"$link\",\"\",".$db->numrows().",18,$width);\n";
		else
			print "Menu$prefix=new Array(\"".addslashes($title)."\",\"$link\",\"\",".$db->numrows().",18,$width);\n";
	}

	if($db->numrows()){
		$num=1;
		while($db->loop()){
			if($db->result("cart") == 'y')
				$cart = 1;
			else
				$cart = 0;
			if($db->result("type") != 'none' && $db->result("type") != '')
				$link = $db->result("type")."/".$db->result("menu.type_id");
			else
				$link = "";
			get_cats($db->result("id"),$maxwidth,($prefix=="")?$num:$prefix."_".$num,$db->result("menu.name"), $link, $cart);

			$num++;	
		}
	}
}
get_cats();
/*
	$output = ob_get_contents();
	ob_end_clean();
	header("Accept-Ranges: bytes");
	header("Content-Length: ".strlen($output));
	print $output;
*/
	
?>	
