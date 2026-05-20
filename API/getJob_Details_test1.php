<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
   
$users = $db->getJobMaster();     
$a = array();     
$b = array(); 
$c = array();   
if ($users != false){  
    
     $service_charg="";   
$job_row=mysqli_fetch_array(mysqli_query($conn,"select job_no,open_date,imei from jobsheet_data where job_no='".$_REQUEST['job_no']."'"));
 //echo $serial_no=$job_row['imei'];
   // echo "<br>";
    //echo "select close_date, job_no  from jobsheet_data where imei = '".$serial_no."' and status in ('10','6') and imei not like 'Z%' and close_date!='0000-00-00' order by job_id desc";
      //echo "<br>";
    $rowk=mysqli_query($conn,"select close_date, job_no  from jobsheet_data where imei = '".$serial_no."' and status in ('10','6') and imei not like 'Z%' and close_date!='0000-00-00' order by job_id desc"); 
    $job_close = mysqli_fetch_assoc($rowk);
    //echo $job_close['job_no'];
 
    if(mysqli_num_rows($rowk)>0){
        
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
$b["repair_status"] = $row["status"];
$b["status_name"] = $status_name ;
if($row['dop']!='0000-00-00'){
$b["dop"] = $row["dop"];
}else {
$b["dop"] ="";  
}
$b["scm"] = $row["h_code"]; 
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
$b['complaint_remark']=$row['remark'];
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
    if($close_re <= 30){
            $repair_detail=mysqli_fetch_array(mysqli_query($conn,"select partcode from repair_detail where job_no='".$job_close['job_no']."' and partcode='39'"));
			$job_type='A';
			$rpt_flg = $job_close['job_no'];
        //echo "<br>";
        //echo $repair_detail['partcode']."-".$row["partcode"];
         //echo "<br>";
           if($repair_detail['partcode']==$row["partcode"]) {
            $service_charg="0.00";
           }
            else {
                $service_charg=$model_details["ser_charge"];
            }
           // exit;   
		}
        elseif($close_re <= 60 && $close_re >= 31){
			$job_type='B';
			$rpt_flg = $job_close['job_no'];
             $service_charg=$model_details["ser_charge"];
		}
        elseif($close_re <= 90 && $close_re >= 61){
			$job_type='C';
			$rpt_flg = $job_close['job_no'];
             $service_charg=$model_details["ser_charge"];
		}
        else{
			$job_type='Normal';
			$rpt_flg = "";
             $service_charg=$model_details["ser_charge"];
		}
//if($row["warranty_status"]!='IN'){
$b["service_charge"] = $service_charg; 
//}
//else{
//$b["service_charge"] ='0.00';	
//}

##### COmplaint Repair Details
$sql_data=mysqli_query($conn,"select fault_code,repair_code from repair_detail where job_no='".$row["job_no"]."' AND partcode !='39'");
$myObj = array();
   while($row_data=mysqli_fetch_array($sql_data)){
	   $fault_name=$db->getAnyDetails($row_data['fault_code'],"defect_desc","defect_code","defect_master");
	   $rep_name=$db->getAnyDetails($row_data['repair_code'],"rep_desc","rep_code","repaircode_master");
	 $c["fault_code"] = $fault_name;
	 $c["repair_code"] = $rep_name;
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
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>