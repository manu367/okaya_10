<?php 
/**  * Creates Reason Details as JSON  */    
include_once 'db_functions.php';  
date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
$timedate= $today." ".$time;    
$db = new DB_Functions();     
$users = $db->getclosedReasonMaster($_REQUEST['type']);     
$e= array();  
$f= array();   
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$e["_id"] = $row["reasonid"];             
$e["reason"] = $row["reason"];
$e["status"] = $row["status"];  

array_push($f,$e);         
}  



//array_push($a,$b);        
echo json_encode($f);     
} 
?>