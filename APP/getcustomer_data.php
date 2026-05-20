<?php 
/**  * Creates Customer details data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getComplaintsMaster();     
$a = array();     
$b = array();    
if ($users != false){ 
while ($row = mysqli_fetch_array($users)) 
{   
         
						$city_param = $db->getcitymaster($row["city_id"],"city");
$cust_datail = mysqli_fetch_array($city_param);        
	$state_param = $db->getstatemaster($row["state_id"],"state");
$st_datail = mysqli_fetch_array($state_param);                  
$b["_id"] = $row["job_id"];             
$b["cust_id"] = $row["customer_id"];
$b["name"] = $row["customer_name"];
$b["contact_no"] = $row["contact_no"]; 
$b["alternate_contact"] = $row["alternate_no"];
$b["address1"] = $row["address"];
$b["address2"] = "";
$b["city"] = $cust_datail["city"];
$b["state"] = $st_datail["state"];
$b["pincode"] = $row["pincode"];            
$i++;
array_push($a,$b);         
}        
echo json_encode($a);     
} 
?>