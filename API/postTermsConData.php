<?php 
include_once './db_functions.php'; 
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a = array();     
$b = array(); 
///for($i=0; $i<count($data) ; $i++) { 
//Store User into MySQL DB 
$res = $db->postTermsConData($_REQUEST['engId'],$_REQUEST['engName'],$_REQUEST['agreeFlag'],$_REQUEST['msg_id']);
//Based on inserttion, create JSON response 
////}
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