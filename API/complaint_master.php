<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';    
$today=date("Y-m-d");   
$db = new DB_Functions();
if(!empty($_REQUEST['eid'])){
$users = $db->getComplaintsMaster($_REQUEST['eid']); 
}
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
$brand_name="DEMO";
$status_name = $db->getAnyDetails($row['status'], "display_status", "status_id", "jobstatus_master"); 
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
$b["close_date"] = $row["close_date"];
$b["status_name"] = $status_name;  
    $b["address1"] = $row["address"];
array_push($a,$b);         
}      


//array_push($a,$b);    
echo json_encode($a);     
} 
?>