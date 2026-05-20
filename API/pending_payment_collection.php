
<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getPendingPaymenytCollection();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  

while ($row = mysqli_fetch_array($users)) 
{   

$row_loc = $db->getAnyDetails($row["engg_id"],"location_code","userloginid","locationuser_master");
	$b["job_no"]=$row["job_no"];
	$b["engg_id"]=$row["engg_id"];
	$b["cr_date"] = $row["cr_date"];
    $b["amount"] = $row["amount"];
	$b["invno"] = $row["inv_no"];
	$b["cr_no"] = $row["cr_no"];
	$b["payment_mode"] = $row["payment_mode"];
    $b["location_code"] = $row_loc;
array_push($a,$b);         
}         
 
}
else {
$b["status"] = 0;
	array_push($a,$b);
}
echo json_encode($a);
?>