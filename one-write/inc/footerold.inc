
<table cellspacing="4" style="margin:25px auto 0 auto;width:98%;">
<tr><td colspan="3" style="font-size:12px;">Visit our other sites and also on <a href="http://www.facebook.com/pages/data-business-systems-Inc/107138945998394"><img src="https://www.databusinesssystems.com/images/logos/facebook.png" alt="DBS on Facebook" style="height:20px;vertical-align:middle;" /></a></td></tr>
<tr>
	<td style="width:100px;text-align:center;"><a href="http://www.medical-forms.com/"><img src="http://www.databusinesssystems.com/images/logos/medical-forms.png" alt="Medical Forms" style="width:90px;" /></a></td>
	<td style="width:100px;text-align:center;"><a href="http://www.print-forms.com/"><img src="https://www.databusinesssystems.com/images/logos/print-forms.png" alt="Print Forms" style="width:90px;" /></a></td>
	<td style="width:100px;text-align:center;"><a href="http://www.print-4cp.com/"><img src="https://www.databusinesssystems.com/images/logos/print-4cp.png" alt="Print-4cp" style="width:90px;" /></a></td>
	<td style="text-align:right;"><a href="http://www.databusinesssystems.com/"><img src="https://www.databusinesssystems.com/images/logos/databusinesssystems.png" alt="Data Business Systems" style="width:90px;" /></a></td>
</tr>
</table>

</td></tr>
</table>
<?
require_once(dirname(__FILE__)."/config.inc");
$results = mysql_query("select * from bottom_link order by sort, name", $dbh);
if(mysql_num_rows($results)){
	print "<div style=\"margin:10px 25px 10px 25px;text-align:center;font-size:10px;color:#FFFFFF;\">";
	$i = 0;
	while($row = mysql_fetch_assoc($results)){
		if($i) print " | ";
		print "<a href=\"".$row['url']."\" style=\"color:#FFFFFF;\">".$row['name']."</a>";
		$i++;
	}
	print "</div>";
}
?>