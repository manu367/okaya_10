<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
require_once("../includes/config.php");
date_default_timezone_set('Asia/Calcutta');

function getAgeingHoursMins($open_date, $open_time, $close_date = '', $close_time = '')
{
    if (empty($open_date) || empty($open_time)) {
        return '';
    }

    $start = strtotime($open_date . ' ' . $open_time);

    if (!empty($close_date) && $close_date != '0000-00-00') {
        $end = strtotime($close_date . ' ' . $close_time);
    } else {
        $end = time();
    }

    if ($end < $start) {
        return '00:00:00';
    }

    $diff_seconds = $end - $start;

    $hours   = floor($diff_seconds / 3600);
    $minutes = floor(($diff_seconds % 3600) / 60);
    $seconds = $diff_seconds % 60;

    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}




/* ================= FILTERS ================= */
$arrstatus = getJobStatus($link1);
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

//// extract all encoded variables
$modelid = base64_decode($_REQUEST['modelid']);
$productid = base64_decode($_REQUEST['proid']);
$brandid = base64_decode($_REQUEST['brand']);
$state = base64_decode($_REQUEST['state']);
$status = base64_decode($_REQUEST['status']);
/*$substatus = base64_decode($_REQUEST['substatus']);*/
$loc_code = base64_decode($_REQUEST['location_code']);
$pending = base64_decode($_REQUEST['pending']);
$date_type = $_REQUEST['date_type'];
////// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$seldate = explode(" - ",$_REQUEST['daterange']);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
}
else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
}

if($date_type=='close_date'){
	$date_filter= " AND (close_date >= '".$fromdate."' and close_date <='".$todate."')";
}
else{
	$date_filter= " AND (open_date >= '".$fromdate."' and open_date <='".$todate."')";
}
	
/////get location///////////////
if($loc_code!=""){
	$locationcode=" current_location NOT IN ('OPASP0203') AND current_location in ('".$loc_code."')";
}
else {
	$locationcode=" current_location NOT IN ('OPASP0203')";
}
/////get model///////////////
if($modelid!=""){
	$model_id=" and model_id in ('".$modelid."')";
}
else {
	$model_id="";
}
/////get product///////////////
if($productid !=""){
	$product_id=" and product_id in ('".$productid."')";
}
else {
	$product_id="";
}
/////get brand///////////////
if($brandid !=""){
	$brand_id=" and brand_id in ('".$brandid."')";
}
else {
	$brand_id=" and brand_id in (".$access_brand.")";
}
/////get status///////////////
if($status !=""){
	$st=" and status in ('".$status."')";
}
else {
	//$st=" and status in ('1','2','3','5','6','7','8','11','12','48','49','50','55','56')";
	$st=" ";
}
/////get state///////////////
if($state !=""){
	$stateid=" and state_id in ('".$state."')";
}
else {
	$stateid="";
}
//////End filters value/////


/* ================= CSV HEADER ================= */

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=Call_Report.csv');

$output = fopen("php://output", "w");

/* Column Headers */

/*fputcsv($output, [
"S.No.",
"Complaint No.",
"Login Date & Time",
"Call Source",
"Call Type",
"Caller Type",
"Customer Type",
"Customer Name",
"Contact No.",
"Address",
"Pincode",
"City",
//"District",
"State",
"Zone",
"BSI Name",
"RWH",
"Logging Serial No.",
"Close Serial No",
"Product Type",
"Model S4 Hana Code",
"Model",
"Capacity (AH)",
"OPG Warranty Start Date",
"OPG Warranty End Date",
"Actual DOP",
"Source Of Warranty",
"Warranty Status",
"Warranty Days",
"Remaining Warranty Days",
"CC Remarks",
"Engineer Sapcode",
"Engineer Name",
"Complaint Handled By",
"Pending Ageing (IN HRS)",
"Pending Ageing (IN DAYS)",
"First Visit Date & Time",
"FTR (IN HRS)",
"FTR Bucket",
"Problem Reported",
"Primary Observation",
"Problem Observed",
"Solution Given",
"Pending Reason",
"Charger Installed",
"Charger Installed Date",
"Charger Removed",
"Charger Removed Date",

"Charging Duration",
"Dis-Charger Used",
"Dis-Charger Not Used Reason",
"Engineer Remarks",
"Send To HO Approval/ Request Date",
"Replacement Approval Status",
"Replacement Approved By",
"Replacement Approver ID",
"Replacement Approver Name",
"Replacement Approved Date",
"Replacement Approved Time",
"Replacement Approved Remarks",
"Replacement Rejected Reason",
"Complaint Status",
"Repair Code Available",
"Repair Code NA Reason",
"Complaint Cancel Reason by Eng.",
"Total Nos Of Visit",
"TAT-1 Date",
"TAT-1 (Resolution IN Hrs)",
"TAT-1 (Bucket)",
"Final Close Date",
"Final Close Time",
"Customer Replacement Status"
]);
*/

$columns = [

"sno" => "S.No.",
"job_no" =>"Complaint No.",
"login_dt" => "Login Date & Time",
"call_source" => "Call Source",
"call_type" => "Call Type",
"caller_type" => "Caller Type",
"customer_type" => "Customer Type",
"customer_name" => "Customer Name",
"contact_no" => "Contact No.",
"address" => "Address",
"pincode" => "Pincode",
"city" => "City",
//"District",
"state" => "State",
"zone" => "Zone",
"bsi_name" => "BSI Name",
"rwh" => "RWH / ASC",
"log_serial" => "Logging Serial No.",
"close_serial" => "Close Serial No",
"product" => "Product Type",
"model_code" => "Model S4 Hana Code",
"model" => "Model",
"capacity" =>"Capacity (AH)",
"opg_start_date" => "OPG Warranty Start Date",
"opg_end_date" => "OPG Warranty End Date",
"dop"=> "Actual DOP",
"warranty_source" => "Source Of Warranty",
"warr_status"=> "Warranty Status",
"warr_days"=> "Warranty Days",
"bal_ws_days" => "Remaining Warranty Days",
"cc_rmk" => "CC Remarks",
"eng_sapcode" => "Engineer Sapcode",
"eng_name" => "Engineer Name",
"job_handle_by" => "Complaint Handled By",
"pending_age_hr" => "Pending Ageing (IN HRS)",
"pending_age_day" => "Pending Ageing (IN DAYS)",
"first_visit_date" => "First Visit Date & Time",
"ftr_hrs" => "FTR (IN HRS)",
"ftr_bucket" => "FTR Bucket",
"problem_reported" => "Problem Reported",
"primary_observation" => "Primary Observation",
"problem_observed" => "Problem Observed",
"solution" => "Solution Given",
"pending_reason" => "Pending Reason (WIP)",
"charger_install" => "Charger Installed",
"charger_install_dt" => "Charger Installed Date",
"charger_remove" => "Charger Removed",
"charger_remove_dt" => "Charger Removed Date",
"charging_duration" => "Charging Duration",
"discharger_user" => "Dis-Charger Used",
"discharger_na_reason" => "Dis-Charger Not Used Reason",
"eng_rmk" => "Engineer Remarks",
"send_to_ho" => "Send To HO Approval/ Request Date",
"repl_appr_status" => "Replacement Approval Status",
"repl_apr_by" => "Replacement Approved By",
"repl_appr_id"=> "Replacement Approver ID",
"repl_appr_name" => "Replacement Approver Name",
"repl_appr_date" => "Replacement Approved Date",
"repl_appr_time" => "Replacement Approved Time",
"repl_appr_rmk" => "Replacement Approved Remarks",
"repl_appr_rej" => "Replacement Rejected Reason",
"job_status"=> "Complaint Status",
"scm_avl" => "Repair Code Available",
"scm_reason" => "Repair Code NA Reason",
"cancel_reason" => "Complaint Cancel Reason by Eng.",
"total_visit" => "Total Nos Of Visit",
"tat1_date" => "TAT-1 Date",
"tat_hrs" => "TAT-1 (Resolution IN Hrs)",
"tat_bucket" => "TAT-1 (Bucket)",
"close_date" => "Final Close Date",
"close_time" => "Final Close Time",
"repl_status" => "Customer Replacement Status",
"er_chk_btr" => "ER Checked Battery",
"er_susp_btr" => "ER Suspected Battery",
"login_id" => "Complaint Logged By User ID",
"login_name" => "Complaint Logged By User Name",
"app_ver" => "App Version",
"discount" => "Discount Amount",
"total_amt" => "Total Collect Amount",
"pay_ref_no" => "Payment Reference No.",
"bkp_method" => "Used Backup Method",
"reason_bulb_load" => "Reason for using Bulb Load",
"area_type" => "Area Type",
"mapped_engid" => "Service Champion ID",
"mapped_engname" => "Service Champion Name",
"travel_km" => "Travel KM",
"repeat_call" => "Repeat Complaint"



];

if($pending == 'checked'){

unset(
$columns['warranty_source'],
$columns['repl_apr_by'],
//$columns['repl_appr_id'],
//$columns['repl_appr_name'],
$columns['repl_appr_date'],
$columns['repl_appr_time'],
$columns['repl_appr_rmk'],
$columns['repl_appr_rej'],
$columns['scm_avl'],
$columns['scm_reason'],
$columns['tat1_date'],
$columns['tat_hrs'],
$columns['tat_bucket'],
$columns['close_date'],
$columns['close_time'],
$columns['repl_status'],
$columns['er_chk_btr'],
$columns['er_susp_btr'],
$columns['app_ver'],
$columns['discount'],
$columns['total_amt'],
$columns['pay_ref_no'],
$columns['bkp_method'],
$columns['reason_bulb_load'],
$columns['area_type'],
$columns['mapped_engid'],
$columns['mapped_engname'],
$columns['travel_km'],
$columns['repeat_call']

);

}

fputcsv($output, array_values($columns));

$i=2;
$count=1;
if($pending == 'checked'){

	$sql_loc = mysqli_query($link1,"Select * from jobsheet_data where ".$locationcode." ".$model_id." ".$stateid." ".$cityid." ".$product_id." ".$brand_id." and status in('1','2','5','7','50','56','58','3','55','81')")or die("error 1 ".mysqli_error($link1));
}
else{
	
	$sql_loc = mysqli_query($link1,"Select * from jobsheet_data where ".$locationcode." ".$date_filter."  ".$model_id." ".$st." ".$stateid." ".$product_id."")or die("error 2 ".mysqli_error($link1));
}

while($row = mysqli_fetch_array($sql_loc)){
	
	$fault_code='';
	$solution_code='';
	$primary_observation='';
	$tat1_date='';
	$bsi_name='';
	$job_attend_by='';

   $cust_det = explode("~",getAnyDetails($row['customer_id'],"pincode,address1,stateid,landmark,email,phone,alt_mobile,cityid,caller_type","customer_id","customer_master",$link1));
	
   $product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where serial_no='".$row['imei']."'"));
   
   $fault_code=$row['fault_code'];
   $solution_code=$row['solution_code'];
   $primary_observation=$row['cust_problem3'];
   
   if($fault_code==''){ 
   $repair_details = explode("~",getAnyDetails($row['job_no'],"fault_code,repair_code,symptom_code","job_no","repair_detail",$link1));
   
   $fault_code=$repair_details[0];
   $solution_code=$repair_details[1];
   $primary_observation=$repair_details[2];
   }
   

	$cust_city = explode("~",getAnyDetails($row['city_id'],"city,state,zone","cityid","city_master",$link1));
	
	$zone = getAnyDetails($cust_city[2] ,"zonename","zoneid","zone_master" ,$link1);
	
	$model_details= explode("~",getAnyDetails($row['model_id'],"modelcode,sim_type,model","model_id","model_master",$link1));

	$job_currentStatus=getAnyDetails($row["status"],"display_status","status_id","jobstatus_master",$link1);
	
	$eng_detail = explode("~",getAnyDetails($row['eng_id'],"locusername,mapped_bsi,location_code,eng_type","userloginid","locationuser_master",$link1));
	
	$job_attend_by = ($eng_detail[3] == 'ASP') ? 'ASC' : $eng_detail[3];
	
	$bsi_name=getAnyDetails($eng_detail[1],"name","sapid","admin_users",$link1);
	
	
	$voc1 = getAnyDetails($row['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
	$product_details=getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1);
	
	$aging='';
	$aging_days='';
	$tat_hrs='';
	$tat_bucket='';
	
	if($row['close_date'] =='0000-00-00' && $row['repl_appr_date'] =='0000-00-00'){ 
	$aging = getAgeingHoursMins($row['open_date'],$row['open_time'],date('Y-m-d'),date('H:i:s'));
	$aging_days = daysDifference($today,$row['open_date']);
	/* Force Excel Text */
	//$aging = '="'.$aging.'"';
	}
	//if($row['close_date'] != '0000-00-00'){$tat = daysDifference($row['close_date'],$row['open_date']);}else{ $tat = "--" ;}
	### TAT Calculation, IF Replacement=> TAT-1 Approved date- open date or if Non- Replacement then TAT-1 => CLose Date- Open date
	if($row['repl_appr_date'] != '0000-00-00' && ($row['status']=='8' || $row['status']=='82')){
		$tat_hrs = getAgeingHoursMins($row['open_date'],$row['open_time'],$row['repl_appr_date'],$row['repl_appr_time']);
		
		$tat1_date=$row['repl_appr_date']." ".$row['repl_appr_time'];
		
	/* Force Excel Text */
	//$tat_hrs = '="'.$tat_hrs.'"';
	}
	else if($row['close_date'] != '0000-00-00' && ($row['status']=='10' || $row['status']=='48' || $row['status']=='12')){
		$tat_hrs = getAgeingHoursMins($row['open_date'],$row['open_time'],$row['close_date'],$row['close_time']);
		
		$tat1_date=$row['close_date']." ".$row['close_time'];
	/* Force Excel Text */
	//$tat_hrs = '="'.$tat_hrs.'"';
	}
	
	else{ 
	$tat = "--" ;

	}
	
	### TAT Bucket
	if($tat_hrs!='' && $tat_hrs <= 23){
	$tat_bucket=" <= 24 HRS ";
	}
	else if($tat_hrs >= 24 && $tat_hrs <= 35){
	$tat_bucket=" <= 36 HRS ";
	}
	else if($tat_hrs >= 36 && $tat_hrs <= 47){
	$tat_bucket=" <= 48 HRS ";
	}
	else if($tat_hrs >= 48 && $tat_hrs <= 71){
	$tat_bucket=" <= 72 HRS ";
	}
	else if($tat_hrs > 71){
	$tat_bucket=" > 72 HRS ";
	}
	else{
		$tat_bucket='';
	}

    /* Call Type */
    $call_type = ($row['call_for']=="Repair") ? "Service Request" : $row['call_for'];
	
	### Replacement Status
	$repl_approved_name='';
	$repl_approved='';
	$repl_appr_by='';
	$repl_st='';
	$repl_appr_st='';
	
	if($row['doa_approval']=='Y') { $repl_appr_st='Approved'; } 
	elseif($row['doa_approval']=='N') { $repl_appr_st='Rejected'; } 
	
	if($row['status']=='81' || $row['status']=='82') { $repl_st='Pending'; } 
	else if($row['status']=='8') { $repl_st='Done'; }
	
	$repl_appr_by=$row['repl_platform'];
	
	if($row['repl_platform']=='ENGINEER' || $row['repl_platform']=='ENGINEER_CONDITIONAL'){
		$repl_appr_by='ENGINEER';
		$repl_approved=$row['eng_id'];
		$repl_approved_name=$eng_detail[0];
	}
	else if($row['repl_platform']=='LEVEL_2'){
		$repl_appr_by='HO';
		$repl_approved=$row['doa_ar_by'];
		$repl_approved_name=getAnyDetails($row['doa_ar_by'],"name","username","admin_users",$link1);
	}
	else if($row['repl_platform']=='BSI'){
		$repl_appr_by='BSI';
		$repl_approved=$eng_detail[1];
		$repl_approved_name=$bsi_name;
	}
	else{
	$repl_appr_by=$row['repl_platform'];	
	$repl_approved=$row['doa_ar_by'];
	$repl_approved_name=getAnyDetails($row['doa_ar_by'],"name","username","admin_users",$link1);
	}
	
	### Job First Visit Activity
	$sql_visit=mysqli_query($link1,"SELECT (SELECT COUNT(*) FROM user_daily_track WHERE ref_no='".$row['job_no']."') AS total_entry, entry_date, entry_time FROM user_daily_track WHERE ref_no='".$row['job_no']."' ORDER BY id ASC LIMIT 1;");
	$punch_details=mysqli_fetch_array($sql_visit);
	
	$ftr_hrs='';
	$total_visit='';
	if($punch_details['entry_date']!='0000-00-00' && $punch_details['entry_date']!=''){
		$ftr_hrs=getAgeingHoursMins($row['open_date'],$row['open_time'],$punch_details['entry_date'],$punch_details['entry_time']);
		/* Force Excel Text */
		//$ftr_hrs = '="'.$ftr_hrs.'"';
		$total_visit=$punch_details['total_entry'];
	}
	else{
		$ftr_hrs ="";
		$total_visit='0';
	}
	
	### FTR Bucket
	$ftr_bucket='';
	if($ftr_hrs!='' && $ftr_hrs <= 23){
	$ftr_bucket=" <= 24 HRS ";
	}
	else if($ftr_hrs >= 24 && $ftr_hrs <= 35){
	$ftr_bucket=" <= 36 HRS ";
	}
	else if($ftr_hrs >= 36 && $ftr_hrs <= 47){
	$ftr_bucket=" <= 48 HRS ";
	}
	else if($ftr_hrs > 47){
	$ftr_bucket=" > 48 HRS ";
	}
	else{
		$ftr_bucket='';
	}
	
	#### CHarging Install Details
	$charger_install_dt='';
	$charger_remove_dt='';
	if($row['charger_remove_dt']!='0000-00-00' && $row['dt1']=='0000-00-00')
	{
		$charger_remove_dt=$row['charger_remove_dt'];
		$charger_install_dt=$row['charger_remove_dt'];
	}
		else{
			$charger_install_dt=$row['dt1'];
			$charger_remove_dt=$row['charger_remove_dt'];
		}
		
	### Check Suspected Job Details
	$sql_job_suspected=mysqli_query($link1,"select batteries_checked,batteries_suspected from suspected_battery_data where job_no='". $row['job_no']."'");
	$row_suspect=mysqli_fetch_array($sql_job_suspected);	
	
	#### Job Created Details
	$login_name='';
	$login_name=getAnyDetails($row['created_by'],"locusername","userloginid","locationuser_master",$link1);
	if($login_name==''){ $login_name=$row['created_by']; }	
	
	#### Payment Details
	$payment_details = explode("~",getAnyDetails($row['job_no'],"discount,amount,transaction_no","job_no","payment_receive_loc",$link1));
	
	$bkp_method='';
	if($row['btr_reason']!=''){ $bkp_method='By Inverter Bulb|System load';  } else { $bkp_method=''; }
	
	$loc_details=explode("~",getAnyDetails($row['current_location'],"locationname,mapped_company_eng","location_code","location_master",$link1));
	
	$mapped_company_eng_nm='';
	
	if($loc_details[1]!=''){
	$mapped_company_eng_nm=getAnyDetails($loc_details[1],"locusername","userloginid","locationuser_master",$link1);	
	}
	
	$claimjob_details=explode("~",getAnyDetails($row['job_no'],"area_type,travel_km,repeatcall","job_no","job_claim_data",$link1));

    /*fputcsv($output, [
        $count,
        $row['job_no'],
        $row['open_date']." ".$row['open_time'],
        $row['call_type'],
        $call_type,
        $cust_det[8],
        $row['customer_type'],
        strtoupper($row['customer_name']),
        $row['contact_no'],
        strtoupper($row['address']),
        $row['pincode'],
        strtoupper($cust_city[0]),
      //  $cust_city[0],
        strtoupper($cust_city[1]),
        $zone,
		getAnyDetails($eng_detail[1],"name","sapid","admin_users",$link1),
        getAnyDetails($row['current_location'],"locationname","location_code","location_master",$link1),
        strtoupper($row['imei']),
        strtoupper($row['imei']),
        $product_details,
        $model_details[0],
        $model_details[2],
        $model_details[1],
        $row['opg_start_date'],
        $row['opg_end_date'],
        $row['dop'],
        $row['th_imei'],
        $row['warranty_status'],
		$row['warranty_days'],
        $row['balance_warranty_days'],
		$row['remark'],
        $row['eng_id'],
        $eng_detail[0],
		$eng_detail[3],
        $aging,
		$aging_days,
		$punch_details['entry_date']." ".$punch_details['entry_time'],
		$ftr_hrs,  // FTR details
		$ftr_bucket,
        $voc1,
		getAnyDetails($primary_observation,"observation","id","observation_master",$link1),  /// primary observation
        getAnyDetails($fault_code,"defect_desc","defect_code","defect_master",$link1),
        getAnyDetails($solution_code,"rep_desc","rep_code","repaircode_master",$link1),
        $row['reason'],
		$row['charger_install'],
		$charger_install_dt, // charger install
		$row['charger_remove'],
		$charger_remove_dt, // charger remove
		$row['problem_detect'],  /// Charging Duration
		$row['discharger_connection'],
		$row['discharger_connection_reason'],
		$row['app_rmk'], // Eng Rmk
		$row['els_date'],  /// Send to HO Approval Date
		$repl_appr_st,
		$repl_appr_by,
		$repl_approved,
		$repl_approved_name,
		$row['repl_appr_date'],
		$row['repl_appr_time'],
		$row['doa_remark'],
		$row['line'],
        $job_currentStatus,
		$row['scm_avl'],
		$row['scm_reason'],
		$row['cancel_reason'],
		$total_visit, // total visit
		$tat1_date,
		$tat_hrs,  // TAT 1 (In HRS)
		$tat_bucket,
		$row['close_date'],
		$row['close_time'],
		$repl_st
    ]);*/
	
	$rowData = [

"sno" => $count,

"job_no" => $row['job_no'],

"login_dt" => $row['open_date']." ".$row['open_time'],

"call_source" => $row['call_type'],

"call_type" => $call_type,

"caller_type" => $cust_det[8],

"customer_type" => $row['customer_type'],

"customer_name" => strtoupper($row['customer_name']),

"contact_no" => $row['contact_no'],

"address" => strtoupper($row['address']),

"pincode" => $cust_det[0],

"city" => strtoupper($cust_city[0]),

"state" => strtoupper($cust_city[1]),

"zone" => $zone,

"bsi_name" => $bsi_name,

"rwh" => getAnyDetails($row['current_location'],"locationname","location_code","location_master",$link1),

"log_serial" => $row['imei'],

"close_serial" => $row['imei'],

"product" => $product_details,

"model_code" => $model_details[0],

"model" => $model_details[2],
"capacity" => $model_details[1],

"opg_start_date" =>  $row['opg_start_date'],
"opg_end_date" => $row['opg_end_date'],
"dop"=> $row['dop'],
"warranty_source" =>  $row['th_imei'],
"warr_status"=> $row['warranty_status'],
"warr_days"=> $row['warranty_days'],
"bal_ws_days" => $row['balance_warranty_days'],
"cc_rmk" => $row['remark'],
"eng_sapcode" => $row['eng_id'],
"eng_name" => $eng_detail[0],
"job_handle_by" => $job_attend_by,

"pending_age_hr" => $aging,

"pending_age_day" => $aging_days,

"first_visit_date" => $punch_details['entry_date']." ".$punch_details['entry_time'],

"ftr_hrs" => $ftr_hrs,

"ftr_bucket" => $ftr_bucket,

"problem_reported" => $voc1,

"primary_observation" => getAnyDetails($primary_observation,"observation","id","observation_master",$link1),

"problem_observed" => getAnyDetails($fault_code,"defect_desc","defect_code","defect_master",$link1),

"solution" => getAnyDetails($solution_code,"rep_desc","rep_code","repaircode_master",$link1),

"pending_reason" => $row['reason'],
"charger_install" => $row['charger_install'],
"charger_install_dt" =>	$charger_install_dt, // charger install
"charger_remove" => $row['charger_remove'],
"charger_remove_dt" => $charger_remove_dt, // charger remove
"charging_duration" => $row['problem_detect'],  /// Charging Duration
"discharger_user" => $row['discharger_connection'],
"discharger_na_reason" => $row['discharger_connection_reason'],
"eng_rmk" => $row['app_rmk'],
"send_to_ho" => $row['els_date'],
"repl_appr_status" => $repl_appr_st,
"repl_apr_by" => $repl_appr_by,
"repl_appr_id"=> $repl_approved,
"repl_appr_name" => $repl_approved_name,
"repl_appr_date" => $row['repl_appr_date'],
"repl_appr_time" => $row['repl_appr_time'],
"repl_appr_rmk" => $row['doa_remark'],
"repl_appr_rej" => $row['line'],
"job_status"=> $job_currentStatus,

"scm_avl" => $row['scm_avl'],

"scm_reason" => $row['scm_reason'],

"cancel_reason" => $row['cancel_reason'],

"total_visit" => $total_visit,

"tat1_date" => $tat1_date,

"tat_hrs" => $tat_hrs,

"tat_bucket" => $tat_bucket,

"close_date" => $row['close_date'],

"close_time" => $row['close_time'],

"repl_status" => $repl_st,
"er_chk_btr" => $row_suspect['batteries_checked'],
"er_susp_btr" => $row_suspect['batteries_suspected'],
"login_id" => $row['created_by'],
"login_name" => $login_name,
"app_ver" => $row['app_version'],
"discount" => $payment_details[0],
"total_amt" => $payment_details[1],
"pay_ref_no" => $payment_details[2],
"bkp_method" => $bkp_method,
"reason_bulb_load" => $row['btr_reason'],
"area_type" => $claimjob_details[0],
"mapped_engid" => $loc_details[1],
"mapped_engname" => $mapped_company_eng_nm,
"travel_km" => $claimjob_details[1],
"repeat_call" => $claimjob_details[2]

];

$finalRow = [];

foreach($columns as $key => $col){
    $finalRow[] = $rowData[$key] ?? '';
}

fputcsv($output,$finalRow);

    $count++;

    /* Flush memory */
    if($count % 1000 == 0){
        flush();
    }
}


fclose($output);
exit;
?>