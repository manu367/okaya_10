<?php 
/**  * Creates Reason Details as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getReasonMaster();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["_id"] = $row["reasonid"];             
$b["reason"] = $row["reason"];
$b["status"] = $row["status"];  
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>