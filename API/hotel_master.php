<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getHotelMaster();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                     
$b["_id"] = $row["id"];
$b["h_code"] = $row["id"];
$b["h_name"] = $row["hotel_name"];
$b["h_address"]=$row["hotel_address"];
$city_details=mysqli_fetch_array(mysqli_query($conn,"select city from city_master where newcityid='".$row['hotel_city']."'"));
$b["h_city"]=$city_details["city"];
$state_details=mysqli_fetch_array(mysqli_query($conn,"select state from state_master where stateid='".$row['hotel_state']."'"));
$b["h_state"]=$state_details["state"];

         
array_push($a,$b);         
}         
echo json_encode($a); 
	mysqli_close($conn);
} 
?>