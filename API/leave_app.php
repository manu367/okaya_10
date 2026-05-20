<?php 
include_once './db_functions.php'; 
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$json = $_POST["usersJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
//Decode JSON into an Array 
$data = json_decode($json); 
//Util arrays to create response JSON 
$a=array(); 
$b=array(); 

$rs1= mysqli_query($conn,"select max(id) from leave_app")or die(mysqli_error($conn));
$row=mysqli_fetch_array($rs1);
$code_id=$row[0]+1;
$pad=str_pad($code_id,3,"0",STR_PAD_LEFT);
$leaveid="LEV".$pad;
$ei = $_REQUEST[eid];
for($i=0; $i<=count($data) ; $i++) { 
//Store User into MySQL DB 
$res = $db->getleave($data[$i]->leave_type,$data[$i]->sub_leave_type,$data[$i]->leave_date,$ei,$data[$i]->leave_reason,$leaveid,$data[$i]->leave_toDate);
//Based on inserttion, create JSON response 
}

if($res){                
$b["status"] = 'Leave Applied';         
array_push($a,$b);     
}else{         
         
$b["status"] = 'Please do again';        
 array_push($a,$b);     
 } 
  //Post JSON response back to Android Application 
 echo json_encode($a); 
 ?>