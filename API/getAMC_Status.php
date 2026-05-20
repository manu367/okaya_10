<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getAMCStatus();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["status_id"] = $row["status_id"]; 
$b["status"] = $row["display_status"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>