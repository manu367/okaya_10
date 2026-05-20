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

$ei = $_REQUEST['eid'];
for($i=0; $i<count($data) ; $i++) {
	$add1=$data[$i]->address_in;
	$clean_add1=cleanData_atten($add1);
	$add2=$data[$i]->address_out;
	$clean_add2=cleanData_atten($add2);
//Store User into MySQL DB 
$res = $db->Mic_Eng_attendence($data[$i]->longitu_in,$data[$i]->latitu_in,$data[$i]->in_datetime,$data[$i]->out_datetime,$data[$i]->status_in,$data[$i]->status_out,$data[$i]->user_id,$clean_add1,$clean_add2,$data[$i]->insert_date,$data[$i]->longitu_out,$data[$i]->latitu_out,$data[$i]->image_in,$data[$i]->image_out);
//Based on inserttion, create JSON response 
array_push($a,$res);    
}
echo json_encode($a); 
 ?>