<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getServerdatetime();     
$a = array();     
$b = array();    
if ($users != false){         
      $users=explode("^",$users);
$b["server_date"] = $users[0];
$b["server_time"] = $users[1]; 

array_push($a,$b);         
      
echo json_encode($a);     
} 
?>