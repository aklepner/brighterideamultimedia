<?
	require_once("../inc/config.inc");
	
	$template_title = 'Medical Claim Forms';
	$template_description = '';
	$template_keywords = 'medical claim forms, hcfa forms, hcfa claim form, ub 92, ub04 claim form';
?>
<html>
<head>
	<title><?=$template_title?> - Medical Forms</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="description" content="Print-Forms - <?=$template_description?>">
	<meta http-equiv="keywords" content="<?=$template_keywords?>">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script language="JavaScript" type="text/javascript" src="<?=DOCUMENT_BASE?>/viewphoto.js"></script>
	<script type="text/javascript" src="<?=DOCUMENT_BASE?>/menu.js"></script>
</head>

<body onLoad="P7_TMopen()">
<?
	include("../inc/header.inc");
?>
<h1>Medical Claim Forms</h1>
<br clear="all">

Medical-Forms.com offers only the highest quality, government compliant medical claim forms.  The UB04 claim form (HCFA) is used to bill a medicare fiscal intermediary and for billing of institutional charges to most Medicaid State agencies. 
<br /><br />
The new HCFA UB04 forms contain numerous improvements that include more close alignment with electronic HIPAA ASC X12N 837-Institutional Standard.  The updated medical claim form will be able to accommodate reporting of National Provider Identifier Number. 
<br /><br />
Medical-forms.com carries the updated UB04 Claim forms , formerly the UB 92, in laser/deskjet and two part continuous formats.  Follow the links below to see available quantities.

<ul>
<li><a href="<?=DOCUMENT_BASE?>/HospitalClaimForms/Hospital-Claim-Forms-Laser-Deskjet-p52.htm">UB04 Medical Claim Form – Laser/Deskjet</a>
<li><a href="<?=DOCUMENT_BASE?>/HospitalClaimForms/Hospital-Claim-Forms-Continuous-p56.htm"> UB04 Medical Claim Form – Two Part Continuous</a>
</ul>

The new HCFA Forms – UB04 –  are guaranteed to be compliant with the most recent CMS regulations.  The UB04 claim form is the replacement for the ub 92 medical claim form.  Even if submissions were on the UB 92 in the past, all rebilling of medical claims should use the UB04 form.  The forms come printed in red optical character recognition ink on special bon paper.  All in strict compliance with government printing.
<br /><br />
Are you Interested? Check out our UB04 medical claim forms at the product pages above.  Want more information? <a href="<?=DOCUMENT_BASE?>/contact.php">Contact us</a> today!



<?
	include("../inc/footer.inc");
?>
</body>
</html>