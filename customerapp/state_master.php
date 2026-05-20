<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getStateMaster();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["_id"] = $row["stateid"];
$b["state_code"] = $row["statecode"];
$b["state_name"] = $row["state"];
       
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>