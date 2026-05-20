<?php 
include_once 'db_functions.php'; 
/////// make db function object
$db = new DB_Functions();
////// make clone of db connection
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db); 
//////requested JSON data
$json = $_REQUEST["usersJSON"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 
$data = json_decode($json); 

#### Check APP JSON
$app_json = "INSERT INTO api_json_data SET doc_no='".$data[0]->job_no."',activity='Complaint API',ip='".$_SERVER['REMOTE_ADDR']."',data='".$json."'";

$sql_json = mysqli_query($conn,$app_json);

################
//Util arrays to create response JSON 
$a=array();
$b=array();

for($i=0; $i<count($data) ; $i++) {
	////// check APP version
	//print_r($data);exit;
	if($data[$i]->app_version == "1.0"){ //$data[$i]->app_version == "2.1" || $data[$i]->app_version == "2.2"
		///////// check repair status is Installation Done
		
  		if($data[$i]->repair_status==48){
  			$res = $db->savejobdatainsllation($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->part_consume,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->warranty_void,$data[$i]->warranty_void_reason,$data[$i]->electric_fail_hrs,$data[$i]->app_version,$data[$i]->verify_serial);
  		}
		///////// check repair status is Request For Approval or Cancel
    	if($data[$i]->repair_status==50 || $data[$i]->repair_status==12 ){  
						
  			$res = $db->savejobdataRequest($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->request_reason,$data[$i]->remark,$data[$i]->part_consume,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->warranty_void,$data[$i]->warranty_void_reason,$data[$i]->electric_fail_hrs,$data[$i]->app_version,$data[$i]->warr_img_documnet,$data[$i]->phy_condition,$data[$i]->fileName_serial,$data[$i]->path_img_serial,$data[$i]->voc_code,$data[$i]->solution_code,$data[$i]->warranty_days,$data[$i]->balance_warranty,$data[$i]->product_id,$data[$i]->mfd,$data[$i]->mfd_ex,$data[$i]->eid,$data[$i]->verify_serial);
  		}  
		///////// check repair status is PNA (Spare Part Unavailable)
    	if($data[$i]->repair_status==3){
  			$res = $db->savejobpna($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->pnaList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->warranty_void,$data[$i]->warranty_void_reason,$data[$i]->electric_fail_hrs,$data[$i]->app_version,$data[$i]->verify_serial);
  		}  
		///////// check repair status is EP (Estimation Pending Approval)
      	if($data[$i]->repair_status==5){
  			$res = $db->savejobep($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->epList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->serviceCharge,$data[$i]->serviceTaxAmt,$data[$i]->totalService,$data[$i]->totalPartAmt,$data[$i]->warranty_void,$data[$i]->warranty_void_reason,$data[$i]->electric_fail_hrs,$data[$i]->app_version,$data[$i]->verify_serial);
  		} 
		///////// check repair status is WIP (Work in progress / Pending) or Handover (Closed)
        if($data[$i]->repair_status==7 || $data[$i]->repair_status==10 ){
  			//print_r($data[$i]->repair_status);exit;
			$res = $db->savejobclosewp_autoinv($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->faultRepairList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->paymentList,$data[$i]->service_charge,$data[$i]->totalPartAmt,$data[$i]->pending_reason,$data[$i]->warranty_void,$data[$i]->warranty_void_reason,$data[$i]->electric_fail_hrs,$data[$i]->s_c_m,$data[$i]->call_processing_charges,$data[$i]->processing_partcode,$data[$i]->amc_collected,$data[$i]->app_version,$data[$i]->battery_option,$data[$i]->other_battery_option,$data[$i]->eid,$data[$i]->warr_img_documnet,$data[$i]->phy_condition,$data[$i]->fileName_serial,$data[$i]->path_img_serial,$data[$i]->warranty_days,$data[$i]->balance_warranty,$data[$i]->product_id,$data[$i]->mfd,$data[$i]->mfd_ex,$data[$i]->verify_serial);
  		}
		///////// check repair status is RWR (NVC Calling)
        if($data[$i]->repair_status==11){
  			
			$res = $db->savejobnvc($data[$i]->job_no,$data[$i]->serial_no,$data[$i]->warranty_status,$data[$i]->model,$data[$i]->model_id,$data[$i]->repair_status,$data[$i]->dop,$data[$i]->close_reason,$data[$i]->remark,$data[$i]->faultRepairList,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign,$data[$i]->paymentList,$data[$i]->service_charge,$data[$i]->totalPartAmt,$data[$i]->pending_reason,$data[$i]->warranty_void,$data[$i]->warranty_void_reason,$data[$i]->electric_fail_hrs,$data[$i]->s_c_m,$data[$i]->call_processing_charges,$data[$i]->processing_partcode,$data[$i]->amc_collected,$data[$i]->app_version,$data[$i]->battery_option,$data[$i]->other_battery_option,$data[$i]->eid,$data[$i]->warr_img_documnet,$data[$i]->phy_condition,$data[$i]->fileName_serial,$data[$i]->path_img_serial,$data[$i]->warranty_days,$data[$i]->balance_warranty,$data[$i]->product_id,$data[$i]->mfd,$data[$i]->mfd_ex,$data[$i]->verify_serial,$data[$i]->call_reason);
  		}
	}
	//Based on inserttion, create JSON response     
	if($res){        
		$b["id"] = $data[$i]->job_no;         
		$b["status"] =$res;
		//$b["status"] = $data[$i]->repair_status;
		array_push($a,$b); 
	}else{    
		$b["id"] = $data[$i]->job_no;         
		$b["status"] = $res;
		//$b["status"] = $data[$i]->repair_status;
		array_push($a,$b);
 	} 
}//Post JSON response back to Android Application 
echo json_encode($a); 

?>