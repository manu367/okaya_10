<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();  

$model_id = $_REQUEST['modelid'];
$call_type = $_REQUEST['calltype']; 

$a = array();     
$b = array();   

$model_ser_charge = "";
if($call_type=="App"){
	$p_name=$db->getAnyDetails($model_id,"app_ser_charge","model_id","model_master");
	$model_details=explode("~",$p_name);
	$model_ser_charge = $model_details[0];
}else{
	$p_name=$db->getAnyDetails($model_id,"ser_charge","model_id","model_master");
	$model_details=explode("~",$p_name);
	$model_ser_charge = $model_details[0];
}
 
if($model_ser_charge!=""){
	$b["servicecharge"]=$model_ser_charge;
	$b["modelid"]=$model_id;
	$b["calltype"]=$call_type;

	array_push($a,$b);   
}
echo json_encode($a);     	
    
?>