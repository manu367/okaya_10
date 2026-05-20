<?php    
include_once 'db_functions.php';  
$db = new DB_Functions();
//Util arrays to create response JSON 
$a = array();
$b = array();
$userid = $_REQUEST["userid"];
$imei_fbs_id = $_REQUEST["imeifbsId"];
///// get last log device details
$resp = $db-> getlastlog($userid);
$row = mysqli_fetch_assoc($resp);
if($row["ip"]==$imei_fbs_id){
	$b["status"] = "1";	
}else{
	$b["status"] = "0";	
}
array_push($a,$b);
echo json_encode($a);
?>