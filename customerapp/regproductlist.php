<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);     
$users = $db->getRegProduct();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{  
	$b["custid"] = $row["customer_id"];
	$b["modelid"] = $row["model_id"];
	$model=$row["model_id"];
$m= mysqli_fetch_array(mysqli_query($conn,"SELECT model FROM model_master WHERE modelcode = '$model'")); 
	
	$b["purchasedate"] = $row["purchase_date"];
	$b["warrantyenddate"] = $row["warranty_end_date"];
	$b["expirydate"] = $row["manufactured_expiry_date"];
	$c = $row["product_id"];
	$d= mysqli_fetch_array(mysqli_query($conn,"SELECT productname FROM product_master WHERE productid = '$c' LIMIT 0,9"));
	$b["productname"] = $row["product_id"];
	$b["serial_no"]=$row["serial_no"];
	$b["model"]=$m["model"];
	array_push($a,$b);
}     
echo json_encode($a);     
} 
?>