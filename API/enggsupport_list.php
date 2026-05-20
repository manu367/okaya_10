
<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getSupportDetails();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  

while ($row = mysqli_fetch_array($users)) 
{    
	$b["eng_id"]=$row["eng_id"];
	$b["subject"] = $row["subject"];
    $b["detail"] = $row["detail"]; 
	$b["req_date"] = $row["punch_date"];
	$b["remark"] = $row["remark"];
    
array_push($a,$b);         
}         
 
}
else {
$b["status"] = 0;
	array_push($a,$b);
}
echo json_encode($a);
?>