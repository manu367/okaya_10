<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';    
$today=date("Y-m-d");   
$db = new DB_Functions();     
$users = $db->getInstallation($_REQUEST['job_no']);     
$a = array();     
$b = array(); 
$x = array(); 
$y= array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
     $c = array();
$d= array();  
     $e = array();
$f= array();
     $g = array();
$h= array();
$pro_name=$db->getAnyDetails($row['product_id'],"product_name","product_id","product_master");
$brand_name=$db->getAnyDetails($row['brand_id'],"brand","brand_id","brand_master");
$status_name = $db->getAnyDetails($row['status'], "display_status", "status_id", "jobstatus_master");
$city_param = $db->getcitymaster($row["city_id"],"city");
$cust_datail = mysqli_fetch_array($city_param);        
	$state_param = $db->getstatemaster($row["state_id"],"state");
$st_datail = mysqli_fetch_array($state_param);         
     
$b["name"] = $row["customer_name"];
$b["contact_no"] = $row["contact_no"]; 
$b["job_no"] = $row["job_no"];
$b["open_date"] = $row["open_date"];
$b["serial_no"] = $row["imei"];
$b["warranty_status"] = $row["warranty_status"];
$b["model"] = $row["model"];
$b["model_id"] = $row["model_id"];
$b["product_id"] = $row["product_id"];
$b["product_name"] =  $pro_name;
$b["brand_id"] = $row["brand_id"];
$b["brand_name"] = $brand_name;
$b["repair_status"] = $row["status"];
$b["status"] = $row["pen_status"];
$b["customer_id"]=$row["customer_id"];
$b["c_type"] = $row["call_for"];
$b["dop"] = $row["dop"];
$b["scm"] = $row["h_code"]; 
$b["close_date"] = $row["close_date"];
$b["installation_date"] = $row["installation_date"];
$b["address1"] = $row["address"];

$b["city"] = $cust_datail["city"];
$b["state"] = $st_datail["state"];
$b["pincode"] = $row["pincode"];  
$b["status_name"] = $status_name; 
array_push($a,$b);         
}      


//array_push($a,$b);    
echo json_encode($a);     
} 
?>