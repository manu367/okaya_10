<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getNoticeDetails();     
$a = array();     
$b = array();
 while($row = mysqli_fetch_array($users)){

	$subject = preg_replace('/[^A-Za-z0-9]/', " ", $row["subject"]);
	//$msg = preg_replace('/[^A-Za-z0-9]/', " ", $row["msg"]);
	 $msg = $row["msg"];
	$b["id"] = $row["sno"];
	$b["subject"] = $subject;
	$b["msg"] =  $msg;
	$b["date"] = $row["date"];
	$b["end_date"] = $row["end_date"]; 
	$b["type"] = $row["type"];
	$b["status"] = $row["status"]; 
 array_push($a,$b);
 }

echo json_encode($a);    
?>