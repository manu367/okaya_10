<?php 
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
$json = $_POST["usersJSON"];
function cleanData_atten($str) {
$str = preg_replace("/\t/", "", $str); 
$str = preg_replace("/\r?\n/", "", $str);
$str = preg_replace("/\n/", "", $str);
$str = preg_replace("/%0A/", "", $str);
$str = preg_replace('/\s+/','',$str);
if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
return $str;
} 
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
//Decode JSON into an Array 
$data = json_decode($json); 
//Util arrays to create response JSON 
$a=array();
for($i=0; $i<count($data) ; $i++) {
	$str=$data[$i]->email;
	$clean_str=cleanData_atten($str);
//Store User into MySQL DB 
$res = $db->Addproduct($data[$i]->customer_id,$data[$i]->modelid,$data[$i]->productname,$data[$i]->purchase_date,$data[$i]->img,$data[$i]->entry_date,$data[$i]->serial_no,$data[$i]->warranty_end_date);

//Based on inserttion, create JSON response 
 
array_push($a,$res);    
}
echo json_encode($a); 
 ?>