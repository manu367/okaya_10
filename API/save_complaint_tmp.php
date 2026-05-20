<?php 

include_once 'db_functions_tmp.php'; 

$db = new DB_Functions(); 
 $json = $_REQUEST["usersJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
	

//Decode JSON into an Array 
$data = json_decode($json); 

//Util arrays to create response JSON 
$a=array();
$b=array(); 



for($i=0; $i<count($data) ; $i++) {
//Store User into MySQL DB 
  $res = $db->storeUser($data[$i]->job_no,$data[$i]->status,$data[$i]->updateDate,$data[$i]->closed_reason,$data[$i]->update_remark,$data[$i]->fault_code,$data[$i]->repairList,$data[$i]->partEP,$data[$i]->partPNA,$data[$i]->partInstallationDone,$data[$i]->partDemoDone,$data[$i]->repair_status,$data[$i]->pending_reason,$data[$i]->eid,$data[$i]->replacedBy,$data[$i]->replacemetModel,$data[$i]->travelKM,$data[$i]->repair_central,$data[$i]->repair_code,$data[$i]->requestReason,$data[$i]->replacedBySrNo,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->address,$data[$i]->confirmedBy,$data[$i]->contactNumber,$data[$i]->customerRemark,$data[$i]->serviceRating,$data[$i]->customerFeedbackDate,$data[$i]->serial_no,$data[$i]->selected_model_id,$data[$i]->selected_product_id,$data[$i]->dop,$data[$i]->warranty_status);

//Based on inserttion, create JSON response     
if($res){        
$b["id"] = $data[$i]->job_no;         
$b["status"] =$res;         
array_push($a,$b); 
}else{    
$b["id"] = $data[$i]->job_no;         
$b["status"] = $res; 
 array_push($a,$b);  

 } 
 } //Post JSON response back to Android Application 
 echo json_encode($a); 
 ?>