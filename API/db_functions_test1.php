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
 
 $result = mysqli_query($this->link,"SELECT ser_charge,model,model_id,product_id,brand_id,wp FROM model_master WHERE status ='1'") or die(mysqli_error($this->link));         
 return $result;     
 }


 //////////////Product Master//////////
 function getProduct($ser_sync_time){
	 $result=mysqli_query($this->link,"select * from product_master where status='1' and autoupdatedate >'".$ser_sync_time."'");
	 return $result;
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
	 $result = mysqli_query($this->link,"SELECT * FROM symptom_master where status='1'  and  product_id ='".$product."' ")  or die(mysqli_error($this->link));
	 return $result;
 }
    /////////////////////// Voc MASTER //////////////////////
 public function getStatus($type) {
 if($type=="Installation"){
 	 $result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('Installation Done','Request For Approval','Cancel') order by display_status")  or die(mysqli_error($this->link));
	 }else{
	  $result = mysqli_query($this->link,"select status_id, display_status,id from jobstatus_master where status_id=main_status_id and system_status in ('EP','PNA','Handover','WIP','Request For Approval','Cancel') order by display_status")  or die(mysqli_error($this->link));
	 
	 }
	 return $result;
 }
 /////////////////////// Part MASTER //////////////////////
 public function getPart($model) {
// echo "SELECT partcode,part_name,product_id,brand_id,part_category,model_id,vendor_partcode,customer_price,status FROM partcode_master where status='1' and find_in_set($model,model_id) <> 0";
	 $result = mysqli_query($this->link, "SELECT partcode,part_name,product_id,brand_id,part_category,model_id,vendor_partcode,customer_price,status FROM partcode_master where status='1' and model_id LIKE '%$model%'") or die(mysqli_error($this->link));
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
 
 if($_REQUEST['from_date']!='' && $_REQUEST['to_date']!=''){
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
		}
		else{
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');	
		}  
	
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
 $result =mysqli_query($this->link,"SELECT * FROM request_reason where  update_date >'".$ser_sync_time."'")or die(mysqli_error($this->link));          
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
 $result =mysqli_query($this->link,"SELECT * FROM repaircode_master WHERE status ='1' and product_id='".$product."'")or die(mysqli_error($this->link));   
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
       
  $result = mysqli_query($this->link,"SELECT * FROM locationuser_master where userloginid='".$_REQUEST['eid']."' and pwd=BINARY  '".$_REQUEST['password']."'") or die(mysqli_error($this->link));  
 
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
	 
$chk=mysqli_query($this->link,"select id from mic_attendence_data where user_id='$ei' and insert_date='$insert_date'");
$chk2=mysqli_fetch_array($chk);
if(mysqli_num_rows($chk)<=0 && $status_in!=''){
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
 public function storeImageJob2($job_no,$fileName1,$fileName2,$fileName3,$fileName4,$fileName5) { 
 $today=date("Y-m-d");
 $res_jobsheet = mysqli_query($this->link,"insert into image_upload_details set img_url='".$fileName1."',img_url1 ='".$fileName2."',img_url2='".$fileName3."',img_url3='".$fileName4."',img_url4='".$fileName5."',job_no='".$job_no."',activity='Image Upload by app',upload_date='".$today."'");
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
					
					if($prd_code!= ""){
					
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


//////////////////////////////////////////////Installation Done///////////////////////////////////


 public function savejobdatainsllation($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$close_reason,$remark,$part_consume,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
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
				
					
				$res_invt = mysqli_query($this->link,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$old_s['current_loction']."' and partcode='".$prd_code."' and locationuser_code='".$old_s['eng_id']."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
				}
						
				
		}
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$today."',close_time='".$currtime."',pen_status='6',dop='".$dop."',warranty_status='".$warranty_status."',close_rmk='".$closed_reason."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' , close_tat = '".$ctat."', ageing_close_tat = '".$getage[1]."', tat_open_close = '".$tat_open_close."',path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$status."',activity='Installation Done',outcome='Installation Done',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."',travel_km='".$travelKM."'";

	$resulth=mysqli_query($this->link,$query);
		if (!$resulth) {
			 $flag = false;
			 $error_msg = "Error details2history: " . mysqli_error($this->link) . ".";
		}
		
		






$max_rep=mysqli_query($this->link,"insert into job_claim_appr set job_no='".$job_no."',brand_id='".$old_s['brand_id']."',action_by='".$old_s['current_location']."',rep_lvl ='".$row_level['b']."',model='".$old_s['model_id']."',product_id='".$old_s['product_id']."',entity_type='".$old_s['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$old_s['area_type']."' , claim_tat='".$ctat."', tatminus='".$tatminus."', tat_open_close = '".$tat_open_close."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($this->link) . ".";
		}	
	



if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 }   
 
 //////////////////////////////////////////////////Request For Approval///////////////////////////////////////////////////
 
  public function savejobdataRequest($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$request_reason,$remark,$part_consume,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
 ////////////////// Update Complaint Master //////////////////
 if($repair_status==50){
 
 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_reason='".$request_reason."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
 
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Pending For Approval',outcome='".$request_reason."',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

}


 if($repair_status==12){
 
	$res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',close_date='".$today."',close_time='".$currtime."',pen_status='6',dop='".$dop."',warranty_status='".$warranty_status."',close_rmk='".$closed_reason."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'  where job_no='".$job_no."'");
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Call Cancel',outcome='Call Cancel',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
			}

}


if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 } 
 
 /////////////////////////////////////PNA/////////////////////////////////
 
 

 
  public function savejobpna($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$request_reason,$remark,$pnaList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
 ////////////////// Update Complaint Master //////////////////
$old_s=mysqli_fetch_array(mysqli_query($this->link,"select * from jobsheet_data where job_no='".$job_no."'"));
 
 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
 
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Part Not Available',outcome='Part Not Available',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
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


if ($flag) {
	
	mysqli_commit($this->link);    
        
return 1;         
} else {
return $error_msg;
}     
 } 
 ////////////////////////////////////////////////////////Estimate Pending List//////////////////////////////////////////
 
 
 
 
 public function savejobep($job_no,$serial_no,$warranty_status,$model,$model_id,$repair_status,$dop,$request_reason,$remark,$epList,$latitude,$longitude,$address,$path_img,$path_img1,$path_img2,$path_img_sign,$serviceCharge,$serviceTaxAmt,$totalService,$totalPartAmt) { 

	$flag = true;
	mysqli_autocommit($this->link, false);
	$error_msg = "";
	
	$today=date('Y-m-d');
	$currtime=date("H:i:s");
 ////////////////// Update Complaint Master //////////////////

 
 $res_jobsheet = mysqli_query($this->link,"UPDATE jobsheet_data set status='".$repair_status."', sub_status='".$repair_status."',pen_status='2',dop='".$dop."',warranty_status='".$warranty_status."' ,model_id = '".$model_id."', model = '".$model."',app_rmk='Done By App',imei='".$serial_no."' ,path_img='".$path_img."',path_img1='".$path_img1."',path_img2='".$path_img2."',path_img_sign='".$path_img_sign."'  where job_no='".$job_no."'");
	
		if (!$res_jobsheet) {
			 $flag = false;
			 $error_msg = "Error details2jobsheet: " . mysqli_error($this->link) . ".";
		}
 
 
 

	
		$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$old_s['current_location']."',status='".$repair_status."',activity='Estimate Pending',outcome='Part Not Available',updated_by='".$old_s['eng_id']."', warranty_status='".$old_s['warranty_status']."', remark='".$remark."',ip='".$_SERVER['REMOTE_ADDR']."' ,travel_km='".$travelKM."',travel='Y',latitude='".$latitude."',longitude='".$longitude."',address ='".$address."'";

	$resulthistory=mysqli_query($this->link,$query);
	if (!$resulthistory) {
				 $flag = false;
				 $error_msg = "Error details3jobsheet: " . mysqli_error($this->link) . ".";
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
					
					if($rep_part['partcode'] != ""){
					
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
				
				
				if($rep_part['partcode'] != ""){
					
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
 $result = mysqli_query($this->link,"SELECT * FROM jobsheet_data where job_no='".$_REQUEST['job_no']."'")or die(mysqli_error($this->link));         
 return $result;     
 }

//////////////////////////////////// count no. of jobs/////////////////////////
	public function getJobSum() {
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 1 then status end) as open,COUNT(case when status = 2 then status end) as pending,COUNT(case when status = 3 then status end) as pna,COUNT(case when status = 5 then status end) as ep,COUNT(case when status = 7 then status end) as wip,COUNT(case when status = 10 then status end) as closed,COUNT(case when status = 50 then status end) as pfa from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end') and call_for!='Installation'");
		return $result;
	} 
	
	//////////////////////////////////// count no. of jobs/////////////////////////
	public function getJobSuminstall() {
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 48 then status end) as inst_done,COUNT(case when status IN ('2','55','56') then status end) as inst_pending from jobsheet_data where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end') and call_for='Installation'");
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
		elseif($_REQUEST['toolType'] == 'SPARE') {
			
			$result = mysqli_query($this->link,"select distinct(partcode) as partcode,id,part_name from partcode_master where status='1' and part_category='SPARE' order by part_name ")or die(mysqli_error($this->link));
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
		$result = mysqli_query($this->link,"SELECT challan_no,to_location,sale_date,status,from_location FROM stn_master WHERE  to_location='".$engid."' order by sale_date")or die(mysqli_error($this->link));
		return $result;
	}
#########################	

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
		$result = mysqli_query($this->link,"SELECT sno,from_location,job_no,imei,partcode,qty,consumedate,status FROM part_to_credit WHERE eng_status='1' and eng_id='".$engid."'")or die(mysqli_error($this->link));
		return $result;
	}
######################### 
###### Function for  FAulty Dispatch
public function getFaulty_Challan($engid) {
		$result = mysqli_query($this->link,"SELECT * FROM part_to_credit WHERE (dispatchstatus = 'Dispatched' or dispatchstatus='ENGDispatched') and eng_id='".$engid."' group by challan_no order by challan_date DESC")or die(mysqli_error($this->link));
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
 public function getstockeng_mdel($eid,$modelid) { 
//	echo "SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' and partcode in (select partcode from partcode_master where model_id LIKE '%$modelid%' and status='1') ";
 $result = mysqli_query($this->link,"SELECT * FROM user_inventory where  locationuser_code 	='".$eid."' and partcode in (select partcode from partcode_master where (model_id LIKE '%".$modelid."%' OR part_category='GLOBAL') and partcode!='39' and status='1') ") or die(mysqli_error($this->link));     
 return $result;     
 }
 
 ////////////////////////////AMC Data/////////////////////////////////////////////////////////////////
 public function getAMC($eid) {  
 
 if($_REQUEST['from_date']!='' && $_REQUEST['to_date']!=''){
		$begin = $_REQUEST['from_date'];
		$end = $_REQUEST['to_date'];
		}
		else{
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');	
		}  
	
 $result = mysqli_query($this->link,"SELECT amcid,serial_no,customer_id,contract_no,product_id,model_id,amc_type,open_date,status,customer_name FROM amc where eng_id='".$eid."' and open_date BETWEEN '$begin' and '$end'") or die(mysqli_error($this->link));     
 return $result;     
 }
//////AMC Details////
 function getAMC_Details($amcid){
	 $result = mysqli_query($this->link,"SELECT amcid,serial_no,customer_id,contract_no,product_id,model_id,amc_type,open_date,status,customer_name,addrs,amc_amount, city_id,state_id,location_code  FROM amc where amcid='".$amcid."' ") or die(mysqli_error($this->link));   
	return $result;
 } 
 
 /////////////////////// AMC Status //////////////////////
 public function getAMCStatus() {
	  $result = mysqli_query($this->link,"select status_id, display_status from jobstatus_master where status_id in ('51','52') order by display_status")  or die(mysqli_error($this->link));
	 return $result;
 } 
###################### 
//////////////////////////////////// count no. Assigned AMC/////////////////////////
	public function getAssignedAMC() {
		$begin = date('Y-m-d', strtotime("-60 days"));
		$end = date('Y-m-d');

		$result = mysqli_query($this->link,"select COUNT(case when status = 2 then status end) as amc_assigned from amc where eng_id='$_REQUEST[eid]' and  (open_date BETWEEN '$begin' and '$end')");
		return $result;
	} 	
########################
//////////////////////////////////// count no. Assigned AMC/////////////////////////
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



}