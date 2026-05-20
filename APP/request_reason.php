<?php 
/**  * Creates Reason Details as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getrequestreason();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["id"] = $row["id"];             
$b["reason"] = $row["desc"];

array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>