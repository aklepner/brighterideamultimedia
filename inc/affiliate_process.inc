<?
$aff_id = 0;
$aff_urlname = '';
$seo__page_title = '';
$seo__meta_kws = '';
$seo__meta_desc = '';
$seo__url_fname = '';
$seo__url_dname = '';
if(isset($_GET['aff_name']))
	$aff_urlname = $_GET['aff_name'];
if(isset($_GET['id']))
	$aff_id = $_GET['id'];

if(isset($_POST['name']) && isset($_POST['data'])){
	$name = $_POST['name'];
	$data = $_POST['data'];
}else{
	if(isset($_GET['id']))
	{
		$results = mysql_query("
			SELECT
				urlname,
				name,
				data,
				page_title,
				meta_keywords,
				meta_description,
				url_filename,
				url_foldername
			FROM affiliate
			WHERE id = '".($_GET['id']+0)."'
		", $dbh);
		if(mysql_num_rows($results)){
			$name = mysql_result($results, 0, "name");
			$data = mysql_result($results, 0, "data");
			$aff_urlname = mysql_result($results, 0, "urlname");
			$_GET['aff_name'] = $aff_urlname;
			
			$seo__page_title = mysql_result($results, 0, "page_title");
			$seo__meta_kws = mysql_result($results, 0, "meta_keywords");
			$seo__meta_desc = mysql_result($results, 0, "meta_description");
			$seo__url_fname = mysql_result($results, 0, "url_filename");
			$seo__url_dname = mysql_result($results, 0, "url_foldername");
		}
	}
	else if(isset($_GET['aff_name'])){
		$results = mysql_query("
			SELECT
				id,
				name,
				data,
				page_title,
				meta_keywords,
				meta_description,
				url_filename,
				url_foldername
			FROM affiliate
			WHERE LOWER(urlname) = '".strtolower($_GET['aff_name'])."'
		", $dbh);
		if(mysql_num_rows($results)){
			$name = mysql_result($results, 0, "name");
			$data = mysql_result($results, 0, "data");
			$aff_id = mysql_result($results, 0, "id");
			$_GET['id'] = $aff_id;
			
			$seo__page_title = mysql_result($results, 0, "page_title");
			$seo__meta_kws = mysql_result($results, 0, "meta_keywords");
			$seo__meta_desc = mysql_result($results, 0, "meta_description");
			$seo__url_fname = mysql_result($results, 0, "url_filename");
			$seo__url_dname = mysql_result($results, 0, "url_foldername");
		}
	}
}
?>