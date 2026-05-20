<?php 
/**  * Creates Reason Details as JSON  */    
include_once 'db_functions.php';  
	date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
$timedate= $today." ".$time;    
$db = new DB_Functions();     
$users = $db->getreplreason($_REQUEST['sync_date_time']);     
$a = array();     
$b = array(); 
$c = array(); 
$d= array();
$e= array();  
$f= array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$e["id"] = $row["id"];             
$e["reason"] = $row["desc"];

array_push($f,$e);         
}     
$b["Replacement_Reason_list"]= $f;  
$c["API NAME"]="GETREPLACEMENTLIST";
$c["sync_date_time"]=$timedate;  

$b["APIDETAILS"] =$c;  
echo json_encode($b);     
} 
?>