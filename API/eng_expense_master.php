<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getExpense();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  

while ($row = mysqli_fetch_array($users)) 
{   


$b["eng_id"] = $row["eng_id"];
    $b["mobile_expense"] = $row["mobile_expense"];
    $b["food_expense"] = $row["food_expense"];
    $b["courier_expense"] = $row["courier_expense"];
    $b["other_expense"] = $row["other_expense"];
    $b["hotel_expense"] = $row["hotel_expense"];
     $b["total_expense"] =$row["mobile_expense"]+$row["food_expense"]+$row["courier_expense"]+$row["courier_expense"]+$row["hotel_expense"]+$row["other_expense"];
    $b["update_date"] = $row["update_date"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>