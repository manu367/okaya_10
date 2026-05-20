<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';    
$today=date("Y-m-d");   
$db = new DB_Functions();     
$users = $db->getAMC($_REQUEST['eid']);     
$a = array();     
$b = array(); 
$x = array(); 
$y= array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
$pro_name=$db->getAnyDetails($row['product_id'],"product_name","product_id","product_master");
$model_name=$db->getAnyDetails($row['model_id'],"model","model_id","model_master");
$status_name = $db->getAnyDetails($row['status'], "display_status", "status_id", "jobstatus_master");     
$b["name"] = $row["customer_name"];
$b["contact_no"] = $row["contract_no"]; 
$b["amcid"] = $row["amcid"];
$b["open_date"] = $row["open_date"];
$b["serial_no"] = $row["serial_no"];
$b["model"] = $model_name;
$b["model_id"] = $row["model_id"];
$b["product_id"] = $row["product_id"];
$b["product_name"] =  $pro_name;
$b["status"] = $row["status"];
$b["customer_id"]=$row["customer_id"];
$b["status_name"] = $status_name;
   
array_push($a,$b);         
}      


//array_push($a,$b);    
echo json_encode($a);     
} 
?>