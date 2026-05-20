<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getTrainingReqList();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  

while ($row = mysqli_fetch_array($users)) 
{   


$b["eng_id"] = $row["eng_id"];
     $b["req_no"] = $row["req_no"];
    $b["sub"] = $row["training_sub"];
    $b["details"] = $row["training_details"];
    $b["request_date"] = $row["request_date"];
    $b["status"] = $row["status"];
    $b["schedule_date"] = $row["start_schedule_date"];
    
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>