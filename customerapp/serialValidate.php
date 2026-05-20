<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->ValidateSerialno();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["serial_no"] = $row["serial_no"];
$b[status] = 1;
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>