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
//////////get main url to reach the server folder////////////////
	public function getAttachmentUrl() {
		$url = "https://beta.okaya.cancrm.in/API/";
		return $url;
	}

function getServerdatetime(){
	date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");

$time=date("H:i:s");
return $today."^".$time;
}


 //////////////////////////////////////////////////////////////
 public function getmodel(){
 
 $result = mysqli_query($this->link,"SELECT ser_charge,model,model_id,product_id,brand_id,wp FROM model_master WHERE status ='1'") or die(mysqli_error($this->link));         
 return $result;     
 }


 //////////////Product Master//////////
/* function getProduct($ser_sync_time){
	 $result=mysqli_query($this->link,"select * from product_master where status='1' and autoupdatedate >'".$ser_sync_time."'");
	 return $result;
 }*/
 function getProduct($user_type,$activity,$userid){
	 
	if($user_type=='SSP' && $activity=='Login'){
	//if($user_type=='SSP' && ($activity=='Login' || $activity=='')){	
		//$result=mysqli_query($this->link,"select * from product_master where product_id in ('4','5','9')");
		$result=mysqli_query($this->link,"select * from product_master where product_id in ('4')");
	}
	else{
	 $result=mysqli_query($this->link,"select * from product_master where status='1' order by product_name");
	}
	 return $result;
 }	
	
/////////////// Complaints Master /////////////////
	public function getJobMaster1() { 
		if(!empty($_REQUEST['job_no'])){
	$result = mysqli_query($this->link,"SELECT * FROM jobsheet_data where (job_no='".$_REQUEST['job_no']."' or repl_appr_no='".$_REQUEST['job_no']."' or imei='".$_REQUEST['job_no']."')")or die(mysqli_error($this->link));         }
	 else {
		 $result=false;
	 }
	return $result;     
	}	
	
/////////////////////// Voc MASTER //////////////////////
	public function getStatusNew($type,$prodid){
		if($type=="Installation"){
			$result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('Installation Done','Request For Approval') order by display_status")  or die(mysqli_error($this->link));
		}else{
					

			if($prodid != '1' && $prodid != '2' && $prodid != '4'){
				//print_r($prodid);exit;
				$result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('PNA','Handover','WIP','RWR','Request For Approval') order by display_status")  or die(mysqli_error($this->link));
			}else{
				$result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('PNA','Handover','WIP','Request For Approval','RWR') order by display_status")  or die(mysqli_error($this->link));
			}		
		}
		return $result;
	}	
///// get latest login details
	public function getlastlog($userid){
		$result = mysqli_query($this->link,"SELECT ip FROM app_login_check WHERE userid='".$userid."' ORDER BY id DESC") or die(mysqli_error($this->link));
	 	return $result;
	}
///// insert in app login
public function appLogin($userid,$deviceid,$imei){
	////////insert in app login
	$applog_sql = "INSERT INTO app_login_check SET userid = '".$userid."', otp = '', otp_gentime = '', phone_no = '', date ='".date("Y-m-d")."', browser_id  = '".$deviceid."', otp_expiretime = '', ip  = '".$imei."'";
	$applog_res = mysqli_query($this->link, $applog_sql);
}

 /////////////////////// Voc MASTER For get detail //////////////////////
 public function getVoc() {
	 $result = mysqli_query($this->link,"SELECT id,voc_desc,voc_code  FROM voc_master  where status='1'") or die(mysqli_error($this->link));         
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
 public function getFault($product) {
	 $result = mysqli_query($this->link,"SELECT * FROM defect_master where status='1'  and  mapped_product like '%".$product."%' ")  or die(mysqli_error($this->link));
	 return $result;
 }
    /////////////////////// Voc MASTER //////////////////////
 public function getStatus($type) {
 if($type=="Installation"){
 	 $result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('Installation Done','Request For Approval') order by display_status")  or die(mysqli_error($this->link));
	 }else{
	  $result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('EP','PNA','Handover','WIP','Request For Approval','Replacement Request') order by display_status")  or die(mysqli_error($this->link));
	 
	 }
	 return $result;
 }
 /////////////////////// Part MASTER //////////////////////
 public function getPart($model) {
// echo "SELECT partcode,part_name,product_id,brand_id,part_category,model_id,vendor_partcode,customer_price,status FROM partcode_master where status='1' and find_in_set($model,model_id) <> 0";
	 $result = mysqli_query($this->link, "SELECT partcode,part_name,product_id,brand_id,part_category,model_id,vendor_partcode,customer_price,status,alternate_partcode FROM partcode_master where status='1' and model_id LIKE '%$model%'") or die(mysqli_error($this->link));
	 return $result;
 }
   /////////////////////// Part MASTER BASED ON PRODUCT ID //////////////////////
 public function getPart_product() {
	 $result = mysqli_query( $this->link,"SELECT partcode,part_name,product_id,brand_id,part_category,model_id,vendor_partcode,customer_price,status FROM partcode_master where status='1'  and product_id='".$_REQUEST['p_id']."'")or die(mysqli_error($this->link));
	 return $result;
 }
 //////////////////////////////////////////Get Serial Number Details//////////////////////////////////////////////////////
 function getserialmodel($serial_no){
$result=mysqli_query( $this->link,"select model_id,serial_no from product_registered where serial_no='".$_REQUEST['serial_no']."'")or die(mysqli_error($this->link));
return $result;
}
 ////////////////////////////complaint_master/////////////////////////////////////////////////////////////////
 public function getComplaintsMaster($eid) {  
 if(!empty($_REQUEST['from_date'])){
 if($_REQUEST['from_date']!='' && $_REQUEST['to_date']!=''){
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
		}
 }
		else{
		$begin = date('Y-m-d', strtotime("-180 days"));
		$end = date('Y-m-d');	
		}  
	// echo "SELECT imei,warranty_status,model,model_id,product_id,status,pen_status,dop,customer_id,reason,close_rmk,remark,call_for,close_date,app_reason,doa_approval,recipient_name,recipient_contact,service_rmak,rating,hand_date,hand_time,job_no,open_date,cust_problem,brand_id,customer_name,contact_no,product_cat,alternate_no,address,city_id,state_id,pincode,installation_date,app_reason FROM jobsheet_data where eng_id='".$eid."' and call_for!='Workshop'  and status!='12' and open_date BETWEEN '$begin' and '$end'  ";exit;
	
 $result = mysqli_query($this->link,"SELECT imei,warranty_status,model,model_id,product_id,status,pen_status,dop,customer_id,reason,close_rmk,remark,call_for,close_date,app_reason,doa_approval,recipient_name,recipient_contact,service_rmak,rating,hand_date,hand_time,job_no,open_date,cust_problem,brand_id,customer_name,contact_no,product_cat,alternate_no,address,city_id,state_id,pincode,installation_date,app_reason FROM jobsheet_data where eng_id='".$eid."' and call_for!='Workshop'  and status!='12' and open_date BETWEEN '$begin' and '$end'  ") or die(mysqli_error($this->link));     
 return $result;     
 }
  ////////////////////////////Stock Status/////////////////////////////////////////////////////////////////
 public function getstockeng($eid) {         
 $result = mysqli_query($this->link,"SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' ") or die(mysqli_error($this->link));     
 return $result;     
 }
 
   /////////////// Customer Master Data ///////////////////////////
 public function getrequestreason($ser_sync_time) {         
 $result =mysqli_query($this->link,"SELECT * FROM request_reason where  1")or die(mysqli_error($this->link));          
 return $result;     
 }
 
 
  public function getwarrantyreason() {         
 $result =mysqli_query($this->link,"SELECT * FROM warranty_reason where status='1'")or die(mysqli_error($this->link));          
 return $result;     
 }
  public function getreplreason($ser_sync_time) {         
 $result =mysqli_query($this->link,"SELECT * FROM rep_reason where  update_date >'".$ser_sync_time."'")or die(mysqli_error($this->link));          
 return $result;     
 }
  /////////////// Solution Master /////////////////////
 public function getSolutionMaster($product) {         
 $result =mysqli_query($this->link,"SELECT * FROM repaircode_master WHERE status ='1' and mapped_product like '%".$product."%'")or die(mysqli_error($this->link));   
 return $result;     
 } 
 ///////////Attendence Data////
function micAttendence_report($from,$to,$eng_id){
 
 $result=mysqli_query($this->link,"select * from mic_attendence_data where insert_date>='".$from."' and insert_date<='".$to."' and  user_id='".$eng_id."'" )or die(mysqli_error($this->link)); 
 return $result;
 
 }
 ///// generic function

public function getAnyDetails($keyid,$fields,$lookupname,$tbname){
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
        if($_REQUEST['token']!=''){
          $token= $_REQUEST['token'];
		
          $sql_chk=mysqli_fetch_array(mysqli_query($this->link,"select userloginid from locationuser_master where device_token='".$token."'"));
		if($sql_chk['userloginid'] != $_REQUEST['eid']){
			
		mysqli_query($this->link,"update locationuser_master set device_token='' where device_token='".$token."'");	
		}
			
		mysqli_query($this->link,"update locationuser_master set device_token='".$token."' where userloginid='".$_REQUEST['eid']."'");		
      
      }    
  $result = mysqli_query($this->link,"SELECT * FROM locationuser_master where userloginid='".$_REQUEST['eid']."' and pwd=BINARY  '".$_REQUEST['password']."' and statusid='1'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
 //////////////////////////////////////Branch Details//////////////////////////////
  public function getlocationstate($loc) { 
       
  $result = mysqli_query($this->link,"SELECT stateid,cityid FROM location_master where location_code='".$loc."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
   ///////////////////// Repair Details ///////////////////////   
 public function getRepairDetails($job) { 
       
  $result = mysqli_query($this->link,"SELECT partcode,fault_code,repair_code FROM repair_detail where job_no='".$job."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
#### Function for Battery Brand Name in APP
public function battery_brand() {
		$result = mysqli_query($this->link,"SELECT id,brand FROM battery_brand WHERE status = '1' order by brand")or die(mysqli_error($this->link));
		return $result;
	}	
	
	/////////////////////////////BTR Before Charging/////////////
public function Complaint_FirstBTR($c1,$c2,$c3,$c4,$c5,$c6,$ocv,$eng_id,$job_no){
	$b = array();
	date_default_timezone_set('Asia/Kolkata');
	$today=date('Y-m-d');
	$today_time=date("H:i:s");
	$todayt=date("Ymd");
	$ip=$_SERVER['REMOTE_ADDR'];
	
	 $link1=$this->link;
	 
	 		//////////////////////////////customer details//////////////////////////////////////////
	$sql_btr="select id from initial_btr_data where job_no='".$job_no."'";
	$result_btr=mysqli_query($link1,$sql_btr);
	if ((mysqli_num_rows($result_btr)==0) ){	
	
	$btr_add="INSERT INTO initial_btr_data set job_no='".$job_no."',c1='".$c1."',c2='".$c2."',c3='".$c3."',c4='".$c4."',c5='".$c5."',c6='".$c6."',ocv='".$ocv."',eng_id='".$eng_id."',entry_date='".$today."',entry_time='".$today_time."',ip='".$ip."'";
	
	$res_addt=mysqli_query($link1,$btr_add); 
		
		$b["error_code"]=200;
	    $b["error_msg"]='Initial Reading Submitted';
		$b['initial_btr']='D';
		$b['final_btr']='P';
	}
	else{
		
		$b["error_code"]=201;
	    $b["error_msg"]='Initial Reading Already Submitted';
		$b['initial_btr']='D';
		$b['final_btr']='P';
		
	}
	 
	return $b; 
}
	
/////////////////////////////BTR After Charging/////////////
public function Complaint_FinalBTR($c1,$c2,$c3,$c4,$c5,$c6,$toc,$charging_hour,$bkp_load,$bkp_time,$eng_id,$job_no,$ocv,$use_load){
	$b = array();
	date_default_timezone_set('Asia/Kolkata');
	$today=date('Y-m-d');
	$today_time=date("H:i:s");
	$todayt=date("Ymd");
	$ip=$_SERVER['REMOTE_ADDR'];
	
	 $link1=$this->link;
	 
	 		//////////////////////////////customer details//////////////////////////////////////////
	$sql_btr="select id from final_btr_data where job_no='".$job_no."'";
	$result_btr=mysqli_query($link1,$sql_btr);
	if ((mysqli_num_rows($result_btr)==0) ){	
	
	$btr_add="INSERT INTO final_btr_data set job_no='".$job_no."',c1='".$c1."',c2='".$c2."',c3='".$c3."',c4='".$c4."',c5='".$c5."',c6='".$c6."',toc='".$toc."',charging_hour='".$charging_hour."',backup_load='".$bkp_load."',backup_time='".$bkp_time."',eng_id='".$eng_id."',entry_date='".$today."',entry_time='".$today_time."',ip='".$ip."',ocv='".$ocv."',use_load='".$use_load."'";
	
	$res_addt=mysqli_query($link1,$btr_add); 
		
		$b["error_code"]=200;
	    $b["error_msg"]='Final Reading Submitted';
		$b['initial_btr']='D';
		$b['final_btr']='D';
	}
	else{
		
		$b["error_code"]=201;
	    $b["error_msg"]='Final Reading Already Submitted';
		$b['initial_btr']='D';
		$b['final_btr']='D';
		
	}
	 
	return $b; 
}
#### Function for Alternate Partcode in APP
public function getAlter_Partcode($partcode) {
		$result = mysqli_fetch_array(mysqli_query($this->link,"SELECT partcode,alternate_partcode FROM partcode_master WHERE partcode = '".$partcode."'"))or die(mysqli_error($this->link));
	print_r($result);
		return $result;
	}	
    ///////////////////// Repair Details ///////////////////////   
 public function getPNADetails($job) { 
       
  $result = mysqli_query($this->link,"SELECT partcode FROM auto_part_request where job_no='".$job."'") or die(mysqli_error($this->link));  
 
 return $result;     
 } 
 
 

  public function getEPDetails($job) { 
       
  $result = mysqli_query($this->link,"SELECT partcode FROM estimate_items where job_no='".$job."'") or die(mysqli_error($this->link));  
 
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
 public function getclosedReasonMaster($type) {         
 $result = mysqli_query($this->link,"SELECT * FROM close_reason_master WHERE status = '1' and  type='".$type."'") or die(mysqli_error($this->link));
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
	 $in_currtime=date("H:i:s",$in_datetime);
     $out_currtime=date("H:i:s",$out_datetime);
$chk=mysqli_query($this->link,"select id from mic_attendence_data where user_id='$ei' and insert_date='$insert_date'");
$chk2=mysqli_fetch_array($chk);
if(mysqli_num_rows($chk)<=0 && $status_in!=''){
	$result=mysqli_query($this->link,"insert into mic_attendence_data set longitude_in='$longitu_in',latitude_in='$latitu_in',longitude_out='$longitu_out',latitude_out='$latitu_out',user_id='$ei',status_in='$status_in',in_datetime='$in_datetime',status_out='$status_out',out_datetime='$out_datetime',address_in='$address_in',address_out='$address_out',insert_date='$insert_date',image_In='$image_in'") or die(mysqli_error($this->link)); 
	$chk_punch_qry=mysqli_query($this->link,"select id from user_daily_track where user_id='".$ei."' and entry_date='".$insert_date."' and activity = 'PUNCH IN' order by id DESC limit 0,1 ");
	if(mysqli_num_rows($chk_punch_qry) > 0){
		$result1=mysqli_query($this->link,"update user_daily_track set activity = 'PUNCH IN', entry_date = '".$insert_date."', entry_time = '".$in_currtime."', longitude = '".$longitu_in."', latitude = '".$latitu_in."', address = '".$address_in."' where user_id = '".$ei."' and entry_date = '".$insert_date."' and activity = 'PUNCH IN' ") or die(mysqli_error($this->link));  
	}else{
		$result1=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$ei."', activity = 'PUNCH IN', entry_date = '".$insert_date."', entry_time = '".$in_currtime."', longitude = '".$longitu_in."', latitude = '".$latitu_in."', address = '".$address_in."', ref_no = '".$ei."' ") or die(mysqli_error($this->link));  
	}
	
	
}else if(mysqli_num_rows($chk)>0 && $chk2['status_out']==''){
	$result=mysqli_query($this->link,"update mic_attendence_data set status_out='$status_out',out_datetime='$out_datetime',address_out='$address_out',longitude_out='$longitu_out',latitude_out='$latitu_out',image_Out='$image_out'  where id='$chk2[id]'");
	$chk_punch_qry1=mysqli_query($this->link,"select id from user_daily_track where user_id='".$ei."' and entry_date='".$insert_date."' and activity = 'PUNCH OUT' order by id DESC limit 0,1 ");
	if(mysqli_num_rows($chk_punch_qry1) > 0){
		$result1=mysqli_query($this->link,"update user_daily_track set activity = 'PUNCH OUT', entry_date = '".$insert_date."', entry_time = '".$out_currtime."', longitude = '".$longitu_out."', latitude = '".$latitu_out."', address = '".$address_out."' where user_id = '".$ei."' and entry_date = '".$insert_date."' and activity = 'PUNCH OUT' ") or die(mysqli_error($this->link));  
	}else{
		$result1=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$ei."', activity = 'PUNCH OUT', entry_date = '".$insert_date."', entry_time = '".$out_currtime."', longitude = '".$longitu_out."', latitude = '".$latitu_out."', address = '".$address_out."', ref_no = '".$ei."' ") or die(mysqli_error($this->link));
	}
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

function stockLedger($inv_no,$inv_date,$itemcode,$from_party,$to_party,$stock_transfer,$stock_type,$type_name,$action_taken,$qty,$price,$create_by,$createdate,$createtime,$ip,$link1,$errorflag){

	$flag=$errorflag;
	

    $result=mysqli_query($this->link,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'");

	//// check if query is not executed

    if (!$result) {

	     $flag = false;

         echo "Error detailsSL: " . mysqli_error($this->link) . "";

	}

	return $flag;

}
/////////////////////////////////////////////////////update image against Job/////////////////////////////////////////////////
 public function storeImageJob($job_no,$fileName1,$fileName2,$fileName3,$fileName4) { 
 $today=date("Y-m-d");
 $folder1="app_image";
 $imag_url="../".$folder1."/".$fileName1;
 $imag_url2="../".$folder1."/".$fileName2;
 $imag_url3="../".$folder1."/".$fileName3;
  $imag_url4="../".$folder1."/".$fileName4;
 $res_jobsheet = mysqli_query($this->link,"insert into image_upload_details set img_url='".$imag_url."',img_url1 ='".$imag_url2."',img_url2='".$imag_url3."',img_url3='".$imag_url4."',job_no='".$job_no."',activity='Image Upload by app',upload_date='".$today."'");
 if ($res_jobsheet) {             
return true;         
}  else {                 
 // For other errors                 
 return false;             
                   
 }
 
 }
 public function storeImageJob2($job_no,$fileName1,$fileName2,$fileName3,$fileName4,$fileName5,$fileName6,$fileName7) { 
 $today=date("Y-m-d");
 $res_jobsheet = mysqli_query($this->link,"insert into image_upload_details set img_url='".$fileName1."',img_url1 ='".$fileName2."',img_url2='".$fileName3."',img_url3='".$fileName4."',img_url4='".$fileName5."',img_url5='".$fileName6."',img_url6='".$fileName7."',job_no='".$job_no."',activity='Image Upload by app',upload_date='".$today."'");
 if ($res_jobsheet) {             
return true;         
}  else {                 
 // For other errors                 
 return false;             
                   
 }
 
 }
 /////////////////////////////////////////////////////update image against Job/////////////////////////////////////////////////
 public function storedigitalImageJob($job_no,$fileName1) { 
 $today=date("Y-m-d");
 $folder1="app_image";
 $imag_url="../".$folder1."/".$fileName1;

 $res_jobsheet = mysqli_query($this->link,"insert into image_upload_details set img_url='".$imag_url."',job_no='".$job_no."',activity='Image Upload by app',upload_date='".$today."'");
 if ($res_jobsheet) {             
return true;         
}  else {                 
 // For other errors                 
 return false;             
                   
 }
 
 }
 
 public function getPaymentList(){
	$pay=mysqli_query($this->link,"select * from payment_master where status='Active'") or die(mysqli_error());
	return $pay;
}  
 ////////////// fifo detail updation function ////////////
public function filo_details_asp($location_code,$partcode,$qty){
	$res_challan = mysqli_query($this->link, "SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$location_code."' and partcode='".$partcode."' and okqty != fifi_ty ") or die(mysqli_error());
	$row_challan = mysqli_fetch_assoc($res_challan);
			
	return $return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
}
////////// function to calculate day difference between two dates //////////
public function daysDifference($endDate, $beginDate){
	$date_parts1=explode("-", $beginDate); $date_parts2=explode("-", $endDate);
	$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	return $end_date - $start_date;
}
/////// calculate agging /////
public function getAgingData(){
	/////// billing product item details /////	
	$ag_count = 0;	
	$aging_close_tat = "";
	$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$jobno."' and activity = 'Job PNA' "));
	$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$jobno."' and activity = 'PNA Part Received' "));
	$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
	$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
	if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
		$ag_count = daysDifference($pna_rec_info[0],$pna_rais_info[0]);
	}else{
		$ag_count = 0;	
	}
	if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
		if($close_tat!=""){
			$aging_close_tat = $close_tat.",".$ag_count;
		}else{
			$aging_close_tat = "";
		}
	}
	return $ag_count."~".$aging_close_tat;
}
/////////count holidays//////
public function holidays($open_date,$weak_day,$loc_state){
	$today=date('Y-m-d');
	$holidays=0;
	$sql_h="select date from holidays where status='1' and date between '".$open_date."' and '".$today."' and (h_type='National' or (state='".$loc_state."' and state!=''))";
	$rs_h=mysqli_query($this->link,$sql_h) or die(mysqli_error($this->link));
	while($row_h=mysqli_fetch_assoc($rs_h)){
		$date=date('D',strtotime($row_h['date']));
		if($date!=$weak_day){
			$holidays++;
		}
	}
	return $holidays;
}


////////////////////////////////////Repair Done /WIP/////////////////////////



//////////////////////////////////////////////Installation Done///////////////////////////////////


 public function savejobclosewp($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$close_reason,$remark,$faultRepairList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$faulty_flag,$paymentList,$service_charge,$totalAmt,$pending_reason) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
 ////////////////// Update Complaint Master //////////////////
 
$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));

	if($old_s['status']!=10){
			for($i=0; $i<count($faultRepairList) ; $i++) {
			////// insert in repair details
			
				$prd_code =  $faultRepairList[$i]->part_code;
				$symp_code =  $faultRepairList[$i]->voc_code;
				$solutioncode =  $faultRepairList[$i]->solution_code;
				$part_price =  $faultRepairList[$i]->part_price;
				$new_serial =  $faultRepairList[$i]->new_serial;
				$old_serial =  $faultRepairList[$i]->old_serial;
				

			
				$res_challan = mysqli_query($this->link, "SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$_SESSION['asc_code']."' and partcode='".$prd_code."' and okqty != fifi_ty ") or die(mysqli_error($this->link));
				$row_challan = mysqli_fetch_assoc($res_challan);
						
				$return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
				
				$splitted_fifo_data = explode("~",$return_data);
					
			$res_reapirdata = mysqli_query($this->link, "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$old_s['eng_id']."' , status='".$repair_status."', remark='".$remark."', repair_code='".$solutioncode."', partcode='".$prd_code."', part_qty='1',fault_code='".$symp_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$today."', warranty_status = '".$warranty_status."' ,old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',replace_serial='".$new_serial."',old_serial='".$old_serial."',part_cost='".$part_price."' ");
			
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error In repair Details table: " . mysqli_error($this->link) . ".";
			}
			
			if($prd_code){
				//// update inventory as user consume part
			
				
				$return_fifo = "UPDATE  fifo_list set fifi_ty =fifi_ty + 1 where  partcode='" .$prd_code. "' and ref_sno='".$splitted_fifo_data[5]."'"; 
				
				$queryfifo_return = mysqli_query($this->link, $return_fifo);
				
				//// check if query is not executed
				if (!$queryfifo_return) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code10: ".mysqli_error($this->link);
				}
											
				$bill_fifo = "UPDATE  billing_product_items set return_qty_fifo=return_qty_fifo+1 where  partcode='" .$prd_code. "' and id='".$splitted_fifo_data[5]."'"; 		
																	
				$queryfifo_bill = mysqli_query($this->link, $bill_fifo);
				
				if (!$queryfifo_bill) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code11: ".mysqli_error($this->link);
				}
				
				$prt_prc = mysqli_fetch_array(mysqli_query($this->link, "select customer_price from partcode_master where partcode = '".$prd_code."' "));
				
				$result=mysqli_query($this->link, "insert into stock_ledger set reference_no='".$job_no."',reference_date='".$today."',partcode='".$prd_code."',from_party='".$old_s['current_location']."', to_party='".$old_s['customer_name']."', stock_transfer='OUT', stock_type='OK', type_of_transfer='JOB REPAIR', action_taken='Repair Done',qty='1', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 echo "Error detailsSL: " . mysqli_error($this->link) . "";
				}
				
			}


				
				$res_invt = mysqli_query($this->link, "UPDATE user_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$old_s['eng_id']."' and okqty >0");
			
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
						
				####### We Receive Faulty Flag Y then Faulty will create as per discussed with Mr. Yadu 09/Mar/2021
				
					$faulty_r='Y';
if($warranty_status=='IN'){
	$faulty_r='Y';
}else {
	$faulty_r=$faulty_flag;
}
				
				if($faulty_r=='Y'){
					
					$sql_part=mysqli_query($this->link, "select faulty_part, partcode from partcode_master where partcode='".$prd_code."'" )or die(mysqli_error($this->link)); 
					$rep_part = mysqli_fetch_array($sql_part);
			
					$res_faulty_user = mysqli_query($this->link, "UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$old_s['eng_id']."'");
					
					//// check if query is not executed
					if (!$res_faulty_user) {
						 $flag = false;
						 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
					}
					
					$result=mysqli_query($this->link, "insert into stock_ledger set reference_no='".$job_no."',reference_date='".$today."',partcode='".$prd_code."',from_party='".$old_s['customer_name']."', to_party='".$old_s['current_location']."', stock_transfer='IN', stock_type='Faulty', type_of_transfer='JOB REPAIR', action_taken='Repair Done',qty='1', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 echo "Error detailsSL: " . mysqli_error($this->link) . "";
				}
					
					if($prd_code!= "" && $prd_code!= "39"){
					
						$res_p2cdata = mysqli_query($this->link, "INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_serial."',from_location='".$old_s['current_location']."', partcode='".$prd_code."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$old_s['eng_id']."',eng_status='1',old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."'");
						
						if (!$res_p2cdata) {
							 $flag = false;
							 $error_msg = "Error details211: " . mysqli_error($this->link) . ".";
						}
					
					}
					
					
			}	
					
				
		
		}
		
		###### Payment Entry Recevied by APP
		for($i=0; $i<count($paymentList) ; $i++) {
			////// insert in repair details
			
				$cr_book_no =  $paymentList[$i]->cr_book_no;
				$cr_no =  $paymentList[$i]->cr_no;
				$trn_no =  $paymentList[$i]->trn_no;
				$payment_mode =  $paymentList[$i]->payment_mode;
				$other_trn_remark =  $paymentList[$i]->other_trn_remark;
				$payment_receive =  $paymentList[$i]->payment_receive;
				
				$paymnet_cdata = mysqli_query($this->link, "INSERT INTO payment_receive_loc set job_no ='".$job_no."', cr_book_no='".$cr_book_no."',cr_no='".$cr_no."', cr_date='".$today."', transaction_no='".$trn_no."',payment_mode='".$payment_mode."',remark='".$other_trn_remark."',amount ='".$payment_receive."'");
						
						if (!$paymnet_cdata) {
							 $flag = false;
							 $error_msg = "Error details212: " . mysqli_error($this->link) . ".";
						}
				
		}
		
		###### If Service Charge Received
		if($service_charge!='0.00' && $service_charge!='' && $service_charge!='0'){
			
		$res_servicedata = mysqli_query($this->link, "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$old_s['eng_id']."' , status='".$repair_status."', remark='".$remark."', partcode='39', part_qty='1', rep_lvl='".$rep_row ['rep_level']."',close_date='".$today."', warranty_status = '".$warranty_status."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',part_cost='".$service_charge."' ");
			
			//// check if query is not executed
			if (!$res_servicedata) {
				 $flag = false;
				 $error_msg = "Error In Service Charge Details table: " . mysqli_error($this->link) . ".";
			}
		}
		
		if($old_s["call_for"]!="Workshop" ){
			
			$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
			$close_tat = $dt_dif-$tatminus;	
		}else{
			if($old_s['close_date']!="0000-00-00"){
				
				$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $old_s['close_date']);
				$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
				$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
				$dt_dif = $end_date - $start_date;
				
				$close_tat = $dt_dif-$tatminus;
			}
            else{
				
				$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
				$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
				$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
				$dt_dif = $end_date - $start_date;
				
				$close_tat = $dt_dif-$tatminus;
			}
		}
		
		//// open close tat ///////
		$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
		$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
		$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
		$dt_dif_oc = $end_date_oc - $start_date_oc;
		
		$tat_open_close = $dt_dif_oc-$tatminus;
		
		/////// billing product item details /////	
		$ag_count = 0;	
		$aging_close_tat = "";
		$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'Job PNA' "));
		$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'PNA Part Received' "));
		$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
		$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			
			$date_parts1=explode("-", $pna_rais_info[0]); $date_parts2=explode("-", $pna_rec_info[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$ag_count = $end_date - $start_date;
			
		}else{
			$ag_count = 0;	
		}
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			if($close_tat!=""){
				$aging_close_tat = $close_tat.",".$ag_count;
			}else{
				$aging_close_tat = "";
			}
		}
		$aggg = $ag_count."~".$aging_close_tat;
		
		$getage = explode("~", $aggg);
		$ctat = ($close_tat-$getage[0]);
		if($repair_status==10){
				$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$today."',close_time='".$currtime."',pen_status='6',dop='".$dop."',warranty_status='".$warranty_status."',close_rmk='".$closed_reason."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' , close_tat = '".$ctat."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."',path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='Repair Done',outcome='Repair Done',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";

	$resulth=mysqli_query($this->link,$query);
		if (!$resulth) {
			 $flag = false;
			 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
		}
		}
     else{
		
						$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',reason='".$pending_reason."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='Work In Progress',outcome='Work In Progress',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";

	$resulth=mysqli_query($this->link,$query);
		if (!$resulth) {
			 $flag = false;
			 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
		}
		
		
		}	
	
	

$sql_rep=mysqli_query($this->link, "select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");

$row_max=mysqli_fetch_array($sql_rep);
if($warranty_status_new=='IN'){

		$max_rep=mysqli_query($this->link, "insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
		
		//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}	
}
	
    }
     else {
         $error_msg="Alrady Closed This Job ".$old_s['job_no'];
     return $error_msg;
     }
 

if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 } 
///////////// Auto invoice script for OUT/VOID warranty JOBs which are going to close written by shekhar on 22 april 2021
###############################
public function savejobclosewp_autoinv($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$closed_reason,$remark,$faultRepairList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$paymentList,$service_charge,$totalAmt,$pending_reason,$ws_void,$ws_void_reason,$electric_fail_hrs,$s_c_m,$call_processing_charges,$processing_partcode,$amc_collected,$app_version,$battery_option,$other_battery,$eid,$upload_doc,$phy_condition,$img_sr,$img_prd,$ws_days,$balance_wsdays,$productid,$mfd,$mfd_ex,$verify_serial) {
		$serial_no = preg_replace('/[^a-zA-Z0-9]/s', '', $serial_no);/// condition add on 11 dec 2024 by shekhar
		////// initialize transaction parameter
		$flag = true;
		mysqli_autocommit($this->link, false);
		$error_msg = "";
		$today = $this->dt_format->format('Y-m-d');
		$currtime = $this->dt_format->format('H:i:s');
		$datetime = $today." ".$currtime;

		///////////////////////////////////////////////////
		$test_duplicate_flag = "";
		if($job_no != ""){ $str = " and job_no != '".$job_no."' "; }else{ $str = ""; }
		$result_AA = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serial_no."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link)); 

		$result_BB = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serial_no."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 

		$result_CC = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serial_no."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 

		/*if(mysqli_num_rows($result_AA)==0){
			if(mysqli_num_rows($result_BB)==0){
				if(mysqli_num_rows($result_CC)==0){
					$test_duplicate_flag = "1";	
				}else{
					$test_duplicate_flag = "2";	
				}	
			}else{
				$test_duplicate_flag = "2";		
			}	
		}else{
			$test_duplicate_flag = "2";	
		}

		//////////////////////////////////////////////////
		if($test_duplicate_flag=="1"){*/

		/////define array for billing item  so that we can hold part and service charge details in single array
		$part = array();
		//////// get job details
		$old_s = mysqli_fetch_array(mysqli_query($this->link,"SELECT status,job_id,job_no,current_location,location_code,city_id,model_id,eng_id,product_id,brand_id,customer_name,call_for,vistor_date,close_date,open_date,warranty_status,city_id,state_id,customer_id,imei,contact_no,email,address,pincode,outws_invno FROM jobsheet_data WHERE job_no='".$job_no."'"));
		
		### Fetch CUstomer Details
		$row_cust= mysqli_fetch_array(mysqli_query($this->link,"select gst_no from customer_master where customer_id='".$old_s['customer_id']."'"));
		$cust_gstin=$row_cust['gst_no'];
		###########################
		
		////// check if status is not Handover (Closed)
		if($old_s['status']!=10 && ($eid==$old_s['eng_id'])){
			/////// fetch fault code/repair code/ part used details etc
			for($i=0; $i<count($faultRepairList) ; $i++) {
				////// insert in repair details							
				$prd_code =  $faultRepairList[$i]->part_code;
				$symp_code =  $faultRepairList[$i]->voc_code;
				$solutioncode =  $faultRepairList[$i]->solution_code;
				$new_serial =  $faultRepairList[$i]->new_serial;
				$old_serial =  $faultRepairList[$i]->old_serial;
				$loc_on_pcb =  $faultRepairList[$i]->location_on_pcb;
				$pcb_repairable =  $faultRepairList[$i]->pcb_repairable;
				$consumed_qty =  $faultRepairList[$i]->qty;
				$img_filenm =  $faultRepairList[$i]->part_filename;
				$scan_type =  $faultRepairList[$i]->type;
				$cust_faulty_type =  $faultRepairList[$i]->customer_faulty;
				$cust_faulty_partcode =  $faultRepairList[$i]->receving_cus_faulty;
				$faulty_flag =  $faultRepairList[$i]->faulty_flag;
				### Condition for Consumption Qty POST 0 to set 1
				if($consumed_qty==0 && $prd_code!=''){ $consumed_qty=1;}
				#### END Condition for Consumption Qty POST 0 to set 1
				//if($old_s['eng_id'] == "CSPL0001U1"){
					///// check if faulty return flag is No then part price should be double as per logic, condition updated by shekhar on 24 nov 2021
					$hold_price = $faultRepairList[$i]->part_price;
					if($faulty_flag=="N" && $pcb_repairable == "No"){
						
						//$part_price = $hold_price + ($hold_price/3);   ////just double price
					
						$part_price = $hold_price; //double price receiving from App
					}else if($faulty_flag=="Y" && $pcb_repairable == "No"){
						$part_price = $hold_price; ////just one and onehalf price
					}else if($faulty_flag=="N" && $pcb_repairable != "No"){
						//$part_price =  2 * ($hold_price);
						// chnage on 180722-
						$part_price = $hold_price;
					}else{
						$part_price =  $hold_price;
					}
				//}else{
					//$part_price = $faultRepairList[$i]->part_price;
				//}
				
				////fifo process

				$res_challan = mysqli_query($this->link, "SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) AS qty FROM fifo_list WHERE location_code='".$_SESSION['asc_code']."' AND partcode='".$prd_code."' AND okqty != fifi_ty") or die(mysqli_error($this->link));
				$row_challan = mysqli_fetch_assoc($res_challan);
				$return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
				$splitted_fifo_data = explode("~",$return_data);
				 
				
				$old_flt = "";
				if($cust_faulty_type=="Different"){ $old_flt = $cust_faulty_partcode; }else{ $old_flt = ""; }
		
				////// insert data in repair table		
				$res_reapirdata = mysqli_query($this->link, "INSERT INTO repair_detail SET job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$old_s['eng_id']."' , status='".$repair_status."', remark='".$remark."', repair_code='".$solutioncode."', partcode='".$prd_code."', part_qty='".$consumed_qty."',fault_code='".$symp_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$today."', warranty_status = '".$warranty_status."' ,old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',replace_serial='".$new_serial."',old_serial='".$old_serial."',part_cost='".$part_price."',loc_on_pcb='".$loc_on_pcb."',pcb_repairable='".$pcb_repairable."',app_version='".$app_version."',scan_type='".$scan_type."',img_name='".$img_filenm."',battery_name='".$battery_option."',battery_other='".$other_battery."', module = '".$old_flt."' ");
				//// check if query is not executed
				if (!$res_reapirdata) {
					 $flag = false;
					 $error_msg = "Error In repair Details table: " . mysqli_error($this->link) . ".";
				}
				if($prd_code){
					//$part[] = $prd_code."~1~".$part_price;
					$part[] = $prd_code."~".$consumed_qty."~".$part_price;
				}
				
				////check partcode should be there
				if($prd_code){
					//// update inventory as user consume part
					$return_fifo = "UPDATE fifo_list SET fifi_ty =fifi_ty + 1 WHERE partcode='" .$prd_code. "' AND ref_sno='".$splitted_fifo_data[5]."'"; 	
					$queryfifo_return = mysqli_query($this->link, $return_fifo);
					//// check if query is not executed
					if (!$queryfifo_return) {
						$flag = false;
						$error_msg = "Fifo Return query Check Code10: ".mysqli_error($this->link);
					}
					
					/////
					$bill_fifo = "UPDATE billing_product_items SET return_qty_fifo=return_qty_fifo+1 WHERE partcode='" .$prd_code. "' AND id='".$splitted_fifo_data[5]."'";
					$queryfifo_bill = mysqli_query($this->link, $bill_fifo);	
					if (!$queryfifo_bill) {
						$flag = false;
						$error_msg = "Fifo Return query Check Code11: ".mysqli_error($this->link);
					}
					$prt_prc = mysqli_fetch_array(mysqli_query($this->link, "SELECT customer_price FROM partcode_master WHERE partcode = '".$prd_code."'"));
					////// stock ledger
					$result = mysqli_query($this->link, "INSERT INTO stock_ledger SET reference_no='".$job_no."',reference_date='".$today."',partcode='".$prd_code."',from_party='".$old_s['current_location']."', to_party='".$old_s['customer_name']."',owner_code='".$old_s['eng_id']."', stock_transfer='OUT', stock_type='OK', type_of_transfer='JOB REPAIR', action_taken='Repair Done',qty='".$consumed_qty."', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
					//// check if query is not executed

					if (!$result) {
						 $flag = false;
						 $error_msg = "Error detailsSL: ".mysqli_error($this->link)."";
					}
					
				}
				$res_invt = mysqli_query($this->link, "UPDATE user_inventory SET okqty = okqty-'".$consumed_qty."' WHERE location_code='".$old_s['current_location']."' AND partcode='".$prd_code."' AND locationuser_code='".$old_s['eng_id']."' AND okqty >= '".$consumed_qty."'");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
				####### We Receive Faulty Flag Y then Faulty will create as per discussed with Mr. Yadu 09/Mar/2021
				$faulty_r='Y';
				if($warranty_status=='IN'){
					$faulty_r='Y';
				}else {
					$faulty_r=$faulty_flag;
				}
				if($cust_faulty_partcode!=''){ $faulty_part=$cust_faulty_partcode; } else { $faulty_part=$prd_code; }
				
				if($faulty_r=='Y' && $faulty_part!=''){					
					$sql_part=mysqli_query($this->link, "SELECT faulty_part, partcode FROM partcode_master WHERE partcode='".$faulty_part."'" )or die(mysqli_error($this->link)); 
					$rep_part = mysqli_fetch_array($sql_part);
					
					$chk_faultyeng=mysqli_num_rows(mysqli_query($this->link,"select id from user_inventory where location_code='".$old_s['current_location']."' AND partcode='".$faulty_part."' AND locationuser_code='".$old_s['eng_id']."'"));
					
					if($chk_faultyeng>0){
					$res_faulty_user = mysqli_query($this->link, "UPDATE user_inventory SET faulty = faulty+'".$consumed_qty."' WHERE location_code='".$old_s['current_location']."' AND partcode='".$faulty_part."' AND locationuser_code='".$old_s['eng_id']."'");
					//// check if query is not executed
					if (!$res_faulty_user) {
						 $flag = false;
						 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
					}
					}  #### ENF If
					else{
					$res_faulty_user2 = mysqli_query($this->link, "insert into user_inventory SET faulty ='".$consumed_qty."',location_code='".$old_s['current_location']."',partcode='".$faulty_part."',locationuser_code='".$old_s['eng_id']."'");
					//// check if query is not executed
					if (!$res_faulty_user2) {
						 $flag = false;
						 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
					}	
					}

					$result = mysqli_query($this->link, "INSERT INTO stock_ledger SET reference_no='".$job_no."',reference_date='".$today."',partcode='".$faulty_part."',from_party='".$old_s['customer_name']."', to_party='".$old_s['current_location']."',owner_code='".$old_s['eng_id']."', stock_transfer='IN', stock_type='Faulty', type_of_transfer='JOB REPAIR', action_taken='Repair Done',qty='".$consumed_qty."', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
					//// check if query is not executed
					if (!$result) {
						 $flag = false;
						 $error_msg = "Error detailsSL: " . mysqli_error($this->link) . "";
					}
					if($prd_code!= ""  && $prd_code!= "39"){
						$res_p2cdata = mysqli_query($this->link, "INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_serial."',from_location='".$old_s['current_location']."', partcode='".$faulty_part."', qty='".$consumed_qty."',consumedate='".$today."',entry_date='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$old_s['eng_id']."',eng_status='1',old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."',cust_faulty_type='".$cust_faulty_type."'");						
						if (!$res_p2cdata) {
							 $flag = false;
							 $error_msg = "Error details211: " . mysqli_error($this->link) . ".";
						}
					}
				}
			}
		
			###### If Service Charge Received
			if($service_charge!='0.00' && $service_charge!='' && $service_charge!='0'){	
				$res_servicedata = mysqli_query($this->link, "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$old_s['eng_id']."' , status='".$repair_status."', remark='".$remark."', partcode='39', part_qty='1', rep_lvl='".$rep_row ['rep_level']."',close_date='".$today."', warranty_status = '".$warranty_status."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',part_cost='".$service_charge."',app_version='".$app_version."', repair_code='".$solutioncode."',fault_code='".$symp_code."' ");
				//// check if query is not executed
				if (!$res_servicedata) {
					 $flag = false;
					 $error_msg = "Error In Service Charge Details table: " . mysqli_error($this->link) . ".";
				}
				$part[] = "39~1~".$service_charge;
			}
			
			if($old_s["call_for"]!="Workshop" ){	
				$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
				$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
				$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
				$dt_dif = $end_date - $start_date;
				$close_tat = $dt_dif-$tatminus;	
			}else{
				if($old_s['close_date']!="0000-00-00"){
					$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $old_s['close_date']);
					$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
					$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
					$dt_dif = $end_date - $start_date;
					
					$close_tat = $dt_dif-$tatminus;
				}
				else{
					$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
					$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
					$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
					$dt_dif = $end_date - $start_date;
					$close_tat = $dt_dif-$tatminus;
				}
			}
			
			//// open close tat ///////
			$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
			$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
			$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
			$dt_dif_oc = $end_date_oc - $start_date_oc;
			$tat_open_close = $dt_dif_oc-$tatminus;
			
			/////// billing product item details /////	
			$ag_count = 0;	
			$aging_close_tat = "";
			$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "SELECT update_date FROM call_history WHERE job_no = '".$job_no."' AND activity = 'Job PNA'"));
			$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "SELECT update_date FROM call_history WHERE job_no = '".$job_no."' AND activity = 'PNA Part Received'"));
			$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
			$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
			if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			
				$date_parts1=explode("-", $pna_rais_info[0]); $date_parts2=explode("-", $pna_rec_info[0]);
				$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
				$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
				$ag_count = $end_date - $start_date;
				
			}else{
				$ag_count = 0;	
			}
			
			if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
				if($close_tat!=""){
					$aging_close_tat = $close_tat.",".$ag_count;
				}else{
					$aging_close_tat = "";
				}
			}
			$aggg = $ag_count."~".$aging_close_tat;
			
			$getage = explode("~", $aggg);
			$ctat = ($close_tat-$getage[0]);
			/////when job is going to Handover (Close)
			if($repair_status==10){
				$query="INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Repair Done',outcome='Repair Done',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."',ip='".$_SERVER['REMOTE_ADDR']."'";
				$resulth=mysqli_query($this->link,$query);
				if (!$resulth) {
					 $flag = false;
					 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
				}
				
				$jsd_invfield = "";
				
				### If Complant will closed & PNA is pending then Part request will auto cancel, requirement by Mr. Ashish Sir 21/Sep/2022
					$sql_autopart = mysqli_query($this->link, "SELECT id FROM auto_part_request WHERE job_no = '".$job_no."' and status='3'");
					$rows_autopart= mysqli_num_rows($sql_autopart);
					if($rows_autopart>0){
						
						$result_autopart = mysqli_query($this->link,"update auto_part_request set status = '5',cancel_date  = '".$today."' ,remark ='Job Closed'  where job_no = '".$job_no."' and status='3' ");
					//// check if query is not executed
						if (!$result_autopart) {

							 $flag = false;
							 $err_msg = "Error detailsAutoPart: " . mysqli_error($this->link) . ".";
						}	
					}
				
					### End Part Request Cancel condition
				########### AUTO INVOICE SCRIPT START IN CASE OF OUT/VOID WARRANTY STATUS
				if($warranty_status != "IN" && $old_s["outws_invno"]=="" && $totalAmt!="" && $totalAmt!="0" && $totalAmt!="0.00"){
					$res_invcount = mysqli_query($this->link, "SELECT claim_series,fy,claim_counter FROM invoice_counter WHERE location_code='".$old_s['current_location']."'");
                	if (mysqli_num_rows($res_invcount)) {
						//////pick max counter of INVOICE
						$row_invcount = mysqli_fetch_array($res_invcount);
						$next_invno = $row_invcount['claim_counter']+1;
						/////update next counter against invoice
						$res_upd = mysqli_query($this->link,"UPDATE invoice_counter SET claim_counter = '".$next_invno."' WHERE location_code='".$old_s['current_location']."'");
						/// check if query is execute or not//
						if(!$res_upd){
							$flag = false;
							$error_msg = "Error1". mysqli_error($this->link) . ".";
						}
					
						///// make invoice no.
						$invno = $row_invcount['claim_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
						/////get basic details of location
						$fromlocdet = mysqli_fetch_array(mysqli_query($this->link,"SELECT locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,contactno1 FROM location_master WHERE location_code='".$old_s['current_location']."'"));
						////// get from city details
						$fromloccity = mysqli_fetch_array(mysqli_query($this->link,"SELECT city,state FROM city_master WHERE cityid='".$fromlocdet[4]."'"));
						////// get to city details
						$toloccity = mysqli_fetch_array(mysqli_query($this->link,"SELECT city,state FROM city_master WHERE cityid='".$old_s['city_id']."'"));
						//////                    
						///// fetch parts
						$disc_val = 0.00;
						$tot_cost = 0.00;
						$grandtotal = 0.00;
						$sgsttotal = 0.00;
						$cgsttotal = 0.00;
						$igsttotal = 0.00;
						foreach ($part as $k => $val) {
							/////explode part details
							$partdet = explode("~",$val);
							$row_partdet = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT part_name, hsn_code FROM partcode_master WHERE partcode='".$partdet[0]."'"));
							$part_tax = mysqli_fetch_array(mysqli_query($this->link,"SELECT igst,sgst,cgst FROM tax_hsn_master WHERE hsn_code='".$row_partdet['hsn_code']."' AND status='1'")) ;
							///calculate taxes
							$igst_val = number_format(($partdet[2] - ($partdet[2]*100)/(100+$part_tax['igst'])),'2','.','');
							$cgst_val = number_format(($igst_val/2),'2','.','');
							$sgst_val = number_format(($igst_val/2),'2','.','');
							//$basic_amt = 0.00;
							$basic_amt = number_format((($partdet[2]*100)/(100+$part_tax['igst'])),'2','.','');
							if($old_s["state_id"]==$fromlocdet['5']){
								$sgst_per = $part_tax['sgst'];
								$cgst_per = $part_tax['cgst'];
								$igst_per = 0.00;
								
								$sgst_amt = $sgst_val;
								$cgst_amt = $cgst_val;
								$igst_amt = 0.00;

							}else{
								$sgst_per = 0.00;
								$cgst_per = 0.00;
								$igst_per = $part_tax['igst'];
								
								$sgst_amt = 0.00;
								$cgst_amt = 0.00;
								$igst_amt = $igst_val;
							}
							
							/////////// insert data
                        	$query2 = "INSERT INTO billing_product_items set from_location='" . $old_s['current_location'] . "', to_location='".$old_s['customer_name']."',challan_no='".$invno."', hsn_code='".$row_partdet['hsn_code']."', partcode='" . $partdet[0] . "', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', part_name='".$row_partdet["part_name"]."', qty='".$partdet[1]."', okqty='".$partdet[1]."', price='" . $basic_amt. "',uom='PCS', mrp='" . $basic_amt . "',hold_value='".$basic_amt."', value='" . $basic_amt. "', discount_amt='', item_total='" . $partdet[2] . "', pty_receive_date='" . $today . "',basic_amt='".$basic_amt."', sgst_per='" . $sgst_per . "',sgst_amt='" . $sgst_amt . "' ,cgst_per='" . $cgst_per . "',cgst_amt='" . $cgst_amt . "',igst_per='" . $igst_per . "',igst_amt='" . $igst_amt . "',job_no='".$old_s['job_no']."', type = 'RETAIL' ";
                        	$result = mysqli_query($this->link, $query2);
							//// check if query is not executed
							if (!$result) {
								$flag = false;
								$error_msg = "Error Code2: ".mysqli_error($this->link);
							}
								
							//$disc_val = $disc_val + $part_wise_disc[$k];
							$tot_cost += $basic_amt;
							$grandtotal += $partdet[2];
							$sgsttotal += $sgst_amt;
							$cgsttotal += $cgst_amt;
							$igsttotal += $igst_amt;
						}////Close for loop					
						$tot_disc = ($disc_val + $ser_charge_dis);


                                                    ###### Payment Entry Recevied by APP

			$cr_book_no =  "";
			$cr_no =  "";
			$trn_no =  "";
			$payment_mode =  "";
			$other_trn_remark =  "";

			$payment_receive =  "0.00";
			$payaid = "";
			for($i=0; $i<count($paymentList) ; $i++) {
				////// insert in repair details			
				$cr_book_no =  $paymentList[$i]->cr_book_no;
				$cr_no =  $paymentList[$i]->cr_no;
				$trn_no =  $paymentList[$i]->trn_no;
				$payment_mode =  $paymentList[$i]->payment_mode;
				$other_trn_remark =  $paymentList[$i]->other_trn_remark;
				$payment_receive =  $paymentList[$i]->payment_receive;
				$paymnet_cdata = mysqli_query($this->link, "INSERT INTO payment_receive_loc set job_no ='".$job_no."', cr_book_no='".$cr_book_no."',cr_no='".$cr_no."', cr_date='".$today."', transaction_no='".$trn_no."',payment_mode='".$payment_mode."',remark='".$other_trn_remark."',amount ='".$grandtotal."', engg_id ='".$old_s['eng_id']."'");
				$payaid =mysqli_insert_id($this->link);
				if (!$paymnet_cdata) {
					 $flag = false;
					 $error_msg = "Error details212: " . mysqli_error($this->link) . ".";
				}
			}  				


						///// Insert Master Data
						$query1 = "INSERT INTO billing_master SET from_location='" . $old_s['current_location'] . "', to_location='" . $old_s['customer_name'] . "',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$cust_gstin."',from_partyname='".$fromlocdet[0]."', party_name='".$old_s['customer_name']."',customer_id='".$old_s['customer_id']."', challan_no='" . $invno . "',job_no='".$old_s['job_no']."',job_serial_no='".$serial_no."', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', logged_by='" . $old_s['eng_id'] . "', document_type='INV' ,basic_cost='" . $tot_cost . "',tax_cost='',total_cost='" . $grandtotal . "',bill_from='" . $old_s['current_location'] . "',from_stateid='".$fromlocdet['5']."',to_stateid='".$old_s["state_id"]."',from_state='".$fromloccity[1]."',to_state='".$toloccity[1]."',from_cityid='".$fromlocdet[4]."',from_city='".$fromloccity[0]."',to_cityid='".$old_s['city_id']."',to_city='".$toloccity[0]."',from_pincode='".$fromlocdet[6]."',to_pincode='".$old_s['pincode']."',from_phone='".$fromlocdet[9]."',to_phone='".$old_s['contact_no']."',from_email='".$fromlocdet[7]."',to_email='".$old_s['email']."',bill_to='".$old_s['customer_name']."',from_addrs='" . $fromlocdet[1] . "',disp_addrs='" . $fromlocdet[2] . "',round_off='" . $round_off . "',to_addrs='" . $old_s['address'] . "',deliv_addrs='" . $old_s['address'] . "',billing_rmk='".$remark."',po_no='FRONT_BILL', status='3', dc_date='" . $today . "',dc_time='" . $currtime . "',sgst_amt='" . $sgsttotal . "',cgst_amt='" . $cgsttotal. "',igst_amt='" . $igsttotal . "',driver_contact='".$old_s['contact_no']."',carrier_no='".$old_s['email']."',po_type='RETAIL',discount_amt='',cr_no='".$cr_no."',cr_book_no='".$cr_book_no."',cr_date='".$today."',transaction_no='".$trn_no."',mode_of_payment='".$payment_mode."',rec_pay_remark='".$other_trn_remark."'";			
						$result = mysqli_query($this->link, $query1);
						//// check if query is not executed
						if (!$result) {
							$flag = false;
							$error_msg = "Error Code1: ". mysqli_error($this->link);
						}	
                               
                   
						////// update payment details
						$sql_inst_pay = "UPDATE payment_receive_loc set inv_no='".$invno."' WHERE id='".$payaid."'";
						$res_inst_pay = mysqli_query($this->link,$sql_inst_pay);
						//// check if query is not executed
						if (!$res_inst_pay) {
							$flag = false;
							$error_msg = "Error payment receive location : " . mysqli_error($this->link) . ".";
						}
						$jsd_invfield = ",outws_inv='Y',outws_invno='".$invno."' ,out_warranty_disc = 'Discounted'";
						//// update status in job sheet
						/*$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set outws_inv='Y',outws_invno='".$invno."' ,out_warranty_disc = 'Discounted' where job_no='".$old_s['job_no']."'");
						//// check if query is not executed
						if (!$res_jobsheet) {
							 $flag = false;
							 $error_msg = "Error details4: " . mysqli_error($this->link) . ".";
						}*/
						///// entry in call/job  history
						 
						$query_ch = "INSERT INTO call_history SET job_no='".$old_s['job_no']."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Invoice Generated',outcome='Ready For Delivery',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='Generated Invoice for OUT/VOID Job',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."',ip='".$_SERVER['REMOTE_ADDR']."'";
						$result_ch = mysqli_query($this->link,$query_ch);
						if (!$result_ch) {
							 $flag = false;
							 $error_msg = "Error details Call history: " . mysqli_error($this->link) . ".";
						}
						///// insert in activity table////
						$query_da = "INSERT INTO daily_activities SET userid='".$old_s['eng_id']."', ref_no='".$old_s['job_no']."', activity_type='JOB INVOICE', action_taken='GENERATED', update_date='".$today."',update_time='".$currtime."',system_ip='".$_SERVER['REMOTE_ADDR']."'";
						$result_da = mysqli_query($this->link,$query_da);
						//// check if query is not executed
						if (!$result_da) {
							$flag = false;
							$error_msg = "Error Code6: ". mysqli_error($this->link);
						}
						
					}else{
						$flag = false;
						$error_msg = "Request could not be processed invoice series not found. Please try again.";
					}
				}
				
				########### AUTO INVOICE SCRIPT START IN CASE OF Only IN WARRANTY STATUS 27 MAY
				$part_in=array();
				$part_in[] = $processing_partcode."~1~".$call_processing_charges;
				
				if($warranty_status == "INHOLD" && $old_s["outws_invno"]=="" && $call_processing_charges!="" && $call_processing_charges!="0" && $call_processing_charges!="0.00" && $processing_partcode!=""){
					$res_invcount = mysqli_query($this->link, "SELECT claim_series,fy,claim_counter FROM invoice_counter WHERE location_code='".$old_s['current_location']."'");
                	if (mysqli_num_rows($res_invcount)) {
						//////pick max counter of INVOICE
						$row_invcount = mysqli_fetch_array($res_invcount);
						$next_invno = $row_invcount['claim_counter']+1;
						/////update next counter against invoice
						$res_upd = mysqli_query($this->link,"UPDATE invoice_counter SET claim_counter = '".$next_invno."' WHERE location_code='".$old_s['current_location']."'");
						/// check if query is execute or not//
						if(!$res_upd){
							$flag = false;
							$error_msg = "Error1". mysqli_error($this->link) . ".";
						}
						
						///// make invoice no.
						$invno = $row_invcount['claim_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
						/////get basic details of location
						$fromlocdet = mysqli_fetch_array(mysqli_query($this->link,"SELECT locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,contactno1 FROM location_master WHERE location_code='".$old_s['current_location']."'"));
						////// get from city details
						$fromloccity = mysqli_fetch_array(mysqli_query($this->link,"SELECT city,state FROM city_master WHERE cityid='".$fromlocdet[4]."'"));
						////// get to city details
						$toloccity = mysqli_fetch_array(mysqli_query($this->link,"SELECT city,state FROM city_master WHERE cityid='".$old_s['city_id']."'"));
						//////                    
						///// fetch parts
						$disc_val = 0.00;
						$tot_cost = 0.00;
						$grandtotal = 0.00;
						$sgsttotal = 0.00;
						$cgsttotal = 0.00;
						$igsttotal = 0.00;
						foreach ($part_in as $k => $val) {
							/////explode part details
							$partdet = explode("~",$val);
							$row_partdet = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT part_name, hsn_code FROM partcode_master WHERE partcode='".$partdet[0]."'"));
							$part_tax = mysqli_fetch_array(mysqli_query($this->link,"SELECT igst,sgst,cgst FROM tax_hsn_master WHERE hsn_code='".$row_partdet['hsn_code']."' AND status='1'")) ;
							///calculate taxes
							$igst_val = number_format(($partdet[2] - ($partdet[2]*100)/(100+$part_tax['igst'])),'2','.','');
							$cgst_val = number_format(($igst_val/2),'2','.','');
							$sgst_val = number_format(($igst_val/2),'2','.','');
							//$basic_amt = 0.00;
							$basic_amt = number_format((($partdet[2]*100)/(100+$part_tax['igst'])),'2','.','');
							if($old_s["state_id"]==$fromlocdet['5']){
								$sgst_per = $part_tax['sgst'];
								$cgst_per = $part_tax['cgst'];
								$igst_per = 0.00;
								
								$sgst_amt = $sgst_val;
								$cgst_amt = $cgst_val;
								$igst_amt = 0.00;
							}else{
								$sgst_per = 0.00;
								$cgst_per = 0.00;
								$igst_per = $part_tax['igst'];
								
								$sgst_amt = 0.00;
								$cgst_amt = 0.00;
								$igst_amt = $igst_val;
							}
							/////////// insert data
                        	$query2 = "INSERT INTO billing_product_items set from_location='" . $old_s['current_location'] . "', to_location='".$old_s['customer_name']."',challan_no='".$invno."', hsn_code='".$row_partdet['hsn_code']."', partcode='" . $partdet[0] . "', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', part_name='".$row_partdet["part_name"]."', qty='1', okqty='1', price='" . $basic_amt. "',uom='PCS', mrp='" . $basic_amt . "',hold_value='".$basic_amt."', value='" . $basic_amt. "', discount_amt='', item_total='" . $partdet[2] . "', pty_receive_date='" . $today . "',basic_amt='".$basic_amt."', sgst_per='" . $sgst_per . "',sgst_amt='" . $sgst_amt . "' ,cgst_per='" . $cgst_per . "',cgst_amt='" . $cgst_amt . "',igst_per='" . $igst_per . "',igst_amt='" . $igst_amt . "',job_no='".$old_s['job_no']."', type = 'RETAIL' ";
                        	$result = mysqli_query($this->link, $query2);
							//// check if query is not executed
							if (!$result) {
								$flag = false;
								$error_msg = "Error Code2: ".mysqli_error($this->link);
							}
							//$disc_val = $disc_val + $part_wise_disc[$k];
							$tot_cost += $basic_amt;
							$grandtotal += $partdet[2];
							$sgsttotal += $sgst_amt;
							$cgsttotal += $cgst_amt;
							$igsttotal += $igst_amt;
						}////Close for loop					
						$tot_disc = ($disc_val + $ser_charge_dis);
						///// Insert Master Data
						$query1 = "INSERT INTO billing_master SET from_location='" . $old_s['current_location'] . "', to_location='" . $old_s['customer_name'] . "',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$cust_gstin."',from_partyname='".$fromlocdet[0]."', party_name='".$old_s['customer_name']."',customer_id='".$old_s['customer_id']."', challan_no='" . $invno . "',job_no='".$old_s['job_no']."',job_serial_no='".$serial_no."', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', logged_by='" . $old_s['eng_id'] . "', document_type='INV' ,basic_cost='" . $tot_cost . "',tax_cost='',total_cost='" . $grandtotal . "',bill_from='" . $old_s['current_location'] . "',from_stateid='".$fromlocdet['5']."',to_stateid='".$old_s["state_id"]."',from_state='".$fromloccity[1]."',to_state='".$toloccity[1]."',from_cityid='".$fromlocdet[4]."',from_city='".$fromloccity[0]."',to_cityid='".$old_s['city_id']."',to_city='".$toloccity[0]."',from_pincode='".$fromlocdet[6]."',to_pincode='".$old_s['pincode']."',from_phone='".$fromlocdet[9]."',to_phone='".$old_s['contact_no']."',from_email='".$fromlocdet[7]."',to_email='".$old_s['email']."',bill_to='".$old_s['customer_name']."',from_addrs='" . $fromlocdet[1] . "',disp_addrs='" . $fromlocdet[2] . "',round_off='" . $round_off . "',to_addrs='" . $old_s['address'] . "',deliv_addrs='" . $old_s['address'] . "',billing_rmk='".$remark."',po_no='FRONT_BILL', status='3', dc_date='" . $today . "',dc_time='" . $currtime . "',sgst_amt='" . $sgsttotal . "',cgst_amt='" . $cgsttotal. "',igst_amt='" . $igsttotal . "',driver_contact='".$old_s['contact_no']."',carrier_no='".$old_s['email']."',po_type='RETAIL',discount_amt='',cr_no='".$cr_no."',cr_book_no='".$cr_book_no."',cr_date='".$today."',transaction_no='".$trn_no."',mode_of_payment='".$payment_mode."',rec_pay_remark='".$other_trn_remark."'";			
						$result = mysqli_query($this->link, $query1);
						//// check if query is not executed
						if (!$result) {
							$flag = false;
							$error_msg = "Error Code1: ". mysqli_error($this->link);
						}					
						////// update payment details
						$sql_inst_pay = "UPDATE payment_receive_loc set inv_no='".$invno."' WHERE id='".$payaid."'";
						$res_inst_pay = mysqli_query($this->link,$sql_inst_pay);
						//// check if query is not executed
						if (!$res_inst_pay) {
							$flag = false;
							$error_msg = "Error payment receive location : " . mysqli_error($this->link) . ".";
						}
						$jsd_invfield = ",outws_inv='Y',outws_invno='".$invno."' ,out_warranty_disc = 'Discounted'";
						//// update status in job sheet
						/*$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set outws_inv='Y',outws_invno='".$invno."' ,out_warranty_disc = 'Discounted' where job_no='".$old_s['job_no']."'");


						//// check if query is not executed
						if (!$res_jobsheet) {
							 $flag = false;
							 $error_msg = "Error details4: " . mysqli_error($this->link) . ".";
						}*/
						///// entry in call/job  history
						$query_ch = "INSERT INTO call_history SET job_no='".$old_s['job_no']."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Invoice Generated',outcome='Ready For Delivery',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='Generated Invoice for IN Job',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."',ip='".$_SERVER['REMOTE_ADDR']."'";
						$result_ch = mysqli_query($this->link,$query_ch);
						if (!$result_ch) {
							 $flag = false;
							 $error_msg = "Error details Call history: " . mysqli_error($this->link) . ".";
						}
						///// insert in activity table////
						$query_da = "INSERT INTO daily_activities SET userid='".$old_s['eng_id']."', ref_no='".$old_s['job_no']."', activity_type='JOB INVOICE', action_taken='GENERATED', update_date='".$today."',update_time='".$currtime."',system_ip='".$_SERVER['REMOTE_ADDR']."'";
						$result_da = mysqli_query($this->link,$query_da);
						//// check if query is not executed
						if (!$result_da) {
							$flag = false;
							$error_msg = "Error Code6: ". mysqli_error($this->link);
						}
					}else{
						$flag = false;
						$error_msg = "Request could not be processed invoice series not found. Please try again.";
					}
				}
				
				$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data SET status='".$repair_status."', sub_status='".$repair_status."',close_date='".$today."',close_time='".$currtime."',pen_status='6',dop='".$dop."',warranty_status='".$warranty_status."',close_rmk='".$closed_reason."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' , close_tat = '".$ctat."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."',path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'".$jsd_invfield.",ws_void='".$ws_void."',ws_void_reason='".$ws_void_reason."' ,electric_fail_hrs = '".$electric_fail_hrs."'  ,  scm_codeuse = '".$s_c_m."',location_on_pcb='".$loc_on_pcb."',amc_collecte='".$amc_collected."' , faulty_flag = '".$faulty_flag."',phy_cond='".$phy_condition."',doa_bag='".$upload_doc."',path_img4='".$img_sr."',path_img5='".$img_prd."',warranty_days='".$ws_days."',balance_warranty_days='".$balance_wsdays."',product_id='".$productid."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',verify_serial='".$verify_serial."', punch_out_address ='".$address."'  WHERE job_no='".$job_no."'");
				if (!$res_jobsheet) {
					 $flag = false;
					 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
				}
				$res_usr_daily=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$old_s['eng_id']."', activity = 'JOB CLOSE', entry_date = '".$today."', entry_time = '".$currtime."', longitude = '".$longitude."', latitude = '".$latitude."', address = '".$address."', ref_no = '".$job_no."', job_status = '".$repair_status."', job_sub_status = '".$repair_status."', warranty_status = '".$warranty_status."' ") or die(mysqli_error($this->link));

				if(!$res_usr_daily){
					$flag = false;
					$error_msg = "Error user_daily_track: " . mysqli_error($this->link) . ".";
				}
				
			}
			else{
				
				//echo "UPDATE jobsheet_data SET status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',reason='".$pending_reason."' , faulty_flag = '".$faulty_flag."',phy_cond='".$phy_condition."',doa_bag='".$upload_doc."',path_img4='".$img_sr."',path_img5='".$img_prd."',warranty_days='".$ws_days."',balance_warranty_days='".$balance_wsdays."',product_id='".$productid."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',verify_serial='".$verify_serial."' WHERE job_no='".$job_no."'";exit;
				$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data SET status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',reason='".$pending_reason."' , faulty_flag = '".$faulty_flag."',phy_cond='".$phy_condition."',doa_bag='".$upload_doc."',path_img4='".$img_sr."',path_img5='".$img_prd."',warranty_days='".$ws_days."',balance_warranty_days='".$balance_wsdays."',product_id='".$productid."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',verify_serial='".$verify_serial."' WHERE job_no='".$job_no."'");

				if (!$res_jobsheet) {
					 $flag = false;
					 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
				}	
				
				$res_usr_daily=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$old_s['eng_id']."', activity = 'JOB CLOSE', entry_date = '".$today."', entry_time = '".$currtime."', longitude = '".$longitude."', latitude = '".$latitude."', address = '".$address."', ref_no = '".$job_no."', job_status = '".$repair_status."', job_sub_status = '".$repair_status."', warranty_status = '".$warranty_status."' ") or die(mysqli_error($this->link));

				if(!$res_usr_daily){
					$flag = false;
					$error_msg = "Error user_daily_track: " . mysqli_error($this->link) . ".";
				}
				
				$query = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Work In Progress',outcome='Work In Progress',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
				$resulth=mysqli_query($this->link,$query);
				if (!$resulth) {
					 $flag = false;
					 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
				}
			}
 
			if(($serial_no != $old_s['imei']) && $serial_no != ""){
				$remark_sr_change = "^ OLD BSN - ".$old_s['imei'].", New BSN - ".$serial_no;
				$query_sr_change = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='BSN Change By App',outcome='BSN Change By App',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark_sr_change."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
				$result_sr_change=mysqli_query($this->link,$query_sr_change);
				if(!$result_sr_change) {
					$flag = false;
					$error_msg = "Error sr_change_history: " . mysqli_error($this->link) . ".";
				}
				
			}
			
			$sql_rep=mysqli_query($this->link, "SELECT SUM(travel_km) AS a,updated_by FROM call_history WHERE job_no ='".$job_no."' AND travel='Y' GROUP BY updated_by");
			$row_max=mysqli_fetch_array($sql_rep);
			if($warranty_status_new=='IN'){
				$max_rep=mysqli_query($this->link, "INSERT INTO job_claim_appr SET job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
				//// check if query is not executed
				if (!$max_rep) {

					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
				
			}
		}
		else {
			$error_msg="Alrady Closed This Job ".$old_s['job_no'];
			return $error_msg;
		}
			
		///// send SMS through curl update on 17 may 2021 shekhar
		if ($flag) {
			
			///////send SMS in any closer of job (feedback MSG with tiny link while Call is closing)
			if($repair_status==10){	
			$sms_msg ="Dear Customer,
Your complaint no. ".$job_no." is successfully resolved, -Eastman";
			
			/*
			$sendsms = $this->sendSMSByURLNew($old_s['contact_no'],$sms_msg); 
			
			
			$sms_resp = explode("~",$sendsms);
			if($sms_resp[0]=="1"){
				//// insert into sms table
				$res_sms = mysqli_query($this->link,"INSERT INTO sms_send_response SET ref_no='".$job_no."', ref_type='COMPLAINT CLOSE', mobile_no='".$old_s['contact_no']."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$old_s['current_location']."', insert_by='".$old_s['eng_id']."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
			}else{
				//// insert into sms table
				$res_sms = mysqli_query($this->link,"INSERT INTO sms_send_response SET ref_no='".$job_no."', ref_type='COMPLAINT CLOSE',mobile_no='".$old_s['contact_no']."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$old_s['current_location']."', insert_by='".$old_s['eng_id']."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
			}*/
			}

			///// update distence in job
			/*if($repair_status==10){	
				
				$updatedistence = $this->calculateLocCustDist($job_no,$latitude,$longitude);
				//print_r('dd');exit;
				$dist_resp = explode("^",$updatedistence);

				if($dist_resp[0]=="1"){

					$res_jobsheet_dist = mysqli_query($this->link,"UPDATE jobsheet_data SET dist_mtr = '".$dist_resp[1]."', dist_km = '".$dist_resp[2]."' WHERE job_no='".$job_no."'");
					if (!$res_jobsheet_dist) {
						$flag = false;
						$error_msg = "Error details jobsheet distence update : " . mysqli_error($this->link) . ".";
					}

				}
			}	*/

		}

	/////////////	end check BSN logic updated bu 11-06-2025
	
		/*}else{
			$flag = false;
			$error_msg = "Error! You are using duplicate BSN, Try again.";
		}*/

		if ($flag) {
			mysqli_commit($this->link);
			return 1;         
		} else {
			return $error_msg;
		}
		
	}
	
	 /////////////////////////////////////PNA/////////////////////////////////
 
 

 
	 public function savejobnvc($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$closed_reason,$remark,$faultRepairList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$paymentList,$service_charge,$totalAmt,$pending_reason,$ws_void,$ws_void_reason,$electric_fail_hrs,$s_c_m,$call_processing_charges,$processing_partcode,$amc_collected,$app_version,$battery_option,$other_battery,$eid,$upload_doc,$phy_condition,$img_sr,$img_prd,$ws_days,$balance_wsdays,$productid,$mfd,$mfd_ex,$verify_serial,$call_reason) { 

		$flag = true;
		mysqli_autocommit($this->link, false);
		$error_msg = "";
		
		$today = $this->dt_format->format('Y-m-d');
		$currtime = $this->dt_format->format('H:i:s');

		///////////////////////////////////////////////////
		$test_duplicate_flag = "";
		if($job_no != ""){ $str = " and job_no != '".$job_no."' "; }else{ $str = ""; }
		$result_AA = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serial_no."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link));

		$result_BB = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serial_no."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 

		$result_CC = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serial_no."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 

		/*if(mysqli_num_rows($result_AA)==0){
			if(mysqli_num_rows($result_BB)==0){
				if(mysqli_num_rows($result_CC)==0){
					$test_duplicate_flag = "1";	
				}else{
					$test_duplicate_flag = "2";	
				}	
			}else{
				$test_duplicate_flag = "2";		
			}	
		}else{
			$test_duplicate_flag = "2";	
		}

		//////////////////////////////////////////////////
		if($test_duplicate_flag=="1"){*/

	 ////////////////// Update Complaint Master //////////////////
	$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
	 
	 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',app_rmk='Done By App', nvc_remark = '".$remark."',call_reason='".$call_reason."' where job_no='".$job_no."'");
		
			if (!$res_jobsheet) {
				 $flag = false;
				 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
			}
	 
		
			$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='NVC Calling',outcome='NVC Calling',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";
	
		$resulthistory=mysqli_query($this->link,$query);
		if (!$resulthistory) {
					 $flag = false;
					 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
				}

				if(($serial_no != $old_s['imei']) && $serial_no != ""){
					$remark_sr_change = "_ OLD BSN - ".$old_s['imei'].", New BSN - ".$serial_no;
					$query_sr_change = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='BSN Change By App',outcome='BSN Change By App',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark_sr_change."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
					$result_sr_change=mysqli_query($this->link,$query_sr_change);
					if(!$result_sr_change) {
						$flag = false;
						$error_msg = "Error sr_change_history: " . mysqli_error($this->link) . ".";
					}
				}	
/////////////	end check BSN logic updated by 11-06-2025
		 
			/*}else{
				$flag = false;
				$error_msg = "Error! You are using duplicate BSN, Try again.";
			}*/			
	
	
	if ($flag) {
		
		mysqli_commit($this->link);    
			
	return 1;         
	} else {
	return $error_msg;
	}     
	 } 	
//////////////////////////////////////////////Installation Done///////////////////////////////////


 public function savejobdatainsllation($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$close_reason,$remark,$part_consume,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$ws_void,$ws_void_reason,$electric_fail_hrs,$app_version,$verify_serial) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";

	//date_default_timezone_set('Asia/Kolkata');
	 $today=$this->dt_format->format('Y-m-d');
	//$currtime=date("H:i:s");
      $currtime = $this->dt_format->format('H:i:s');

		///////////////////////////////////////////////////
		$test_duplicate_flag = "";
		if($job_no != ""){ $str = " and job_no != '".$job_no."' "; }else{ $str = ""; }
		$result_AA = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serial_no."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link));

		$result_BB = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serial_no."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 

		$result_CC = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serial_no."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 

		/*if(mysqli_num_rows($result_AA)==0){
			if(mysqli_num_rows($result_BB)==0){
				if(mysqli_num_rows($result_CC)==0){
					$test_duplicate_flag = "1";	
				}else{
					$test_duplicate_flag = "2";	
				}	
			}else{
				$test_duplicate_flag = "2";		
			}	
		}else{
			$test_duplicate_flag = "2";	
		}

		//////////////////////////////////////////////////
		if($test_duplicate_flag=="1"){*/
 ////////////////// Update Complaint Master //////////////////
 
$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));

		
		if($old_s["call_for"]!="Workshop" ){
			
			$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
			$close_tat = $dt_dif-$tatminus;
		}else{
			
			$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
	    	$close_tat = $dt_dif-$tatminus;
		}
		
		//// open close tat ///////
		$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
		$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
		$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
		$dt_dif_oc = $end_date_oc - $start_date_oc;
		
		$tat_open_close = $dt_dif_oc-$tatminus;
		
		/////// billing product item details /////	
		$ag_count = 0;	
		$aging_close_tat = "";
		$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'Job PNA' "));
		$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'PNA Part Received' "));
		$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
		$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			
			$date_parts1=explode("-", $pna_rais_info[0]); $date_parts2=explode("-", $pna_rec_info[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$ag_count = $end_date - $start_date;
			
		}else{
			$ag_count = 0;	
		}
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			if($close_tat!=""){
				$aging_close_tat = $close_tat.",".$ag_count;
			}else{
				$aging_close_tat = "";
			}
		}
		$aggg = $ag_count."~".$aging_close_tat;
		
		$getage = explode("~", $aggg);
		
		$ctat = ($close_tat-$getage[0]);
		
			for($i=0; $i<count($part_consume) ; $i++) {
				////// insert in repair details
				$prd_code =  $part_consume[$i]->partcode;
			
				//// update inventory as user consume part
				$res_challan = mysqli_query($this->link, "SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$_SESSION['asc_code']."' and partcode='".$prd_code."' and okqty != fifi_ty ") or die(mysqli_error($this->link));
				$row_challan = mysqli_fetch_assoc($res_challan);
						
				$return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
				
				$splitted_fifo_data = explode("~",$return_data);
				
				$return_fifo = "UPDATE  fifo_list set fifi_ty =fifi_ty + 1 where  partcode='" .$prd_code. "' and ref_sno='".$splitted_fifo_data[5]."'"; 
				$queryfifo_return = mysqli_query($this->link, $return_fifo);
				//// check if query is not executed
				if (!$queryfifo_return) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code10: ".mysqli_error($this->link);
				}
										
				$bill_fifo = "UPDATE  billing_product_items set return_qty_fifo=return_qty_fifo+1 where  partcode='" .$prd_code. "' and id='".$splitted_fifo_data[5]."'"; 
				$queryfifo_bill = mysqli_query($this->link, $bill_fifo);
				//// check if query is not executed
				if (!$queryfifo_bill) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code11: ".mysqli_error($this->link);
				}
								
				$prt_prc = mysqli_fetch_array(mysqli_query($this->link, "select customer_price from partcode_master where partcode = '".$prd_code."' "));
				
				$result=mysqli_query($this->link, "insert into stock_ledger set reference_no='".$job_no."',reference_date='".$today."',partcode='".$prd_code."',from_party='".$old_s['current_location']."', to_party='".$old_s['customer_name']."',owner_code='".$old_s['eng_id']."', stock_transfer='OUT', stock_type='OK', type_of_transfer='".$repair_type."', action_taken='Repair Done',qty='1', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 echo "Error detailsSL: " . mysqli_error($this->link) . "";
				}
			
				$res_reapirdata = mysqli_query($this->link,"INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_loction']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='R0087', partcode='".$prd_code."', part_qty='1',close_date='".$today."', warranty_status = '".$warranty_status_new."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',app_version='".$app_version."'");
				//// check if query is not executed
				if (!$res_reapirdata) {
					 $flag = false;
					 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
				}
				
					
				$res_invt = mysqli_query($this->link,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$old_s['current_loction']."' and partcode='".$prd_code."' and locationuser_code='".$old_s['eng_id']."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
						
				
		}
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$today."',close_time='".$currtime."',pen_status='6',dop='".$dop."',warranty_status='".$warranty_status."',close_rmk='".$closed_reason."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' , close_tat = '".$ctat."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."',path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',ws_void='".$ws_void."',ws_void_reason='".$ws_void_reason."' , electric_fail_hrs = '".$electric_fail_hrs."',verify_serial='".$verify_serial."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	 
	  $res_usr_daily=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$old_s['eng_id']."', activity = 'JOB CLOSE', entry_date = '".$today."', entry_time = '".$currtime."', longitude = '".$longitude."', latitude = '".$latitude."', address = '".$address."', ref_no = '".$job_no."', job_status = '".$repair_status."', job_sub_status = '".$repair_status."', warranty_status = '".$warranty_status."' ") or die(mysqli_error($this->link));

				if(!$res_usr_daily){
					$flag = false;
					$error_msg = "Error user_daily_track: " . mysqli_error($this->link) . ".";
				}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='Installation Done',outcome='Installation Done',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";

	$resulth=mysqli_query($this->link,$query);
		if (!$resulth) {
			 $flag = false;
			 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
		}
		
		

		if(($serial_no != $old_s['imei']) && $serial_no != ""){
			$remark_sr_change = "~ OLD BSN - ".$old_s['imei'].", New BSN - ".$serial_no;
			$query_sr_change = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='BSN Change By App',outcome='BSN Change By App',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark_sr_change."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
			$result_sr_change=mysqli_query($this->link,$query_sr_change);
			if(!$result_sr_change) {
				$flag = false;
				$error_msg = "Error sr_change_history: " . mysqli_error($this->link) . ".";
			}
		}




$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}	
	/////////////	end check BSN logic commented by 11-06-2025
 	/*}else{
		$flag = false;
		$error_msg = "Error! You are using duplicate BSN, Try again.";
	}	*/


if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 }     
 
 //////////////////////////////////////////////////Request For Approval///////////////////////////////////////////////////
 
  public function savejobdataRequest($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$request_reason,$remark,$part_consume,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$ws_void,$ws_void_reason,$electric_fail_hrs,$app_version,$upload_doc,$phy_condition,$img_sr,$img_prd,$fault,$solution,$ws_days,$balance_wsdays,$productid,$mfd,$mfd_ex,$eng_id,$verify_serial) { 
//print_r('ddddddddddd');exit;

	$flag = true;


	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today = $this->dt_format->format('Y-m-d');
	$currtime = $this->dt_format->format('H:i:s');

	///////////////////////////////////////////////////
	$test_duplicate_flag = "";
	if($job_no != ""){ $str = " and job_no != '".$job_no."' "; }else{ $str = ""; }
	$result_AA = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serial_no."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link)); 
	$result_BB = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serial_no."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 

	$result_CC = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serial_no."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 

	/*if(mysqli_num_rows($result_AA)==0){
		if(mysqli_num_rows($result_BB)==0){
			if(mysqli_num_rows($result_CC)==0){
				$test_duplicate_flag = "1";	
			}else{
				$test_duplicate_flag = "2";	
			}	
		}else{
			$test_duplicate_flag = "2";		
		}	
	}else{
		$test_duplicate_flag = "2";	
	}

	//////////////////////////////////////////////////
	if($test_duplicate_flag=="1"){*/
	
	$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
 ////////////////// Update Complaint Master //////////////////
 if($repair_status==50){
	 
	 ##### Calculate Warranty End Date
	 $warr_end_date = date('Y-m-d', strtotime($dop. ' + '.$ws_days.' days'));
	  
	 if($repair_status==50 && $solution=='SG031'){
	     $repair_st = 81;
		 $repair_st1 = 8;
		 $repl_req_flag = "Replacement Request";
	 }else{
	     $repair_st = $repair_status;
		 $repair_st1 = $repair_status;
		 $repl_req_flag = "";
	 }
	 
 
 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_st."', sub_status='".$repair_st1."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_reason='".$request_reason."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',ws_void='".$ws_void."',ws_void_reason='".$ws_void_reason."' , electric_fail_hrs = '".$electric_fail_hrs."',phy_cond='".$phy_condition."',doa_bag='".$upload_doc."',path_img4='".$img_sr."',path_img5='".$img_prd."',fault_code='".$fault."',solution_code='".$solution."',warranty_days='".$ws_days."',balance_warranty_days='".$balance_wsdays."',product_id='".$productid."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',warranty_end_date='".$warr_end_date."',verify_serial='".$verify_serial."', doa_rej_rmk = '".$remark."',repl_req_flag='".$repl_req_flag."' where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
 
 
 $res_usr_daily=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$old_s['eng_id']."', activity = 'Request For Approval', entry_date = '".$today."', entry_time = '".$currtime."', longitude = '".$longitude."', latitude = '".$latitude."', address = '".$address."', ref_no = '".$job_no."', job_status = '".$repair_status."', job_sub_status = '".$repair_status."', warranty_status = '".$warranty_status."' ") or die(mysqli_error($this->link));

				if(!$res_usr_daily){
					$flag = false;
					$error_msg = "Error user_daily_track: " . mysqli_error($this->link) . ".";
				}

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_st."',activity='Pending For Approval',outcome='".$request_reason."',updated_by='".$eng_id."', warranty_status='".$warranty_status."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

}


 if($repair_status==12){
 
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$today."',close_time='".$currtime."',pen_status='6',dop='".$dop."',warranty_status='".$warranty_status."',close_rmk='".$closed_reason."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',ws_void='".$ws_void."',ws_void_reason='".$ws_void_reason."' ,electric_fail_hrs = '".$electric_fail_hrs."',fault_code='".$fault."',solution_code='".$solution."',warranty_days='".$ws_days."',balance_warranty_days='".$balance_wsdays."',product_id='".$productid."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',warranty_end_date='".$warr_end_date."',verify_serial='".$verify_serial."' where job_no='".$job_no."'");
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Call Cancel',outcome='Call Cancel',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
			
	### If Complant will closed & PNA is pending then Part request will auto cancel, requirement by Mr. Ashish Sir 21/Sep/2022
					$sql_autopart = mysqli_query($this->link, "SELECT id FROM auto_part_request WHERE job_no = '".$job_no."' and status='3'");
					$rows_autopart= mysqli_num_rows($sql_autopart);
					if($rows_autopart>0){
						
						$result_autopart = mysqli_query($this->link,"update auto_part_request set status = '5',cancel_date  = '".$today."' ,remark ='Job Cancel'  where job_no = '".$job_no."' and status='3' ");
					//// check if query is not executed
						if (!$result_autopart) {
							 $flag = false;
							 $err_msg = "Error detailsAutoPart: " . mysqli_error($this->link) . ".";
						}	
					}
					### End Part Request Cancel condition		

}

if(($serial_no != $old_s['imei']) && $serial_no != ""){
	$remark_sr_change = "# OLD BSN - ".$old_s['imei'].", New BSN - ".$serial_no;
	$query_sr_change = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_st."',activity='BSN Change By App',outcome='BSN Change By App',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark_sr_change."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
	$result_sr_change=mysqli_query($this->link,$query_sr_change);
	if(!$result_sr_change) {
		$flag = false;
		$error_msg = "Error sr_change_history: " . mysqli_error($this->link) . ".";
	}
}
/////////////	end check BSN logic commented by 11-06-2025
/*}else{
	$flag = false;
	$error_msg = "Error! You are using duplicate BSN, Try again.";
}*/	


if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 } 
 
/////////////////////////////////////PNA/////////////////////////////////
 
 

 
  public function savejobpna($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$request_reason,$remark,$pnaList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$ws_void,$ws_void_reason ,$electric_fail_hrs,$app_version,$verify_serial) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today = $this->dt_format->format('Y-m-d');
	$currtime = $this->dt_format->format('H:i:s');

	///////////////////////////////////////////////////
	$test_duplicate_flag = "";
	if($job_no != ""){ $str = " and job_no != '".$job_no."' "; }else{ $str = ""; }
	$result_AA = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serial_no."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link));

	$result_BB = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serial_no."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 

	$result_CC = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serial_no."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 

	/*if(mysqli_num_rows($result_AA)==0){
		if(mysqli_num_rows($result_BB)==0){
			if(mysqli_num_rows($result_CC)==0){
				$test_duplicate_flag = "1";	
			}else{
				$test_duplicate_flag = "2";	
			}	
		}else{
			$test_duplicate_flag = "2";		
		}	
	}else{
		$test_duplicate_flag = "2";	
	}

	//////////////////////////////////////////////////
	if($test_duplicate_flag=="1"){*/

 ////////////////// Update Complaint Master //////////////////
$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
 
 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',ws_void='".$ws_void."',ws_void_reason='".$ws_void_reason."' ,electric_fail_hrs = '".$electric_fail_hrs."',verify_serial='".$verify_serial."' where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	  
	  $res_usr_daily=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$old_s['eng_id']."', activity = 'PNA', entry_date = '".$today."', entry_time = '".$currtime."', longitude = '".$longitude."', latitude = '".$latitude."', address = '".$address."', ref_no = '".$job_no."', job_status = '".$repair_status."', job_sub_status = '".$repair_status."', warranty_status = '".$warranty_status."' ") or die(mysqli_error($this->link));

				if(!$res_usr_daily){
					$flag = false;
					$error_msg = "Error user_daily_track: " . mysqli_error($this->link) . ".";
				}
 	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Part Not Available',outcome='Part Not Available',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

			if(($serial_no != $old_s['imei']) && $serial_no != ""){
				$remark_sr_change = "* OLD BSN - ".$old_s['imei'].", New BSN - ".$serial_no;
				$query_sr_change = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='BSN Change By App',outcome='BSN Change By App',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark_sr_change."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
				$result_sr_change=mysqli_query($this->link,$query_sr_change);
				if(!$result_sr_change) {
					$flag = false;
					$error_msg = "Error sr_change_history: " . mysqli_error($this->link) . ".";
				}
			}		
	
	//echo $partUsedList[0]["partid"];
		for($i=0; $i<count($pnaList) ; $i++) {
			////// insert in repair details
	
			//echo $prd_code = $partUsedList[$i];
			 $prd_code =  $pnaList[$i]->partcode;
	
			

$res_autopartreq = mysqli_query($this->link,"INSERT INTO auto_part_request set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."' , qty='1', status='3', request_date='".$updateDate."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
			}

			$res_autopartreqeng = mysqli_query($this->link,"INSERT INTO part_demand set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."', qty='1', status='1', request_date='".$updateDate."',eng_id='".$old_s['eng_id']."'");
			//// check if query is not executed
			if (!$res_autopartreqeng) {
				 $flag = false;
				 $error_msg = "Error details2eng: " . mysqli_error($this->link) . ".";
			}
			}
/////////////	end check BSN logic commented by 11-06-2025
		/*}else{
			$flag = false;
			$error_msg = "Error! You are using duplicate BSN, Try again.";
		}*/


if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 } 
 ////////////////////////////////////////////////////////Estimate Pending List//////////////////////////////////////////
 
 
 
 
 public function savejobep($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$request_reason,$remark,$epList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$serviceCharge,$serviceTaxAmt,$totalService,$totalPartAmt,$ws_void,$ws_void_reason,$electric_fail_hrs,$app_version,$verify_serial) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today = $this->dt_format->format('Y-m-d');
	$currtime = $this->dt_format->format('H:i:s');

	///////////////////////////////////////////////////
	$test_duplicate_flag = "";
	if($job_no != ""){ $str = " and job_no != '".$job_no."' "; }else{ $str = ""; }
	$result_AA = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serial_no."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link));

	$result_BB = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serial_no."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 

	$result_CC = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serial_no."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 

	/*if(mysqli_num_rows($result_AA)==0){
		if(mysqli_num_rows($result_BB)==0){
			if(mysqli_num_rows($result_CC)==0){
				$test_duplicate_flag = "1";	
			}else{
				$test_duplicate_flag = "2";	
			}	
		}else{
			$test_duplicate_flag = "2";		
		}	
	}else{
		$test_duplicate_flag = "2";	
	}

	//////////////////////////////////////////////////
	if($test_duplicate_flag=="1"){*/

 ////////////////// Update Complaint Master //////////////////
 $old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
 
 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."',ws_void='".$ws_void."',ws_void_reason='".$ws_void_reason."',electric_fail_hrs = '".$electric_fail_hrs."',verify_serial='".$verify_serial."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	 $res_usr_daily=mysqli_query($this->link,"insert into user_daily_track set user_id = '".$old_s['eng_id']."', activity = 'Estimate Pending For Cost', entry_date = '".$today."', entry_time = '".$currtime."', longitude = '".$longitude."', latitude = '".$latitude."', address = '".$address."', ref_no = '".$job_no."', job_status = '".$repair_status."', job_sub_status = '".$repair_status."', warranty_status = '".$warranty_status."' ") or die(mysqli_error($this->link));

				if(!$res_usr_daily){
					$flag = false;
					$error_msg = "Error user_daily_track: " . mysqli_error($this->link) . ".";
				}
 
 
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Estimate Pending',outcome='Part Not Available',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

			if(($serial_no != $old_s['imei']) && $serial_no != ""){
				$remark_sr_change = "% OLD BSN - ".$old_s['imei'].", New BSN - ".$serial_no;
				$query_sr_change = "INSERT INTO call_history SET job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='BSN Change By App',outcome='BSN Change By App',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark_sr_change."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";
				$result_sr_change=mysqli_query($this->link,$query_sr_change);
				if(!$result_sr_change) {
					$flag = false;
					$error_msg = "Error sr_change_history: " . mysqli_error($this->link) . ".";
				}
			}


		$res_maxcount = mysqli_query($this->link,"SELECT COUNT(eid) as maxcnt FROM estimate_master where location_code='".$old_s['current_location']."'");
		$row_maxcount = mysqli_fetch_assoc($res_maxcount);
		//// next estimate no.
		$next_no = $row_maxcount['maxcnt']+1;
		$estimate_no = $job_no."E".$next_no;
		 $totalPartAmt =  $partEP->totalPartAmt;
		
		////// insert in estimate master
		$res_estimaster = mysqli_query($this->link,"INSERT INTO estimate_master set estimate_no='".$estimate_no."', estimate_date='".$cls_dt[0]."', location_code='".$old_s['current_location']."', from_address='".$location_addrs."', to_address='".$old_s['address']."', estimate_amount='".$totalPartAmt."' , entry_by='".$_SESSION['userid']."', entry_ip='".$_SERVER['REMOTE_ADDR']."', status='5',job_no='".$job_no."'");
		//// check if query is not executed
		if (!$res_estimaster) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
		}
		///// Insert in item data by picking each data row one by one
		
		/////initialize post array variables
	for($i=0; $i<count($epList) ; $i++) {
			////// insert in repair details
			 $prd_code =  $epList[$i]->partcode;
			  $price =  $epList[$i]->price;
			   $taxAmt =  $epList[$i]->taxAmt;
			     $total =  $epList[$i]->total;
			  
			//// insert in estimate data
			$res_estidata = mysqli_query($this->link,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='".$prd_code."', hsn_code='".$ep_hsncode[$k]."', part_name='".$partdetail."', basic_amount='".$price."', tax_per='18', tax_amt='".$taxAmt."' , total_amount='".$total."',job_no='".$job_no."'");
			//// check if query is not executed
			if (!$res_estidata) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
			}
		}/////end foreach loop
		
		
		 $serv =  $serviceCharge;
		 $servtax =  $serviceTaxAmt;
		 $totalService =  $totalService;
		//// check if any service charge is applicable then we have to insert one more entry in estimate items
		$res_servcharge = mysqli_query($this->link,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='SERV001', hsn_code='".$_POST['ser_tax_hsn']."', part_name='Service Charge', basic_amount='".$serv."', tax_per='18', tax_amt='".$servtax."' , total_amount='".$totalService."',job_no='".$job_no."'");
		//// check if query is not executed
		if (!$res_servcharge) {
			 $flag = false;
			 $error_msg = "Error details4: " . mysqli_error($this->link) . ".";
		}
/////////////	end check BSN logic commented by 11-06-2025

	/*}else{
		$flag = false;
		$error_msg = "Error! You are using duplicate BSN, Try again.";
	}*/	


if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 } 
 
 
 
 
 
 
 

 public function storeUser($job_no,$status,$updateDate,$closed_reason,$update_remark,$fault_code,$repairList,$partEP,$partPNA,$partInstallationDone,$partDemoDone,$pending_reason,$eid,$replacedBy,$replacemetModel,$travelKM,$repair_central,$repair_code,$requestReason,$replacedBySrNo,$latitude,$longitude,$address,$confirmedBy,$contactNumber,$customerRemark,$serviceRating,$customerFeedbackDate,$serial_no,$selected_model_id,$selected_product_id,$dop,$warranty_status,$modifDone,$ta_da,$newProductProvidedSpinner_txt,$revisit_status,$revisit_date,$revisit_remark,$engObsrvdReport,$mfd_ex,$app_version,$amc_expiry_date,$main_serial,$location_on_pcb,$pcb_changed_flag,$battery_make,$old_pcb_number,$new_modelid,$battery_rate,$mfd,$r_sticker,$payment_receive_flag,$new_pcb_number) { 
 
 $repair_status=$status;
 $cls_dt=explode(" ",$updateDate); 
  $con_dt=explode(" ",$customerFeedbackDate);
   
    $mod_name_s=mysqli_fetch_array(mysqli_query($this->link,"select model, wp, out_warranty from model_master where model_id='".$selected_model_id."'"));
    $model_name = $mod_name_s['model'];
 
	$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
	/////////////// Warranty calculate ////////////////////
	if($mod_name_s['out_warranty'] == "Y"){
		$warranty_status_new = "OUT";
	}else{
		$date_parts1=explode("-", $dop); $date_parts2=explode("-", $old_s['open_date']);
		$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
		$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
		$date_diff = ($end_date - $start_date);
		//$date_diff = daysDifference($old_s['open_date'],$dop);
		if($date_diff <= $mod_name_s['wp']){
			$warranty_status_new = "IN";
		}else{
			$warranty_status_new = "OUT";
		}	
	}
	///////////////////////////////////////////////////////

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	
	if($repair_status==3){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='".$status."',reason='".$pending_reason."',app_rmk='Done By App' ,dop='".$dop."',warranty_status='".$warranty_status_new."',imei='".$serial_no."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."' where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Part Not Available',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";




	$result=mysqli_query($this->link,$query);
	
	//echo $partUsedList[0]["partid"];
		for($i=0; $i<count($partPNA) ; $i++) {
			////// insert in repair details
	
			//echo $prd_code = $partUsedList[$i];
			 $prd_code =  $partPNA[$i]->partid;
	
			

$res_autopartreq = mysqli_query($this->link,"INSERT INTO auto_part_request set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."' , qty='1', status='3', request_date='".$updateDate."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
			}

			$res_autopartreqeng = mysqli_query($this->link,"INSERT INTO part_demand set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."', qty='1', status='1', request_date='".$updateDate."',eng_id='".$eid."'");
			//// check if query is not executed
			if (!$res_autopartreqeng) {
				 $flag = false;
				 $error_msg = "Error details2eng: " . mysqli_error($this->link) . ".";
			}
			}

}

if($repair_status==50){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',app_reason='".$requestReason."',pen_status='".$status."' ,app_rmk='Done By App',dop='".$dop."',warranty_status='".$warranty_status_new."',reason='".$pending_reason."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."',imei='".$serial_no."',doa_approval='' where job_no='".$job_no."'");
	
			if (!$res_jobsheet) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Pending For Approval',outcome='".$requestReason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

}
		
if($old_s['status']!=$repair_status){
	$close_tat = "";
	$lc_st = mysqli_fetch_array(mysqli_query($this->link, "select stateid from location_master where location_code = '".$old_s['current_location']."' "));
	$loc_state=$lc_st['stateid'];
	//echo "UPDATE jobsheet_data set modifDone='".$modifDone."', ta_da='".$ta_da."',newProductProvidedSpinner_txt='".$newProductProvidedSpinner_txt."',revisit_status='".$revisit_status."' ,revisit_date='".$revisit_date."',revisit_remark='".$revisit_remark."',engObsrvdReport='".$engObsrvdReport."',mfd_ex='".$mfd_ex."', app_version = '".$app_version."', amc_expiry_date = '".$amc_expiry_date."',main_serial='".$main_serial."',location_on_pcb='".$location_on_pcb."',pcb_changed_flag='".$pcb_changed_flag."',battery_make='".$battery_make."',old_pcb_number='".$old_pcb_number."',new_modelid='".$new_modelid."',battery_rate='".$battery_rate."',mfd='".$mfd."', r_sticker='".$r_sticker."',payment_receive_flag='".$payment_receive_flag."',new_pcb_number='".$new_pcb_number."' where job_no='".$job_no."'";

	
		$res_jobsheetk = mysqli_query($this->link,"UPDATE jobsheet_data set modifDone='".$modifDone."', ta_da='".$ta_da."',newProductProvidedSpinner_txt='".$newProductProvidedSpinner_txt."',revisit_status='".$revisit_status."' ,revisit_date='".$revisit_date."',revisit_remark='".$revisit_remark."',engObsrvdReport='".$engObsrvdReport."',mfd_ex='".$mfd_ex."', app_version = '".$app_version."', amc_expiry_date = '".$amc_expiry_date."',main_serial='".$main_serial."',location_on_pcb='".$location_on_pcb."',pcb_changed_flag='".$pcb_changed_flag."',battery_make='".$battery_make."',old_pcb_number='".$old_pcb_number."',new_modelid='".$new_modelid."',battery_rate='".$battery_rate."',mfd='".$mfd."', r_sticker='".$r_sticker."',payment_receive_flag='".$payment_receive_flag."',new_pcb_number='".$new_pcb_number."' where job_no='".$job_no."'");
	
			if (!$res_jobsheetk) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
	////////////////////////////////check in weak days///////////////////
	$sql_wk="select weak_day from holidays where state='".$loc_state."' and status='1'";
	$rs_wk=mysqli_query($this->link, $sql_wk) or die("err in holiday".mysqli_error($this->link));
	$row_wk=mysqli_fetch_assoc($rs_wk);
	$weak_day=$row_wk['weak_day'];
	
	if($old_s["call_for"]!="Workshop" ){
		$open=$old_s['vistor_date'];
	}else {
		$open=$old_s['open_date'];	
	}	
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
	$holidays=0;
	$sql_h="select date from holidays where status='1' and date between '".$open."' and '".$today."' and (h_type='National' or (state='".$loc_state."' and state!=''))";
	$rs_h=mysqli_query($this->link,$sql_h) or die(mysqli_error($this->link));
	while($row_h=mysqli_fetch_assoc($rs_h)){
		$date=date('D',strtotime($row_h['date']));
		if($date!=$weak_day){
			$holidays++;
		}
	}
	
	$count_wk=0;
	while($open<=$today){
		$chk_date=date('D',strtotime($open));
		if($chk_date==$weak_day) {
			$count_wk++;
		}
		$open=date('Y-m-d', strtotime("+1 day", strtotime($open)));
	}

	$tatminus=$count_wk+$holidays;
	
	if($repair_status==10 || $repair_status==8 || $repair_status==48 || $repair_status==49 || $repair_status==12){
		$st_status=6;
	}else{
		$st_status=2;
	}

//////////////////////////////status Refer to Centeral Workshop/////////////////////////////////////////////////////
if($repair_status==4){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',reason='".$pending_reason."',pen_status='".$status."',app_rmk='Done By App',dop='".$dop."',warranty_status='".$warranty_status_new."',imei='".$serial_no."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."' where job_no='".$job_no."'");
	
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
		
		$repairdetail = mysqli_query($this->link,"INSERT INTO repair_detail set location_code='".$old_s['current_location']."', repair_location='".$old_s['current_location']."', job_no='".$job_no."', model_id='".$old_s['model_id']."', partcode='".$old_s['partcode']."',rep_lvl = '1.00' ,status='4', repair_code = '".$repair_code."' , fault_code ='".$fault_code."',eng_id='".$eid."', warranty_status = '".$warranty_status_new."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."' ");

		//// check if query is not executed

		if (!$repairdetail) {

			 $flag = false;

			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";

		}

}

//////////////////////////////status WIP/////////////////////////////////////////////////////
/*if($repair_status==7){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',reason='".$pending_reason."',pen_status='".$status."' ,app_rmk='Done By App',dop='".$dop."',warranty_status='".$warranty_status_new."',imei='".$serial_no."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."' where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Job Pending',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$result=mysqli_query($this->link,$query);

}*/

//////////////////////////////status WIP/////////////////////////////////////////////////////
/*if($repair_status==50){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',app_reason='".$requestReason."',pen_status='".$status."' ,app_rmk='Done By App',dop='".$dop."',warranty_status='".$warranty_status."',reason='".$pending_reason."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."',imei='".$serial_no."' where job_no='".$job_no."'");
	
			if (!$res_jobsheet) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Pending For Approval',outcome='".$requestReason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

}
*/
////////////////////////////////////////EP/
if($repair_status==5){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='".$status."',reason='".$pending_reason."',app_rmk='Done By App',dop='".$dop."',warranty_status='".$warranty_status_new."',imei='".$serial_no."' ,model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."' where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Estimate Pending',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$result=mysqli_query($this->link,$query);
	
	
		$res_maxcount = mysqli_query($this->link,"SELECT COUNT(eid) as maxcnt FROM estimate_master where location_code='".$old_s['current_location']."'");
		$row_maxcount = mysqli_fetch_assoc($res_maxcount);
		//// next estimate no.
		$next_no = $row_maxcount['maxcnt']+1;
		$estimate_no = $job_no."E".$next_no;
		 $totalPartAmt =  $partEP->totalPartAmt;
		
		////// insert in estimate master
		$res_estimaster = mysqli_query($this->link,"INSERT INTO estimate_master set estimate_no='".$estimate_no."', estimate_date='".$cls_dt[0]."', location_code='".$old_s['current_location']."', from_address='".$location_addrs."', to_address='".$old_s['address']."', estimate_amount='".$totalPartAmt."' , entry_by='".$_SESSION['userid']."', entry_ip='".$_SERVER['REMOTE_ADDR']."', status='5',job_no='".$job_no."'");
		//// check if query is not executed
		if (!$res_estimaster) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
		}
		///// Insert in item data by picking each data row one by one
		
		/////initialize post array variables
	for($i=0; $i<count($partEP) ; $i++) {
			////// insert in repair details
			 $prd_code =  $partEP[$i]->part_code;
			  $price =  $partEP[$i]->price;
			   $taxAmt =  $partEP[$i]->taxAmt;
			     $total =  $partEP[$i]->total;
			  
			//// insert in estimate data
			$res_estidata = mysqli_query($this->link,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='".$prd_code."', hsn_code='".$ep_hsncode[$k]."', part_name='".$partdetail."', basic_amount='".$price."', tax_per='18', tax_amt='".$taxAmt."' , total_amount='".$total."',job_no='".$job_no."'");
			//// check if query is not executed
			if (!$res_estidata) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
			}
		}/////end foreach loop
		
		
		 $serv =  $partEP->serviceCharge;
		 $servtax =  $partEP->serviceTaxAmt;
		 $totalService =  $partEP->totalService;
		//// check if any service charge is applicable then we have to insert one more entry in estimate items
		$res_servcharge = mysqli_query($this->link,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='SERV001', hsn_code='".$_POST['ser_tax_hsn']."', part_name='Service Charge', basic_amount='".$serv."', tax_per='18', tax_amt='".$servtax."' , total_amount='".$totalService."',job_no='".$job_no."'");
		//// check if query is not executed
		if (!$res_servcharge) {
			 $flag = false;
			 $error_msg = "Error details4: " . mysqli_error($this->link) . ".";
		}

}

/////////////////////////////////////// PNA/////////////////////////////////////////////////////////
/*if($repair_status==3){
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='".$status."',reason='".$pending_reason."',app_rmk='Done By App' ,dop='".$dop."',warranty_status='".$warranty_status."',imei='".$serial_no."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."' where job_no='".$job_no."'");
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Part Not Available',outcome='".$reason."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";




	$result=mysqli_query($this->link,$query);
	
		for($i=0; $i <count( $partPNA) ; $i++) {
		
	
			 $prd_code =  $partPNA[$i]->partid;
	
			

$res_autopartreq = mysqli_query($this->link,"INSERT INTO auto_part_request set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."' , qty='1', status='3', request_date='".$today."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
			}

			$res_autopartreqeng = mysqli_query($this->link,"INSERT INTO part_demand set location_code='".$old_s['current_location']."', to_location='', job_no='".$job_no."', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', model_id='".$old_s['model_id']."', partcode='".$prd_code."', qty='1', status='1', request_date='".$today."',eng_id='".$eid."'");
			//// check if query is not executed
			if (!$res_autopartreqeng) {
				 $flag = false;
				 $error_msg = "Error details2eng: " . mysqli_error($this->link) . ".";
			}
			}

}*/
////////////////////////////////// installation/Demo Status///////////////////////////////////////////////////////
if($repair_status==48 || $repair_status==49){
	if($repair_status==48){
		$repair_type="Installation";
		$partUsedList=partInstallationDone;
		}else{
		$repair_type="Demo";
		$partUsedList=partDemoDone;
		}
		
		if($old_s["call_for"]!="Workshop" ){

			
			$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
			$close_tat = $dt_dif-$tatminus;
		}else{
			
			$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
	    	$close_tat = $dt_dif-$tatminus;
		}
		
		//// open close tat ///////
		$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
		$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
		$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
		$dt_dif_oc = $end_date_oc - $start_date_oc;
		
		$tat_open_close = $dt_dif_oc-$tatminus;
		
		/////// billing product item details /////	
		$ag_count = 0;	
		$aging_close_tat = "";
		$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'Job PNA' "));
		$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'PNA Part Received' "));
		$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
		$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			
			$date_parts1=explode("-", $pna_rais_info[0]); $date_parts2=explode("-", $pna_rec_info[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$ag_count = $end_date - $start_date;
			
		}else{
			$ag_count = 0;	
		}
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			if($close_tat!=""){
				$aging_close_tat = $close_tat.",".$ag_count;
			}else{
				$aging_close_tat = "";
			}
		}
		$aggg = $ag_count."~".$aging_close_tat;
		
		$getage = explode("~", $aggg);
		
		$ctat = ($close_tat-$getage[0]);
		
			for($i=0; $i<count($partUsedList) ; $i++) {
				////// insert in repair details
				$prd_code =  $partUsedList[$i]->partid;
			
				//// update inventory as user consume part
				$res_challan = mysqli_query($this->link, "SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$_SESSION['asc_code']."' and partcode='".$prd_code."' and okqty != fifi_ty ") or die(mysqli_error($this->link));
				$row_challan = mysqli_fetch_assoc($res_challan);
						
				$return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
				
				$splitted_fifo_data = explode("~",$return_data);



				
				$return_fifo = "UPDATE  fifo_list set fifi_ty =fifi_ty + 1 where  partcode='" .$prd_code. "' and ref_sno='".$splitted_fifo_data[5]."'"; 
				$queryfifo_return = mysqli_query($this->link, $return_fifo);
				//// check if query is not executed
				if (!$queryfifo_return) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code10: ".mysqli_error($this->link);
				}
										
				$bill_fifo = "UPDATE  billing_product_items set return_qty_fifo=return_qty_fifo+1 where  partcode='" .$prd_code. "' and id='".$splitted_fifo_data[5]."'"; 
				$queryfifo_bill = mysqli_query($this->link, $bill_fifo);
				//// check if query is not executed
				if (!$queryfifo_bill) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code11: ".mysqli_error($this->link);
				}
								
				$prt_prc = mysqli_fetch_array(mysqli_query($this->link, "select customer_price from partcode_master where partcode = '".$prd_code."' "));
				
				$result=mysqli_query($this->link, "insert into stock_ledger set reference_no='".$job_no."',reference_date='".$today."',partcode='".$prd_code."',from_party='".$old_s['current_location']."', to_party='".$old_s['customer_name']."', stock_transfer='OUT', stock_type='OK', type_of_transfer='".$repair_type."', action_taken='Repair Done',qty='1', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 echo "Error detailsSL: " . mysqli_error($this->link) . "";
				}
			
				$res_reapirdata = mysqli_query($this->link,"INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_loction']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='R0087', partcode='".$prd_code."', part_qty='1',close_date='".$today."', warranty_status = '".$warranty_status_new."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."' ");
				//// check if query is not executed
				if (!$res_reapirdata) {
					 $flag = false;
					 $error_msg = "Error details2: " . mysqli_error($this->link) . ".";
				}
				
					
				$res_invt = mysqli_query($this->link,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$old_s['current_loction']."' and partcode='".$prd_code."' and locationuser_code='".$eid."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
						
				
		}
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',dop='".$dop."',warranty_status='".$warranty_status_new."',close_rmk='".$closed_reason."' ,model_id = '".$selected_model_id."', model = '".$model_name."',app_rmk='Done By App',imei='".$serial_no."' , close_tat = '".$ctat."', pen_status='".$st_status."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";

	$resulth=mysqli_query($this->link,$query);
		if (!$resulth) {
			 $flag = false;
			 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
		}
		
		


$sql_rep=mysqli_query($this->link,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");

$row_max=mysqli_fetch_array($sql_rep);
if($warranty_status_new=='IN'){



$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}	
}
	
}



/////////////////////////////////////////////////////Confirmation  CASE//////////////
if($repair_status==344){

		///// entry in call/job  history
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='Call Confirmed',outcome='Call Confirmed By customer',updated_by='".$eid."',  warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."',ip='".$_SERVER['REMOTE_ADDR']."',travel_km='".$travelKM."',travel='Y'";
	$result=mysqli_query($this->link,$query);
			
	    $sql_update = "UPDATE jobsheet_data set status ='10', sub_status ='10', hand_date ='".$con_dt[0]."',hand_time='".$con_dt[1]."',recipient_name='".$confirmedBy."',recipient_contact='".$contactNumber."',service_rmak='".$customerRemark."',rating='".$serviceRating."' where job_no ='".$job_no."' ";
    	$res_update=mysqli_query($this->link,$sql_update);
		//// check if query is not executed
		if (!$res_update) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($this->link) . ".";
		}	

}

////////////////////////////////// Repair Done///////////////////////////////////////////////////////
if($repair_status==10 || $repair_status==7){
	
		$repair_type="Set delivered";

		
			for($i=0; $i<count($repairList) ; $i++) {
			////// insert in repair details
			
				$prd_code =  $repairList[$i]->partcode;
				$symp_code =  $repairList[$i]->symp_code;
				$solutioncode =  $repairList[$i]->solutioncode;
				
			 $sql_rep=mysqli_query($this->link, "select rep_level,part_replace from repaircode_master where rep_code='".$solutioncode."'" )or die(mysqli_error($this->link)); 
			$rep_row = mysqli_fetch_array($sql_rep);
			
				$res_challan = mysqli_query($this->link, "SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$_SESSION['asc_code']."' and partcode='".$prd_code."' and okqty != fifi_ty ") or die(mysqli_error($this->link));
				$row_challan = mysqli_fetch_assoc($res_challan);
						
				$return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
				
				$splitted_fifo_data = explode("~",$return_data);
					
			$res_reapirdata = mysqli_query($this->link, "INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='".$solutioncode."', partcode='".$prd_code."', part_qty='1',fault_code='".$symp_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$cls_dt[0]."', warranty_status = '".$warranty_status_new."' ,old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."' ");
			
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error In repair Details table: " . mysqli_error($this->link) . ".";
			}
			
			if($prd_code){
				//// update inventory as user consume part
			
				
				$return_fifo = "UPDATE  fifo_list set fifi_ty =fifi_ty + 1 where  partcode='" .$prd_code. "' and ref_sno='".$splitted_fifo_data[5]."'"; 
				
				$queryfifo_return = mysqli_query($this->link, $return_fifo);
				
				//// check if query is not executed
				if (!$queryfifo_return) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code10: ".mysqli_error($this->link);
				}
											
				$bill_fifo = "UPDATE  billing_product_items set return_qty_fifo=return_qty_fifo+1 where  partcode='" .$prd_code. "' and id='".$splitted_fifo_data[5]."'"; 		
																	
				$queryfifo_bill = mysqli_query($this->link, $bill_fifo);
				
				if (!$queryfifo_bill) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code11: ".mysqli_error($this->link);
				}
				
				$prt_prc = mysqli_fetch_array(mysqli_query($this->link, "select customer_price from partcode_master where partcode = '".$prd_code."' "));
				
				$result=mysqli_query($this->link, "insert into stock_ledger set reference_no='".$job_no."',reference_date='".$today."',partcode='".$prd_code."',from_party='".$old_s['current_location']."', to_party='".$old_s['customer_name']."', stock_transfer='OUT', stock_type='OK', type_of_transfer='JOB REPAIR', action_taken='Repair Done',qty='1', rate='".$prt_prc['customer_price']."', create_by='".$old_s['eng_id']."', create_date='".$today."', create_time='".$currtime."', ip='".$_SERVER['REMOTE_ADDR']."'");
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 echo "Error detailsSL: " . mysqli_error($this->link) . "";
				}
				
			}

 			$res_invt_mo = mysqli_query($this->link, "UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and mount_qty >0");
			
				//// check if query is not executed
				if (!$res_invt_mo) {
					 $flag = false;
					 $error_msg = "Error detailsMount: " . mysqli_error($this->link) . ".";
				}
				
				$res_invt = mysqli_query($this->link, "UPDATE user_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$eid."' and okqty >0");
			
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
						
				/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($warranty_status_new=='IN'){
					
					$sql_part=mysqli_query($this->link, "select faulty_part, partcode from partcode_master where partcode='".$prd_code."'" )or die(mysqli_error($this->link)); 
					$rep_part = mysqli_fetch_array($sql_part);
			
					$res_faulty_user = mysqli_query($this->link, "UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$rep_part['partcode']."' and locationuser_code='".$eid."'");
					
					//// check if query is not executed
					if (!$res_faulty_user) {
						 $flag = false;
						 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
					}
					
					if($rep_part['partcode']!= "" && $rep_part['partcode']!= "39"){
					
						$res_p2cdata = mysqli_query($this->link, "INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_s['imei']."',from_location='".$old_s['current_location']."', partcode='".$rep_part['partcode']."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$eid."',eng_status='1',old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."'");
						
						if (!$res_p2cdata) {

							 $flag = false;
							 $error_msg = "Error details21: " . mysqli_error($this->link) . ".";
						}
					
					}
					
					
			}	
					
				
		
		}
		
		if($old_s["call_for"]!="Workshop" ){
			
			$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
			$close_tat = $dt_dif-$tatminus;	
		}else{
			if($old_s['close_date']!="0000-00-00"){
				
				$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $old_s['close_date']);
				$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
				$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
				$dt_dif = $end_date - $start_date;
				
				$close_tat = $dt_dif-$tatminus;
			}else{
				
				$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
				$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
				$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
				$dt_dif = $end_date - $start_date;
				
				$close_tat = $dt_dif-$tatminus;
			}
		}
		
		//// open close tat ///////
		$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
		$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
		$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
		$dt_dif_oc = $end_date_oc - $start_date_oc;
		
		$tat_open_close = $dt_dif_oc-$tatminus;
		
		/////// billing product item details /////	
		$ag_count = 0;	
		$aging_close_tat = "";
		$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'Job PNA' "));
		$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'PNA Part Received' "));
		$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
		$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			
			$date_parts1=explode("-", $pna_rais_info[0]); $date_parts2=explode("-", $pna_rec_info[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$ag_count = $end_date - $start_date;
			
		}else{
			$ag_count = 0;	
		}
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			if($close_tat!=""){
				$aging_close_tat = $close_tat.",".$ag_count;
			}else{
				$aging_close_tat = "";
			}
		}
		$aggg = $ag_count."~".$aging_close_tat;
		
		$getage = explode("~", $aggg);
		$ctat = ($close_tat-$getage[0]);
		
		$res_jobsheet = mysqli_query($this->link, "UPDATE jobsheet_data set status='10', sub_status='10',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."',app_rmk='Done By App' ,imei='".$serial_no."', model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."',dop='".$dop."',warranty_status='".$warranty_status_new."' , close_tat = '".$ctat."', pen_status='".$st_status."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error detailsin jobsheet1: " . mysqli_error($this->link) . ".";
		}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";
		
		$callhistory=mysqli_query($this->link,$query);
		
		if (!$callhistory) {
			 $flag = false;
			 $error_msg = "Error detailsin call history: " . mysqli_error($this->link) . ".";
		}
	

$sql_rep=mysqli_query($this->link, "select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");

$row_max=mysqli_fetch_array($sql_rep);
if($warranty_status_new=='IN'){

		$max_rep=mysqli_query($this->link, "insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$cls_dt[0]."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
		
		//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}	
}

}

////////////////////////////////// Replacement Done///////////////////////////////////////////////////////
if($repair_status==8){
	
		$repair_type="Job Replacement";

		$prd_code=$replacedBy;
		
		if($old_s["call_for"]!="Workshop" ){
			
			
			$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
			$close_tat = $dt_dif-$tatminus;
		}else{
			
			$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
	    	$close_tat = $dt_dif-$tatminus;
		}
		
		//// open close tat ///////
		$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
		$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
		$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
		$dt_dif_oc = $end_date_oc - $start_date_oc;
		
		$tat_open_close = $dt_dif_oc-$tatminus;
		
		/////// billing product item details /////	
		$ag_count = 0;	
		$aging_close_tat = "";
		$pna_rais_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'Job PNA' "));
		$pna_rec_data = mysqli_fetch_array(mysqli_query($this->link, "Select update_date from call_history where job_no = '".$job_no."' and activity = 'PNA Part Received' "));
		$pna_rais_info = explode(" ",$pna_rais_data['update_date']);
		$pna_rec_info = explode(" ",$pna_rec_data['update_date']);
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			
			$date_parts1=explode("-", $pna_rais_info[0]); $date_parts2=explode("-", $pna_rec_info[0]);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$ag_count = $end_date - $start_date;
			
		}else{
			$ag_count = 0;	
		}
		if(($pna_rais_info[0]!="") && ($pna_rec_info[0]!="")){
			if($close_tat!=""){
				$aging_close_tat = $close_tat.",".$ag_count;
			}else{
				$aging_close_tat = "";
			}
		}
		$aggg = $ag_count."~".$aging_close_tat;




		
		$getage = explode("~", $aggg);
		$ctat = ($close_tat-$getage[0]);
				
			
			
			 $sql_rep=mysqli_query($this->link,"select rep_level,part_replace from repaircode_master where rep_code='".$solutioncode."'" )or die(mysqli_error($this->link)); 
			$rep_row = mysqli_fetch_array($sql_rep);
		
			$res_reapirdata = mysqli_query($this->link,"INSERT INTO repair_detail set job_id='".$old_s['job_id']."', job_no ='".$job_no."', repair_location='".$old_s['current_location']."', repair_type='".$repair_type."', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$eid."' , status='".$repair_status."', remark='".$remark."', repair_code='".$repair_code."', partcode='".$prd_code."', part_qty='1',fault_code='".$fault_code."', rep_lvl='".$rep_row ['rep_level']."',part_repl='".$rep_row ['part_replace'] ."',close_date='".$today."',replace_imei1='".$replacedBySrNo."', warranty_status = '".$warranty_status_new."', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."' ");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error detailsrepair details: " . mysqli_error($this->link) . ".";
			}
			///// extra field of jobsheet data which is to be update
		

	//// check if query is not executed

 	$res_invt_mo = mysqli_query($this->link,"UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and mount_qty >0");
				//// check if query is not executed
				if (!$res_invt_mo) {
					 $flag = false;
					 $error_msg = "Error detailsMount: " . mysqli_error($this->link) . ".";
				}
				
			$res_invt = mysqli_query($this->link,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$old_s['current_location']."' and partcode='".$prd_code."' and locationuser_code='".$eid."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
						
				/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($warranty_status_new=='IN'){
					
				
					 $sql_part=mysqli_query($this->link,"select faulty_part, partcode from partcode_master where partcode='".$prd_code."'" )or die(mysqli_error($this->link)); 
					$rep_part = mysqli_fetch_array($sql_part);
			
				
						$res_faulty_user = mysqli_query($this->link,"UPDATE user_inventory set faulty = faulty+'1' where location_code='".$old_s['current_location']."' and partcode='".$rep_part['partcode']."' and locationuser_code='".$eid."'");
				//// check if query is not executed
				if (!$res_faulty_user) {
					 $flag = false;
					 $error_msg = "Error detailsuserfauty: " . mysqli_error($this->link) . ".";
				}
				
				
				if($rep_part['partcode'] != "" && $rep_part['partcode']!= "39"){
					
					$res_p2cdata = mysqli_query($this->link,"INSERT INTO part_to_credit set job_no ='".$job_no."', imei='".$old_s['imei']."',from_location='".$old_s['current_location']."', partcode='".$rep_part['partcode']."', qty='1',consumedate='".$today."',model_id='".$old_s['model_id']."',status ='4', product_id='".$old_s['product_id']."', brand_id='".$old_s['brand_id']."',type='EP2C',eng_id='".$eid."',eng_status='1'");
					if (!$res_p2cdata) {
						 $flag = false;
						 $error_msg = "Error details21: " . mysqli_error($this->link) . ".";
					}
				
				}
				
				
			}	
					
				

	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."',pen_status='".$status."',close_rmk='".$closed_reason."',app_rmk='Done By App',imei='".$serial_no."',model_id = '".$selected_model_id."', model = '".$model_name."',product_id='".$selected_product_id."',dop='".$dop."',warranty_status='".$warranty_status_new."' , close_tat = '".$ctat."', pen_status='".$st_status."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."'  where job_no='".$job_no."'");
	
			if (!$res_jobsheet) {
				 $flag = false;
				 $error_msg = "Error details21cjobsheet: " . mysqli_error($this->link) . ".";
			}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	
		if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}

if($warranty_status_new=='IN'){
$sql_rep=mysqli_query($this->link,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$job_no."' and travel='Y' group by updated_by ");
$row_max=mysqli_fetch_array($sql_rep);



$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}	
}

}
////////////////////////////Set Found Ok/////////////////////////////////
	

if($repair_status==11){
	
		if($old_s["call_for"]!="Workshop" ){
			
			$date_parts1=explode("-", $old_s['vistor_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
			$close_tat = $dt_dif-$tatminus;
		}else{
			
			$date_parts1=explode("-", $old_s['open_date']); $date_parts2=explode("-", $today);
			$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
			$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			$dt_dif = $end_date - $start_date;
			
	    	$close_tat = $dt_dif-$tatminus;
		}
		
		//// open close tat ///////
		$date_parts_oc1=explode("-", $old_s['open_date']); $date_parts_oc2=explode("-", $today);
		$start_date_oc=gregoriantojd($date_parts_oc1[1], $date_parts_oc1[2], $date_parts_oc1[0]);
		$end_date_oc=gregoriantojd($date_parts_oc2[1], $date_parts_oc2[2], $date_parts_oc2[0]);
		$dt_dif_oc = $end_date_oc - $start_date_oc;
		
		$tat_open_close = $dt_dif_oc-$tatminus;
	
		///// entry in call/job  history
		$query="INSERT INTO call_history set job_no='".$job_no."', location_code='".$old_s['current_location']."', status='".$repair_status."', activity='Set Found Ok', outcome='Set Found Ok', updated_by='".$eid."',  warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."', ip='".$_SERVER['REMOTE_ADDR']."', travel_km='".$travelKM."', travel='Y'";
	$result=mysqli_query($this->link,$query);
			
	    $sql_update = "UPDATE jobsheet_data set status ='11', sub_status ='11', close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."', pen_status='6', close_rmk='".$closed_reason."', tat_open_close = '".$tat_open_close."' where job_no ='".$job_no."' ";
		
    	$res_update=mysqli_query($this->link,$sql_update);
		//// check if query is not executed
		if (!$res_update) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($this->link) . ".";
		}	
	$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$close_tat."', tatminus='".$tatminus."' ,status='".$repair_status."', tat_open_close = '".$tat_open_close."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}

}

/////////////////////////////////////////////////////Job Cancel CASE//////////////
if($repair_status==12){
	
		///// entry in call/job  history
		$query="INSERT INTO call_history set job_no='".$job_no."', location_code='".$old_s['current_location']."', status='".$repair_status."', activity='Cancel', outcome='Cancel', updated_by='".$eid."',  warranty_status='".$old_s['warranty_status']."', remark='".$update_remark."', ip='".$_SERVER['REMOTE_ADDR']."', travel_km='".$travelKM."', travel='Y'";
	$result=mysqli_query($this->link,$query);
			
	    $sql_update = "UPDATE jobsheet_data set status ='12', sub_status ='12', close_date='".$cls_dt[0]."',close_time='".$cls_dt[1]."', pen_status='6', close_rmk='".$closed_reason."' where job_no ='".$job_no."' ";
		
    	$res_update=mysqli_query($this->link,$sql_update);
		//// check if query is not executed
		if (!$res_update) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($this->link) . ".";
		}	

}

}		
if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 }   
 
 public function storeHistory($job_no,$status,$updateDate,$repair_status,$eid,$address,$branch_code) { 

$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
//////////////////////////////status Refer to Centeral Workshop/////////////////////////////////////////////////////
if($repair_status==4){

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='Job Esclated',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


}

//////////////////////////////status WIP/////////////////////////////////////////////////////
if($repair_status==7){
	

	
			$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='Job Pending',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}

}

//////////////////////////////status WIP/////////////////////////////////////////////////////
if($repair_status==50){


				$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='Pending For Approval',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."' ,app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


}

////////////////////////////////////////EP/
if($repair_status==5){



				$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='Estimate Pending',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
}

/////////////////////////////////////// PNA/////////////////////////////////////////////////////////
if($repair_status==3){



$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='Part Not Available',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

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
		
			
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='".$repair_type."',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}


	
}



/////////////////////////////////////////////////////Confirmation  CASE//////////////
if($repair_status==10){

		///// entry in call/job  history
	
				$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='Call Confirmed',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
		

		}

////////////////////////////////// Repair Done///////////////////////////////////////////////////////
if($repair_status==6){
	
		$repair_type="Repair Done";


		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='".$repair_type."',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
		


}

////////////////////////////////// Replacement Done///////////////////////////////////////////////////////
if($repair_status==8){
	
		$repair_type="Job Replacement";


						

		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='".$repair_type."',outcome='".$status."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details21history: " . mysqli_error($this->link) . ".";
			}
	
}

////////////////////////////////// Job Cancel ///////////////////////////////////////////////////////
if($repair_status==12){
	
		$repair_type="Cancel";

		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$branch_code."',status='".$repair_status."',activity='".$repair_type."',outcome='".$repair_type."',updated_by='".$eid."', remark='Update By APP' ,address ='".$address."',app_date='".$updateDate."'";

	$result=mysqli_query($this->link,$query);
		if (!$result) {
				 $flag = false;
				 $error_msg = "Error details22history: " . mysqli_error($this->link) . ".";
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
  public function getComplaintsData($eid) {  
	 $result = mysqli_query($this->link,"SELECT  COUNT( CASE WHEN pen_status ='2' THEN job_no END) as assignjob ,COUNT(CASE WHEN pen_status ='6' THEN job_no END) as closejob FROM jobsheet_data where   eng_id='".$eid."' and status!='12'")  or die(mysqli_error($this->link)); 
	 $row_count=mysqli_fetch_array($result);
	     
  $seven_days=mysqli_fetch_array(mysqli_query($this->link,"select count(job_no) as a,eng_id from jobsheet_data where  datediff(sysdate(),open_date) >7  and pen_status !='6' and eng_id='".$_REQUEST['eid']."' and status!='12'"));
          
 return $row_count['assignjob']."~".$row_count['closejob']."~".$seven_days[a];     
 }
 
/////////////////////// Get Part Category //////////////////////
 public function getPartCat() {
	 $result = mysqli_query( $this->link,"SELECT DISTINCT(part_category) as category FROM `partcode_master` WHERE (part_category='MISC' or part_category='SPARE') order by part_category") or die(mysqli_error($this->link));
	 return $result;
 } 

######### Function for Eng. Part Request
	public function getsparetool($type, $spare, $qty, $engg, $challanno, $code_id) {
	$today = date('Y-m-d');

	$sql_loc=mysqli_query($this->link,"select location_code from locationuser_master where userloginid='".$engg."'") or die(	mysqli_error($this->link));
	$row_loc=mysqli_fetch_assoc($sql_loc);
	
	$sql_parts=mysqli_query($this->link,"select product_id,brand_id from partcode_master where partcode='".$spare."'") or die(	mysqli_error($this->link));
	$row_parts=mysqli_fetch_assoc($sql_parts);
	
	$location_code=$row_loc['location_code'];

	$req_ins = "insert into part_demand set location_code='".$location_code."',job_no='".$challanno."', eng_id='".$engg."', part_category='".$type."', partcode='".$spare."', qty='".$qty."', request_date='".$today."',status ='1',temp_id='".$code_id."',product_id='".$row_parts['product_id']."',brand_id='".$row_parts['brand_id']."' ";
		$req_res = mysqli_query($this->link,$req_ins) or die(mysqli_error($this->link));
		return $req_res;

	} 
 
####################################################### 

/////////////// Complaints Master /////////////////
 public function getJobMaster() { 
	 if(!empty($_REQUEST['job_no'])){
 $result = mysqli_query($this->link,"SELECT * FROM jobsheet_data where job_no='".$_REQUEST['job_no']."'")or die(mysqli_error($this->link));         }
  else {
	  $result=false;
  }
 return $result;     
 }

//////////////////////////////////// count no. of jobs/////////////////////////
	/*public function getJobSum() {
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 1 then status end) as open,COUNT(case when status = 2 then status end) as pending,COUNT(case when status = 3 then status end) as pna,COUNT(case when status = 5 then status end) as ep,COUNT(case when status = 7 then status end) as wip,COUNT(case when status = 10 then status end) as closed,COUNT(case when status = 50 then status end) as pfa from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end') and call_for!='Installation' and call_for!='PicknDrop'");
		return $result;
	} */
	//////////////////////////////////// count no. of jobs/////////////////////////
	public function getJobSum() {
		$begin = date('Y-m-d', strtotime("-90 days"));
		$end = date('Y-m-d');
		//echo "select COUNT(case when status = 1 then status end) as open,COUNT(case when status = 2 then status end) as pending,COUNT(case when status = 3 then status end) as pna,COUNT(case when status = 81 then status end) as repl_request,COUNT(case when status = 5 then status end) as ep,COUNT(case when status = 7 then status end) as wip,COUNT(case when status = 10 then status end) as closed,COUNT(case when status = 50 then status end) as pfa,COUNT(case when ((status = 51 or status = 59) AND line !='DC Pending') then status end) as repl_approved from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end')";exit;

		$result = mysqli_query($this->link,"select COUNT(case when status = 1 then status end) as open,COUNT(case when status = 2 then status end) as pending,COUNT(case when status = 3 then status end) as pna,COUNT(case when status = 81 then status end) as repl_request,COUNT(case when status = 5 then status end) as ep,COUNT(case when status = 7 then status end) as wip,COUNT(case when status = 10 then status end) as closed,COUNT(case when status = 50 then status end) as pfa,COUNT(case when ((status = 51 or status = 59) AND line !='DC Pending') then status end) as repl_approved from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end')");
		
		return $result;
	} 
	
	//////////////////////////////////// count no. of jobs/////////////////////////
	public function getJobSuminstall() {
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 48 then status end) as inst_done,COUNT(case when status IN ('2','55','56') then status end) as inst_pending from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end') and call_for='Installation'");
		return $result;
	} 
		//////////////////////////////////// count no. of jobs/////////////////////////
	public function getJobSumpickup() {
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 6 then status end) as drp,COUNT(case when status IN ('2') then status end) as pickup from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end') and call_for='PicknDrop'");
		return $result;
	} 

/////////////////////// Voc MASTER For get detail //////////////////////
 public function getVocmaster($voccode,$vocdesc) {
	 $result = mysqli_query($this->link,"SELECT $vocdesc  FROM voc_master  where voc_code='".$voccode."'") or die(mysqli_error($this->link));         
	 return $result;
 }
 
   /////////////////////// get leave insert start line 
     public function getleave($leave_type, $sub_leave_type, $leave_date, $engg, $leave_reason,$leaveid,$leave_toDate) {
		$today = date('Y-m-d');
		$req_ins = "insert into leave_app set leaveid='".$leaveid."',leave_type='".$leave_type."', sub_leave_type='".$sub_leave_type."', leave_date='".$leave_date."',requested_by='".$engg."', leave_reason='".$leave_reason."', status ='pending',leave_todate='".$leave_toDate."'";
		$req_res = mysqli_query($this->link,$req_ins)or die("error1".mysqli_error($this->link));
		 return $req_res;

	}
	
  /////////////////////// get leave start line 
    public function getleaveview($engid,$from,$to) {
		if($from!=''){
			$fiter=" and  (leave_date BETWEEN '".$from."' AND '".$to."')";
		}
		else{
		$fiter="";	
		}
		 $req_ins = "select * from  leave_app where requested_by='".$engid."' $fiter";
		
		$req_res = mysqli_query($this->link,$req_ins)or die("error1".mysqli_error($this->link));
		return $req_res;
	} 
        /////////////////////// get leave end line 	
		
		////////////////////// get Tool Request List /////////////////////////
	public function getToolReqList() {
		if ($_REQUEST['toolType'] == 'MISC') {
			$result = mysqli_query($this->link,"select distinct(partcode) as partcode,id,part_name from partcode_master where status='1' and part_category='MISC' order by part_name ")or die(mysqli_error($this->link));
		}
		elseif($_REQUEST['toolType'] == 'SPARE' || $_REQUEST['toolType'] == 'PCB') {
			
			//$result = mysqli_query($this->link,"select distinct(partcode) as partcode,id,part_name from partcode_master where status='1' and part_category='SPARE' order by part_name ")or die(mysqli_error($this->link));
			$result = mysqli_query($this->link,"select distinct(partcode) as partcode,id,part_name from partcode_master where status='1' and part_category in('SPARE','PCB') order by part_name ")or die(mysqli_error($this->link));
		}
		return $result;
	}


public function toolkitstock($engid){
	$sql="select * from part_demand where eng_id='$engid'";
	$rs=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
	
	return $rs;	
}
//
###### Function for  Engg. Stock Allocation
public function getStock_Challan($engid){
	//print_r('ddddd');exit;
		$result = mysqli_query($this->link,"SELECT challan_no,to_location,sale_date,status,from_location FROM stn_master WHERE  to_location='".$engid."' order by sale_date")or die(mysqli_error($this->link));
		return $result;
	}
#########################	
###### Function for Location Stock Allocation
public function getLocStock_Challan($engid){
		$result = mysqli_query($this->link,"SELECT challan_no,to_location,sale_date,status,from_location,tally_challan_no FROM billing_master WHERE  to_location='".$engid."' and status != '5' order by sale_date")or die(mysqli_error($this->link));
		return $result;
	}

////////////////////////////Stock Serial Status/////////////////////////////////////////////////////////////////
 public function getEng_Serial($eid,$partcode) {         
 $result = mysqli_query($this->link,"SELECT imei1,partcode FROM imei_details_asp where  location_code='".$eid."' and status='1'  and partcode='".$partcode."'") or die(mysqli_error($this->link));     
 return $result;     
 }	

//////Installation Pending////
 function getInstallation($job){
	 $result = mysqli_query($this->link,"SELECT imei,warranty_status,model,model_id,product_id,status,pen_status,dop,customer_id,reason,close_rmk,remark,call_for,close_date,app_reason,doa_approval,recipient_name,recipient_contact,service_rmak,rating,hand_date,hand_time,job_no,open_date,cust_problem,brand_id,customer_name,contact_no,product_cat,alternate_no,address,city_id,state_id,pincode,installation_date,app_reason,h_code FROM jobsheet_data where job_no='".$job."' ") or die(mysqli_error($this->link));   
	return $result;
 }
	###### Function for Pending FAulty ENgwise
public function getFaulty_Details($engid) {
		$result = mysqli_query($this->link,"SELECT sno,from_location,job_no,imei,partcode,qty,consumedate,status,fresh2faulty FROM part_to_credit WHERE eng_status='1' and eng_id='".$engid."'")or die(mysqli_error($this->link));
		return $result;
	}
######################### 
###### Function for  FAulty Dispatch
public function getFaulty_Challan($engid) {
		$result = mysqli_query($this->link,"SELECT * FROM part_to_credit WHERE (dispatchstatus = 'Dispatched' or dispatchstatus='ENGDispatched') and eng_id='".$engid."' group by eng_challan_no order by challan_date DESC")or die(mysqli_error($this->link));
		return $result;
	}
#########################
//////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function freshstock($engid){
	$sql="select challan_no,sale_date,to_location,status from stn_master where from_location='$engid' order by sale_date";
	$rs=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
	
	return $rs;	
}
	 ////////////////////////////complaint_master/////////////////////////////////////////////////////////////////
 public function getInstallationMaster($eid) {  
 
 if($_REQUEST['from_date']!='' && $_REQUEST['to_date']!=''){
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
		}
		else{
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');	
		}  
	
 $result = mysqli_query($this->link,"SELECT imei,warranty_status,model,model_id,product_id,status,pen_status,dop,customer_id,reason,close_rmk,remark,call_for,close_date,app_reason,doa_approval,recipient_name,recipient_contact,service_rmak,rating,hand_date,hand_time,job_no,open_date,cust_problem,brand_id,customer_name,contact_no,product_cat,alternate_no,address,city_id,state_id,pincode,installation_date,app_reason FROM jobsheet_data where eng_id='".$eid."' and call_for!='Workshop'  and status!='12' and open_date BETWEEN '$begin' and '$end' and call_for='Installation' and status not in ('12') ") or die(mysqli_error($this->link));     
 return $result;     
 }
 ////////////////////////////Stock Status Model wise/////////////////////////////////////////////////////////////////
 /*public function getstockeng_mdel($eid,$modelid) { 
//	echo "SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' and partcode in (select partcode from partcode_master where model_id LIKE '%$modelid%' and status='1') ";
 $result = mysqli_query($this->link,"SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' and  location_code!='' and location_code!='0' and partcode in (select partcode from partcode_master where (model_id LIKE '%".$modelid."%' OR part_category='GLOBAL') and partcode!='39' and status='1') ") or die(mysqli_error($this->link));     
 return $result;     
 }*/
public function getstockeng_mdel($eid,$modelid,$user_type) { 
//print_r('dddddd');exit;
if($user_type=='SSP' || $user_type=='ASP' || $user_type=='Branch'){
	//echo "SELECT * FROM client_inventory where  location_code='".$eid."' and partcode in (select partcode from partcode_master where (model_id LIKE '%".$modelid."%' or part_category='GLOBAL' or part_category='SPARE') and partcode!='39' and status='1') ";exit;
$result = mysqli_query($this->link,"SELECT * FROM client_inventory where  location_code='".$eid."' and partcode in (select partcode from partcode_master where (model_id LIKE '%".$modelid."%' or part_category='GLOBAL' or part_category='SPARE') and partcode!='39' and status='1') ") or die(mysqli_error($this->link));	
}
else{
	//echo "SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' and  location_code!='' and location_code!='0' and partcode in (select partcode from partcode_master where (model_id LIKE '%".$modelid."%' or part_category='GLOBAL' or part_category='SPARE') and partcode!='39' and status='1') ";exit;
 $result = mysqli_query($this->link,"SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' and  location_code!='' and location_code!='0' and partcode in (select partcode from partcode_master where (model_id LIKE '%".$modelid."%' or part_category='GLOBAL' or part_category='SPARE') and partcode!='39' and status='1') ") or die(mysqli_error($this->link));  
}
 return $result;     
 }	
 
 ////////////////////////////AMC Data/////////////////////////////////////////////////////////////////
 public function getAMC($eid) {  
 
 if($_REQUEST['from_date']!='' && $_REQUEST['to_date']!=''){
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
		}
		else{
		$begin = date('Y-m-d', strtotime("-180 days"));
		$end = date('Y-m-d');	
		}  
	
 $result = mysqli_query($this->link,"SELECT amcid,serial_no,customer_id,contract_no,product_id,model_id,amc_type,open_date,status,customer_name FROM amc where eng_id='".$eid."' and open_date BETWEEN '$begin' and '$end' and status in ('1','2')") or die(mysqli_error($this->link));     
 return $result;     
 }
#########################	
public function getUser_Details($eid) {
		$result = mysqli_query($this->link,"SELECT pwd,statusid FROM locationuser_master where userloginid='".$eid."'")or die(mysqli_error($this->link));
		return $result;
		}
public function getLocation_Details($eid) {
		$result = mysqli_query($this->link,"SELECT pwd,statusid FROM location_master where location_code='".$eid."'")or die(mysqli_error($this->link));
		return $result;
		}	
//////AMC Details////
 function getAMC_Details($amcid){
	 $result = mysqli_query($this->link,"SELECT amcid,serial_no,customer_id,contract_no,product_id,model_id,amc_type,open_date,status,customer_name,addrs,amc_amount, city_id,state_id,location_code,inv_no,cr_no,cr_book_no,cr_date,rec_pay_remark,app_remark,mode_of_payment  FROM amc where amcid='".$amcid."' ") or die(mysqli_error($this->link));   
	return $result;
 } 
 
 /////////////////////// AMC Status //////////////////////
 public function getAMCStatus() {
	  $result = mysqli_query($this->link,"select status_id, display_status from jobstatus_master where status_id in ('51','52') order by display_status")  or die(mysqli_error($this->link));
	 return $result;
 } 
###################### 
    /////////////////////// AMC LIST Data//////////////////////
 public function getAMCLIST() {
	  $result = mysqli_query($this->link,"select amcid,status,customer_name from amc where eng_id='".$_REQUEST['eid']."' and eng_id!=''  and app_status in ('51','52')")  or die(mysqli_error($this->link));
	 return $result;
 } 
    
     /////////////////////// AMC Status //////////////////////
 public function getAMCStatus_details($st) {
	  $result = mysqli_query($this->link,"select display_status from jobstatus_master where status_id = '".$st."' order by display_status")  or die(mysqli_error($this->link));
     $data=mysqli_fetch_array($result);
	 return $data['display_status'];
 } 
//////////////////////////////////// count no. Assigned AMC/////////////////////////
	public function getAssignedAMC() {
		$begin = date('Y-m-d', strtotime("-180 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 2 then status end) as amc_assigned from amc where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end')");
		return $result;
	} 	
########################
//////////////////////////////////// count no. Assigned AMC/////////////////////////
	//////////////////////////////////// count no. Closed AMC/////////////////////////
	public function getClosedAMC() {
		$begin = date('Y-m-d', strtotime("-180 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status IN ('52','51') then status end) as amc_closed from amc where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end')");
		return $result;
	} 	
########################
//////////////////////////////////// count no. Closed AMC/////////////////////////
	public function saveAMC($amc_status,$remark,$amcid,$eid,$latitude,$longitude,$location_address,$app_version,$path_img,$path_img1,$path_img2,$path_img_serial,$paymentDetail,$location_code,$cust_name) {
		$today=date("Y-m-d");

		if($amcid!='' && $amc_status!=''){
			
		if($amc_status=='52'){ $quotetype='R'; } else{ $quotetype='A';}	
			
		$result = mysqli_query($this->link,"update amc set status='".$amc_status."', app_status='".$amc_status."',quotetype='".$quotetype."',app_remark='".$remark."',app_by='".$eid."',app_date='".$today."',mode_of_payment='".$paymentDetail->payment_mode."',cr_no='".$paymentDetail->cr_no."',cr_book_no='".$paymentDetail->cr_book_no."',cr_date='".$today."',transaction_no='".$paymentDetail->trn_no."',rec_pay_remark='".$paymentDetail->other_trn_remark."',latitude='".$latitude."',longitude='".$longitude."',location_address='".$location_address."',app_version='".$app_version."' where amcid='".$amcid."'");
		}
		if($amc_status=='51'){
			$sql_inst_pay = "INSERT INTO payment_receive_loc set location_code='".$location_code."', job_no='".$amcid."',amount='".$paymentDetail->payment_receive."',customer_name='".$cust_name."',remark='".$remark."',payment_mode='".$paymentDetail->payment_mode."',payment_remark='".$paymentDetail->other_trn_remark."',ip='".$_SERVER['REMOTE_ADDR']."',cr_no='".$paymentDetail->cr_no."',cr_book_no='".$paymentDetail->cr_book_no."',cr_date='".$today."',transaction_no='".$paymentDetail->trn_no."'";
				$res_inst_pay = mysqli_query($this->link,$sql_inst_pay);
		}
		
		if($result){
		return 1;	
		}
		else{
		return 0;	
		}
		
	} 	
//////////////////////////////////// Make a copy function of AMC save for auto invoicing written by shekhar on 21 april 2021/////////////////////////
	public function saveAMCAutoInvoice($amc_status,$remark,$amcid,$eid,$latitude,$longitude,$location_address,$app_version,$path_img,$path_img1,$path_img2,$path_img_serial,$paymentDetail,$location_code,$cust_name) {
		//// check in payment details
		if(mysqli_num_rows(mysqli_query($this->link,"SELECT id FROM payment_receive_loc WHERE job_no='".$amcid."'"))==0){
		$flag = true;
		mysqli_autocommit($this->link, false);
		$err_msg = "";
		$today = $this->dt_format->format('Y-m-d');
		$currtime = $this->dt_format->format('H:i:s');
		////// check amc no. and amc status should not be blank
		if($amcid!='' && $amc_status!=''){
			///// check if status is rejected
			if($amc_status=='52'){ 
				$quotetype='R'; 
			}else{ 
				$quotetype='A';
			}
			///// update AMC details
			$result = mysqli_query($this->link,"update amc set status='".$amc_status."', app_status='".$amc_status."',quotetype='".$quotetype."',app_remark='".$remark."',app_by='".$eid."',app_date='".$today."',mode_of_payment='".$paymentDetail->payment_mode."',cr_no='".$paymentDetail->cr_no."',cr_book_no='".$paymentDetail->cr_book_no."',cr_date='".$today."',transaction_no='".$paymentDetail->trn_no."',rec_pay_remark='".$paymentDetail->other_trn_remark."',latitude='".$latitude."',longitude='".$longitude."',location_address='".$location_address."',app_version='".$app_version."' where amcid='".$amcid."'");
			/// check if query is execute or not//
			if(!$result){
				$flag = false;
				$err_msg = "Error1". mysqli_error($this->link) . ".";
			}
		}
		///// if AMC is Approved then save payment receive details and make auto invoice
		if($amc_status=='51'){
			//// get AMC details
			$amc_res = mysqli_query($this->link,"SELECT * FROM amc WHERE amcid='".$amcid."'");
			$amc_row = mysqli_fetch_assoc($amc_res);
			$location_code = $amc_row['location_id'];
			//if($amc_row["mode_of_payment"]==""){
			////////
			$sql_inst_pay = "INSERT INTO payment_receive_loc set location_code='".$location_code."', job_no='".$amcid."',amount='".$paymentDetail->payment_receive."',customer_name='".$cust_name."',remark='".$remark."',payment_mode='".$paymentDetail->payment_mode."',payment_remark='".$paymentDetail->other_trn_remark."',ip='".$_SERVER['REMOTE_ADDR']."',cr_no='".$paymentDetail->cr_no."',cr_book_no='".$paymentDetail->cr_book_no."',cr_date='".$today."',transaction_no='".$paymentDetail->trn_no."', engg_id ='".$old_s['eng_id']."'";
			$res_inst_pay = mysqli_query($this->link,$sql_inst_pay);
			/// check if query is execute or not//
			if(!$res_inst_pay){
				$flag = false;

				$err_msg = "Error2". mysqli_error($this->link) . ".";
			}
			if($amc_row["inv_no"]==""){
			///// start script for making invoice
			//////customer gst no. ////
			$custdet = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT gst_no FROM customer_master WHERE customer_id='".$amc_row["customer_id"]."'"));
			///// model master
			$model_m = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT model,warrantymonth FROM model_master WHERE model_id='".$amc_row["model_id"]."'"));
			/////// make an auto invoice like service invoice developed by shehar on 06 march 2021
			$res_invcount = mysqli_query($this->link, "SELECT claim_series,fy,claim_counter FROM invoice_counter where location_code='".$location_code."'");
			if (mysqli_num_rows($res_invcount)) {
				//////pick max counter of INVOICE
				$row_invcount = mysqli_fetch_array($res_invcount);
				$next_invno = $row_invcount['claim_counter']+1;
				/////update next counter against invoice	
				$res_upd = mysqli_query($this->link,"UPDATE invoice_counter set claim_counter = '".$next_invno."' where location_code='".$location_code."'");
				/// check if query is execute or not//
				if(!$res_upd){
					$flag = false;
					$err_msg = "Error3". mysqli_error($this->link) . ".";
				}
				///// make invoice no.
				$invno = $row_invcount['claim_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
				/////get basic details of location
				$fromlocdet = mysqli_fetch_array(mysqli_query($this->link,"SELECT locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,contactno1 FROM location_master WHERE location_code='".$location_code."'"));
				////// get from city details
				$fromloccity = mysqli_fetch_array(mysqli_query($this->link,"SELECT city,state FROM city_master WHERE cityid='".$fromlocdet[4]."'"));
				////// get to city details
				$toloccity = mysqli_fetch_array(mysqli_query($this->link,"SELECT city,state FROM city_master WHERE cityid='".$amc_row['city_id']."'"));
				////// reverse calculation of price
				$tax_per = 18;
				$basic_amt = ($amc_row['amc_amount']*100)/(100+$tax_per);
				$tax_amt = $amc_row['amc_amount']-$basic_amt;
				$total_amt = $amc_row['amc_amount'];
				//////check GST variable
				if($amc_row["state_id"]==$fromlocdet[5]){ 
					$cgst_ser_tax_per = $tax_per/2; 
					$sgst_ser_tax_per = $tax_per/2;; 
					$igst_ser_tax_per = 0.00;
					
					$cgst_ser_tax_amt = $tax_amt/2;
					$sgst_ser_tax_amt = $tax_amt/2;
					$igst_ser_tax_amt = 0.00;
				}else{
					$cgst_ser_tax_per = 0.00; 
					$sgst_ser_tax_per = 0.00; 
					$igst_ser_tax_per = $tax_per;
					
					$cgst_ser_tax_amt = 0.00;
					$sgst_ser_tax_amt = 0.00;
					$igst_ser_tax_amt = $tax_amt;
		
				}
				///// make one entry of AMC charge in billing item table
				$result3 = mysqli_query($this->link,"INSERT INTO billing_product_items set  from_location='" . $location_code . "', to_location='".$amc_row['customer_name']."',challan_no='".$invno."',hsn_code='998711',partcode='AMC0001',job_no='".$amcid."', product_id='".$amc_row['product_id']."', brand_id='".$amc_row['brand_id']."', model_id='".$amc_row['model_id']."', part_name='AMC',qty='1', okqty='1', price='".$basic_amt."',uom='', mrp='" . $total_amt . "',hold_value='".$basic_amt."',value='".$basic_amt."' , discount_amt='', basic_amt='".$basic_amt."',cgst_per='".$cgst_ser_tax_per."',cgst_amt='".$cgst_ser_tax_amt."', sgst_per='".$sgst_ser_tax_per."',sgst_amt='".$sgst_ser_tax_amt."',igst_per='".$igst_ser_tax_per."',igst_amt='".$igst_ser_tax_amt."', item_total='" . $total_amt . "', pty_receive_date='" . $today . "', type = 'RETAIL' ");
				
				//// check if query is not executed
				if (!$result3) {
					$flag = false;
					$err_msg = "Error Code4: ".mysqli_error($this->link);
				}
				$tot_disc = 0.00;
				//// Insert Master Data
				$query1 = "INSERT INTO billing_master set from_location='" . $location_code . "', to_location='" . $amc_row['customer_name'] . "',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$custdet["gst_no"]."',from_partyname='".$fromlocdet[0]."', party_name='".$amc_row['customer_name']."',customer_id='".$amc_row['customer_id']."', challan_no='" . $invno . "',job_no='".$amcid."',job_serial_no='".$amc_row['serial_no']."', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', logged_by='" . $eid . "', document_type='INV' ,basic_cost='" . $basic_amt . "',tax_cost='',total_cost='" . $total_amt . "',bill_from='" . $location_code . "',from_stateid='".$fromlocdet['5']."',to_stateid='".$amc_row["state_id"]."',from_state='".$fromloccity[1]."',to_state='".$toloccity[1]."',from_cityid='".$fromlocdet[4]."',from_city='".$fromloccity[0]."',to_cityid='".$amc_row['city_id']."',to_city='".$toloccity[0]."',from_pincode='".$fromlocdet[6]."',to_pincode='".$amc_row['pincode']."',from_phone='".$fromlocdet[9]."',to_phone='".$amc_row['contract_no']."',from_email='".$fromlocdet[7]."',to_email='".$amc_row['email']."',bill_to='".$amc_row['customer_name']."',from_addrs='" . $fromlocdet[1] . "',disp_addrs='" . $fromlocdet[2] . "',round_off='" . $round_off . "',to_addrs='" . $amc_row['addrs'] . "',deliv_addrs='" . $amc_row['addrs'] . "',billing_rmk='".$remark."',po_no='FRONT_BILL', status='3', dc_date='" . $today . "',dc_time='" . $currtime . "',sgst_amt='" . $sgst_ser_tax_amt . "',cgst_amt='" . $cgst_ser_tax_amt. "',igst_amt='" . $igst_ser_tax_amt . "',driver_contact='',carrier_no='',po_type='RETAIL',discount_amt='',cr_no='".$paymentDetail->cr_no."',cr_book_no='".$paymentDetail->cr_book_no."',cr_date='".$today."',transaction_no='".$paymentDetail->trn_no."',mode_of_payment='".$paymentDetail->payment_mode."',rec_pay_remark='".$paymentDetail->other_trn_remark."'";
				$result = mysqli_query($this->link, $query1);
				//// check if query is not executed
				if (!$result) {
					$flag = false;
					$err_msg = "Error Code5: ". mysqli_error($this->link);
				}					
				////// insert in activity table////
				$query_da = "INSERT INTO daily_activities set userid='".$eid."',ref_no='".$invno."',activity_type='AMC INVOICE',action_taken='GENERATED',update_date='".$today."',update_time='".$currtime."',system_ip='".$_SERVER['REMOTE_ADDR']."'";
				$result_da = mysqli_query($this->link,$query_da);
				//// check if query is not executed
				if (!$result_da) {
					$flag = false;
					$err_msg = "Error Code6: ". mysqli_error($this->link);
				}
				///////calculate AMC start date and AMC end date
				///CASE 1 check in AMC data
				$res_amc = mysqli_query($this->link,"SELECT amc_end_date FROM amc WHERE serial_no='".$amc_row['serial_no']."' AND amcid!='".$amcid."' AND app_status='51' ORDER BY sno DESC");
				if(mysqli_num_rows($res_amc)>0){
					$row_amc = mysqli_fetch_assoc($res_amc);
					if($row_amc["amc_end_date"] > $today){
						$amc_start_date = date('Y-m-d', strtotime($row_amc["amc_end_date"]. ' + 1 days'));
						$amc_end_date = date('Y-m-d', strtotime("+".$amc_row['amc_duration']." months", strtotime($row_amc["amc_end_date"])));
					}else{
						$amc_start_date = $today;
						$amc_end_date = date('Y-m-d', strtotime("+".$amc_row['amc_duration']." months", strtotime($today)));
					}
				}////CASE 2 check in SaleData
				else{
					$res_sd = mysqli_query($this->link,"SELECT invoice_date FROM sales_data WHERE serail_no='".$amc_row['serial_no']."'");
					if(mysqli_num_rows($res_sd)>0){
						$row_sd = mysqli_fetch_assoc($res_sd);
						$make_wed = date('Y-m-d', strtotime("+".$model_m["warrantymonth"]." months", strtotime($row_sd["invoice_date"])));
						if($make_wed > $today){
							$amc_start_date = date('Y-m-d', strtotime($make_wed. ' + 1 days'));
							$amc_end_date = date('Y-m-d', strtotime("+".$amc_row['amc_duration']." months", strtotime($make_wed)));
						}else{
							$amc_start_date = $today;
							$amc_end_date = date('Y-m-d', strtotime("+".$amc_row['amc_duration']." months", strtotime($today)));
						}
					}else{
						$amc_start_date = $today;
						$amc_end_date = date('Y-m-d', strtotime("+".$amc_row['amc_duration']." months", strtotime($today)));
					}
				}
				//////////////////update amc details
				$sql_inst = "update amc set inv_no='".$invno."',amc_start_date='".$amc_start_date."',amc_end_date='".$amc_end_date."',remarks=CONCAT(remarks,' ASD $amc_row[amc_start_date] AED $amc_row[amc_end_date]') where amcid='".$amcid."'";
				$res_inst = mysqli_query($this->link,$sql_inst);
				//// check if query is not executed
				if (!$res_inst) {
					$flag = false;
					$err_msg = "Error amc : " . mysqli_error($this->link) . ".";
				}
				////// insert payment details
				//////
				$sql_inst_pay = "UPDATE payment_receive_loc set inv_no='".$invno."' WHERE job_no='".$amcid."'";
				$res_inst_pay = mysqli_query($this->link,$sql_inst_pay);
				//// check if query is not executed
				if (!$res_inst_pay) {
					$flag = false;
					$err_msg = "Error payment update location : " . mysqli_error($this->link) . ".";
				}
			}else {
				$flag = false;
				$err_msg = "Error Code4: Invoice series not found of location";
			}
			}else{
				$flag = false;
				$err_msg = "Error Code5: Invoice already generated";
			}
			/*}else{
				$flag = false;
				$err_msg = "Error Code6: Re-submition";
			}*/
		}
		if ($flag) {	
			mysqli_commit($this->link);
			return 1;         
		} else {
			mysqli_rollback($this->link);
			return 0;
		}
		}else{
			return 0;
		}
	}
///////////////////////// Insert Hotel Expenses ///////////////////////////
public function Expense_Claim($food_expns,$courier_expns,$local_expns,$mobile_expns,$other_expns,$food_expns_Img,$courier_expns_Img,$local_expns_Img,$mobile_expns_Img, $other_expns_Img,$sap_code,$personName,$travelling_state,$travelling_city,$hotel_name,$other_hotel_name,$hotel_address,$hotel_city,$hotel_state,$limit_hotel,$checkInDate,$checkOutDate,$accomdation_days,$roomCharge,$travellExpense,$difference,$totalExpenses,$expense_date,$eng_id){
### if travellExpense is not blank or gretar than 0 then hotel entry will done other wise general expense book. 18/08/2020

$sql_chk="select id from expenses_image_url where eng_id='$eng_id' and update_date='$expense_date' and expanse_flag='Y'";
$rs_chk=mysqli_query($this->link,$sql_chk) or die(mysqli_error($this->link));
if(mysqli_num_rows($rs_chk)==0){

 if($travellExpense!='' || $travellExpense!='0.00')
 {
		$result1 = mysqli_query($this->link,"insert into hotel_mgmt_data set sap_id='$sap_code',division='$division',travel_state='$travelling_state',travel_city='$travelling_city',hotel_name='$hotel_name',hotel_address='$hotel_address',hotel_city='$hotel_city',hotel_state='$hotel_state',hotel_limit='$limit_hotel',check_in='$checkInDate',check_out='$checkOutDate',total_days='$accomdation_days',room_charge='$roomCharge',total_expense='".$travellExpense."',difference='".$difference."',update_date='".$expense_date."'")or die(mysqli_error($this->link));
		
	#### If hotel_name is others then hotel will create.
	if($hotel_name=='Others')
	{
	$result2 = mysqli_query($this->link,"insert into hotel_master set hotel_name='".$other_hotel_name."',hotel_address='".$hotel_address."',hotel_city='".$hotel_city."',hotel_state='".$hotel_state."',status='A'")or die(mysqli_error($this->link));	
	}
  }

	$set_img = "insert into expenses_image_url set mobile_expense='$mobile_expns',food_expense='$food_expns' ,courier_expense='$courier_expns',other_expense='$other_expns',local_expense='$local_expns',mobile_expense_img='$mobile_expns_Img',food_expense_img='$food_expns_Img',local_expense_img='$local_expns_Img',other_expense_img='$other_expns_Img',courier_expense_img='$courier_expns_Img',update_date='$expense_date',eng_id='$eng_id',claim_flag='1',total_misc='$total_misc',grand_total='$grand_total',expanse_flag='Y' ";

		$result = mysqli_query($this->link,$set_img)or die(mysqli_error($this->link));
		if (mysqli_affected_rows() >= 0) {
			return true;
		} else {
			if (mysqli_errno() == 1062) {
				// Duplicate key - Primary Key Violation
			return true;
			} else {
				// For other errors
			return false;
			}
		}
		}else{
		return false;
		}
		
	}
######
     //////////////////////////////////// GET totol Expensive/////////////////////////
	public function getExpense() {
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
     
		$result = mysqli_query($this->link,"select * from expenses_image_url where eng_id='".$_REQUEST['eng_id']."' and  (update_date BETWEEN '$begin' and '$end')");
		return $result;
	} 	
########################
    ########################
     //////////////////////////////////// Tranning Master /////////////////////////
	public function getTrainingMaster() {
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
     
		$result = mysqli_query($this->link,"select * from training_master where status='Active'");
		return $result;
	} 	
     public function postTrainingdata($training_subject,$details,$eng_id) { 
 $today=date("Y-m-d h:i:s");
$reqtoday=date("Y-m-d");
         $sql_rep=mysqli_query($this->link, "select MAX(id) as id from training_request where 1");
         $row_max=mysqli_fetch_array($sql_rep);
         $row_count=$row_max['id']+1;
         $req_no="TR".$row_count;
 $res_jobsheet = mysqli_query($this->link,"insert into training_request set training_sub='".$training_subject."',training_details='".$details."',eng_id='".$eng_id."',request_by='APP',status='pending',req_no='".$req_no."',request_date='".$reqtoday."'");
 if ($res_jobsheet) {             
return 1;         
}  else {                 
 // For other errors                 
 return 0;             
                   
 }
     }
    //////////////////////////////////// GET totol Expensive/////////////////////////
	public function getTrainingReqList() {
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
     
		$result = mysqli_query($this->link,"select * from training_request where eng_id='".$_REQUEST['eng_id']."' and  (request_date BETWEEN '$begin' and '$end')");
		return $result;
	}
	//////////////////////////////////// GET totol Expensive/////////////////////////
	public function getAgreementDetails() {
	
		$result = mysqli_query($this->link,"SELECT * FROM `agrement_master` WHERE eng_id='".$_REQUEST['eid']."' and msg_id = '2'  ");
		return $result;
	}
	//////////////////////////////////// GET getSupportDetails  /////////////////////////
	public function getSupportDetails() {
	
		$result = mysqli_query($this->link,"SELECT * FROM eng_support_query WHERE eng_id='".$_REQUEST['eid']."'");
		return $result;
	}
	public function electric_failure() {
		$result = mysqli_query($this->link,"SELECT id,name FROM electric_failure WHERE status = 'A' order by id")or die(mysqli_error($this->link));
		return $result;
	}
#########

public function  postTermsConData($engId,$engName,$agreeFlag,$msg_id) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
	$datetimeval = $today ." ".$currtime;
 	$sql_agrem=mysqli_query($this->link, "SELECT eng_id FROM agrement_master WHERE eng_id='".$engId."' and 	msg_id='".$msg_id."'" )or die(mysqli_error($this->link)); 
					$rep_agrem = mysqli_fetch_array($sql_agrem);
	if($rep_agrem['eng_id']==''){
	$query="INSERT INTO agrement_master set eng_id='".$engId."',	eng_name='".$engName."',agrement_flag='".$agreeFlag."',	msg_id='".$msg_id."',type='APP' ";

	$resulthistory=mysqli_query($this->link,$query);
		}
else {	

   $query2="update agrement_master set agrement_flag='".$agreeFlag."' , update_date = '".$datetimeval."'   WHERE eng_id='".$engId."' and 	msg_id='".$msg_id."' ";

	   $resulthistory1=mysqli_query($this->link,$query2);
	    mysqli_commit($this->link);  
            return 2;
}
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return 0;
}     
 } 
	
	/////////////////////punch post data check
	
	public function  postPunchData($job_no,$eng_id,$punch_latitude,$punch_longitude,$punch_address,$punch_date,$punch_time) { 
	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
	$datetimeval = $today ." ".$currtime;
 	$sql_agrem=mysqli_query($this->link, "SELECT id FROM job_punch_details WHERE eng_id='".$eng_id."' and job_no='".$job_no."'" )or die(mysqli_error($this->link)); 
	$rep_agrem = mysqli_fetch_array($sql_agrem);
	if($rep_agrem['id']==''){
	$query="INSERT INTO job_punch_details set job_no='".$job_no."',	eng_id='".$eng_id."',punch_latitude='".$punch_latitude."',punch_longitude='".$punch_longitude."',punch_flag='Y',punch_address='".$punch_address."',punch_date='".$punch_date."',punch_time='".$punch_time."' ";

	$resulthistory=mysqli_query($this->link,$query);
		
	$query25="update jobsheet_data set punch_time='".$punch_time."' WHERE job_no='".$job_no."'";
		 $resultjob=mysqli_query($this->link,$query25);
		
		}
else {	
   $query2="update job_punch_details set punch_latitude='".$punch_latitude."',punch_longitude='".$punch_longitude."',punch_flag='Y',punch_address='".$punch_address."',punch_date='".$punch_date."',punch_time='".$punch_time."'   WHERE eng_id='".$engId."' and job_no='".$job_no."' ";
	   $resulthistory1=mysqli_query($this->link,$query2);
	
	$query25="update jobsheet_data set punch_time='".$punch_time."' WHERE job_no='".$job_no."'";
		 $resultjob=mysqli_query($this->link,$query25);
	    mysqli_commit($this->link);
	
            return 2;
}
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return 0;
}     
 } 
	
	/////////////Customer support
	
	
	/////////////////////punch post data check
	
	public function  postEnggSupprtData($eng_id,$subject,$detail) { 
	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
	$datetimeval = $today ." ".$currtime;
	  $query="INSERT INTO eng_support_query set eng_id='".$eng_id."',subject='".$subject."',detail='".$detail."',punch_date='".$today."',punch_time='".$currtime."' ";
		
	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}
if ($flag) {
	mysqli_commit($this->link);    
return 1;         
} else {
return 0;
}     
 } 
	
/////// function to send SMS written by shekhar on 17 may 2021
public function sendSMSByURL($mobile_no,$msg){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL =>'http://www.smsjust.com/sms/user/urlsms.php?username=microtek&pass=saloni19&senderid=MtekIn&dest_mobileno='.$mobile_no.'&message='.urlencode($msg).'&response=Y',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		));
		$response = curl_exec($curl);
		curl_close($curl);
		////// check whether response is ok or not so we will take last 10 character sub string from response it should be in today date format like YYYY_MM_DD
		$respdate = substr($response,-10);
		if($respdate == date("Y_m_d")){
			return "1~".$response;
		}else{
			return "0~Bad URL";
		}
	}
	/////// function to convert url in tiny url written by shekhar on 19 may 2021
	public function get_tiny_url($url)  {  
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
	// save  pickup
 public function savejob_pickup($job_no,$eid,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign) {

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
 ////////////////// Update Complaint Master //////////////////


$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='17', sub_status='2',pen_status='2' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."' where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
 
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='',status='Pickup Done',activity='Pickup Done',outcome='Pickup Done',updated_by='".$eid."', warranty_status='', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}






if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 
 
 }	
	// Save Pickup

	
	
	// save  Drop
 public function savejob_drop($job_no,$eid,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign) {

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
 ////////////////// Update Complaint Master //////////////////


$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='10', sub_status='10',pen_status='10' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."' where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
 
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='',status='Delivery Done',activity='Delivery Done',outcome='Delivery Done',updated_by='".$eid."', warranty_status='', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}






if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 
 
 }	
	// Save Drop
	//////////////////////////////////// GET totol Expensive/////////////////////////
	public function getPendingPaymenytCollection() {
	
		$result = mysqli_query($this->link,"SELECT * FROM `payment_receive_loc` WHERE engg_id='".$_REQUEST['eid']."' and engg_id!='' and payment_mode = 'By Cash' and collection_flag='' and inv_no!='' ");
		return $result;
	}
	
	///////// get user navigation
 	public function getTabRights($ei){
		$result_set = mysqli_query($this->link,"SELECT tabid, status FROM access_tab WHERE userid = '".$ei."'") or die(mysqli_error($this->link));
		return $result_set;
	}
	////// get main tab name 
	public function getMainTab(){
		$res_tab = mysqli_query($this->link,"SELECT maintabname FROM tab_master WHERE status = '1' GROUP BY maintabname ORDER BY maintabseq") or die(mysqli_error($this->link));
		return $res_tab;
	}
	////// get main tab name 
	public function getSubTab($maintab){
		$res_subtab = mysqli_query($this->link,"SELECT tabid , subtabname , app_filename FROM tab_master WHERE status = '1' AND  maintabname = '".$maintab."' ORDER BY subtabseq") or die(mysqli_error($this->link));
		return $res_subtab;
	}
	/////////////////////post collection post data check
	
	public function  postCollectonData($deposit_trn_no,$deposit_mode,$total_amt,$eng_code,$remark,$complaint_details) { 
		
	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
	$datetimeval = $today ." ".$currtime;
 	$sql_agrem=mysqli_query($this->link, "SELECT id FROM payment_receive_loc WHERE engg_id='".$eng_code."' and payment_mode = 'By Cash' and collection_flag='' " )or die(mysqli_error($this->link)); 
	$rep_agrem = mysqli_fetch_array($sql_agrem);
	if($rep_agrem['id']>0){
		if($eng_code!=''){
	//// Make System generated PNA no.//////
	$res_po=mysqli_query($this->link,"select max(temp_id) as no from collection_master where eng_id ='".$eng_code."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po['no']+1;
	$po_no="CLAP".$eng_code.$c_nos; 
	///////////////////
	
	for($i=0; $i<count($complaint_details) ; $i++) {
	$job_id=$complaint_details[$i]->job_no;
	$invno=$complaint_details[$i]->invno;	
	$pay_mode=$complaint_details[$i]->mode;
	$cr_no=$complaint_details[$i]->cr_no;
	$amt = $complaint_details[$i]->amt;
	$close_date = $complaint_details[$i]->amt;
	$location_code = $complaint_details[$i]->location_code;
		
		
		$sql_payrec=mysqli_query($this->link, "SELECT id FROM payment_receive_loc WHERE engg_id='".$eng_code."' and job_no='".$job_id."' and inv_no='".$invno."' and collection_flag='' " )or die(mysqli_error($this->link)); 
	$rep_datarec = mysqli_fetch_array($sql_payrec);
	if($rep_datarec['id']>0){
	
	$result = mysqli_query($this->link,"update payment_receive_loc set collection_flag = 'Y' , collection_no = '".$po_no."', collection_date = '".$today."'  where engg_id='".$eng_code."' and job_no='".$job_id."' and inv_no='".$invno."' ");
//// check if query is not executed
	if (!$result) {
	     $flag = false;
          $err_msg = "Error details Pay Receive: " . mysqli_error($this->link) . ".";
		
    }	
	
	##################333 data table entry ############################################################3
	$result1 = mysqli_query($this->link," insert into collection_data set collection_no='".$po_no."' , inv_no = '".$invno."', payment_mode = '".$pay_mode."' ,job_no ='".$job_id."' , close_date  = '".$close_date."' , cr_no = '".$cr_no."' ,amount = '".$amt."' , entry_date = '".$today."' , entry_by = '".$eng_code."' , location_code ='".$location_code."' , eng_id='".$eng_code."'  ");
	//// check if query is not executed
	if (!$result1) {
	     $flag = false;
         $err_msg = "Error details Collection data: " . mysqli_error($this->link) . ".";
		
    }

  $totamt+=$_REQUEST[$amt]; 
	}
		else {
		$flag = false;
        $err_msg = "Error details check 1: ";
	
		}
}//////////for loop check data

////////////////////
      $usr_add="INSERT INTO collection_master set collection_no='".$po_no."', collection_date ='".$today."',deposit_trn_no='".$deposit_trn_no."',deposit_mode='".$deposit_mode."' , location_code ='".$location_code."' , eng_id='".$eng_code."' , create_by = '".$eng_code."', totalamt = '".$total_amt."' , temp_id = '".$c_nos."'  ";
			
  $result3=mysqli_query($this->link,$usr_add);
	//// check if query is not executed
	if (!$result3) {
	     $flag = false;
          $err_msg = "Error details collection master: " . mysqli_error($this->link) . ".";
		
    }
	
	////// insert in activity table////
		$query_da = "INSERT INTO daily_activities SET userid='".$old_s['eng_id']."', ref_no='".$old_s['job_no']."', activity_type='Collection List', action_taken='Add', update_date='".$today."',update_time='".$currtime."',system_ip='".$_SERVER['REMOTE_ADDR']."'";
		$result_da = mysqli_query($this->link,$query_da);
						//// check if query is not executed
						if (!$result_da) {
							$flag = false;
							 $error_msg = "Error Code daily: ". mysqli_error($this->link);
							
						}
		}
		else {
		$flag = false;
         $err_msg = "Engg ID: ";
			
		}
		}////////data check
		else {
		$flag = false;
         $err_msg = "Error details6: ";
			
		}
	
if ($flag) {
	//mysqli_rollback($this->link);
	mysqli_commit($this->link);     
return 1;         
} else {
mysqli_rollback($this->link);
return $err_msg;
}     
 } 
/////////////////////// check Pending Complaint for Serial //////////////////////
	public function checkSerialDupli($serialno,$jobno) {
		$str = "";
		if($jobno != ""){ $str = " and job_no != '".$jobno."' "; }else{ $str = ""; }
		$result = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serialno."' ".$str." and status in('1','2','3','4','5','7','11','50','51','52','59','501','502')") or die(mysqli_error($this->link)); 
		return $result;         
	}	
	/////////////////////// check Pending Complaint for Serial //////////////////////
	public function checkSerialDupliRepl($serialno,$jobno) {
		$str = "";
		if($jobno != ""){ $str = " and job_no != '".$jobno."' "; }else{ $str = ""; }
		$result = mysqli_query($this->link,"SELECT replace_serial FROM repair_detail  where old_serial='".$serialno."' and repair_type='Replacement'") or die(mysqli_error($this->link)); 
		return $result;         
	}
	/////////////////////// check Pending Complaint for Serial //////////////////////
public function checkSerialDupliReplTTBtr($serialno,$jobno) {
	$str = "";
	if($jobno != ""){ $str = " and job_no != '".$jobno."' "; }else{ $str = ""; }
	$result = mysqli_query($this->link,"SELECT replace_serial FROM jobsheet_data  where replace_serial='".$serialno."' and status = '51' and sub_status = '51' and l3_status = '51' and line = 'DC Create' ".$str." ") or die(mysqli_error($this->link)); 
	return $result;         
}
 /////////////////////// check BSN replacement token genrated or not //////////////////////
 public function checkReplTokenGenrated($serialno,$jobno) {
	$str = "";
	if($jobno != ""){ $str = " and job_no != '".$jobno."' "; }else{ $str = ""; }
	$result = mysqli_query($this->link,"select imei from jobsheet_data where imei='".$serialno."' ".$str." and repl_appr_no != '' and status not in ('12')") or die(mysqli_error($this->link)); 
	return $result;         
}	
###### FUnction for Call Data
	/////////////////////punch post data check
	
	public function  postCallData1($job_no,$eng_id,$call_duration,$start_time,$end_time,$call_time,$call_date) { 
	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	$today = $this->dt_format->format('Y-m-d');
	$currtime = $this->dt_format->format('H:i:s');
	$datetimeval = $today ." ".$currtime;
 	
	//$job_count=mysqli_num_rows(mysqli_query($this->link,"select id from job_call_details where job_no='".$job_no."' and end_time = '".$end_time."' "));
	//$call_duration > '00:00:45'
	//if($job_count=='0'){
		$query="INSERT INTO job_call_details set job_no='".$job_no."',	eng_id='".$eng_id."',call_duration='".$call_duration."',start_time='".$start_time."',end_time='".$end_time."', call_time = '".$call_time."', call_date = '".$call_date."' ";
		
		$resulthistory=mysqli_query($this->link,$query);
	//}
	if(!$resulthistory){
		$flag = false;
		$error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
	}
	if($flag){
		mysqli_commit($this->link);    	
		return 1;         
	} else {
		return 0;
	}     
 } 	
	////////////////////////// Pincode Detail ///////////////////////   
 public function getPincode_data() {  
 	$result = mysqli_query($this->link,"SELECT pincode,cityid,stateid FROM pincode_master where  pincode ='".$_REQUEST['pincode']."' and statusid='1' LIMIT 1") or die(mysqli_error($this->link));  
 	return $result;    
 } 
}