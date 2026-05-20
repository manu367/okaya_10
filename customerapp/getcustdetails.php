<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();   

$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
  
$customer = $db->getCustomerDetails();     
$a = array();     
$b = array();
while ($row = mysqli_fetch_array($customer)) 
   {  
   $b["id"] = $row["id"];
	$b["customer_id"] = $row["customer_id"];
	$b["custname"] = $row["customer_name"];
	$b["address"] = $row["address1"]; 
	$b["cityid"] = $row["cityid"];
	$e= mysqli_fetch_array(mysqli_query($conn,"SELECT city FROM  city_master WHERE cityid = '$row[cityid]' "));
	$b["city"] = $e["city"];
	$b["email"] = $row["email"];
	$b["phone"] = $row["phone"];
	$b["mobile"] = $row["mobile"];
	$b["stateid"] = $row["stateid"];
	$f= mysqli_fetch_array(mysqli_query($conn,"SELECT state FROM  state_master WHERE stateid = '$row[stateid]' "));
	$b["state"] = $f["state"];
	array_push($a,$b);
}
echo json_encode($a);    
?>