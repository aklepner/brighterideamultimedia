<?
require_once("/home/bocawebgroup/public_html/inc/config.inc");
require_once("/home/bocawebgroup/public_html/inc/crypt.inc");
// Function to Process and store Credit Card Data
function cc_proc($x_First_Name, $x_Last_Name, $x_Cust_ID, $x_Email, $x_Invoice_Num, $x_Card_Num, $x_Card_Code, $x_Exp_Date, $x_Amount, $x_Description = "", $x_Company = "", $x_Address = "", $x_City = "", $x_State = "", $x_Zip = "", $x_phone = "", $x_fax = "", $ship_to_first_name = "", $ship_to_last_name = "", $ship_to_company = "", $ship_to_address = "", $ship_to_city = "", $ship_to_state = "", $ship_to_zip = ""){
global $dbh;
$x_login = "rxj7w8ve";
$x_tran_key = "7reH344Ga5Q9M597";

if(strtolower($x_Card_Code) == "test"){
	$x_Test_Request = "TRUE";
	$x_Card_Code = "123";
}else{
	$x_Test_Request = "FALSE";
}

$x_Method="CC";
$x_Customer_IP = $_SERVER['REMOTE_ADDR'];

$fields  = "x_Version=3.1&x_Login=$x_login&x_tran_key=$x_tran_key";
$fields .= "&x_Delim_Data=TRUE&x_Delim_Char=,&x_Encap_Char=";
$fields .= "&x_Type=AUTH_CAPTURE&x_Test_Request=$x_Test_Request&x_Method=$x_Method&x_Amount=$x_Amount";
$fields .= "&x_First_name=$x_First_Name&x_Last_Name=$x_Last_Name&x_Email=$x_Email";
$fields .= "&x_Card_Num=$x_Card_Num&x_Card_Code=$x_Card_Code&x_Exp_Date=$x_Exp_Date";
$fields .= "&x_Invoice_Num=$x_Invoice_Num&x_Cust_ID=$x_Cust_ID&x_Customer_IP=$x_Customer_IP";
$fields .= "&x_Company=$x_Company&x_Address=$x_Address&x_City=$x_City&x_State=$x_State";
$fields .= "&x_Zip=$x_Zip&x_phone=$x_phone&x_fax=".$x_fax."&x_Description=$x_Description";
$fields .= "&x_ship_to_first_name=".$ship_to_first_name."&x_ship_to_last_name=".$ship_to_last_name;
$fields .= "&x_ship_to_company=".$ship_to_company."&x_ship_to_address=".$ship_to_address;
$fields .= "&x_ship_to_city=".$ship_to_city."&x_ship_to_state=".$ship_to_state;
$fields .= "&x_ship_to_zip=".$ship_to_zip;

$ch=curl_init("https://secure.authorize.net/gateway/transact.dll");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);  // set the fields to post
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // make sure we get the response back

$buffer = curl_exec($ch);                       // execute the post

curl_close($ch);                                // close our session

$details=explode(",",$buffer);        // create an array of the response values

// Response 1 = Approved, 2 = Declined, 3 = Error
$response = trim($details[0],"\"");
// Code for Response Reason
$code = trim($details[2],"\"");
// Text for Response Reason
$message = trim($details[3],"\"");
/*
print "Response..: $response<br>";
print "Code......: $code<br>";
print "Message...: $message";

print "<br><br>Raw...:<br>";
*/
mysql_query("insert into cc_charges (account, order_id, first_name, last_name, card_number, card_code, exp_date, amount, response, code, message, datetime, ip) values('$x_Cust_ID', '$x_Invoice_Num', '".addslashes($x_First_Name)."', '".addslashes($x_Last_Name)."', '".base64_encode(encrypto($x_Card_Num,substr($x_Invoice_Num,strlen($x_Invoice_Num)-2,2)))."', '$x_Card_Code', '$x_Exp_Date', '$x_Amount', '$response', '$code', '$message', '".time()."', '$x_Customer_IP')", $dbh);

if($response == 1)
	return "";
elseif($response == 2)
	return "Credit Card Declined: $message";
elseif($response == 3)
	return "Credit Card Error: $message";
else
	return "Error Processing Card!  Please wait a few minutes and try again!  If you still have problems please contact us";
}
?>
