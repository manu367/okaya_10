<?php 

include_once 'db_functions_test1.php'; 

$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
 $json = $_REQUEST["usersJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
//Decode JSON into an Array 
$data = json_decode($json); 
#### Check APP JSON
$app_json="insert into api_json_data set doc_no='".$data[0]->job_no."',data='".$json."',activity='Complaint API',ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json=mysqli_query($conn,$app_json);
################
//Util arrays to create response JSON 
$a=array();
$b=array();
for($i=0; $i<count($data) ; $i++) {
//Store User into MySQL DB
//$res = $db->storeUser($data[$i]->job_no,$data[$i]->status,$data[$i]->updateDate,$data[$i]->closed_reason,$data[$i]->update_remark,$data[$i]->fault_code,$data[$i]->faultRepairList,$data[$i]->epList,$data[$i]->partPNA,$data[$i]->partInstallationDone,$data[$i]->partDemoDone,$data[$i]->repair_status,$data[$i]->pending_reason,$data[$i]->eid,$data[$i]->replacedBy,$data[$i]->replacemetModel,$data[$i]->travelKM,$data[$i]->repair_central,$data[$i]->repair_code,$data[$i]->requestReason,$data[$i]->replacedBySrNo,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->confirmedBy,$data[$i]->contactNumber,$data[$i]->customerRemark,$data[$i]->serviceRating,$data[$i]->customerFeedbackDate,$data[$i]->serial_no,$data[$i]->selected_model_id,$data[$i]->selected_product_id,$data[$i]->dop,$data[$i]->warranty_status,$data[$i]->modifDone,$data[$i]->ta_da,$data[$i]->newProductProvidedSpinner_txt,$data[$i]->revisit_status,$data[$i]->revisit_date,$data[$i]->revisit_remark,$data[$i]->engObsrvdReport,$data[$i]->mfd_ex,$data[$i]->app_version,$data[$i]->amc_expiry_date,$data[$i]->main_serial,$data[$i]->location_on_pcb,$data[$i]->pcb_changed_flag,$data[$i]->battery_make,$data[$i]->old_pcb_number,$data[$i]->new_modelid,$data[$i]->battery_rate,$data[$i]->mfd,$data[$i]->r_sticker,$data[$i]->payment_receive_flag,$data[$i]->new_pcb_number);
//  $res = $db->storeUser($data[$i]->job_no,$data[$i]->status,$data[$i]->updateDate,$data[$i]->closed_reason,$data[$i]->update_remark,$data[$i]->fault_code,$data[$i]->faultRepairList,$data[$i]->epList,$data[$i]->partPNA,$data[$i]->partInstallationDone,$data[$i]->partDemoDone,$data[$i]->pending_reason,$data[$i]->eid,$data[$i]->replacedBy,$data[$i]->replacemetModel,$data[$i]->travelKM,$data[$i]->repair_central,$data[$i]->repair_code,$data[$i]->requestReason,$data[$i]->replacedBySrNo,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->confirmedBy,$data[$i]->contactNumber,$data[$i]->customerRemark,$data[$i]->serviceRating,$data[$i]->customerFeedbackDate,$data[$i]->serial_no,$data[$i]->selected_model_id,$data[$i]->selected_product_id,$data[$i]->dop,$data[$i]->warranty_status,$data[$i]->modifDone,$data[$i]->ta_da,$data[$i]->newProductProvidedSpinner_txt,$data[$i]->revisit_status,$data[$i]->revisit_date,$data[$i]->revisit_remark,$data[$i]->engObsrvdReport,$data[$i]->mfd_ex,$data[$i]->app_version,$data[$i]->amc_expiry_date,$data[$i]->main_serial,$data[$i]->location_on_pcb,$data[$i]->pcb_changed_flag,$data[$i]->battery_make,$data[$i]->old_pcb_number,$data[$i]->new_modelid,$data[$i]->battery_rate,$data[$i]->mfd,$data[$i]->r_sticker,$data[$i]->payment_receive_flag,$data[$i]->new_pcb_number);
	if($data[$i]->app_version=="2.1"){
  if($data[$i]->repair_status==48){  
  $res = $db->savejobdatainsllation($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->part_consume,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign);
  }  
    if($data[$i]->repair_status==50 || $data[$i]->repair_status==12 ){  
  $res = $db->savejobdataRequest($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->request_reason,$data[$i]->remark,$data[$i]->part_consume,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign);
  }  
    if($data[$i]->repair_status==3){
  $res = $db->savejobpna($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->pnaList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign);
  }  
      if($data[$i]->repair_status==5){
  $res = $db->savejobep($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->epList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->serviceCharge,$data[$i]->serviceTaxAmt,$data[$i]->totalService,$data[$i]->totalPartAmt);
  } 
        if($data[$i]->repair_status==7 || $data[$i]->repair_status==10 ){
  $res = $db->savejobclosewp($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->faultRepairList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->faulty_flag,$data[$i]->paymentList,$data[$i]->service_charge,$data[$i]->totalPartAmt,$data[$i]->pending_reason);
  }}
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