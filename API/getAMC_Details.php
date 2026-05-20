<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';    
$today=date("Y-m-d");   
$db = new DB_Functions();     
$users = $db->getAMC_Details($_REQUEST['amc_id']);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
$pro_name=$db->getAnyDetails($row['product_id'],"product_name","product_id","product_master");
$brand_name=$db->getAnyDetails($row['brand_id'],"brand","brand_id","brand_master");
$model_name=$db->getAnyDetails($row['model_id'],"model","model_id","model_master");
$city_param = $db->getcitymaster($row["city_id"],"city");
$cust_datail = mysqli_fetch_array($city_param);        
$state_param = $db->getstatemaster($row["state_id"],"state");
$st_datail = mysqli_fetch_array($state_param);         

$status_name = $db->getAnyDetails($row['status'], "display_status", "status_id", "jobstatus_master"); 

$b["amcid"] = $row["amcid"];
$b["customer_id"]=$row["customer_id"];    
$b["name"] = $row["customer_name"];
$b["contact_no"] = $row["contract_no"]; 
$b["city"] = $cust_datail["city"];
$b["state"] = $st_datail["state"];
$b["open_date"] = $row["open_date"];
$b["serial_no"] = $row["serial_no"];
$b["model_id"] = $row["model_id"];
$b["model"] = $model_name;
$b["product_id"] = $row["product_id"];
$b["product_name"] =  $pro_name;
$b["status"] = $row["status"];
$b["status_name"] = $status_name;
$b["address"] = $row["addrs"];
$b["amc_amount"] = $row["amc_amount"];
$b["amc_type"] = $row["amc_type"];
$b["location_code"] = $row["location_code"];
    
    $b["inv_no"] = $row["inv_no"];
    $b["cr_no"] = $row["cr_no"];
    $b["cr_book_no"] = $row["cr_book_no"];
    $b["cr_date"] = $row["cr_date"];
    $b["rec_pay_remark"] = $row["rec_pay_remark"];
    $b["app_remark"] = $row["app_remark"];
    $b["mode_of_payment"] = $row["mode_of_payment"];

array_push($a,$b);         
}      


//array_push($a,$b);    
echo json_encode($a);     
} 
?>