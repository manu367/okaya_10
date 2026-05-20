<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getEng_Serial($_REQUEST['eng_id'],$_REQUEST['partcode']);     
$a = array();     
$b = array(); 
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{        
$b["partcode"] =  $row["partcode"];
$b["serial_no"] =  $row["imei1"];

 array_push($a,$b);        
}          
echo json_encode($a);     
} 
?>