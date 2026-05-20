<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getAMCLIST();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["amcid"] = $row["amcid"]; 
$b["status"] =  $db->getAMCStatus_details($row["status"]);
$b["customer_name"] = $row["customer_name"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>