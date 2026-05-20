<?php 
include_once './db_functions.php'; 
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a = array();     
$b = array(); 
$json = $_REQUEST["usersJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
$data = json_decode($json);

for($i=0; $i<count($data) ; $i++) { 
//Store User into MySQL DB 
///$res = $db->postCollectonData($data[$i]->deposit_trn_no ,$data[$i]->deposit_mode,$data[$i]->total_amt,$data[$i]->eng_id,$data[$i]->remark,$data[$i]->complaint_details);
//Based on inserttion, create JSON response 
}
if($res){               
$b["status"] = $res; 
	array_push($a,$b);    
}else{         
$b["status"] = $res; 
	array_push($a,$b);    
 } 
echo json_encode($a); 
  //Post JSON response back to Android Application 

 ?>