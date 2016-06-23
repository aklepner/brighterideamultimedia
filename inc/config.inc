<?	
// Store all Configuration Variables
// Shipping Options
$ship_type = array( 
	"GND" => "Ground", 
	"3DS" => "3 Day Select", 
	"2DA" => "2nd Day Air", 
	"1DA" => "Next Day Air"
);

// Connect to Database Server
$dbh = mysql_connect("localhost", "databiz_2", "L5mdjAF2")
        or die("Couldn't connect to Database Server!\n");
// Select Default Database
mysql_select_db("databiz_0612", $dbh) or die("Couldn't open Database!\n");

function parse_dynamic($data){
	$data = preg_replace("/\<get_product\=([0-9]+)\>/e", "get_product('\\1')", $data);
	$data = preg_replace("/\<document_base\>/i", DOCUMENT_BASE, $data);
	$data = preg_replace("/\<seo_url=([0-9]+),((product)|(info)|(sample)|(affiliate)|(link))\>/ei", "SEO_load_url('\\1', '\\2')", $data);
	$data = preg_replace("/\<seo_url=((product)|(info)|(sample)|(affiliate)|(link)),([0-9]+)\>/ei", "SEO_load_url('\\7', '\\1')", $data);
	return $data;
}
// Function to find the (first) menu parent of a product/info/affiliate/link/sample
function find_menu_parent($menu_type, $menu_type_id, $site_id, $parent_id = -1)
{
	global $dbh;
	$results = NULL;
	
	// Can skip looking up the item if we already know the parent_id (aka pass a value greater than -1)
	if($parent_id >= 0)
	{
		$results = mysql_query('
			SELECT
				m.parent,
				m.id,
				m.name,
				c.name AS cat_name,
				i.name AS info_name,
				s.name AS samp_name,
				m.type,
				m.type_id
			FROM
				menu AS m
				LEFT JOIN product_category AS c
					ON (c.id = m.type_id)
				LEFT JOIN info AS i
					ON (i.id = m.type_id)
				LEFT JOIN sample_category AS s
					ON (s.id = m.type_id)
			WHERE m.site = \'' . $site_id . '\'
			AND m.id = \'' . $parent_id . '\'
			ORDER BY m.sort DESC
			LIMIT 1
		', $dbh);
	}
	else
	{
		// Find this item, then lookup the parent info
		$results = mysql_query('
			SELECT
				m2.parent,
				m2.id,
				m2.name,
				c.name AS cat_name,
				i.name AS info_name,
				s.name AS samp_name,
				m2.type,
				m2.type_id
			FROM
				menu AS m
				LEFT JOIN menu AS m2
					ON (m.parent = m2.id)
				LEFT JOIN product_category AS c
					ON (c.id = m2.type_id)
				LEFT JOIN info AS i
					ON (i.id = m2.type_id)
				LEFT JOIN sample_category AS s
					ON (s.id = m2.type_id)
			WHERE m.site = \'' . $site_id . '\'
			AND m.type = \'' . $menu_type . '\'
			AND m.type_id = \'' . $menu_type_id . '\'
			ORDER BY m.sort DESC
			LIMIT 1
		', $dbh);
	}
	if(mysql_num_rows($results)){
		$row = mysql_fetch_assoc($results);
		return $row;
	}
	
	return false;
}

// NEW! Function to handle formatting URLs
function SEO_load_url($id, $type)
{
	global $dbh;
	
	//$id += 0; // affiliate is not a number
	$use_num = false;
	if(is_numeric($id))
	{
		$id += 0;
		$use_num = true;
	}
	$type = trim(strtolower($type));
	$dbtable = 'product_category';
	$dbid = 'id';
	switch($type)
	{
		case 'product':
			$type = 'product';
			$dbtable = 'product_category';
			break;
		case 'info':
			$type = 'info';
			$dbtable = 'info';
			break;
		case 'sample':
			$type = 'sample';
			$dbtable = 'product_category';
			break;
		case 'affiliate':
			$type = 'affiliate';
			$dbtable = 'affiliate';
			if(!$use_num)
				$dbid = 'urlname';
			break;
		case 'link':
			$type = 'link';
			$dbtable = 'link';
			break;
		case 'none':
			return '';
			break;
		default:
			$type = 'product';
			break;
	}
	if($type != 'affiliate')
		$id += 0;
	
	$q = '
		SELECT
			pg.id,
			pg.name,
			pg.url_filename,
			pg.url_foldername
		FROM
			' . $dbtable . ' AS pg
		WHERE
				pg.site = \'' . SITE . '\'
			AND pg.' . $dbid . ' = \'' . $id . '\'
		LIMIT 1
	';
	
	$results = mysql_query($q, $dbh);
	if(!mysql_num_rows($results) && $type == 'affiliate')
	{
		$id += 0;
		$dbid = 'id';
		$q = '
			SELECT
				pg.id,
				pg.name,
				pg.url_filename,
				pg.url_foldername
			FROM
				' . $dbtable . ' AS pg
			WHERE
					pg.site = \'' . SITE . '\'
				AND pg.' . $dbid . ' = \'' . $id . '\'
			LIMIT 1
		';
		$results = mysql_query($q, $dbh);
	}
	
	if(mysql_num_rows($results))
	{
		$row = mysql_fetch_assoc($results);
		if($row['id'] == $id)
		{
			return SEO_format_url($row['id'], $type, $row['name'], $row['url_filename'], $row['url_foldername']);
		}
	}
	
	return '';
}

function SEO_format_url($id, $type, $name, $fName = '', $foldName = '')
{
	//$tmpURL = $row['type'] . '/' . $row['type_id'] . '/';
	$tmpURL = '';
	$urlID = 'p';
	switch($type)
	{
		case 'product':
			$type = 'product';
			break;
		case 'info':
			$type = 'info';
			$urlID = 'i';
			break;
		case 'sample':
			$type = 'sample';
			$urlID = 's';
			break;
		case 'affiliate':
			$type = 'affiliate';
			$urlID = 'a';
			break;
		case 'link':
			$type = 'link';
			$urlID = 'l';
			break;
		case 'none':
			return '';
			break;
		default:
			$type = 'product';
			break;
	}
	if($type != 'affiliate')
		$id += 0;
	
	if(!$id)
		return '';
	
	if($fName != '')
	{
		$tmpURL = $fName . '-' . $urlID . $id;
	}
	else
	{
		$tmpURL = cleanforurl2($name) . '-' . $urlID . $id;
	}
	
	if($foldName != '')
	{
		$tmpURL = $foldName . '/' . $tmpURL;
	}
	
	$tmpURL .= '.htm';
	
	return $tmpURL;
}

// Functions to handle formatting the URLs in a more SEO friendly way
function cleanforurl2($surl)
{
	$surl = str_replace(array('"', '&quot;', '\''), '', trim(strip_tags($surl)));
	return trim(preg_replace('/([^a-zA-Z0-9]+)/', '-', $surl), '-');
}

function getfoldersfromurl2($surl)
{
	$tmpFolder = preg_replace('/(^|-)(([0-9]+)|A|(The))-/i', '$1', $surl);
	$tmpFolder = ($tmpFolder) ? $tmpFolder : $surl;
	return str_replace('-', '', preg_replace('/^((([^-]+)(-?)){1,3})(.*)$/', '$1', $tmpFolder));
}

function upsrate($origin, $destination, $weight, $service = "GND") {
   switch(strtoupper($service)){ 
        case "1DA":            
          $service = "01"; 
          break;          
        case "2DA":            
          $service = "02"; 
          break;
        case "3DS":            
          $service = "12"; 
          break; 
        default:            
          $service = "03"; 
          break;
    } 
	$AccessLicenseNumber = 'CBFF20DE64554C8D'; // Your license number
	$UserId = 'cbartow'; // Username
	$Password = 'my*ups'; // Password
	$ShipperNumber = ''; // Your UPS shipper number
	
   	$data ="<?xml version=\"1.0\"?>
    	<AccessRequest xml:lang=\"en-US\">
    		<AccessLicenseNumber>$AccessLicenseNumber</AccessLicenseNumber>
    		<UserId>$UserId</UserId>
    		<Password>$Password</Password>
    	</AccessRequest>
    	<?xml version=\"1.0\"?>
    	<RatingServiceSelectionRequest xml:lang=\"en-US\">
    		<Request>
    			<TransactionReference>
    				<CustomerContext></CustomerContext>
    				<XpciVersion>1.0001</XpciVersion>
    			</TransactionReference>
    			<RequestAction>Rate</RequestAction>
    			<RequestOption>rate</RequestOption>
    		</Request>
    	<PickupType>
    		<Code>01</Code>
    	</PickupType>
    	<Shipment>
    		<Shipper>
    			<Address>
    				<PostalCode>$origin</PostalCode>
    				<CountryCode>us</CountryCode>
    			</Address>
				<ShipperNumber>$ShipperNumber</ShipperNumber>
    		</Shipper>
    		<ShipTo>
    			<Address>
    				<PostalCode>$destination</PostalCode>
    				<CountryCode>us</CountryCode>
				    <ResidentialAddress/>
    			</Address>
    		</ShipTo>
			<ShipFrom>  
			<Address>  
			    <PostalCode>$origin</PostalCode>  
			    <CountryCode>US</CountryCode>  
			</Address>  
		    </ShipFrom>
    		<Service>
    			<Code>$service</Code>
    		</Service>
    		<Package>
    			<PackagingType>
    				<Code>02</Code>
					<Description>Package</Description>
    			</PackagingType>
                <Description>UPS Package</Description>
    			<PackageWeight>
     				<Weight>$weight</Weight>
    				<UnitOfMeasurement>
    					<Code>LBS</Code>
    				</UnitOfMeasurement>
       			</PackageWeight>
    		</Package>
    	</Shipment>
    	</RatingServiceSelectionRequest>";
		//echo "<pre>".htmlentities($data)."<pre>";
    	$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");
    	curl_setopt($ch, CURLOPT_HEADER, 1);
    	curl_setopt($ch,CURLOPT_POST,1);
    	curl_setopt($ch,CURLOPT_TIMEOUT, 60);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch,CUROPT_SSLVERSION, 3.0);
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    	$result=curl_exec ($ch);
		// echo "<pre>".htmlentities($result)."</pre>";
		$xml = simplexml_load_string(strstr($result, '<?'));
		
    curl_close($ch);
	return (string) $xml->RatedShipment->TotalCharges->MonetaryValue;
}
?>
