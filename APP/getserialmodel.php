<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getserialmodel($_REQUEST['serial_no']);     
$a = array();     
$b = array();    
if ($users != false){       
  while($data=mysqli_fetch_array($users))
  {
$model= $db->getAnyDetails($data["model_id"],"model","model_id","model_master");
$b["serial_no"] = $data[1];
$b["model_id"]=$data[0];
$b["model_name"] = $model; 
$b["status"]=1;

array_push($a,$b);         
     } 
echo json_encode($a);     
} 
?>