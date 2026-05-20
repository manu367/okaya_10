<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();   

$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
  
$ticket = $db->CustTicketDetails();     
$a = array();     
$b = array();
while ($row = mysqli_fetch_array($ticket)) 
   {  
	$b["custid"] = $row["customer_id"];
	$e= mysqli_fetch_array(mysqli_query($conn,"SELECT model FROM  model_master WHERE modelcode = '$row[modelid]' "));
	//$b["productname"] = $e["productname"];
	$b["serialNo"] = $row["serial_no"];
	$b["ticketno"] = $row["job_no"]; 
	$b["description"] = $row["description"];
	$b["status"] = $row["status"];
	$b["productName"] = $row["productid"];
	$b["modelName"] = $e["model"];
	$b["SCM"]=$row["SCM"];
	$b["engg_assign"]=$row["engg_assign"];
	$sql_user="select username from user_master where userid='$row[engg_assign]'";
	$rs_user=mysqli_query($conn,$sql_user) or die(mysqli_error($conn));
	if(mysqli_num_rows($rs_user)>0)
	{
	$row_usr=mysqli_fetch_assoc($rs_user);
	$b["engg_name"]=$row_usr["username"];
}else {
	$b["engg_name"]='';
	}
	array_push($a,$b);

}
echo json_encode($a);    
?>
