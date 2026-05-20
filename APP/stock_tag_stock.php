<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getstockengtagno($_REQUEST['eid']);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
     

$b["tag_no"] = $row["imei1"];


         
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>