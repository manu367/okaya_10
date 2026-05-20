<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$a = array();     
$b = array();
 
 
$users = $db->getPhyDetails();     
while($row = mysqli_fetch_array($users)){  
$b["id"] = $row["id"];
$b["description"] = $row["description"];
$b["result"] = $row["result"];
$b["final_decision"] = $row["final_decision"];	
	#########################

	array_push($a,$b);
}
echo json_encode($a); 
mysqli_close($conn);
?>