<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';  
$today=date("Y-m-d");     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
  if(!empty($_REQUEST['job_no'])){  
$users = $db->getJobMaster1(); 
  }
  
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}   
  
$a = array();     
$b = array(); 
$c = array(); 
$d = array();   
if ($users != false){  
    
     $service_charg="";
	$close_re=0;
	$repeat=0;
//$job_row=mysqli_fetch_array(mysqli_query($conn,"select job_no,open_date,imei from jobsheet_data where ( job_no='".$_REQUEST['job_no']."' or (imei='".$_REQUEST['job_no']."' and imei != '')) order by job_id DESC "));
$job_row=mysqli_fetch_array(mysqli_query($conn,"select job_no,open_date,imei from jobsheet_data where job_no='".$_REQUEST['job_no']."' "));
 $serial_no=$job_row['imei'];
    $rowk=mysqli_query($conn,"select close_date, job_no  from jobsheet_data where imei = '".$serial_no."' and status in ('10','6') and imei not like 'Z%' and close_date!='0000-00-00' and close_date > ('".$job_row['open_date']."' - INTERVAL 30 day)  and warranty_status='OUT'  order by job_id DESC"); 
    $job_close = mysqli_fetch_assoc($rowk);
    if(mysqli_num_rows($rowk)>0){
        $repeat=1;
	 	//$close_re = daysDifference($job_row['open_date'],$job_close['close_date']);
       
		if($job_row['open_date'] > $job_close['close_date']){
	 		$close_re =$db-> daysDifference($job_row['open_date'],$job_close['close_date']);
		}else{
			$close_re =$db-> daysDifference($job_close['close_date'],$job_row['open_date']);
		}
    }
while ($row = mysqli_fetch_array($users)) 
{   
$pr_param = $db->getVocmaster($row["cust_problem"],"voc_desc");

$parametername = mysqli_fetch_array($pr_param);
$status_name = $db->getAnyDetails($row['status'], "display_status", "status_id", "jobstatus_master"); 
//$customer_details = mysqli_fetch_array(mysqli_query($conn,"SELECT id,customer_name,mobile,phone,address1,address2,cityid,stateid,pincode FROM customer_master WHERE customer_id = '".$row['customer_id']."'"));

/*$sql_amc="select amc_end_date,amcid from amc where serial_no='".$row['imei']."' order by amcid desc";
$rs_amc=mysqli_query($sql_amc) or die(mysql_error());
$amc=mysql_fetch_assoc($rs_amc);*/

//  checking for type of customer, dealer /distributor  or customer/others
/*$model_details="";
if($row["customer_type"]=='Dealer' || $row["customer_type"]=='Distributo' || $row["customer_type"]=='Distributor'){
$model_details = mysqli_fetch_array(mysqli_query($conn,"SELECT app_delar_ser_charge FROM model_master WHERE model_id = '".$row["model_id"]."'"));
}else{
$model_details = mysqli_fetch_array(mysqli_query($conn,"SELECT app_ser_charge FROM model_master WHERE model_id = '".$row["model_id"]."'"));
}*/
##########
	/////////
$model_details = mysqli_fetch_array(mysqli_query($conn,"SELECT ser_charge FROM model_master WHERE model_id = '".$row["model_id"]."'"));

$b["cust_id"] = $row["customer_id"];
$b["job_no"] = $row["job_no"];
$b["open_date"] = $row["open_date"];
$c = $row["problem_detect"];
$b["serial_no"] = $row["imei"];
$b["amc_expiry_date"]=$amc["amc_end_date"];
$b["amcid"]=$amc["amcid"];
$b["problem_detail"] = $parametername["voc_desc"];
$b["warranty_status"] = $row["warranty_status"];
$b["model"] = $row["model"];
$b["product_id"] = $row["product_id"];
$b["product_name"] = $row["product_cat"];
$b["repair_status"] = $row["status"];
$b["status_name"] = $status_name ;
if($row['dop']!='0000-00-00'){
$b["dop"] = $row["dop"];
}else {
$b["dop"] ="";  
}
$b["scm"] = $row["h_code"];
$b["unhapppy_code"] = $row["unhapppy_code"]; 
$b["sold_unsold"] = $row["sold_unsold"];	
/*$b["pcb_changed_flag"] = $row["pcb_changed_flag"]; 
$b["old_pcb_number"] = $row["old_pcb_number"]; 
$b["new_pcb_number"] = $row["new_pcb_number"]; 
$b["payment_receive_flag"] = $row["payment_receive_flag"]; 
$b["revisit_status"] = $row["revisit_status"]; 
$b["revisit_date"] = $row["revisit_date"];
$b["revisit_remark"] = $row["revisit_remark"];
$b["serial_no_image"] = $row["serial_no_image"];
$b["latitude"] = $row["latitude"];
$b["longitude"] = $row["longitude"];
$b["server_time"] = $row["server_time"];
$b["location_address"] = $row["location_address"];*/

$get_eng_rmk = mysqli_fetch_array(mysqli_query($conn,"select remark from call_history where job_no='".$row['job_no']."' and status = '50' and activity = 'Pending For Approval' order by id DESC "));

$eng_rmk = "";
if($get_eng_rmk['remark']==""){
	$eng_rmk = $row['remark'];
}else{
	$eng_rmk = $get_eng_rmk['remark'];
}

$b['complaint_remark']=$eng_rmk; //$row['remark']

$b["modelid"]=$row["model_id"];
 ////////////customer details
//$b["id"] = $customer_details["id"];
$b["name"] = $row["customer_name"];
$b["contact_no"] = $row["contact_no"]; 
$b["alternate_contact"] = $row["alternate_no"];
$b["residence_number"] = $row["alternate_no"];
$b["address1"] = $row["address"];
$b["address2"] = $row["address"];
$b["city"] = $row["city_id"];
$b["state"] = $row["state_id"];
$b["pincode"] = $row["pincode"];
$b["close_date"] = $row["close_date"];
$b['punch_time']=$row['punch_time'];
$b['warr_void']=$row['ws_void'];
$b['void_reason']=$row['ws_void_reason'];
$b['call_type']=$row['call_type'];
$b['repl_appr_no']=$row['repl_appr_no'];  #### Token No.
$b["warranty_days"] = $row['warranty_days'];
$b["balance_warranty"] = $row['balance_warranty_days'];  
$b["replappr_remark"] = $row['doa_remark'];   #### Replacement Approval Remark
$b['replacement_serial']=$row['replace_serial'];  #### Replaced Serial No. by Approval
$b['phy_condition']=$row['phy_cond'];   #### Physical Condition
$b['warrdoc_upload']=$row['doa_bag'];   #### Document Upload
$b['mfd']=$row['mfd'];
$b['mfd_ex']=$row['manufactured_expiry_date'];	 
$b['verify_serial']=$row['verify_serial'];
###### BTR Details 
$firstbtr_flag='';
$finalbtr_flag='';

$sql_prd=mysqli_fetch_array(mysqli_query($conn,"select btr_require from product_master where product_id='".$row['product_id']."'"));

if($sql_prd['btr_require']=='Y')
{ 

		$initial_btr=mysqli_num_rows(mysqli_query($conn,"select id from initial_btr_data where job_no='".$row['job_no']."'"));
		
		$final_btr=mysqli_num_rows(mysqli_query($conn,"select id from final_btr_data where job_no='".$row['job_no']."'"));
		
		if($initial_btr==0){ $firstbtr_flag='P';  } else { $firstbtr_flag='D';  }
		
		if($final_btr==0){ $finalbtr_flag='P';  } else { $finalbtr_flag='D';  }

}
else
 { 
 $firstbtr_flag='P';
 $finalbtr_flag='P';
 }
######################### BTR Require Details END

$b['initial_btr']=$firstbtr_flag;
$b['final_btr']=$finalbtr_flag;


	if(mysqli_num_rows($rowk)>0){
		///echo $close_re;
	
	$row_JobC=mysqli_query($conn,"select GROUP_CONCAT(CONCAT('''', job_no, '''' )) as jobNo  from jobsheet_data where imei = '".$serial_no."' and status in ('10','6') and imei not like 'Z%' and close_date!='0000-00-00' and close_date > ('".$job_row['open_date']."' - INTERVAL 30 day)  and warranty_status in ('OUT','IN')  order by job_id DESC"); 
    $resjob_close = mysqli_fetch_assoc($row_JobC);
		
    if($close_re <= 30 && $close_re >= 0){
		 ///$service_charg="0.00";
		//echo "YES";
            $repair_detail=mysqli_fetch_array(mysqli_query($conn,"select partcode from repair_detail where job_no IN (".$resjob_close['jobNo'].") and partcode='39'"));
			$job_type='A';
			$rpt_flg = $job_close['job_no'];
           if($repair_detail['partcode']=="39") {
            $service_charg="0.00";
           }
            else if($row["warranty_status"]=='IN' && $close_re <= 30){
		      $service_charg="0.00";		   
		   } 
            else {
				if($row["warranty_status"]=='OUT' && $close_re <= 30){
                 $service_charg=$model_details[0];
				}
			     else{
				 $service_charg="0.00";
				 }
            }
            
		}
        elseif($close_re <= 60 && $close_re >= 31){
			$job_type='B';
			$rpt_flg = $job_close['job_no'];
             $service_charg=$model_details[0];
		}
        elseif($close_re <= 90 && $close_re >= 61){
			$job_type='C';
			$rpt_flg = $job_close['job_no'];
             $service_charg=$model_details[0];
		}
        else{
			  $job_type='Normal';
			$rpt_flg = "";
             $service_charg=$model_details[0];
		}}else{
			  $job_type='Normal';
			$rpt_flg = "";
             $service_charg=$model_details[0];
		}
	$tt='';
if($row["warranty_status"]=='OUT'){
	if($model_details[0]!=''){
$b["service_charge"] = $service_charg;
		$tt="1";
	}
	else {
		if($tt==""){
		if($close_re > 30){
	 $b["service_charge"] ="350.00";
		}
		else {
		 $b["service_charge"] ="0.00";
		}
		}
	}
	
}

if($row["warranty_status"]=='IN'){
$b["service_charge"] = $service_charg;
}

##### COmplaint Repair Details
$sql_data=mysqli_query($conn,"select fault_code,repair_code,partcode,part_qty,part_cost,old_serial,remark,pcb_repairable,replace_serial from repair_detail where job_no='".$row["job_no"]."'");
$myObj = array();
   while($row_data=mysqli_fetch_array($sql_data)){
	   $fault_name=$db->getAnyDetails($row_data['fault_code'],"defect_desc","defect_code","defect_master");
	   $rep_name=$db->getAnyDetails($row_data['repair_code'],"rep_desc","rep_code","repaircode_master");
	   $partname = "";
	   if($row_data['partcode']!=''){
	   $partname = $db->getAnyDetails($row_data['partcode'],"part_desc","partcode","partcode_master");
	   }
	 $c["fault_code"] = $fault_name;
	 $c["repair_code"] = $rep_name;
	 $c["partcode"] = $row_data['partcode'];
	 $c["part_name"] = cleanData($partname);
	 $c["consumed_qty"] = $row_data['part_qty'];
	 $c["part_cost"] = $row_data['part_cost'];
	 $c["old_serial"] = $row_data['old_serial'];
	 $c["consumed_remark"] = $row_data['remark'];
	 $c["pcb_repairable"] = $row_data['pcb_repairable'];
	 $c["replace_serial"] = $row_data['replace_serial'];
	 
	 array_push($myObj,$c);  
   }
    $b["repairdetails"] =  $myObj;
	$paymnet_cdata = mysqli_query($conn, "select cr_book_no, cr_no, cr_date, transaction_no, payment_mode, remark, amount from payment_receive_loc where job_no ='".$row["job_no"]."'");
	$paymentObj = array();
	while($pay_row_data=mysqli_fetch_array($paymnet_cdata)){
		$paymentObj["cr_book_no"] = $pay_row_data['cr_book_no'];
		$paymentObj["cr_no"] = $pay_row_data['cr_no'];
		$paymentObj["cr_date"] = $pay_row_data['cr_date'];
		$paymentObj["transaction_no"] = $pay_row_data['transaction_no'];
		$paymentObj["payment_mode"] = $pay_row_data['payment_mode'];
		$paymentObj["remark"] = $pay_row_data['remark'];
		$paymentObj["amount"] = $pay_row_data['amount'];
	}
	$b["paymentdetails"] = $paymentObj;
	if($row["warranty_status"]=="IN"){
	 $part_detail=mysqli_fetch_array(mysqli_query($conn,"select customer_price,partcode from partcode_master where partcode='40'"));
	$b['call_processing_charges']=$part_detail['customer_price'];
	$b['processing_partcode']=$part_detail['partcode'];
	}
	else {
	$b['call_processing_charges']="";
	$b['processing_partcode']="";
	}
$b["repeatcount"] = $repeat;

###### Fetch Complaint Faulty Details
$p2c_detail=mysqli_fetch_array(mysqli_query($conn,"select fresh2faulty from part_to_credit where job_no='".$row["job_no"]."'"));

if($p2c_detail['fresh2faulty']=='Y'){ $b["faulty_return"] = "Fresh Convert"; } else { $b["faulty_return"] = "Consumptions";  }

#### Check IMage Uploaded or not
$registered_prd = mysqli_fetch_array(mysqli_query($conn, "select serial_img,invoice_img,product_img from product_registered where serial_no ='".$row["imei"]."'"));
#### Product Registered Image Flag
if($registered_prd['serial_img']!=''){ $sr_flag='TRUE';  } else { $sr_flag='FALSE'; }
if($registered_prd['invoice_img']!=''){ $inv_flag='TRUE';  } else { $inv_flag='FALSE'; }
if($registered_prd['product_img']!=''){ $prd_flag='TRUE';  } else { $prd_flag='FALSE'; }

$b["sr_img_flag"] = $sr_flag;
$b["prd_img_flag"] = $prd_flag;
$b["inv_img_flag"] = $inv_flag;

#### Serial Validation Flag
if($row['smrn']!='D'){
	$serial_validate='P';
	$sr_validate_st='Pending';
}
else{
	$serial_validate=$row['smrn'];
	$sr_validate_st='Pass';
}
#### END Serial Validation Flag

$b["serial_validate"] = $serial_validate;
$b["sr_validate_status"] = $sr_validate_st;

#### NEW ####
$act_status = '';
if($_REQUEST['u_type'] == "ASP" || $_REQUEST['u_type'] == "Branch" || $_REQUEST['u_type'] == "")
{
	$sql_sjcd = "SELECT * FROM job_call_details WHERE job_no = '".$_REQUEST['job_no']."' LIMIT 1";
	$res_sjcd = mysqli_query($conn, $sql_sjcd);
	if($res_sjcd)
	{
		if(mysqli_num_rows($res_sjcd) > 0)
		{
			$act_status = "D";
		}
		else
		{
			$act_status = "P";
		}
	}	
}
$b["call_option"] = $act_status;


###### PNA Data
$pna_data = mysqli_query($conn, "select partcode,qty from auto_part_request where job_no ='".$row["job_no"]."'");
	$pnaObj = array();
	while($pna_row_data=mysqli_fetch_array($pna_data)){
		$pnapartname = $db->getAnyDetails($pna_row_data['partcode'],"part_desc","partcode","partcode_master");
		$d["pna_part"] = $pna_row_data['partcode'];
		$d["pna_part_name"] = cleanData($pnapartname);
		$d["pna_qty"] = $pna_row_data['qty'];
		array_push($pnaObj,$d);
	}
	$b["pnadetails"] = $pnaObj;
###### END PNA DATA

	
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>