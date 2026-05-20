<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();  
$userid=$_REQUEST[eid];
$from=$_REQUEST[from];
$to=$_REQUEST[to];

 $users1 = $db->getleaveview($userid,$from,$to);  
$a = array();     
$b = array();   

if ($users1!= false){
	         
while ($row = mysqli_fetch_array($users1)) 
{ 
  
$b["leaveid"] = $row["leaveid"]; 
$b["leave_type"] = $row["leave_type"];
$b["sub_leave_type"]=$row["sub_leave_type"];
$b["leave_date"]=$row["leave_date"];
$b["app_date"] = $row["app_date"]; 
$b["approve_by"] = $row["approve_by"];
$b["requested_by"] = $row["requested_by"]; 
$b["leave_reason"] = $row["leave_reason"];
$b["leave_todate"]= $row["leave_todate"];
$b["status"] = $row["status"]; 

//$b["statusapp"]="hello";  
array_push($a,$b);         
}    
	
echo json_encode($a);     
} 

?>