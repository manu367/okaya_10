<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getclosetype();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["id"] = $row["sno"];
$b["type"] = $row["type"]; 


array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>