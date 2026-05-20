<?php 

include_once 'db_functions.php'; 

$db = new DB_Functions(); 
 $json = $_REQUEST["usersJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
	

//Decode JSON into an Array 
$data = json_decode($json); 

//Util arrays to create response JSON 
$a=array();
$b=array(); 



for($i=0; $i<count($data) ; $i++) {
//Store User into MySQL DB 
   $res = $db->storeHistory($data[$i]->job_no,$data[$i]->status,$data[$i]->updateDate,$data[$i]->repair_status,$data[$i]->eid,$data[$i]->address,$data[$i]->branch_code);

//Based on inserttion, create JSON response     
 $res2=explode("~",$res);
  
if($res2){        
$b["job_no"] = $res2[0];         
$b["update_status"] =0;      
$b["updateDate"] =$res2[2];   
array_push($a,$b); 
}else{    
$b["job_no"] = $res2[0];        
$b["status"] = 1; 
 array_push($a,$b);  

 } 
 } //Post JSON response back to Android Application 
 echo json_encode($a); 
 ?>