
</div>
<div align="right"><a href="http://www.databusinesssystems.com"><img src="images/dbs.jpg" alt="Data Business Systems" border="0"></a></div>
</td></tr>
<tr><td colspan="2" style="padding:10px 25px 10px 25px;text-align:center;font-size:10px;"><?
require_once(dirname(__FILE__)."/config.inc");
$results = mysql_query("select * from bottom_link order by sort, name", $dbh);
if(mysql_num_rows($results)){
	$i = 0;
	while($row = mysql_fetch_assoc($results)){
		if($i) print " | ";
		print "<a href=\"".$row['url']."\">".$row['name']."</a>";
		$i++;
	}
}
?></td></tr>
</table>
<br>