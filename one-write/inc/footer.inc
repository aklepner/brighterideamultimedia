<table width="500" border="0" cellspacing="5" cellpadding="5">
  <tr>
   
    <td><a href="http://medical-forms.com" target="_blank"><img src="http://www.databusinesssystems.com/buttons/medicalforms_off.gif" width="150" border="0"></a></td>
    <td><a href="http://www.print-forms.com" target="_blank"><img src="http://www.databusinesssystems.com/buttons/printforms_off.gif" width="150" border="0"></a></td>
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