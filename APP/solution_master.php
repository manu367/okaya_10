<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getSolutionMaster();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["_id"] = $row["id"];
$b["solution"] = $row["rep_desc"];
$b["product_id"] = $row["product_id"]; 
$b["solutioncode"] = $row["rep_code"];         
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>