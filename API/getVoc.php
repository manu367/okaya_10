<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getVoc();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["id"] = $row["id"];
$b["voc_code"] = $row["voc_code"]; 
$b["voc_desc"] = $row["voc_desc"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>