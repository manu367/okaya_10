<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getPart();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["partid"] = $row["partcode"];
$b["partname"] = $row["part_name"]; 
$b["productid"] = $row["product_id"];
$b["brandid"] = $row["brand_id"];
$b["modelid"] = $row["model_id"];
$b["Category"] =   $row["part_category"];
$b["partcode"] = $row["vendor_partcode"];         
$b["saleprice"] = $row["customer_price"];
$b["status"] = $row["status"];         

array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>