</div>
<br />
<div style="text-align:center;line-height:18px;"><a href="/contact.php">Contact</a> | <a href="/privacy.php">Privacy</a> | <a href="/returns.php">Returns</a> | <a href="/cancellations.php">Cancellations</a> | <a href="/affiliates.php">Affiliates</a> | <a href="/simplicity.php">Client Referral Plan</a><br /><b>Data Business Systems, Inc.</b> PO Box 780, Flanders, NJ 07836 | <b>Toll Free: 1-800-778-6247</b></div>

<? if($_SERVER["HTTPS"] == 'on'){ ?>
<br />
<div align="center">
<!-- <script language="JavaScript" src='https://secure.comodo.net/trustlogo/javascript/trustlogo.js'></script>
<script type="text/javascript">TrustLogo("https://www.databusinesssystems.com/images/secure_safe.gif", "PAIR", "none");</script> -->
<script language="JavaScript" type="text/javascript">
TrustLogo("http://databusinesssystems.com/comodo_secure_100x85_white.png", "CL1", "none");
</script>
<a  href="http://ssl.comodo.com" id="comodoTL">SSL Certificates</a></div>
<? } ?>
<br />
<?
require_once(dirname(__FILE__)."/config.inc");
$results = mysql_query("select * from bottom_link order by sort, name", $dbh);
if(mysql_num_rows($results)){
	print "<div style=\"margin:5px 25px 10px 25px;text-align:center;font-size:10px;\">";
	$i = 0;
	while($row = mysql_fetch_assoc($results)){
		if($i) print " | ";
		print "<a href=\"".$row['url']."\">".$row['name']."</a>";
		$i++;
	}
	print "</div>";
}
?>
	</td>
</tr>
</table>