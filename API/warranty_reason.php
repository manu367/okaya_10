<?php 
/**  * Creates Reason Details as JSON  */    
include_once 'db_functions.php';  
	date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
$timedate= $today." ".$time;    
$db = new DB_Functions();     
$users = $db->getwarrantyreason();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["id"] = $row["id"];             
$b["reason"] = $row["reason"];

array_push($a,$b);         
}     
echo json_encode($a);     
} 
?>