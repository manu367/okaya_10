<?php 
/**  * Creates Unsynced rows data as JSON  */

include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getComplaintsData($_REQUEST['eid']);  

$a = array();     
$b = array();  
 
if ($users != false ){  
$res=explode("~",$users);       

         
$b["pending"] =$res[0];     
$b["assigned"] =$res[0]+$res[1];  
$b["closed"] =$res[1]; 

$b["wo_collection"]="";
$b["amc_collect"]="";  
$b["total_amc"]=""; 
$b["pending_seven"]=$res[2];

$b["total_call"]="0";
$b["tat_4hour"]="0";  
$b["tat_same"]="0"; 
$b["csat"]="0";
$b["amc"]="0";  
$b["oow_collection"]="0"; 
$b["amount_pending"]="0";
$b["attendance"]="0";  


array_push($a,$b);         
        
echo json_encode($a);     
} 
/////////////////////


?>