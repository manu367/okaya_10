<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getTrainingMaster();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  

while ($row = mysqli_fetch_array($users)) 
{   
$b["id"] = $row["id"];
$b["name"] = $row["name"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>