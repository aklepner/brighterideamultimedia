<?
	require_once("/home/bocawebgroup/public_html/inc/ad_track.inc");
	require_once("inc/config.inc");
	require_once("../inc/dbi.inc");
	if(isset($_POST['name']) && isset($_POST['data'])){
		$name = $_POST['name'];
		$date = $_POST['data'];
	}else{
		$db = new dbi();

		$db->query("select * from info where id = '".$_GET[id]."' and site = '".SITE."'");
		if($db->numrows()){
			$name = $db->result("info.name");
			$data = $db->result("info.data");
		}else{
			header("HTTP/1.1 404 Not Found");
			exit;
		}
	}
?>
<html>
<head>
	<title>One-Write - <?=$name?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script language="javascript" src="<?=DOCUMENT_BASE?>/viewphoto.js"></script>
</head>

<body onLoad="P7_TMopen()">
<?
	require_once("../inc/dbi.inc");
	include("inc/header.inc");
	include("../inc/info_page.inc");
	include("inc/footer.inc");
?>



</body>
</html>
