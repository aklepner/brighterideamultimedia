<?php 


// start user configuration
//========================================================================================
// To use this script, you have to register at UPS OnLine Tools.

// http://www.ec.ups.com/ecommerce/gettools/gtools_intro.html
// Enter your XML Access Key for the AccessLicenseNumber, provided by UPS to access the UPS XML systems.
// Enter your UPS User Id from the UPS OnLine Tools registration.
// Enter your UPS password from the UPS OnLine Tools registration.
 
// global UPS OnLine Tools variables
/****************************************************************************/
	  $AccessLicenseNumber='3CE7E4D492AE6395';
    $UserId='DBSFEFFER';
    $Password='DataBiz#1';
/* This script can use the PHP builtin fsockopen function to connect to the
UPS server on SSL port 443. PHP must be compiled with OpenSSL to use this function.
If your server does suppport the fsockopen function and does not have cURL installed then
comment-out the line $_CURL = true; line to use the PHP fsockopen function instead of the cURL module.
*/
    $_CURL = true;
		
/******************************************************************************/		
//========================================================================================
// end user configuration

$UPS_Server = "https://wwwcie.ups.com/ups.app/xml/Rate";
//$UPS_Server = "https://onlinetools.ups.com/ups.app/xml/Rate";
//$UPS_Server = "https://www.ups.com/ups.app/xml/Rate";

/*
SYNOPSIS:

Integrate your e-commerce applications with this PHP module that return shipping rates from the UPS OnLine Tools
using the XML version. 

PHP library to retrieve shipping rate quotes from the 
UPS XML Online Tools Rating and Service Selection HTML, Version 2000a.

UPS Rates & Services Selection lets your online customers compare, 
price and select the UPS shipping service that best fits the shipping
rate that they can afford. 

functions in this module:
1.)
$rate = getUPSQuote($upsProductCode,$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode);
    
Returns UPS shipping rate for the selected service for success 
or an error message for failure. 

2.)
$select_list_rates = getUPSSelectList($type,$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode);
    
Returns UPS shipping rate HTML select list or a group of radio buttons for success 
or an error message for failure. 

3.)
$vaild = validateUPSAddress($destPostalCode,$destCountryCode);

Returns UPS destination postal City, State and zipcode for success  
or an error message for failure. 


NOTE: UPS is a registered trademark of United Parcel Service of America, Inc. 

*/

/* currency symbol */
$m_symbol = "$";

/**********************************************************************************/
/* Two letter ISO country codes */
$iso_countries = array("ad"=> "Andorra","ae"=> "United Arab Emirates",
"af"=> "Afghanistan","ag"=> "Antigua and Barbuda","ai"=> "Anguilla",
"al"=> "Albania","am"=> "Armenia","an"=> "Netherland Antilles",
"ao"=> "Angola","aq"=> "Antarctica","ar"=> "Argentina",
"as"=> "American Samoa","at"=> "Austria","au"=> "Australia",
"aw"=> "Aruba","az"=> "Azerbaidjan","ba"=> "Bosnia and Herzegovina",
"bb"=> "Barbados","bd"=> "Banglades","be"=> "Belgium","bf"=> "Burkina Faso",
"bg"=> "Bulgaria","bh"=> "Bahrain","bi"=> "Burundi","bj"=> "Benin",
"bm"=> "Bermuda","bn"=> "Brunei Darussalam","bo"=> "Bolivia",
"br"=> "Brazil","bs"=> "Bahamas","bt"=> "Bhutan","bv"=> "Bouvet Island",
"bw"=> "Botswana","by"=> "Belarus","bz"=> "Belize","ca"=> "Canada",
"cc"=> "Cocos (Keeling) Islands","cd"=> "Congo, Democratic Republic of the",
"cf"=> "Central African Republic","cg"=> "Congo, Republic of the",
"ch"=> "Switzerland","ci"=> "Cote d'Ivoire",
"ck"=> "Cook Islands","cl"=> "Chile","cm"=> "Cameroon","cn"=> "China",
"co"=> "Colombia","cr"=> "Costa Rica","cu"=> "Cuba","cv"=> "Cape Verde",
"cx"=> "Christmas Island","cy"=> "Cyprus","de"=> "Germany",
"dj"=> "Djibouti","dk"=> "Denmark","dm"=> "Dominica","do"=> "Dominican Republic",
"dz"=> "Algeria","ec"=> "Ecuador","ee"=> "Estonia","eg"=> "Egypt",
"eh"=> "Western Sahara","er"=> "Eritrea",
"es"=> "Spain","et"=> "Ethiopia","fi"=> "Finland","fj"=> "Fiji",
"fk"=> "Falkland Islands","fm"=> "Micronesia, Federated States of",
"fo"=> "Faroe Islands","fr"=> "France","ga"=> "Gabon","gb"=> "Great Britain",
"gd"=> "Grenada","ge"=> "Georgia","gg"=> "Guernsey","gh"=> "Ghana",
"gi"=> "Gibraltar","gl"=> "Greenland","gm"=> "Gambia","gn"=> "Guinea",
"gp"=> "Guadeloupe","gq"=> "Equatorial Guinea","gr"=> "Greece",
"gt"=> "Guatemala","gu"=> "Guam","gw"=> "Guinea Bissau","gy"=> "Guyana",
"hk"=> "Hong Kong","hm"=> "Heard Island and McDonald Islands",
"hn"=> "Honduras","hr"=> "Croatia","ht"=> "Haiti","hu"=> "Hungary",
"id"=> "Indonesia","ie"=> "Ireland","il"=> "Israel","im"=> "Man, Isle of",
"in"=> "India","io"=> "British Indian Ocean Territory","iq"=> "Iraq",
"ir"=> "Iran","is"=> "Iceland","it"=> "Italy","jm"=> "Jamaica",
"jo"=> "Jordan","jp"=> "Japan","ke"=> "Kenya","kg"=> "Kyrgyzstan",
"kh"=> "Cambodia","ki"=> "Kiribati","km"=> "Comoros",
"kn"=> "Saint Kitts and Nevis Anguilla","kp"=> "North Korea",
"kr"=> "South Korea","kw"=> "Kuwait","ky"=> "Cayman Islands",
"kz"=> "Kazachstan","la"=> "Laos","lb"=> "Lebanon","lc"=> "Saint Lucia",
"li"=> "Liechtenstein","lk"=> "Sri Lanka","lr"=> "Liberia",
"ls"=> "Lesotho","lt"=> "Lithuania",
"lu"=> "Luxembourg","lv"=> "Latvia","ly"=> "Libya","ma"=> "Morocco",
"mc"=> "Monaco","md"=> "Moldova, Republic of","mg"=> "Madagascar",
"mh"=> "Marshall Islands","mk"=> "Macedonia, The former Yougoslav Republic of",
"ml"=> "Mali","mm"=> "Myanmar","mn"=> "Mongolia","mo"=> "Macau",
"mp"=> "Northern Mariana Islands","mq"=> "Martinique","mr"=> "Mauritania",
"ms"=> "Montserrat","mt"=> "Malta","mu"=> "Mauritius","mv"=> "Maldives",
"mw"=> "Malawi","mx"=> "Mexico","my"=> "Malaysia","mz"=> "Mozambique",
"na"=> "Namibia","nc"=> "New Caledonia","ne"=> "Niger",
"nf"=> "Norfolk Island","ng"=> "Nigeria","ni"=> "Nicaragua",
"nl"=> "Netherlands","no"=> "Norway","np"=> "Nepal","nr"=> "Nauru",
"nu"=> "Niue","nz"=> "New Zealand","om"=> "Oman",
"os"=> "Palestinian Territory, Occupied","pa"=> "Panama",
"pe"=> "Peru","pf"=> "Polynesia","pg"=> "Papua New Guinea",
"ph"=> "Philippines","pk"=> "Pakistan","pl"=> "Poland",
"pm"=> "Saint Pierre and Miquelon","pn"=> "Pitcairn",
"pr"=> "Puerto Rico","pt"=> "Portugal","pw"=> "Palau",
"py"=> "Paraguay","qa"=> "Qatar","re"=> "Reunion",
"ro"=> "Romania","ru"=> "Russian Federation","rw"=> "Rwanda",
"sa"=> "Saudi Arabia","sb"=> "Solomon Islands","sc"=> "Seychelles",
"sd"=> "Sudan","se"=> "Sweden","sg"=> "Singapore","sh"=> "Saint Helena",
"si"=> "Slovenia","sj"=> "Svalbard and Jan Mayen Islands",
"sk"=> "Slovak Republic","sl"=> "Sierra Leone","sm"=> "San Marino",
"sn"=> "Senegal","so"=> "Somalia","sr"=> "Suriname",
"st"=> "Sao Tome and Principe","su"=> "Soviet Union",
"sx"=> "Saint Croix","sv"=> "El Salvador","sy"=> "Syria",
"sz"=> "Swaziland","tc"=> "Turks and Caicos Islands",
"td"=> "Chad","tf"=> "French Southern Territories",
"tg"=> "Togo","th"=> "Thailand","tj"=> "Tajikistan",
"tk"=> "Tokelau","tm"=> "Turkmenistan",
"tn"=> "Tunisia","to"=> "Tonga","tp"=> "East Timor",
"tr"=> "Turkey","tt"=> "Trinidad and Tobago",
"tv"=> "Tuvalu","tw"=> "Taiwan","tz"=> "Tanzania",
"ua"=> "Ukraine","ug"=> "Uganda","uk"=> "United Kingdom",
"us"=> "United States","uy"=> "Uruguay","uz"=> "Uzbekistan",
"va"=> "Holly See (Vatican City State)",
"vc"=> "Saint Vincent and the Grenadines","ve"=> "Venezuela",
"vg"=> "Virgin Islands (British)","vi"=> "Virgin Islands (US)",
"vn"=> "Vietnam","vu"=> "Vanuatu",
"wf"=> "Wallis and Futuna Islands","ws"=> "Samoa","ye"=> "Yemen",
"yt"=> "Mayotte","yu"=> "Yugoslavia","za"=> "South Africa",
"zm"=> "Zambia","zr"=> "Zaire","zw"=> "Zimbabwe",
"eu"=> "Saint EUSTATIUS",
"uv"=> "Saint JOHN",
"nt"=> "Saint BARTHELEMY",
"sw"=> "Saint CHRISTOPHER",
"vl"=> "Saint THOMAS"
);
/****************************************************************************************/
/*
function: getUPSQuote
Usage:

$rate = getUPSQuote($upsProductCode,$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode);
    
Input Parameters:

1.) $upsProductCode

This parameter defines the type of UPS shipping service.
The following table lists the UPS services with the UPS product 
code used by the UPS Online Tools Rating and Service.
Or you can send the UPS numeric service codes like 03 for GND.

UPS Service             UPS Product Code
Next Day Air Early AM   1DM
Next Day Air            1DA
Next Day Air Saver      1DP
2nd Day Air AM          2DM
2nd Day Air             2DA
3 Day Select            3DS
Ground                  GND
Canada Standard         STD
Worldwide Express       XPR
Worldwide Express Plus  XDM
Worldwide Expedited     XPD

2.) $packageWeight

This parameter specifies the weight of the package to ship, must be greater than 0.
Must be entered in pounds. (lbs)

3.) $originPostalCode

This parameter specifies the origin postal code of the package to ship.
Must be a one to six alphanumeric-character postal code.

4.) $originCountryCode

This parameter specifies the origin country of the package to ship.
Can be an ISO two letter country code or the full name of the country.
If the origin country name is a full county name, then the script will
use the list of ISO country codes to transform the full counry name into
a two letter ISO country code.

5.) $destPostalCode

This parameter specifies the destination postal code of the package.
Must be a one to six alphanumeric-character postal code.

6.) $destCountryCode

This parameter specifies the destination country of the package.
Can be an ISO two letter country code or the full name of the country.
If the destination country name is a full county name, then the script will
use the list of ISO country codes to transform the full counry name into
a two letter ISO country code.

Optional input parameters
*****************************************************************
7.) $destPostalCity

This parameter specifies the destination city of the package.
default = ""

8.) $UPSPickupTypeCode

This parameter specifies the UPS pick up code.
default = "03"

'01', 'Daily Pickup'
'03', 'Customer Counter'
'06', 'One Time Pickup'
'07', 'On Call Air'	
'19', 'Letter Center'	
'20', 'Air Service Center'	

9.) $PackageType

This parameter specifies the UPS package type code.
default = "02"

'00', 'Unknown'
'01', 'UPS letter'
'02', 'Package'
'03', 'UPS Tube'
'04', 'UPS Pak'
'21', 'UPS Express Box'
'24', 'UPS 25KG Box'
'25', 'UPS 10KG Box'

						

10.) $packageValue

This parameter specifies the value of the package.
Every package you send by UPS is automatically protected
against loss or damage up to $100. 
default = ""

11.) $packageLength

This parameter specifies the length of the package.
default = ""

12.) $packageWidth

This parameter specifies the width of the package.
default = ""

13.) $packageHeight

This parameter specifies the height of the package.
default = ""



Return Values:

Returns UPS shipping rate for the selected service for success 
or an error message for failure. 


*/
        
/***************************************************************************************/      
function getUPSQuote($upsProductCode,$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode,$destPostalCity = '',
    $UPSPickupTypeCode='01',$PackageType = '02',
    $packageValue = '',$packageLength = '',$packageWidth = '',
    $packageHeight = '')
   { 
    
    global $iso_countries,$UPS_Server,$return_code, 
    $AccessLicenseNumber,$UserId,$Password,$_CURL;
    
    if(empty($upsProductCode)){
      return ("Missing UPS service with the UPS product code.");
    }
    if(empty($packageWeight)){
      return ("Missing package weight.");
    }
    if(empty($originPostalCode)){
      return ("Missing the five digit postal ZIP code of the shipment origin.");
    }
  	if(empty($originCountryCode)){
      return ("Missing the country of the shipment origin.");
    }
  	if(strlen($originCountryCode) > 3){
  	   $originCountryCode = preg_replace("'&AMP;'si","and",$originCountryCode);
  	   $originCountryCode = preg_replace("'St. 'si","Saint ",$originCountryCode);
  	   reset($iso_countries);
      	while (list($key, $value) = each($iso_countries)) {
      	       if(strcasecmp($originCountryCode,$value)==0){
      	         $originCountryCode = strtoupper($key);
      	         break;
      	       }  
        }
  	} 
    $code = strtolower($originCountryCode);
  	$country = $iso_countries["$code"];
  
    if(empty($country)){
      return ("Missing the ISO country code of the shipment origin country $originCountryCode .");
    }
   
  
    if(empty($destPostalCode)){
      return ("Missing the one to six alphanumeric-character postal code of the destination country, if the destination country uses postal codes.");
    }
    if(empty($destCountryCode)){
      return ("Missing the country of the destination country.");
    }
    if(strlen($destCountryCode) > 3){
    	 reset($iso_countries);
    	 $destCountryCode = preg_replace("'&AMP;'si","and",$destCountryCode);
    	 $destCountryCode = preg_replace("'ST. 'si","Saint ",$destCountryCode);
      	while (list($key, $value) = each($iso_countries)) {
      	       if(strcasecmp($destCountryCode,$value)==0){
      	         $destCountryCode = strtoupper($key);
      	         break;
      	       }  
        }
  	} 

  	
    $code = strtolower($destCountryCode);
  	$country = $iso_countries["$code"];
  
    if(empty($country)){
      return ("Missing the ISO country code of the destination country $destCountryCode .");
    }
  if(is_numeric($upsProductCode)){
    $UPSServiceCode = $upsProductCode;  
  }
  else{
  $UPSServiceCode = get_service_code($upsProductCode);   
  }       


  $Dimensions='';
  $InSure='';
	
			if(!empty($packageLength) && !empty($packageWidth) && !empty($packageHeight))
			{			
$Dimensions=<<<__DIM__
  <Dimensions>
      <UnitOfMeasurement>
        <Code>IN</Code>
      </UnitOfMeasurement>
      <Length>$packageLength</Length>
      <Width>$packageWidth</Width>
      <Height>$packageHeight</Height>
    </Dimensions>
__DIM__;
			}

			if(!empty($packageValue))
			{
				$InSure=<<<__INS__
		<PackageServiceOptions>
      <InsuredValue>
        <CurrencyCode>USD</CurrencyCode>
        <MonetaryValue>$packageValue</MonetaryValue>
      </InsuredValue>
    </PackageServiceOptions>
__INS__;
				
			}

// create UPS XML requset work string
$work_string=<<<__REQUEST__

<?xml version="1.0"?>
<AccessRequest xml:lang="en-US">
  <AccessLicenseNumber>$AccessLicenseNumber</AccessLicenseNumber>
  <UserId>$UserId</UserId>
  <Password>$Password</Password>
</AccessRequest>

<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang="en-US">
  <Request>
   <TransactionReference>
    <CustomerContext></CustomerContext>
    <XpciVersion>1.0001</XpciVersion>
   </TransactionReference>
   <RequestAction>Rate</RequestAction>
   <RequestOption>rate</RequestOption>
   </Request>

  <PickupType>
   <Code>$UPSPickupTypeCode</Code>
  </PickupType>

  <Shipment>
   <Shipper>
    <Address>
      <City></City>
      <StateProvinceCode></StateProvinceCode>
      <PostalCode>$originPostalCode</PostalCode>
      <CountryCode>$originCountryCode</CountryCode>
    </Address>
   </Shipper>

   <ShipTo>
    <Address>
      <City>$destPostalCity</City>
      <StateProvinceCode></StateProvinceCode>
      <PostalCode>$destPostalCode</PostalCode>
      <CountryCode>$destCountryCode</CountryCode>
      <ResidentialAddress/>
    </Address>
   </ShipTo>
   
   <Service>
    <Code>$UPSServiceCode</Code>
   </Service>
  <Package>
    <PackagingType>
      <Code>$PackageType</Code>
      <Description>Package</Description>
    </PackagingType>
    <Description>UPS Package</Description>
      $Dimensions    
    <PackageWeight>
      <Weight>$packageWeight</Weight>
      <UnitOfMeasurement>
        <Code>LBS</Code>
      </UnitOfMeasurement>
    </PackageWeight>
      $InSure
  </Package>
  </Shipment>
</RatingServiceSelectionRequest>
__REQUEST__;

    //echo "<p>$work_string"; 
  
    if($_CURL == true){$result = UPS_Post_CURL($UPS_Server,$work_string);}
    else{$result = UPS_Post_PHP($UPS_Server,$work_string);}
    
	  
	// print_r($result);	
        
    if(! $result) { return "Error connecting to UPS shipping rates calculator server."; } 
    if($return_code > 200) { return "Error connecting to UPS shipping rates calculator server."; } 

    $attributes = array();
    $attributes = XMLParser($result);
    $rate = 0;            
    reset($attributes);
		while (list ($key, $val) = each ($attributes)) 
		{
			$error = false;
			switch($val['tag'])
			{
				case 'ResponseStatusCode':
					$ResponseStatusCode =$val['value'];
					break;
	       case 'Error':
					while((list($key,$val)=each($attributes)) && ($val['tag']!='Error'))
					{			
						if($val['tag'] == 'ErrorSeverity' && $val['value'] != "Warning"){
							$error = true;
						}	
						if($val['tag']=='ErrorDescription' && $error)
						{
							$error_description=$val['value'];
						}	
					
					}
				  if($error_description){return $error_description;}
					break;
					
				case 'RatedShipment':
					while((list($key,$val)=each($attributes)) && ($val['tag']!='RatedShipment'))
					{
						switch($val['tag'])
						{

							case 'TotalCharges':
								while((list($key,$val)=each($attributes)) && ($val['tag']!='TotalCharges'))
								{
									if($val['tag']=='MonetaryValue')
									{
										if($rate == 0){$rate=$val['value'];}
									}
								}
								break;
														 
						}
					}
				  break;
			}
			
		}
      
      return $rate; 
}
//===============================================================================
function UPS_Post_PHP($url,$post_string){

 $header = "POST /ups.app/xml/Rate HTTP/1.0\r\n";
 $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
 $header .= "Content-Length: " . strlen ($post_string) . "\r\n\r\n";
   
 $fp = fsockopen ("https://wwwcie.ups.com", 443, $errno, $errstr, 15);
 
 if (!$fp) {
 // HTTPS ERROR
 return "$errstr ($errno)";
 } else {
    fputs ($fp, $header . $post_string);
    $results=fread($fp,4096);
    $bytes_left = socket_get_status($fp);
    if ($bytes_left > 0) { $results.=fread($fp, $bytes_left); }
    fclose ($fp);
 }
   if(! eregi(' 200 ',$results)) {echo "HTTPS error connecting to UPS Online Tools"; return; } 
   if(! eregi('close',$results)) {echo "HTTP HEAD error connecting to UPS Online Tools"; return;} 
   list($junk,$xml) = split("close",$results);
   $xml = trim($xml);
   return "$xml";
}

//==================================================================================
function UPS_Post_CURL($url,$post_string) {
   global $return_code;
   $return_code = 200;
   
   $ch = curl_init('https://www.ups.com/ups.app/xml/Rate');
   // curl_setopt ($ch, CURLOPT_URL, $url);
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_string);
   # curl_setopt ($ch, CURLOPT_POSTFIELDSIZE, 0);
   curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 240);
   curl_setopt ($ch, CURLOPT_TIMEOUT, 240);
   curl_setopt ($ch, CURLOPT_HEADER, 1);
   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
   curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt ($ch, CURLOPT_SSLVERSION, 3);
   curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3'); 
   curl_setopt ($ch, CURLOPT_VERBOSE, 1 );
   
   $result = curl_exec ($ch); 
   $array1 = array();
   $array1 = curl_getinfo($ch);
   $code = $array1['http_code'];
   $codestring = "http_code " .$code ." "  .curl_error ($ch);
   // echo $codestring;
   curl_close ($ch); 


	   if (strstr($code,"200")){
	        $return_code = 200;
          return $result;
        }
	   if (strstr($code,"404")){
	          $return_code = 404;
            return ("404 Error. The $url server responded with a File Not Found message.");
        }
        if (strstr($code,"40")){
            $return_code = 404;
            return ("$code Error. The $url server responded with an Unauthorized Access Forbidden Error.");
        }
 
        if (strstr($code,"30")){
            $return_code = 300;
            return("$code Error. The $url server responded with redirects.");
        }
       if (strstr($code,"50")){
            $return_code = 500;
            return("$code Error. The $url server responded with a Server Error.");
        }
    return ("Error. $url cannot be accessed at this time. <br> $codestring");
}
/* XMLParser to put all the values from response into an array */
/************************************************************************/

	function XMLParser($post_results) 
	{
		if ( !($xml_parser = xml_parser_create()) )
          die("Couldn't create XML Parser!");
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($xml_parser,$post_results,$vals,$index);
		xml_parser_free($xml_parser);

		return $vals;
	}
/****************************************************************************/   
  

/*
function: validateUPSAddress
Usage:

$vaild = validateUPSAddress($destPostalCode,$destCountryCode);
    
Input Parameters:


1.) $destPostalCode

This parameter specifies the destination postal code to validate.
Must be a one to six alphanumeric-character postal code.

6.) $destCountryCode

This parameter specifies the destination country of the postal code to validate.
Can be an ISO two letter country code or the full name of the country.
If the destination country name is a full county name, then the script will
use the list of ISO country codes to transform the full counry name into
a two letter ISO country code.

Return Values:

Returns UPS destination postal City, State and zipcode for success  
or an error message for failure. 


*/
        
/***************************************************************************************/      
function validateUPSAddress($destPostalCode,$destCountryCode){ 
    
    global $iso_countries;
     
    if(empty($destPostalCode)){
      return ("Missing the one to six alphanumeric-character postal code of the destination country, if the destination country uses postal codes.");
    }
    
    if(empty($destCountryCode)){
      return ("Missing the country of the destination postal code.");
    }
    if(strlen($destCountryCode) > 3){
    	 reset($iso_countries);
    	 $destCountryCode = preg_replace("'&AMP;'si","and",$destCountryCode);
    	 $destCountryCode = preg_replace("'ST. 'si","Saint ",$destCountryCode);
      	while (list($key, $value) = each($iso_countries)) {
      	       if(strcasecmp($destCountryCode,$value)==0){
      	         $destCountryCode = strtoupper($key);
      	         break;
      	       }  
        }
  	} 

  	
    $code = strtolower($destCountryCode);
  	$country = $iso_countries["$code"];
  
    if(empty($country)){
      return ("Missing the ISO country code of the destination country $destCountryCode .");
    }
  
    if(! eregi('US',$destCountryCode)) {return ("Valid Postal Code check not supported for $destCountryCode."); } 

   
    $destPostalCode = urlencode($destPostalCode);
          
          
$work_string = "http://www.ups.com/using/services/cszval/cszval_dss.cgi?";
$work_string .= "AcceptUPSLicenseAgreement=yes";
$work_string .= "&AppVersion=1.0";
$work_string .= "&ResponseType=application/x-ups-cszval";
$work_string .= "&PostalCode=$destPostalCode";
                 
      $fp = fopen($work_string, "r"); 
      if(!$fp){ return "Error connecting to UPS shipping rates calculator server."; } 
      while(!feof($fp)){ 
        $result1 = fgets($fp, 2048); 
        //echo "<p>$result1";
        if(eregi('error',$result1)) { fclose($fp); return ("Not a Valid US Postal Code."); } 
        $result = array();
        $result = split("%", $result1);
        if(is_array($result)){
          if(preg_match("/^[0-9]/",$result[8])){
            $returnval .= ": $result[6], $result[7] $result[8]"; 
          }  
        }  
  
      } 
      fclose($fp); 
      if(! $returnval) { $returnval = "Error connecting to UPS zipcode validating server."; } 
      return $returnval; 
}
/*********************************************************************/
function get_service_string($service){
  if(empty($service)){ return;}
   switch($service){ 
        case "1DM":            
          $shipping_method = "UPS Next Day Early AM"; 
          break; 
        case "1DML":            
          $shipping_method = "UPS Next Day Early AM Letter"; 
          break; 
        case "1DA":            
          $shipping_method = "UPS Next Day Air"; 
          break;          
        case "1DAL":            
          $shipping_method = "UPS Next Day Air Letter"; 
          break; 
        case "1DAPI":            
          $shipping_method = "UPS Next Day Air Intra (Puerto Rico)"; 
          break; 
        case "1DP":            
          $shipping_method = "UPS Next Day Air Saver"; 
          break; 
        case "1DPL":            
          $shipping_method = "UPS Next Day Air Saver Letter"; 
          break;   
        case "2DM":            
          $shipping_method = "UPS 2nd Day Air AM"; 
          break; 
        case "2DA":            
          $shipping_method = "UPS 2nd Day Air"; 
          break; 
        case "2DML":            
          $shipping_method = "UPS 2nd Day Air AM Letter"; 
          break; 
        case "2DAL":            
          $shipping_method = "UPS 2nd Day Air Letter"; 
          break; 
        case "3DS":            
          $shipping_method = "UPS 3 Day Select"; 
          break; 
        case "GND":            
          $shipping_method = "UPS Ground"; 
          break; 
        case "GNDRES":            
          $shipping_method = "UPS Ground Residential"; 
          break; 
        case "GNDCOM":            
          $shipping_method = "UPS Ground Commercial"; 
          break;           
        case "STD":            
          $shipping_method = "Canada Standard"; 
          break; 
        case "XPR":            
          $shipping_method = "UPS Worldwide Express"; 
          break; 
        case "XDM":            
          $shipping_method = "UPS Worldwide Express Plus"; 
          break; 
        case "XPRL":            
          $shipping_method = "UPS Worldwide Express Letter"; 
          break; 
        case "XDML":            
          $shipping_method = "UPS Worldwide Express Plus Letter"; 
          break; 
        case "XPD":            
          $shipping_method = "UPS Worldwide Expedited"; 
          break; 

    } 
    return $shipping_method;
}  

/*********************************************************************/
function get_service_code($service){
  if(empty($service)){ return;}
   switch($service){ 
        case "1DM":            
          $shipping_method = "14"; 
          break; 
        case "1DA":            
          $shipping_method = "01"; 
          break;          
         case "1DAPI":            
          $shipping_method = "01"; 
          break; 
        case "1DP":            
          $shipping_method = "13"; 
          break; 
        case "2DM":            
          $shipping_method = "59"; 
          break; 
        case "2DA":            
          $shipping_method = "02"; 
          break; 
        case "3DS":            
          $shipping_method = "12"; 
          break; 
        case "GND":            
          $shipping_method = "03"; 
          break; 
        case "GNDRES":            
          $shipping_method = "03"; 
          break; 
        case "GNDCOM":            
          $shipping_method = "03"; 
          break;           
        case "STD":            
          $shipping_method = "03"; 
          break; 
        case "XPR":            
          $shipping_method = "07"; 
          break; 
        case "XDM":            
          $shipping_method = "54"; 
          break; 
        case "XPD":            
          $shipping_method = "08"; 
          break; 
        default:            
          $shipping_method = "03"; 
          break; 

    } 
    return $shipping_method;
}  


/*********************************************************************/
?>