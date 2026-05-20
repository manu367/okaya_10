<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getDivison();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["div_id"] = $row["divisionid"];
$b["divison"] = $row["division"]; 
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>