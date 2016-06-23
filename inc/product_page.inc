<?
	if(defined('USE_SEO_URL_REWRITES') && USE_SEO_URL_REWRITES === true)
	{
		?>
<h1 title="<?=SITE_NAME?>.com - <?=str_replace('"', '&quot;', trim(strip_tags($category['name'])))?>"><?=$category['name']?></h1>
		<?
	}
	else
	{
		?>
<h1><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		$link_color = (SITE_NAME == 'Print-Forms') ? '#FFFFFF' : '#CC0000';
		print '<a href="' . DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'product', $name, $seo__url_fname, $seo__url_dname) . '" title="' . str_replace('"', '&quot;', $category['name']) . '" style="text-decoration:none;color:' . $link_color . ';">';
	}
?><?=$category['name']?><? 
	if(SITE_NAME == 'Print-Forms' || SITE_NAME == 'Medical-Forms')
	{
		print '</a>';
	}
?></h1>
		<?
	}
?>

<? /*
	$tmpProdImgPP = DOCUMENT_BASE."/images/product_category/".$category['id'].".jpg";
	if(is_file($_SERVER['DOCUMENT_ROOT'].$tmpProdImgPP)){
		print "<td width=\"80\"><a href=\"javascript:viewphoto('".$tmpProdImgPP."')\"><img src=\"".DOCUMENT_BASE."/images/thumb/product_category/".$category['id'].".jpg\" alt=\"Category Thumbnail\" border=\"1\"></a></td>";
	} */
	$tmpProdImgPP = DOCUMENT_BASE."/images/product_category/logo/".$category['id'].".jpg";
	if(is_file($_SERVER['DOCUMENT_ROOT'].$tmpProdImgPP)){
		print "<img src=\"".$tmpProdImgPP."\"  alt=\"".$category['name']."\" title=\"".$category['name']."\" border=\"0\" style=\"margin:0 15px 10px 10px;float:right;\">";
	}
	print "<p style=\"margin:0 15px 10px 15px;\">".parse_dynamic(nl2br($category['description']))."</p>";
?>
<br clear="all">
<?
	include("../inc/product.inc");
	// Turn on Purchase Orders for any Eden Products.
	// May want to add a field to the database to determine this per product category
	if(in_array($category['id'],array(179,181,54,197,180,182)))
		print_product_cart($category['id'],0,true);
	else
		print_product_cart($category['id']);
?>
