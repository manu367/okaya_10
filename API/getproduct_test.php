<?php 
/**  * Creates Customer details data as JSON  */    
include_once 'db_functions.php';   
	date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
$timedate= $today." ".$time;   
$db = new DB_Functions();     
$users = $db->getProduct($_REQUEST['sync_date_time']);     
$a = array();     
$b = array(); 
$c = array(); 
$d= array();
$e= array();  
$f= array();
if ($users != false){ 
while ($row = mysqli_fetch_array($users)) 
{   
		                
$e["productid"] = $row["product_id"];             
$e["productname"] = $row["product_name"];
 array_push($f,$e);



$i++;
 
       
} 
$b["Product_list"]= $f; 
 //array_push($a,$b); 

$c["API NAME"]="GETPRODUCT";
$c["sync_date_time"]=$timedate;  

$b["APIDETAILS"] =$c; 


//array_push($a,$b);  
echo json_encode($b);     
} 
?>