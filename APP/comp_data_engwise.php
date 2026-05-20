<?php 
/**  * Creates Unsynced rows data as JSON  */

include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getComplaintsData();  

$a = array();     
$b = array();  
 
if ($users != false ){  
$res=explode("~",$users);       

         
$b["pending"] =$res[0];     
$b["assigned"] =$res[0]+$res[1];  
$b["closed"] =$res[1]; ; 

$b["wo_collection"]="";
$b["amc_collect"]="";  
$b["total_amc"]=""; 
$b["pending_seven"]=$res[2];  
array_push($a,$b);         
        
echo json_encode($a);     
} 
/////////////////////


?>