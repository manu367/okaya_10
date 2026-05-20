<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a = array();     
$b = array();
$eid=$_REQUEST['eid'];
$pswd=$_REQUEST['password']; 
$app_version=$_REQUEST['app_version'];
//if($app_version == "1.3" || $app_version == "1.4"){
if($app_version == "1.4"){
$users = $db->getUserExistDetails($eid,$pswd);     
if($users==""){
	$b["err_flag"]="1";
	$b["err_msg"]="Wrong Credentials";
	array_push($a,$b);	
}else{
$row = mysqli_fetch_array($users);  
$b["user_name"] = $row["username"];
$b["contact_no"] = $row["mobile"];
$b["eid"] = $row["userid"];
$b["addrs"] = $row["address1"]; 
$b["sap_code"] =$row["sapcode"];
$c = $row["cityid"];
$d= mysqli_fetch_array(mysqli_query($conn,"SELECT city FROM city_master WHERE cityid = '$c'"));
$b["city"] = $d["city"];
if($row['status']=='A'){$status='1';}else{$status='0';}
$b["status"]=$status;
$b["err_msg"]="Success";
$b["err_flag"]="0";
	#########################

	array_push($a,$b);
}
}else{

$b["status"]="App Version Error";
$b["err_flag"]="1";
$b["err_msg"]="App Version Error";
array_push($a,$b);
}
echo json_encode($b); 
mysqli_close($conn);
?>