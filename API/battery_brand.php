<?php 
/**  * Creates Reason Details as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);  
$users = $db->battery_brand();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["id"] = $row["id"];             
$b["name"] = $row["brand"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>