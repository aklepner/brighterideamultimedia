<?php 
/*
SYNOPSIS:

Integrate your e-commerce applications with this PHP module that return shipping rates from the UPS OnLine Tools. 

PHP library to retrieve shipping rate quotes from the 
UPS Online Tools Rating and Service Selection HTML, Version 2000a.

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

8.) $resCode

This parameter specifies whether delivery is to a residential or commercial location.
default = "1"
1 for Residential
0 for Commercial

9.) $containerCode

This parameter specifies the type of shipping container.
default = "00"
00 Your Packaging
01 UPS Letter Envelope
03 UPS Tube
21 UPS Express Box
24 UPS Worldwide 25KG Box
25 UPS Worldwide 10KG Box

10.) $rateCode

This parameter specifies how the package gets to UPS.
Rates vary depending on where the shipment is picked up from or dropped off at.
default = "One+Time+Pickup"
Can be one of the following: 
Regular Daily Pickup
On Call Air
One Time Pickup
Letter Center
Customer Counter

11.) $packageValue

This parameter specifies the value of the package.
Every package you send by UPS is automatically protected
against loss or damage up to $100. 
default = ""

12.) $packageLength

This parameter specifies the length of the package.
default = ""

13.) $packageWidth

This parameter specifies the width of the package.
default = ""

14.) $packageHeight

This parameter specifies the height of the package.
default = ""

.Maximum single package weight: 150 pounds.
.Maximum size: 130 inches in length and girth combined.
.Maximum length: 108 inches

Return Values:

Returns UPS shipping rate for the selected service for success 
or an error message for failure. 


*/
        
/***************************************************************************************/      
function getUPSQuote($upsProductCode,$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode,
    $destPostalCity = "",
    $resCode = "0",
    $containerCode = "00",
    $rateCode = "Regular+Daily+Pickup",
    $packageValue = "",
    $packageLength = "",
    $packageWidth = "",
    $packageHeight = ""){ 
    
    global $iso_countries;
    //Specifies level of UPS service details
    $upsAction = "3"; //Price a single UPS service
    
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
  
  
    $destPostalCode = urlencode($destPostalCode);
    $originPostalCode = urlencode($originPostalCode);          
          
          
$work_string = "http://www.ups.com/using/services/rave/qcostcgi.cgi?";
$work_string .= "accept_UPS_license_agreement=yes";
$work_string .= "&10_action=$upsAction";
$work_string .= "&13_product=$upsProductCode";
$work_string .= "&14_origCountry=$originCountryCode";
$work_string .= "&15_origPostal=$originPostalCode";
$work_string .= "&19_destPostal=$destPostalCode";
$work_string .= "&22_destCountry=$destCountryCode";
$work_string .= "&23_weight=$packageWeight";
$work_string .= "&47_rateChart=$rateCode";
$work_string .= "&48_container=$containerCode";
$work_string .= "&49_residential=$resCode";
if($destPostalCity){
  $destPostalCity = urlencode($destPostalCity);
  $work_string .= "&20_destCity=$destPostalCity";
}
if($packageValue){
  $packageValue = urlencode($packageValue);
  $work_string .= "&24_value=$packageValue";
}
if($packageLength){
  $packageLength = urlencode($packageLength);
  $work_string .= "&25_length=$packageLength";
}
if($packageWidth){
  $packageWidth = urlencode($packageWidth);
  $work_string .= "&26_width=$packageWidth";
}
if($packageHeight){
  $packageHeight = urlencode($packageHeight);
  $work_string .= "&27_height=$packageHeight";
}

                 
      $fp = fopen($work_string, "r"); 
      if(!$fp){ return "Error connecting to UPS shipping rates calculator server."; } 

      while(!feof($fp)){ 
        $result1 = fgets($fp, 1024); 
        //echo "<p>$result1";
        
        $result = split("%", $result1);
        $errcode = substr($result[0], -1); 
 
        switch($errcode){ 
          case 3: 
            $returnval = $result[8]; 
                break; 
          case 4: 
            $returnval = $result[8]; 
            break; 
          case 5: 
            $returnval = $result[1]; 
            break; 
          case 6: 
            $returnval = $result[1]; 
            break; 
        } 
      } 
      fclose($fp); 
      if(! $returnval) { $returnval = "Error connecting to UPS shipping rates calculator server."; } 
      return $returnval; 
 
}

/****************************************************************************************/
/*
function: getUPSSelectList
Usage:

$select_list_rates = getUPSSelectList($type,$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode);
    
Input Parameters:

1.) $type

This parameter defines the type of html return.
default = "1"
1 select list
0 radio buttons

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

8.) $resCode

This parameter specifies whether delivery is to a residential or commercial location.
default = "1"
1 for Residential
0 for Commercial

9.) $containerCode

This parameter specifies the type of shipping container.
default = "00"
00 Your Packaging
01 UPS Letter Envelope
03 UPS Tube
21 UPS Express Box
24 UPS Worldwide 25KG Box
25 UPS Worldwide 10KG Box

10.) $rateCode

This parameter specifies how the package gets to UPS.
Rates vary depending on where the shipment is picked up from or dropped off at.
default = "One+Time+Pickup"
Can be one of the following: 
Regular Daily Pickup
On Call Air
One Time Pickup
Letter Center
Customer Counter

11.) $packageValue

This parameter specifies the value of the package.
Every package you send by UPS is automatically protected
against loss or damage up to $100. 
default = ""

12.) $packageLength

This parameter specifies the length of the package.
default = ""

13.) $packageWidth

This parameter specifies the width of the package.
default = ""

14.) $packageHeight

This parameter specifies the height of the package.
default = ""

.Maximum single package weight: 150 pounds.
.Maximum size: 130 inches in length and girth combined.
.Maximum length: 108 inches

Return Values:

Returns UPS shipping rate HTML select list or a group of radio buttons for success 
or an error message for failure. 

The following table lists the UPS services with the UPS product 
code used by the UPS Online Tools Rating and Service.

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


*/
        
/***************************************************************************************/      
function getUPSSelectList($type="1",$packageWeight,
    $originPostalCode,$originCountryCode,
    $destPostalCode,$destCountryCode,
    $destPostalCity = "",
    $resCode = "0",
    $containerCode = "00",
    $rateCode = "One+Time+Pickup",
    $packageValue = "",
    $packageLength = "",
    $packageWidth = "",
    $packageHeight = ""){ 
    
    global $iso_countries,$m_symbol;
    //Specifies level of UPS service details
    $upsAction = "4"; // Shop entire UPS product range, starting with the selected product
    
    if(empty($packageWeight)){
      return ("<option value=\"\">Error,Missing package weight.");
    }
    if(empty($originPostalCode)){
      return ("<option value=\"\">Error,Missing the five digit postal ZIP code of the shipment origin.");
    }
  	if(empty($originCountryCode)){
      return ("<option value=\"\">Error,Missing the country of the shipment origin.");
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
      return ("<option value=\"\">Error,Missing the ISO country code of the shipment origin country $originCountryCode .");
    }
   
  
    if(empty($destPostalCode)){
      return ("<option value=\"\">Error,Missing the one to six alphanumeric-character postal code of the destination country, if the destination country uses postal codes.");
    }
    if(empty($destCountryCode)){
      return ("<option value=\"\">Error,Missing the country of the destination country.");
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
      return ("<option value=\"\">Error,Missing the ISO country code of the destination country $destCountryCode .");
    }
  
  
    $destPostalCode = urlencode($destPostalCode);
    $originPostalCode = urlencode($originPostalCode);          
          
          
$work_string = "http://www.ups.com/using/services/rave/qcostcgi.cgi?";
$work_string .= "accept_UPS_license_agreement=yes";
$work_string .= "&10_action=$upsAction";
$work_string .= "&13_product=GND";
$work_string .= "&14_origCountry=$originCountryCode";
$work_string .= "&15_origPostal=$originPostalCode";
$work_string .= "&19_destPostal=$destPostalCode";
$work_string .= "&22_destCountry=$destCountryCode";
$work_string .= "&23_weight=$packageWeight";
$work_string .= "&47_rateChart=$rateCode";
$work_string .= "&48_container=$containerCode";
$work_string .= "&49_residential=$resCode";
if($destPostalCity){
  $destPostalCity = urlencode($destPostalCity);
  $work_string .= "&20_destCity=$destPostalCity";
}
if($packageValue){
  $packageValue = urlencode($packageValue);
  $work_string .= "&24_value=$packageValue";
}
if($packageLength){
  $packageLength = urlencode($packageLength);
  $work_string .= "&25_length=$packageLength";
}
if($packageWidth){
  $packageWidth = urlencode($packageWidth);
  $work_string .= "&26_width=$packageWidth";
}
if($packageHeight){
  $packageHeight = urlencode($packageHeight);
  $work_string .= "&27_height=$packageHeight";
}

                 
      $fp = fopen($work_string, "r"); 
      if(!$fp){ return "Error connecting to UPS shipping rates calculator server."; } 
      while(!feof($fp)){ 
        $result1 = fgets($fp, 1024); 
        //echo "<p>$result1";
        
        $result = split("%", $result1);
        $errcode = substr($result[0], -1); 
 
        switch($errcode){ 
          case 3: 
             $text = get_service_string($result[1]);
              if($type == 1){
                  $returnval .= "<option value=\"$result[8]\">$text add $m_symbol$result[8]</option>\n"; 
              }
              else{
                  $returnval .= "<input type=\"radio\" name=\"rate\" value=\"$result[8]\">$text  $m_symbol$result[8]<br>\n"; 
              }
            break; 
          case 4: 
            $text = get_service_string($result[1]);
              if($type == 1){
                  $returnval .= "<option value=\"$result[8]\">$text add $m_symbol$result[8]</option>\n"; 
              }
              else{
                  $returnval .= "<input type=\"radio\" name=\"rate\" value=\"$result[8]\">$text  $m_symbol$result[8]<br>\n"; 
              }
            break; 
        } 
      } 
      fclose($fp); 
      if(! $returnval) { $returnval = "<option value=\"\">Error connecting to UPS shipping rates calculator server.</option>\n"; } 
      $returnval  = str_replace('$',"&#036;", $returnval);
      return $returnval; 
 
}
/*********************************************************************/

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
        $result1 = fgets($fp, 1024); 
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
?>  

