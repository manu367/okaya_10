<?php   

class DB_Functions{       
private $db;
	private $link;
	private $dt_format;
	//////////// functions
	function __construct() {
		include_once './db_connect.php'; 
		$this->db = new DB_Connect();         
		$this->link = $this->db->connect();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}       
	function __destruct() {
	}
	function escape( $x ) {
		return "'" . mysql_real_escape_string($x) . "'";
	}
	
function getServerdatetime(){
	date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
return $today."^".$time;
}


 //////////////////////////////////////////////////////////////
 public function getmodel(){
 $result = mysqli_query($this->link,"SELECT * FROM model_master WHERE status ='1'") or die(mysqli_error($this->link));         
 return $result;     
 }


 //////////////Product Master//////////
 function getProduct(){
	 $result=mysqli_query($this->link,"select * from product_master where status='1'");
	 return $result;
 }
 //////////////Get Close Type//////////
 function getclosetype(){
	 $result=mysqli_query($this->link,"select * from  close_type  ");
	 return $result;
 }


 /////////////////////// Voc MASTER For get detail //////////////////////
 public function getVocmaster($voccode,$vocdesc) {
	 $result = mysqli_query($this->link,"SELECT $vocdesc  FROM voc_master  where voc_code='".$voccode."'") or die(mysqli_error($this->link));         
	 return $result;
 }
 
  /////////////////////// City MASTER For get detail //////////////////////
 public function getcitymaster($city_id,$city) {
	 $result = mysqli_query($this->link,"SELECT $city  FROM city_master  where cityid='".$city_id."'") or die(mysqli_error($this->link));         
	 return $result;
 }
   /////////////////////// State MASTER For get detail //////////////////////
 public function getstatemaster($state_id,$state){
	 $result = mysqli_query($this->link,"SELECT $state  FROM state_master  where stateid='".$state_id."'") or die(mysqli_error($this->link));         
	 return $result;
 }
  /////////////////////// Voc MASTER //////////////////////
 public function getVoc() {
	 $result = mysqli_query($this->link,"SELECT * FROM voc_master where status='1' group by voc_code")  or die(mysqli_error($this->link));
	 return $result;
 }
   /////////////////////// Voc MASTER //////////////////////
 public function getFault() {
	 $result = mysqli_query($this->link,"SELECT * FROM  defect_master where status='1' group by  	defect_desc ")  or die(mysqli_error($this->link));
	 return $result;
 }
    /////////////////////// Voc MASTER //////////////////////
 public function getStatus() {
	 $result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('EP','PNA','Repair Done','Replacement','WIP','Demo Done','Installation Done','RWR','Request For Approval') order by display_status")  or die(mysqli_error($this->link));
	 return $result;
 }
 /////////////////////// Part MASTER //////////////////////
 public function getPart() {
	 $result = mysqli_query( $this->link,"SELECT * FROM partcode_master where status='1' ") or die(mysqli_error($this->link));
	 return $result;
 }
   /////////////////////// Part MASTER BASED ON PRODUCT ID //////////////////////
 public function getPart_product() {
	 $result = mysqli_query( $this->link,"SELECT * FROM partcode_master where status='1'  and product_id='".$_REQUEST['p_id']."' group by part_name")or die(mysqli_error($this->link));
	 return $result;
 }
 //////////////////////////////////////////Get Serial Number Details//////////////////////////////////////////////////////
 function getserialmodel($serial_no){
$result=mysqli_query( $this->link,"select model_id,serial_no from product_registered where serial_no='".$_REQUEST['serial_no']."'")or die(mysqli_error($this->link));
return $result;
}
 ////////////////////////////complaint_master/////////////////////////////////////////////////////////////////
 public function getComplaintsMaster($eid) {         
 $result = mysqli_query($this->link,"SELECT * FROM jobsheet_data where eng_id='".$eid."' and status!='12'") or die(mysqli_error($this->link));     
 return $result;     
 }
  ////////////////////////////complaint_master/////////////////////////////////////////////////////////////////
 public function getstockeng($eid) {        
 
 $loc=mysqli_fetch_array(mysqli_query($this->link,"SELECT 	location_code FROM locationuser_master where  userloginid 	='".$eid."'"));
 
 $result = mysqli_query($this->link,"SELECT * FROM  client_inventory where  location_code 	='".$loc['location_code']."'") or die(mysqli_error($this->link));     
 return $result;     
 }
 
 
   ////////////////////////////complaint_master/////////////////////////////////////////////////////////////////
 public function getstockengtagno($eid) {         
 $result = mysqli_query($this->link,"SELECT * FROM imei_details_eng where  locationuser_code 	='".$eid."' and status=1 and stock_type='ok'") or die(mysqli_error($this->link));     
 return $result;     
 }
  /////////////// Customer Master Data ///////////////////////////
 public function getCustomerMaster() {         
 $result =mysqli_query($this->link,"SELECT * FROM customer_master where 1")or die(mysqli_error($this->link));          
 return $result;     
 }
   /////////////// Customer Master Data ///////////////////////////
 public function getrequestreason() {         
 $result =mysqli_query($this->link,"SELECT * FROM request_reason where 1")or die(mysqli_error($this->link));          
 return $result;     
 }
  /////////////// Solution Master /////////////////////
 public function getSolutionMaster() {         
 $result =mysqli_query($this->link,"SELECT * FROM repaircode_master WHERE status ='1'")or die(mysqli_error($this->link));   
 return $result;     
 } 
 ///////////Attendence Data////
function micAttendence_report($from,$to,$eng_id){
 
 $result=mysqli_query($this->link,"select * from mic_attendence_data where insert_date>='".$from."' and insert_date<='".$to."' and  user_id='".$eng_id."'" )or die(mysqli_error($this->link)); 
 return $result;
 
 }
 ///// generic function

function getAnyDetails($keyid,$fields,$lookupname,$tbname){
	///// check no. of column
	$chk_keyword = substr_count($fields, ',');
   	if($chk_keyword > 0){
		$explodee = explode(",",$fields);
   		$tb_details = mysqli_fetch_array(mysqli_query($this->link,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
   		$rtn_str = "";
   		for($k=0;$k < count($explodee);$k++){
       		if($rtn_str==""){
          		$rtn_str.= $tb_details[$k];
	   		}
       		else{
          		$rtn_str.= "~".$tb_details[$k];
			}
		}
	}
	else{
		$tb_details = mysqli_fetch_array(mysqli_query($this->link,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
		$rtn_str = $tb_details[$fields];
	}
   return $rtn_str;
}

  ///////////////////// Engg Detail ///////////////////////   
 public function getUserDetails() { 
       
  $result = mysqli_query($this->link,"SELECT * FROM locationuser_master where userloginid='".$_REQUEST['eid']."' and pwd=BINARY  '".$_REQUEST['password']."' and statusid='1'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
 //////////////////////////////////////Branch Details//////////////////////////////
  public function getlocationstate($loc) { 
       
  $result = mysqli_query($this->link,"SELECT * FROM location_master where location_code='".$loc."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
   ///////////////////// Repair Details ///////////////////////   
 public function getRepairDetails($job) { 
       
  $result = mysqli_query($this->link,"SELECT * FROM repair_detail where job_no='".$job."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
    ///////////////////// Repair Details ///////////////////////   
 public function getPNADetails($job) { 
       
  $result = mysqli_query($this->link,"SELECT * FROM auto_part_request where job_no='".$job."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
 

  public function getEPDetails($job) { 
       
  $result = mysqli_query($this->link,"SELECT * FROM estimate_items where job_no='".$job."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
  ///////////////////// Engg Detail ///////////////////////   
 public function getmaploc($loc) { 
       
  $result = mysqli_query($this->link,"select * from map_repair_location where location_code='".$loc."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
  //////////////// Reason Master ////////////////////
 public function getReasonMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM reason_master WHERE status = '1'") or die(mysqli_error($this->link));
 return $result;     
 } 
   //////////////// Reason Master ////////////////////
 public function getclosedReasonMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM close_reason_master WHERE status = '1'") or die(mysqli_error($this->link));
 return $result;     
 } 
  ///////////////////////Notice Master//////////////
public function getNoticeDetails(){
	$result=mysqli_query($this->link,"select * from notice where status='1'")or die(mysqli_error($this->link));  
	return $result;
}
 /////////////Microtek Attendence Data/////////////
  public function Mic_Eng_attendence($longitu_in,$latitu_in,$in_datetime,$out_datetime,$status_in,$status_out,$ei,$address_in,$address_out,$insert_date,$longitu_out,$latitu_out,$image_in,$image_out) {
	 $a=array();
	 $msg='';
	 
$chk=mysqli_query($this->link,"select * from mic_attendence_data where user_id='$ei' and insert_date='$insert_date'");
$chk2=mysqli_fetch_array($chk);
if(mysqli_num_rows($chk)<=0 && $status_in!=''){
	//echo "insert into mic_attendence_data set longitude_in='$longitu_in',latitude_in='$latitu_in',longitude_out='$longitu_out',latitude_out='$latitu_out',user_id='$ei',status_in='$status_in',in_datetime='$in_datetime',status_out='$status_out',out_datetime='$out_datetime',address_in='$address_in',address_out='$address_out',insert_date='$insert_date'";
	$result=mysqli_query($this->link,"insert into mic_attendence_data set longitude_in='$longitu_in',latitude_in='$latitu_in',longitude_out='$longitu_out',latitude_out='$latitu_out',user_id='$ei',status_in='$status_in',in_datetime='$in_datetime',status_out='$status_out',out_datetime='$out_datetime',address_in='$address_in',address_out='$address_out',insert_date='$insert_date',image_In='$image_in'") or die(mysqli_error($this->link));  
}else if(mysqli_num_rows($chk)>0 && $chk2['status_out']==''){
	$result=mysqli_query($this->link,"update mic_attendence_data set status_out='$status_out',out_datetime='$out_datetime',address_out='$address_out',longitude_out='$longitu_out',latitude_out='$latitu_out',image_Out='$image_out'  where id='$chk2[id]'");
}
elseif(mysqli_num_rows($chk)>0 && $chk2[status_in]!='' && $chk2[status_out]!='' ){
	$msg="Data Already Exist!";
}
else{
	$msg="Plese Log In First!";
}
if($result) {  
   $a["status"]='yes';
   $a["id"]=$id;  
   $a["msg"]=$msg;      
return $a;         
} else {             
if( mysqli_errno() == 1062) {                
 // Duplicate key - Primary Key Violation   
 $a["status"]='yes';
   $a["userid"]=$ei;
    $a["msg"]=$msg;               
 return $a;             
 } else {                 
 // For other errors 
 $a["status"]='no';
   $a["userid"]=$ei;
    $a["msg"]=$msg;                 
 return $a;             
 }                     
 }

}

/////////////////////////////////////////////////////update image against Job/////////////////////////////////////////////////
 public function storeImageJob($job_no,$fileName1,$fileName2,$fileName3,$fileNameSerial,$fileName5) { 
 $today=date("Y-m-d");
 $folder1="app_image";
 $imag_url="../".$folder1."/".$fileName1;
 $imag_url2="../".$folder1."/".$fileName2;
 $imag_url3="../".$folder1."/".$fileName3;
  $imag_url4="../".$folder1."/".$fileNameSerial;
   $imag_url5="../".$folder1."/".$fileName5;
   
   //echo "insert into image_upload_details set img_url='".$imag_url."',img_url1 ='".$imag_url2."',img_url2='".$imag_url3."',img_url3='".$fileNameSerial."',img_url4='".$imag_url5."',job_no='".$job_no."',activity='Image Upload by app',upload_date='".$today."'";
 $res_jobsheet = mysqli_query($this->link,"insert into image_upload_details set img_url='".$imag_url."',img_url1 ='".$imag_url2."',img_url2='".$imag_url3."',img_url3='".$imag_url4."',img_url4='".$imag_url5."',job_no='".$job_no."',activity='Image Upload by app',upload_date='".$today."'");
 if ($res_jobsheet) {             
return true;         
}  else {                 
 // For other errors                 
 return false;             
                   
 }
 
 }
 ////////////////// Update Complaint Master //////////////////
 public function storeUser($job_no,$status,$updateDate,$closed_reason,$update_remark,$fault_code,$repairList,$partEP,$partPNA,$partInstallationDone,$partDemoDone,$repair_status,$pending_reason,$eid,$replacedBy,$replacemetModel,$travelKM,$repair_central,$repair_code,$requestReason,$replacedBySrNo,$latitude,$longitude,$address,$confirmedBy,$contactNumber,$customerRemark,$serviceRating,$customerFeedbackDate,$closeType,$valueCloseType,$tag,$serialNo,$model_id)
{
 
 $cls_dt=explode(" ",$updateDate); 
  $con_dt=explode(" ",$customerFeedbackDate);
  
  // get model name ////
  $mod_name_s=mysqli_fetch_array(mysqli_query($this->link,"select model from model_master where model_id='".$model_id."'"));
  $model_name = $mod_name_s[0];
  $mod_str = " , model_id = '".$model_id."', model = '".$model_name."' ";
  
  $old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
	
	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
if($old_s['status']!=$repair_status){

//////////////////////////////status Refer to Centeral Workshop/////////////////////////////////////////////////////
if($repair_status==4){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',reason='".$reason."',pen_status='".$status."',app_rmk='Done By App' ".$mod_str." where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Job Esclated',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$result=mysqli_query($this->link,$query);
	
	 $unit_part=mysqli_query($this->link,"select partcode from partcode_master where   model_id Like '%".$old_s['model_id']."%' and status='1' and  	part_category='UNIT'" )or die(mysqli_error($this->link)); 
			$row_part = mysqli_fetch_array($unit_part);
			if($row_part['partcode']==""){
			 $flag = false;
			 $error_msg = "Partcode Not found in partcode master please check : " .$old_s['model_id']. ".";
			}
			
	
			$res_sfrbin = mysqli_query($this->link,"INSERT INTO sfr_bin set location_code='".$old_s['current_location']."', to_location='".$repair_central."', job_no='".$job_no."', imei='".$old_s['imei']."', model_id='".$old_s['model_id']."', partcode='".$old_s['partcode']."', qty='1', entry_date='".$updateDate."', status='4'");
		//// check if query is not executed
		if (!$res_sfrbin) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
		}
		
		$repairdetail = mysqli_query($this->link,"INSERT INTO repair_detail set location_code='".$old_s['current_location']."', repair_location='".$old_s['current_location']."', job_no='".$job_no."', model_id='".$old_s['model_id']."', partcode='".$old_s['partcode']."',rep_lvl = '1.00' ,status='4', repair_code = '".$repair_code."' , fault_code ='".$fault_code."',eng_id='".$eid."' ");

		//// check if query is not executed

		if (!$repairdetail) {

			 $flag = false;

			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";

		}

}

//////////////////////////////status WIP/////////////////////////////////////////////////////
if($repair_status==7){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',reason='".$reason."',pen_status='".$status."' ,app_rmk='Done By App' ".$mod_str." where job_no='".$job_no."'");
	
 	$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Job Pending',activity='Job Pending',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$result=mysqli_query($this->link,$query);

}

if($repair_status==11){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."' ,pen_status='6',app_rmk='Done By App' ".$mod_str." where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Closed Without Repair',activity='Closed Without Repair',outcome='Closed Without Repair',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$result=mysqli_query($this->link,$query);

}

//////////////////////////////status WIP/////////////////////////////////////////////////////
if($repair_status==50){
//echo "UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',app_reason='".$requestReason.",pen_status='".$status."' ,app_rmk='Done By App' where job_no='".$job_no."'";

	if($closeType=="Manufacturing date"){
	
	$doctype = 	"manufacter_date='".$valueCloseType."',";
	}else if($closeType=="Invoice"){
		$doctype = 	"invoice_no='".$valueCloseType."',";
	}

	else if($closeType=="Warranty Card"){
		$doctype = 	"warranty_card='".$valueCloseType."',";
	}else if($closeType=="Serial No"){
		$doctype = 	"imei='".$valueCloseType."',";
	}
	else if($closeType=="Nothing"){
		$doctype = 	"customer_satif='".$valueCloseType."',";
	}else{$doctype = 	"";}
	
	
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',app_reason='".$requestReason."',pen_status='".$status."' , ".$doctype." app_rmk='Done By App' ".$mod_str." where job_no='".$job_no."'");
	
	if (!$res_jobsheet) {
		 $flag = false;
		 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
	}
	
	 $query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Pending For Approval',activity='Pending For Approval',outcome='".$requestReason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

}

////////////////////////////////////////EP/
if($repair_status==5){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='".$status."',reason='".$reason."',app_rmk='Done By App' ".$mod_str." where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Job EP',activity='Estimate Pending',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$result=mysqli_query($this->link,$query);
	
	
		$res_maxcount = mysqli_query($this->link,"SELECT COUNT(eid) as maxcnt FROM estimate_master where location_code='".$old_s['current_location']."'");
		$row_maxcount = mysqli_fetch_assoc($res_maxcount);
		//// next estimate no.
		$next_no = $row_maxcount['maxcnt']+1;
		$estimate_no = $job_no."E".$next_no;
		///// get addressess for the parties
		//$location_addrs = getAnyDetails($_SESSION['asc_code'],"locationaddress","location_code","location_master",$link1);
		////// insert in estimate master
		$res_estimaster = mysqli_query($this->link,"INSERT INTO estimate_master set estimate_no='".$estimate_no."', estimate_date='".$updateDate."', location_code='".$old_s['current_location']."', from_address='".$location_addrs."', to_address='".$job_details['address']."', estimate_amount='".$_POST['ep_new_es']."' , entry_by='".$_SESSION['userid']."', entry_ip='".$_SERVER['REMOTE_ADDR']."', status='5',job_no='".$job_no."'");
		//// check if query is not executed
		if (!$res_estimaster) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
		}
		///// Insert in item data by picking each data row one by one
		
		/////initialize post array variables
	for($i=0; $i<count($partEP) ; $i++) {
			////// insert in repair details /////
	
			//echo $prd_code = $partUsedList[$i];
			 $prd_code =  $partEP[$i]->partid;
			///// get addressess for the parties
		//	$partdetail = getAnyDetails($ep_partarr[$k],"part_name","partcode","partcode_master",$link1); 
			//// insert in estimate data
			$res_estidata = mysqli_query($this->link,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='".$prd_code."', hsn_code='".$ep_hsncode[$k]."', part_name='".$partdetail."', basic_amount='".$ep_basicamt[$k]."', tax_per='".$ep_taxper[$k]."', tax_amt='".$ep_taxamt[$k]."' , tax_name='', total_amount='".$ep_totamt[$k]."',job_no='".$job_no."'");
			//// check if query is not executed
			if (!$res_estidata) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
			}
		}/////end foreach loop
		//// check if any service charge is applicable then we have to insert one more entry in estimate items
		$res_servcharge = mysqli_query($this->link,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='SERV001', hsn_code='".$_POST['ser_tax_hsn']."', part_name='Service Charge', basic_amount='".$_POST['ser_charge']."', tax_per='".$_POST['ser_tax_per']."', tax_amt='".$_POST['ser_tax_amt']."' , tax_name='', total_amount='".$_POST['total_ser_tax_amt']."',job_no='".$job_no."'");
		//// check if query is not executed
		if (!$res_servcharge) {
			 $flag = false;
			 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
		}

}

/////////////////////////////////////// PNA/////////////////////////////////////////////////////////
if($repair_status==3){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='".$status."',reason='".$reason."',app_rmk='Done By App' ".$mod_str." where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Part Not Available',activity='Part Not Available',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";




	$result=mysqli_query($this->link,$query);
	
	//echo $partUsedList[0]["partid"];
		for($i=0; $i<count($partPNA) ; $i++) {
			////// insert in repair details //////
	
			//echo $prd_code = $partUsedList[$i];
			 $prd_code =  $partPNA[$i]->partid;
	
			

$res_autopartreq = mysqli_query($this->link,"INSERT INTO auto_part_request set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."' , qty='1', status='3', request_date='".$today."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}

			$res_autopartreqeng = mysqli_query($this->link,"INSERT INTO part_demand set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."', qty='1', status='1', request_date='".$today."',eng_id='".$eid."'");
			//// check if query is not executed
			if (!$res_autopartreqeng) {
				 $flag = false;
				 $error_msg = "Error details2eng: " . mysqli_error($link1) . ".";
			}
			}

}
////////////////////////////////// installation/Demo Status///////////////////////////////////////////////////////
if($repair_status==48 || $repair_status==49){
	if($repair_status==48){
		$repair_type="Installation";
		$partUsedList= $partInstallationDone;
		}else{
		$repair_type="Demo";
		$partUsedList= $partDemoDone;
		}
			/////// replacement flag ///////////
			$replace_flag = 'N';
		
			for($i=0; $i<count($partUsedList) ; $i++) {
			
				$replace_flag = 'Y';
				////// insert in repair details/////
				$prd_code =  $partUsedList[$i]->partid;
				
				//echo "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['current_location']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='R0087', partcode='".$prd_code."', part_qty='1',close_date='".$cls_dt[0]."'"."<br><br>";
				
				$res_reapirdata = mysqli_query($this->link,"INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['current_location']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='R0087', partcode='".$prd_code."', part_qty='1',close_date='".$cls_dt[0]."'");
				//// check if query is not executed
				if (!$res_reapirdata) {
					 $flag = false;
					 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
				}
				
				///// entry in stock ledger				
				$inv_no = $job_no;
				$inv_date = $cls_dt[0];
				$itemcode = $prd_code;
				$from_party = $old_s['current_loction'];
				$to_party = $old_s['customer_name'];
				$stock_transfer = "OUT";
				$stock_type = "OK";
				$type_name = $repair_type;
				$action_taken = "Repair Done By App";
				$qty =  "1";
				$price = "";
				$create_by = $eid;
				$createdate = $cls_dt[0];
				$createtime = $cls_dt[1];
				$ip = $_SERVER['REMOTE_ADDR'];
				$errorflag = $flag;
				
				$result_00=mysqli_query($this->link,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'")or die(mysqli_error($this->link));
			
				//// check if query is not executed
				if (!$result_00) {
					 $flag = false;
					 echo "Error details SL0 : " . mysqli_error($this->link) . "";
				}

				
			
				//echo "UPDATE client_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and okqty >0"."<br><br>";
				
				$res_invt = mysqli_query($this->link,"UPDATE client_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}		
				
		
		}
			
			$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Job Repair Done',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
			
			//echo $query."<br><br>";

			$resulth=mysqli_query($this->link,$query);
			if (!$resulth) {
				 $flag = false;
				 $error_msg = "Error details2history: " . mysqli_error($link1) . ".";
			}
			
			/////////find close tat ////
			$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $cls_dt[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$close_tat = $end_date - $start_date;
			
			//////////// update into job_claim_appr //////////////////
			
			//echo "select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by "."<br><br>";
			
			$sql_rep=mysqli_query($this->link,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");
			
			while($row_max=mysqli_fetch_array($sql_rep)){
			
				//echo "insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$old_s['eng_id']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."',claim_tat='".$close_tat."', status = '".$repair_status."', job_count = '".$old_s['job_count']."', part_repl = '".$replace_flag."' "."<br><br>";
			
				$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$old_s['eng_id']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."',claim_tat='".$close_tat."', status = '".$repair_status."', job_count = '".$old_s['job_count']."', part_repl = '".$replace_flag."' ");
				//// check if query is not executed
				if (!$max_rep) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}	
			}
				

	//// check if query is not executed
	
	if($closeType=="Manufacturing date"){
	
	$doctype = 	"manufacter_date='".$valueCloseType."',";
		$warrenty=$old_s['warranty_status'];
	}else if($closeType=="Invoice"){
		$doctype = 	"invoice_no='".$valueCloseType."',";
			$warrenty=$old_s['warranty_status'];
	}

	else if($closeType=="Warranty Card"){
		$doctype = 	"warranty_card='".$valueCloseType."',";
			$warrenty=$old_s['warranty_status'];
	}
	else if($closeType=="Nothing"){
		$doctype = 	"customer_satif='".$valueCloseType."',";
		$warrenty='VOID';
	}else{$doctype = 	"";
		$warrenty=$old_s['warranty_status'];}
		
		//echo "UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."', ".$doctype." app_rmk='Done By App',doc_type='".$closeType."',warranty_status='".$warrenty."',imei='".$serialNo."', close_tat = '".$close_tat."' ".$mod_str."  where job_no='".$job_no."' "."<br><br>";

	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."', ".$doctype." app_rmk='Done By App',doc_type='".$closeType."',warranty_status='".$warrenty."',imei='".$serialNo."', close_tat = '".$close_tat."' ".$mod_str."  where job_no='".$job_no."' ");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($link1) . ".";
		}
	
	
	
}



/////////////////////////////////////////////////////Confirmation  CASE//////////////
if($repair_status==10){

		///// entry in call/job  history
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Call Confirmed',activity='Call Confirmed',outcome='Call Confirmed By customer',updated_by='".$eid."',  warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y'";
	$result=mysqli_query($this->link,$query);
			
	    	$sql_update = "UPDATE jobsheet_data set status ='10', sub_status ='10', hand_date ='".$con_dt[0]."',hand_time='".$con_dt[1]."',recipient_name='".$confirmedBy."',recipient_contact='".$contactNumber."',service_rmak='".$customerRemark."',rating='".$serviceRating."' ".$mod_str." where job_no ='".$job_no."' ";
    	$res_update=mysqli_query($this->link,$sql_update);
		//// check if query is not executed
		if (!$res_update) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}	

}

////////////////////////////////// Repair Done///////////////////////////////////////////////////////
if($repair_status==6){
	
		$repair_type="Repair Done";
		
			$rp_fg = 1;
			for($i=0; $i<count($repairList) ; $i++) {
				
				////// insert in repair details //////
				$prd_code =  $repairList[$i]->partcode;
				$symp_code =  $repairList[$i]->symp_code;
				$solutioncode =  $repairList[$i]->solutioncode;
				
				if($prd_code!=""){
					$rp_fg *= 0;
				}else{
					$rp_fg *= 1;
				}
				
			//echo "select rep_level,part_replace from repaircode_master where rep_code='".$solutioncode."'"."<br><br>";	
			
			 $sql_rep=mysqli_query($this->link,"select rep_level,part_replace from repaircode_master where rep_code='".$solutioncode."'")or die(mysqli_error($this->link)); 
			$rep_row = mysqli_fetch_array($sql_rep);
			
			//echo "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='".$solutioncode."', partcode='".$prd_code."', part_qty='1',fault_code='".$symp_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$cls_dt[0]."'"."<br><br>";
			
			$res_reapirdata = mysqli_query($this->link,"INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='".$solutioncode."', partcode='".$prd_code."', part_qty='1',fault_code='".$symp_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$cls_dt[0]."'");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error In repair Details table: " . mysqli_error($this->link) . ".";
			}
			
			///// entry in stock ledger
			$inv_no = $job_no;
			$inv_date = $cls_dt[0];
			$itemcode = $prd_code;
			$from_party = $old_s['current_loction'];
			$to_party = $old_s['customer_name'];
			$stock_transfer = "OUT";
			$stock_type = "OK";
			$type_name = $repair_type;
			$action_taken = "Repair Done By App";
			$qty =  "1";
			$price = "";
			$create_by = $eid;
			$createdate = $cls_dt[0];
			$createtime = $cls_dt[1];
			$ip = $_SERVER['REMOTE_ADDR'];
			$errorflag = $flag;
			
			//echo "insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'"."<br><br>";
			
			$result_00=mysqli_query($this->link,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'")or die(mysqli_error($this->link));
		
			//// check if query is not executed
			if (!$result_00) {
				 $flag = false;
				 echo "Error details SL0 : " . mysqli_error($this->link) . "";
			}
				

	//// check if query is not executed
	
	//echo "UPDATE client_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and okqty >0"."<br><br>";

 	$res_invt_mo = mysqli_query($this->link,"UPDATE client_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt_mo) {
					 $flag = false;
					 $error_msg = "Error detailsMount: " . mysqli_error($this->link) . ".";
				}
				
	
						
				/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($old_s['warranty_status']=='IN'){
								
					//echo "select faulty_part from partcode_master where partcode='".$prd_code."'"."<br><br>";			
								
					 $sql_part=mysqli_query($this->link,"select faulty_part from partcode_master where partcode='".$prd_code."'")or die(mysqli_error($this->link)); 
			$rep_part = mysqli_fetch_array($sql_part);
				if($rep_part["faulty_part"]=='Y'){
				
						//echo "UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$eid."'"."<br><br>";
				
						$res_faulty_user = mysqli_query($this->link,"UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$eid."'");
				//// check if query is not executed
				if (!$res_faulty_user) {
					 $flag = false;
					 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
				}
				
				//echo "INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_s['imei']."',from_location='".$old_s['current_location']."', partcode='".$prd_code."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$eid."',eng_status='1'"."<br><br>";
				
				$res_p2cdata = mysqli_query($this->link,"INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_s['imei']."',from_location='".$old_s['current_location']."', partcode='".$prd_code."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$eid."',eng_status='1'");
								if (!$res_p2cdata) {
				 $flag = false;
				 $error_msg = "Error details21: " . mysqli_error($this->link) . ".";
			}
				}	}	
					
				
		
		}
		
			
	if($closeType=="Manufacturing date"){
	
	$doctype = 	"manufacter_date='".$valueCloseType."',";
		$warrenty=$old_s['warranty_status'];
	}else if($closeType=="Invoice"){
		$doctype = 	"invoice_no='".$valueCloseType."',";
			$warrenty=$old_s['warranty_status'];
	}

	else if($closeType=="Warranty Card"){
		$doctype = 	"warranty_card='".$valueCloseType."',";
			$warrenty=$old_s['warranty_status'];
	}
	else if($closeType=="Nothing"){
		$doctype = 	"customer_satif='".$valueCloseType."',";
		$warrenty='VOID';
	}else{$doctype = 	"";
	$warrenty=$old_s['warranty_status'];
	
	
	}
	
	/////////find close tat ////
	$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $cls_dt[0]);
	$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	$close_tat = $end_date - $start_date;

	//echo "UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."',".$doctype." app_rmk='Done By App',doc_type='".$closeType."',warranty_status='".$warrenty."',imei='".$serialNo."', close_tat = '".$close_tat."' ".$mod_str." where job_no='".$job_no."'"."<br><br>";

	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."',".$doctype." app_rmk='Done By App',doc_type='".$closeType."',warranty_status='".$warrenty."',imei='".$serialNo."', close_tat = '".$close_tat."' ".$mod_str." where job_no='".$job_no."'");
	
	if (!$res_jobsheet) {
				 $flag = false;
				 $error_msg = "Error detailsin jobsheet1: " . mysqli_error($this->link) . ".";
			}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Job Repair Done',activity='Job Repair Done',outcome='Job Repair Done',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";
		
		//echo $query."<br><br>";

	$callhistory=mysqli_query($this->link,$query);
	if (!$callhistory) {
				 $flag = false;
				 $error_msg = "Error detailsin call history: " . mysqli_error($this->link) . ".";
			}
			
		//echo "select max(rep_lvl) as b from repair_detail where job_no ='".$job_no."' and repair_location='".$old_s['current_location']."'"."<br><br>";	
	
		$sql_lvel=mysqli_query($this->link,"select max(rep_lvl) as b from repair_detail where job_no ='".$job_no."' and repair_location='".$old_s['current_location']."'");

		$row_level=mysqli_fetch_array($sql_lvel);
		
		//echo "select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by "."<br><br>";
		
		$sql_rep=mysqli_query($this->link,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");
		
		/////// replacement flag ///////////
		if($rp_fg == 1){
			$replace_flag = 'N';
		}else{
			$replace_flag = 'Y';
		}
		
		while($row_max=mysqli_fetch_array($sql_rep)){
		
			//echo "insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$old_s['eng_id']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."',claim_tat='".$close_tat."', status = '".$repair_status."', job_count = '".$old_s['job_count']."', part_repl = '".$replace_flag."' "."<br><br>";
		
			$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$old_s['eng_id']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."',claim_tat='".$close_tat."', status = '".$repair_status."', job_count = '".$old_s['job_count']."', part_repl = '".$replace_flag."' ");
			//// check if query is not executed
			if (!$max_rep){
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
			}	
		}
		
}

////////////////////////////////// Replacement Done///////////////////////////////////////////////////////
if($repair_status==8){
	
		$repair_type="Job Replacement";
		$solutioncode = "Replacement";
		$prd_code=$replacedBy;
		
			//echo "select rep_level,part_replace from repaircode_master where rep_code='".$solutioncode."'"."<br><br>";
				
			$sql_rep=mysqli_query($this->link,"select rep_level,part_replace from repaircode_master where rep_code='".$solutioncode."'")or die(mysqli_error($this->link)); 
			$rep_row = mysqli_fetch_array($sql_rep);
			
			//echo "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='".$repair_code."', partcode='".$prd_code."', part_qty='1',fault_code='".$fault_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$cls_dt[0]."',replace_imei1='".$tag."'"."<br><br>";
			
			$res_reapirdata = mysqli_query($this->link,"INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='".$repair_code."', partcode='".$prd_code."', part_qty='1',fault_code='".$fault_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$cls_dt[0]."',replace_imei1='".$tag."'");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error detailsrepair details: " . mysqli_error($this->link) . ".";
			}
			
			///// entry in stock ledger
			$inv_no = $job_no;
			$inv_date = $cls_dt[0];
			$itemcode = $prd_code;
			$from_party = $old_s['current_loction'];
			$to_party = $old_s['customer_name'];
			$stock_transfer = "OUT";
			$stock_type = "OK";
			$type_name = $repair_type;
			$action_taken = "Repair Done By App";
			$qty =  "1";
			$price = "";
			$create_by = $eid;
			$createdate = $cls_dt[0];
			$createtime = $cls_dt[1];
			$ip = $_SERVER['REMOTE_ADDR'];
			$errorflag = $flag;
			
			//echo "insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'"."<br><br>";
			
			$result_00=mysqli_query($this->link,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'")or die(mysqli_error($this->link));
		
			//// check if query is not executed
			if (!$result_00) {
				 $flag = false;
				 echo "Error details SL0 : " . mysqli_error($this->link) . "";
			}
				

	//// check if query is not executed
	
		if($closeType=="Manufacturing date"){
	
	$doctype = 	"manufacter_date='".$valueCloseType."',";
	$warrenty=$old_s['warranty_status'];
	}else if($closeType=="Invoice"){
		$doctype = 	"invoice_no='".$valueCloseType."',";
			$warrenty=$old_s['warranty_status'];
	}

	else if($closeType=="Warranty Card"){
		$doctype = 	"warranty_card='".$valueCloseType."',";
			$warrenty=$old_s['warranty_status'];
	}
	else if($closeType=="Nothing"){
		$doctype = 	"customer_satif='".$valueCloseType."',";
		$warrenty="VOID";
	}
	
	else{$doctype = 	"";
	
	
	$warrenty=$old_s['warranty_status'];
	}
	
	//echo "UPDATE client_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and okqty >0"."<br><br>";

 	$res_invt_mo = mysqli_query($this->link,"UPDATE client_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt_mo) {
					 $flag = false;
					 $error_msg = "Error detailsMount: " . mysqli_error($this->link) . ".";
				}
				
			
						
				/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($old_s['warranty_status']=='IN'){
				
					//echo "select faulty_part from partcode_master where partcode='".$prd_code."'"."<br><br>";
									
					 $sql_part=mysqli_query($this->link,"select faulty_part from partcode_master where partcode='".$prd_code."'")or die(mysqli_error($this->link)); 
			$rep_part = mysqli_fetch_array($sql_part);
				if($rep_part["faulty_part"]=='Y'){
				
						//echo "UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$eid."'"."<br><br>";
				
						$res_faulty_user = mysqli_query($this->link,"UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$eid."'");
				//// check if query is not executed
				if (!$res_faulty_user) {
					 $flag = false;
					 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
				}
				
					//echo "INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_s['imei']."',from_location='".$old_s['current_location']."', partcode='".$prd_code."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$eid."',eng_status='1'"."<br><br>";
				
					$res_p2cdata = mysqli_query($this->link,"INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_s['imei']."',from_location='".$old_s['current_location']."', partcode='".$prd_code."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$eid."',eng_status='1'");
								if (!$res_p2cdata) {
				 $flag = false;
				 $error_msg = "Error details21: " . mysqli_error($this->link) . ".";
			}
				}	}	
				
					//echo "update imei_details_eng set job_no ='".$job_no."',dis_date='".$today."',status ='3' where (imei1='".$tag."' ) and location_code='".$old_s['current_location']."' and stock_type='ok' and status ='1'"."<br><br>";
				
					$res_p2chset = mysqli_query($this->link,"update imei_details_eng set job_no ='".$job_no."',dis_date='".$today."',status ='3' where (imei1='".$tag."' ) and location_code='".$old_s['current_location']."' and stock_type='ok' and status ='1'");
											
								if (!$res_p2chset) {
				 $flag = false;
				 $error_msg = "Error detailsImei Details: " . mysqli_error($this->link) . ".";
		
					}
					
			/////////find close tat ////
			$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $cls_dt[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$close_tat = $end_date - $start_date;
			
			//echo "UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."',".$doctype." app_rmk='Done By App',doc_type='".$closeType."',warranty_status='".$warrenty."',imei='".$serialNo."', close_tat = '".$close_tat."' ".$mod_str." where job_no='".$job_no."'"."<br><br>";
		
			$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."',".$doctype." app_rmk='Done By App',doc_type='".$closeType."',warranty_status='".$warrenty."',imei='".$serialNo."', close_tat = '".$close_tat."' ".$mod_str." where job_no='".$job_no."'");
	
			if (!$res_jobsheet) {
				 $flag = false;
				 $error_msg = "Error details21cjobsheet: " . mysqli_error($this->link) . ".";
			}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='Job Replacement',activity='Replacement',outcome='Replacement',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";
		
		//echo $query."<br><br>";

		$resulthistory=mysqli_query($this->link,$query);
	
		if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
			
		//echo "select max(rep_lvl) as b from repair_detail where job_no ='".$job_no."' and repair_location='".$old_s['current_location']."'"."<br><br>";	
			
		$sql_lvel=mysqli_query($this->link,"select max(rep_lvl) as b from repair_detail where job_no ='".$job_no."' and repair_location='".$old_s['current_location']."'");

$row_level=mysqli_fetch_array($sql_lvel);
/////// replacement flag ///////////
$replace_flag = 'N';

//echo "select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by "."<br><br>";

$sql_rep=mysqli_query($this->link,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");

while($row_max=mysqli_fetch_array($sql_rep)){

	//echo "insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$old_s['eng_id']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."',claim_tat='".$close_tat."', status = '".$repair_status."', job_count = '".$old_s['job_count']."', part_repl = '".$replace_flag."' "."<br><br>";

	$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$old_s['eng_id']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."',claim_tat='".$close_tat."', status = '".$repair_status."', job_count = '".$old_s['job_count']."', part_repl = '".$replace_flag."' ");
	//// check if query is not executed
	if (!$max_rep) {
		 $flag = false;
		 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
	}	
}

} ///////// end of replacement done /////////

}	

		$res_product= mysqli_query($this->link,"UPDATE product_registered set serial_no ='".$serialNo."' where job_no='".$job_no."'");
	
	//// check if query is not executed
	if (!$res_product) {
		 $flag = false;
		 $error_msg = "Error detailsProducgt: " . mysqli_error($this->link) . ".";
	}
if ($flag) {
mysqli_commit($this->link);    
        
return 1;         
} else {
return 0;
}  
   
}

/////////////////////////////////////////// End of StoreUser function /////////////////////////////////////////////////////////
 
  public function storeHistory($job_no,$status,$updateDate,$repair_status,$eid,$address,$branch_code) { 

$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
//////////////////////////////status Refer to Centeral Workshop/////////////////////////////////////////////////////
if($repair_status==4){

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='Job Esclated',activity='Job Esclated',outcome='Job Esclated',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


}

//////////////////////////////status WIP/////////////////////////////////////////////////////
if($repair_status==7){
	

	
			$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='Job Pending',activity='Job Pending',outcome='Job Pending',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}

}

//////////////////////////////status WIP/////////////////////////////////////////////////////
if($repair_status==50){


				$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='Pending For Approval',activity='Pending For Approval',outcome='Pending For Approval',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."' ,app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


}

////////////////////////////////////////EP/
if($repair_status==5){



				$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='Estimate Pending',activity='Estimate Pending',outcome='Estimate Pending',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
}

/////////////////////////////////////// PNA/////////////////////////////////////////////////////////
if($repair_status==3){



$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='Part Not Available',activity='Part Not Available',outcome='Part Not Available',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


}
////////////////////////////////// installation/Demo Status///////////////////////////////////////////////////////
if($repair_status==48 || $repair_status==49){
	if($repair_status==48){
		$repair_type="Installation Done";
		}else{
		$repair_type="Demo Done";
		}
		
			
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_type."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


	
}



/////////////////////////////////////////////////////Confirmation  CASE//////////////
if($repair_status==10){

		///// entry in call/job  history
	
				$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='Call Confirmed',activity='Call Confirmed',outcome='Call Confirmed',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
		

		}

////////////////////////////////// Repair Done///////////////////////////////////////////////////////
if($repair_status==6){
	
		$repair_type="Repair Done";


		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_type."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
		


}

////////////////////////////////// Replacement Done///////////////////////////////////////////////////////
if($repair_status==8){
	
		$repair_type="Job Replacement";


						

		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_type."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
	
}

if ($flag) {
mysqli_commit($this->link);    
        
return $job_no."~".$repair_status."~".$updateDate;       
} else {
return $error_msg;
}  


  
  }
 ////////////////////////////////////////////Count pending Job
 
  /////////////////Complaints Data For Eng Performance/////
  public function getComplaintsData() {  
   // echo "SELECT  COUNT( CASE WHEN status='2' THEN job_no END) as assignjob ,COUNT(CASE WHEN status='6' THEN job_no END) as closejob FROM jobsheet_data where  eng_id='".$_REQUEST['eid']."'";
	 $result = mysqli_query($this->link,"SELECT  COUNT( CASE WHEN pen_status ='2' THEN job_no END) as assignjob ,COUNT(CASE WHEN pen_status ='6' THEN job_no END) as closejob FROM jobsheet_data where   eng_id='".$_REQUEST['eid']."'")  or die(mysqli_error($this->link)); 
	 $row_count=mysqli_fetch_array($result);
	     
  $seven_days=mysqli_fetch_array(mysqli_query($this->link,"select count(job_no) as a,eng_id from jobsheet_data where  datediff(sysdate(),open_date) >7  and pen_status !='6' and eng_id='$_REQUEST[eid]'"));
          
 return $row_count['assignjob']."~".$row_count['closejob']."~".$seven_days[a];     
 }
}