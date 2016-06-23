<?

function get_product($cat_id, $po=0){
	ob_start();
	print_product_cart($cat_id,0,$po);
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function print_product_cart($category_id = 0, $user_id = 0, $purchase_order = 0){
	global $dbh;
	global $category;
	$SEO_YES = (defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true) ? true : false;
	$global_catName = '';
	
	if($SEO_YES)
	{
		if(!$category)
		{
			$tmpCatInfo = mysql_query('SELECT product_category.name FROM product_category WHERE id = \''.$category_id.'\' AND site = \''.SITE.'\' LIMIT 1', $dbh);
			if(mysql_num_rows($tmpCatInfo)){
				$category = mysql_fetch_assoc($tmpCatInfo);
			}
		}
		if(is_array($category))
		{
			$global_catName = str_replace('"', '&quot;', trim(strip_tags($category['name'])));
		}
	}
	
	$query = "select product.*, site.document_base from product inner join product_category on product_category.id = product.category inner join site on site.id = product_category.site where status != 'disable'";
	if($category_id != 0)
		$query .= " and product.category = '$category_id'";
	$query .= " and product.user = '$user_id' order by sort, description asc";
	$products = mysql_query($query, $dbh) 
		or error_log("MySQL Error: ".mysql_error($dbh)." in ".__FILE__." on line ".__LINE__);
	if(mysql_num_rows($products)){
		print "<table width=\"95%\" cellpadding=\"4\" cellspacing=\"0\" border=\"0\" align=\"center\"" . ($SEO_YES ? " summary=\"A list of purchase options for ".$global_catName." available from ".SITE_NAME.".com\"" : '') . ">";
		// Check for all DropDowns and remove header line
		$query = "select product.id from product inner join product_category on product_category.id = product.category inner join site on site.id = product_category.site where status != 'disable'";
		if($category_id != 0)
			$query .= " and product.category = '$category_id'";
		$query .= " and product.user = '$user_id' and product.dropdown = 'n'";
		$results = mysql_query($query, $dbh);
		if(mysql_num_rows($results)){
			print "<tr><td colspan=\"2\">&nbsp;</td><td width=\"230\"><table cellpadding=\"1\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" width=\"60\" style=\"font-weight:bold;font-size:11px;\">Quantity</td><td align=\"right\" width=\"80\" style=\"font-weight:bold;font-size:11px;\">Unit Price</td><td align=\"right\" width=\"90\" style=\"font-weight:bold;font-size:11px;\">Net Price</td></tr></table></td><td>&nbsp;</td></tr>";
		}
		$i = 0;
		while($pro = mysql_fetch_assoc($products)){
			$tmpCleanedProdName = trim(strip_tags($pro['description']));
			if($i%2)
				print "<tr class=\"product_light\">";
			else
				print "<tr class=\"product_dark\">";
			if(preg_match("/^(www\.)?((print|medical)-forms|one-write).com$/",$_SERVER['SERVER_NAME']))
				$image_base = "";
			else
				$image_base = $pro['document_base'];
			
			
			$tmpSubProdImg1 = $image_base . "/images/product/".$pro['id'].".jpg";
			$tmpSubProdImg2 = $image_base . "/pdf/product/".$pro['id'].".pdf";
			$tmpSubProdTest1 = false;
			$tmpSubProdTest2 = false;
			if(is_file($_SERVER['DOCUMENT_ROOT'] . $tmpSubProdImg1))
			{
				$tmpSubProdTest1 = true;
			}
			if(is_file($_SERVER['DOCUMENT_ROOT'] . $tmpSubProdImg2))
			{
				$tmpSubProdTest2 = true;
			}
			if($tmpSubProdTest1 || $tmpSubProdTest2){
				print "<td align=\"center\" width=\"60\" style=\"font-size:11px;font-weight:bold;\">";
				// Exception for Route Slips - Manual Handwritten
				if($category_id == "98")
					print "View Color Sheet";
				else
					print "View Sample";
				print "<br>";
				if($tmpSubProdTest1)
					print "<a href=\"".$tmpSubProdImg1."\" onclick=\"viewphoto('".$tmpSubProdImg1."');return false;\" style=\"font-size:11px;\"" . ($SEO_YES ? " title=\"" . $tmpCleanedProdName . " - Image\"" : '') . "><b><i>JPEG</i></b></a>";
				if($tmpSubProdTest1 && $tmpSubProdTest2)
					print "/";
				if($tmpSubProdTest2)
					print "<a target=\"_blank\" href=\"".$tmpSubProdImg2."\" style=\"font-size:11px;font-weight:bold;font-style:italic;\"" . ($SEO_YES ? " title=\"" . $tmpCleanedProdName . " - PDF\"" : '') . ">PDF</a>";
				print "</span></td>";
			}else{
				print "<td></td>";
			}
			print "<td align=\"left\" style=\"font-size:11px;\"><form style=\"margin:0;\" method=\"post\" action=\"";
			if(defined('SSL_BASE'))
				print SSL_BASE;
			else
				print "https://www.databusinesssystems.com".$pro['document_base'];
			print "/cart/\">".$pro['description']."<br>";
			if($pro['dropdown'] == 'n'){
				print "<div align=\"right\">";
				print_product_options($pro['id']);
				print "</div>";
			}
			print "</td>";
			$results = mysql_query("select * from product_quantity where product = '".$pro['id']."' order by quantity", $dbh);
			if(mysql_num_rows($results)){
				print "<td><table cellpadding=\"1\" cellspacing=\"0\" border=\"0\"" . ($SEO_YES ? " summary=\"Buy ".$global_catName." - ".$tmpCleanedProdName."\"" : '') . ">";
				if($pro['dropdown'] == 'y'){
					print "<tr><td align=\"left\"><select name=\"quantity\" style=\"font-size:11px;position:relative;z-index:inherit;\">";
					while($row = mysql_fetch_assoc($results)){
						print "<option value=\"".$row['quantity']."\">".$row['quantity']." for $". number_format(($row['quantity']/$pro['unit_quantity']*$row['unit_price']),2). "</option>";
					}
					print "</select><br>";
					print_product_options($pro['id']);
					print "</td></tr>";
				}elseif(mysql_num_rows($results) == 1){
					$row = mysql_fetch_assoc($results);
					print "<tr><td nowrap=\"nowrap\" width=\"60\" align=\"left\" style=\"font-size:11px;\"><input type=\"hidden\" name=\"quantity\" value=\"".$row['quantity']."\"> ". $row['quantity']."</td><td align=\"right\" width=\"80\" style=\"font-size:11px;\">$".number_format($row['unit_price'],2)."</td><td align=\"right\" width=\"90\" style=\"font-size:11px;\">$". number_format(($row['quantity']/$pro['unit_quantity']*$row['unit_price']),2). "</td></tr>";
				}else{
					while($row = mysql_fetch_assoc($results)){
						print "<tr><td nowrap=\"nowrap\" width=\"60\" align=\"left\" style=\"font-size:11px;\"><input type=\"radio\" name=\"quantity\" value=\"".$row['quantity']."\"> ". $row['quantity']."</td><td align=\"right\" width=\"80\" style=\"font-size:11px;\">$".number_format($row['unit_price'],2)."</td><td align=\"right\" width=\"90\" style=\"font-size:11px;\">$". number_format(($row['quantity']/$pro['unit_quantity']*$row['unit_price']),2). "</td></tr>";
					}
				}
				print "</table></td><td align=\"center\" style=\"font-size:11px;\">";
				if(preg_match("/affiliate((-new)?)\.php/",$_SERVER['PHP_SELF'])){
					global $aff_name;
					print "<input type=\"hidden\" name=\"affiliate\" value=\"".strtolower($aff_name)."\">";
				}
				if($pro['status'] == "in"){
					print "<input type=\"hidden\" name=\"product\" value=\"".$pro['id']."\">";
					if($purchase_order)
						print "<input type=\"hidden\" name=\"po\" value=\"1\" />";
					print "<input type=\"submit\" name=\"add\" value=\"Add to Cart\" style=\"font-size:11px;position:relative;z-index:inherit;\">";
				}elseif($pro['status'] == "discontinued")
					print "Discontinued";
				else
					print "Out of Stock";
				print "</td>";
			}
			print "</tr></form>";
			$i++;
		}
		print "</table>";
		
		// View Related Products
		if($category_id != 0){
			if($SEO_YES)
			{
				$results = mysql_query('
					SELECT
						c.name AS product_name,
						c.url_filename AS product_fname,
						c.url_foldername AS product_dname,
						i.name AS info_name,
						i.url_filename AS info_fname,
						i.url_foldername AS info_dname,
						s.name AS sample_name,
						s.url_filename AS sample_fname,
						s.url_foldername AS sample_dname,
						a.name AS affiliate_name,
						a.url_filename AS affiliate_fname,
						a.url_foldername AS affiliate_dname,
						l.name AS link_name,
						l.url_filename AS link_fname,
						l.url_foldername AS link_dname,
						
						pr.*
					FROM
						product_related AS pr
						LEFT JOIN product_category AS c
							ON (c.id = pr.type_id AND c.site = \'' . SITE . '\' AND pr.type = \'product\')
						LEFT JOIN info AS i
							ON (i.id = pr.type_id AND i.site = \'' . SITE . '\' AND pr.type = \'info\')
						LEFT JOIN sample_category AS s
							ON (s.id = pr.type_id AND s.site = \'' . SITE . '\' AND pr.type = \'sample\')
						LEFT JOIN affiliate AS a
							ON (a.id = pr.type_id AND a.site = \'' . SITE . '\' AND pr.type = \'affiliate\')
						LEFT JOIN link AS l
							ON (l.id = pr.type_id AND l.site = \'' . SITE . '\' AND pr.type = \'link\')
					WHERE pr.category = \'' . $category_id . '\'
				', $dbh);
				if(mysql_num_rows($results)){
					print "<h2 title=\"".SITE_NAME.".com - ".$global_catName." - Print Forms, Software Compatible forms, International Trade, Tax Forms, and Digital Printing\">Customers shopping for these items, may also be interested in the following:</h2>";
					print "<ul type=\"square\">";
					while($row = mysql_fetch_assoc($results)){
						$urlPath = SEO_format_url($row['type_id'], $row['type'], $row[$row['type'] . '_name'], $row[$row['type'] . '_fname'], $row[$row['type'] . '_dname']);
						print "<li><a href=\"".DOCUMENT_BASE."/".$urlPath."\" title=\"".trim(strip_tags($row['description']))."\">".$row['description']."</a></li>";
					}	
					print "</ul>";
				}
			}
			else
			{
				$results = mysql_query("select * from product_related where category = '".$category_id."'", $dbh);
				if(mysql_num_rows($results)){
					print "<h2>Customers shopping for these items, may also be interested in the following:</h2>";
					print "<ul type=\"square\">";
					while($row = mysql_fetch_assoc($results)){
						print "<li><a href=\"".DOCUMENT_BASE."/".$row['type']."/".$row['type_id']."/\">".$row['description']."</a></li>";
					}	
					print "</ul>";
				}
			}
		}
		return true;
	}else{
		return false;
	}
}

function print_product_options($product){
	global $dbh;
	
	$results = mysql_query("select product_option.*, if(sort=0,1,0) as zero from product_option where product = '".$product."' order by zero asc, sort asc, sku", $dbh)
		or error_log("MySQL Error: ".mysql_error($dbh)." in ".__FILE__." on line ".__LINE__);
	if(mysql_num_rows($results) > 1){
		print "<select name=\"option\" style=\"font-size:11px;position:relative;z-index:inherit;\">";
		while($row = mysql_fetch_assoc($results)){
			print "<option value=\"".$row['id']."\">".$row['description']."</option>";
		}
		print "</select>";
	}elseif(mysql_num_rows($results) == 1){
		print "<input type=\"hidden\" name=\"option\" value=\"".$row['id']."\">".$row['description'];
	}
}
?>
