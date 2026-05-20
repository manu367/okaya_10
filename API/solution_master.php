<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
$timedate= $today." ".$time; 
$db = new DB_Functions();     
$users = $db->getSolutionMaster($_REQUEST['product_id']);     
$a = array();     
$b = array();  
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["_id"] = $row["id"];
$b["solution"] = $row["rep_desc"];
$b["product_id"] = $row["product_id"]; 
$b["product_cat"] = $row["prod_cat"];  
$b["part_replace"] = $row["part_replace"];
$b["solutioncode"] = $row["rep_code"];   
    
array_push($a,$b);      
}  
   
echo json_encode($a);     
} 
?>