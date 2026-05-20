
<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$users = $db->getAgreementDetails();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  

while ($row = mysqli_fetch_array($users)) 
{   

 if($row["agrement_flag"] == 'Y'){
    $agreeflag = "Y";
 }else {
    $agreeflag = "N";
 }
 
	$b["usercode"]=$row["eng_id"];
	$b["eng_name"] = $row["eng_name"];
    $b["agrement_flag"] = $agreeflag; 
	$b["agree_date"] = $row["insert_date"];
	$b["msg_id"] = $row["msg_id"];
    
array_push($a,$b);         
}         
 
}
else {
$b["status"] = 0;
	array_push($a,$b);
}
echo json_encode($a);
?>