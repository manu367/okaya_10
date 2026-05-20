<?php 
/**  * Creates Customer details data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getProduct($_REQUEST['u_type'],$_REQUEST['activity'],$_REQUEST['userid']);     
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
$e["btr_require"] = $row["btr_require"];
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