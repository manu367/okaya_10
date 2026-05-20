<?php 
/**  * Creates Unsynced rows data as JSON  */    
//include_once 'db_functions.php'; 
include_once 'db_functions.php';  
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a = array();     
$b = array();
$eid=$_REQUEST['eid'];
 
$users = $db->getUser_Details($eid);     
$users_location = $db->getLocation_Details($eid);
if(mysqli_num_rows($users)>0){
$row = mysqli_fetch_array($users);  
$b["eng_id"] = $_REQUEST['eid'];
$b["status"] = $row["statusid"]; 
$b["pwd"] =$row["pwd"];
}
else if(mysqli_num_rows($users_location)>0){
$row_location = mysqli_fetch_array($users_location);  
$b["eng_id"] = $_REQUEST['eid'];
$b["status"] = $row_location["statusid"]; 
$b["pwd"] =$row_location["pwd"];	
}
	array_push($a,$b);

echo json_encode($a); 
mysqli_close($conn);
?>