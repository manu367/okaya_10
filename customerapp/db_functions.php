<?php
class DB_Functions {       
private $db;
private $link;
private $dt_format;
function __construct() {         
include_once 'db_connect.php'; 
		$this->db = new DB_Connect();         
		$this->link = $this->db->connect();
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp     
}       
function __destruct() {       
}
///////////// Part Master ////////////////////
 public function getPartMaster() {        
 $result = mysqli_query($this->link,"SELECT * FROM part_master WHERE status = 'A'")or die(mysqli_error($this->link));         
 return $result;     
 }   
 /////////////// Fault Master /////////////////////
 public function getFaultMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM defect_detected_master WHERE status = 'A'")or die(mysqli_error($this->link));         
 return $result;     
 } 
 //////////////// VOC Code Master ///////////////
   public function getVocCodeMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM voc_master WHERE status = 'A'")or die(mysqli_error($this->link));         
 return $result;     
 } 
 //////////////// Repair Code Master ///////////////
   public function getRepairCodeMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM solution_master WHERE status = 'A'")or die(mysqli_error($this->link));         
 return $result;     
 }    
 //////////////// Reason Master ////////////////////
 public function getReasonMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM reason_master WHERE status = 'A'")or die(mysqli_error($this->link));         
 return $result;     
 } 
 //////////////// Close Reason Master ////////////////////
 public function getCloseReasonMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM close_reason_master WHERE status = 'A'")or die(mysqli_error($this->link));         
 return $result;     
 }  
 ////////////////////////Customer Master////////
 public function getCustomerData($id){
 $result=mysqli_query($this->link,"select * from customer_master where id='$id'");
 return $result;
 }
 /////////////// Complaints Master /////////////////
 public function getComplaintsMaster() {       
  
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master where engg_assign='$_REQUEST[eid]' and (status='2' or  status='6') and open_date>='2016-11-01' ")or die("err1".mysqli_error($this->link));         
 return $result;     
 }
 
 public function getAllComplaints($eid) {       
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master where engg_assign='$eid' and status!='8' and open_date>='2016-11-01'")or die("err2".mysqli_error($this->link));         
 return $result;     
 }
 
 public function getPendingComplaints($eid){
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master where engg_assign='$eid'  and status='2' and open_date>='2016-11-01'")or die("err2".mysqli_error($this->link));         
 return $result;   
 }
  public function getClosedComplaints($eid){
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master where engg_assign='$eid'  and status='6' and open_date>='2016-11-01'")or die("err2".mysqli_error($this->link));         
 return $result;   
 }
/////////////// Customer Master ///////////////////////////
 public function getCustomerMaster() {         
 $result = mysqli_query($this->link,"SELECT a.* FROM customer_master a, complaints_master b where b.engg_assign='$_REQUEST[eid]' and (b.status='2' or b.status='6' ) and  b.open_date>='2016-11-01' and a.id=b.customer_id group by b.customer_id")or die(mysqli_error($this->link));         
 return $result;     
 }
 ///////////////////user login//////////////////
 public function getUserLogin(){
  $result = mysqli_query($this->link,"SELECT * FROM user_master where userid='$_REQUEST[eid]' and password='$_REQUEST[password]' and status='A'") or die(mysqli_error($this->link));         
 return $result; 
 }
 //////////////////////state master///////////////////////
  public function getStateMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM state_master  where  countryid = '1' order by state")or die(mysqli_error($this->link));         
 return $result;     
 } 
 /////////////// City Master /////////////////////
 public function getCityMaster() {         
 $result = mysqli_query($this->link,"SELECT * FROM city_master  order by city")or die(mysqli_error($this->link));         
 return $result;     
 } 
 
 public function getCityMaster_State() {         
 $result = mysqli_query($this->link,"SELECT * FROM city_master where stateid='$_REQUEST[stateid]'  order by city")or die(mysqli_error($this->link));         
 return $result;     
 } 
 ///////////////////// Engg Detail ///////////////////////   
 public function getUserDetails() {         
 $result = mysqli_query($this->link,"SELECT * FROM user_master where userid='$_REQUEST[eid]' and status='A'") or die(mysqli_error($this->link));         
 return $result;     
 } 
 
////////////////////////// Customer Detail ///////////////////////   
 public function getCustomerDetails() {     
 $result = mysqli_query($this->link,"SELECT * FROM customer_master where id='$_REQUEST[customer_id]'  or mobile ='$_REQUEST[mobile]' ") or die(mysqli_error($this->link));  
 return $result;    
 } 
 
 
 ////////////////////////// Registered Product Detail ///////////////////////limit removed//   
 
 public function RegProductDetails() {      
 $result = mysqli_query($this->link,"SELECT * FROM product_registered where customer_id='$_REQUEST[customer_id]' ") or die(mysqli_error($this->link));  return $result;    
 } 
 
 
 ////////////////////////// Registered Ticket Detail ///////////////////////   
 public function RegTicketDetails() {   
 $today=date('Y-m-d');
 $startdate=strtotime('-30 days',strtotime($today));
  $start=date('Y-m-d',($startdate));    
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master where customer_id='$_REQUEST[customer_id]' and open_date between '$start' AND '$today' order by open_date desc ") or die(mysqli_error($this->link)); 
 return $result;   
 }
 
public function RegTicketDetailsNSK() {  
$today=date('Y-m-d');
 $startdate=strtotime('-30 days',strtotime($today));
  $start=date('Y-m-d',($startdate));     
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master_wp where customer_id='$_REQUEST[customer_id]' and open_date between '$start' AND '$today' order by open_date desc ") or die(mysqli_error($this->link)); 
 return $result;   
 } 
 
  ////////////////////////// Registered Ticket Detail of Paricular Customer ///////////////////////   
 public function CustTicketDetails() {     
 $result = mysqli_query($this->link,"SELECT * FROM complaints_master where customer_id='$_REQUEST[customer_id]'  and job_no ='$_REQUEST[ticket_no]'") or die(mysqli_error($this->link)); 
 return $result;   
 } 
 
 
 /////////////////////// MODEL MASTER //////////////////////
 public function getModel() {

	 $result = mysqli_query($this->link,"SELECT * FROM model_master where productid='$_REQUEST[productid]' and status='A' group by model") or die(mysqli_error($this->link));
	 return $result;
 }
 
  /////////////////////// Brand MASTER //////////////////////
 public function getBrand(){
	 $result = mysqli_query($this->link,"SELECT distinct(make) FROM model_master where status='A' and make != '' ") or die(mysqli_error($this->link));
	 return $result;
 }
 //////////////////// Company Master //////////////////////////
 public function getCompany() {
	 $result = mysqli_query($this->link,"SELECT * FROM company_master where status='A'") or die (mysqli_error($this->link));
	 return $result;
 }
  
 
 /////////////////////// PRODUCT MASTER /////////////////////
 public function getProduct() {
	 $result = mysqli_query($this->link,"SELECT * FROM product_master  where status='A'") or die (mysqli_error($this->link));
	 return $result;
 }
 
  /////////////////////// Get Registered PRODUCT  for Customer/////////////////////
	public function getRegProduct() {
	 $result = mysqli_query($this->link,"SELECT distinct(model_id), product_id FROM product_registered where customer_id ='$_REQUEST[customer_id]'") or die (mysqli_error($this->link));
	 return $result;
 }
 

////////////////////////////////////////// Customer Registration /////////////////////////////////////////////////////
public function  Registration_data($id,$customer_id,$name,$mobile,$address,$city,$email,$stateid){
	$today=date("Y-m-d");
	$sel_uid="select max(id) from customer_master";
	$res_uid=mysqli_query($this->link,$sel_uid);
$arr_result2=mysqli_fetch_array($res_uid);
$code_id=$arr_result2[0];
$pad=str_pad(++$code_id,5,"0",STR_PAD_LEFT);
$customer_id="C".$stCode.$pad;
//////////////////////check to findout whther entered mobile number exists in db ///////////////////////////////////////////
$sql = "select mobile from customer_master where mobile = '$mobile' ";
$rows = mysqli_query($this->link,$sql);
if(mysqli_num_rows($rows)>0)
{
$b["status"] = '2'; 
}
else {
$res=mysqli_query($this->link,"insert into customer_master set  customer_id='$customer_id',customer_name='$name',mobile='$mobile', address1='$address',cityid='$city',email='$email' ,stateid='$stateid', update_by='c_app',update_date='$today' ")or die("".mysqli_error($this->link));
if(mysqli_affected_rows($res)>=0){
	$d= mysqli_fetch_array(mysqli_query($this->link,"SELECT id FROM customer_master WHERE customer_id = '$customer_id' "));
	$b["id"] = $d["id"];
	$b["customer_id"] = $customer_id; 
	$b["customer_name"] =$name;   
	$b["mobile"] = $mobile; 
	$b["address1"]=$address;
	$e= mysqli_fetch_array(mysqli_query($this->link,"SELECT city FROM  	city_master WHERE cityid = '$city'"));
	$b["city"] = $e["city"];
	$b["cityid"]=$city;   	
	$b["email"]=$email;
	$b["stateid"]=$stateid;
	$f= mysqli_fetch_array(mysqli_query($this->link,"SELECT state FROM  	state_master WHERE stateid = '$stateid'"));
	$b["state"] = $f["state"];
	$b["status"] = '1';  
  }   else {             
  if( mysqli_errno() == 1062) {                
   // Duplicate key - Primary Key Violation                 
   
	$b["customer_id"] = $customer_id; 
	$b["customer_name"] =$name;   
	$b["mobile"] = $mobile; 
	$b["address1"]=$address;
	$b["cityid"]=$city;   
	$b["email"]=$email;  
	$b["stateid"]=$stateid;
	$f= mysqli_fetch_array(mysqli_query($this->link,"SELECT state FROM state_master WHERE stateid = '$stateid'"));
	$b["state"] = $f["state"];
	$b["status"] = '1';          
     } else {                 
   // For other errors                 
  
	$b["customer_id"] = $customer_id; 
	$b["customer_name"] =$name;   
	$b["mobile"] = $mobile; 
	$b["address1"]=$address;
	$b["cityid"]=$city;   
	$b["email"]=$email;
	$b["stateid"]=$stateid;
	$f= mysqli_fetch_array(mysqli_query($this->link,"SELECT state FROM state_master WHERE stateid = '$stateid'"));
	$b["state"] = $f["state"];   
	$b["status"] = '0';             
       }                     
  } 
  }    
	return $b; 
	}
////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////// validate serial number /////////////////////////////////////////////////
 		public function ValidateSerialno() {
 		$today=date("Y-m-d");
	 	$result = mysqli_query($this->link,"SELECT serial_no FROM product_registered where model_id ='$_REQUEST[model_id]' and serial_no='$_REQUEST[serial_no]'") or die (mysqli_error($this->link)); 
	 return $result;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function ValidateSerialnobattery() {
 		$today=date("Y-m-d");
	 	$result = mysqli_query($this->link,"SELECT distinct(serial_no),model FROM warranty_data_battery where serial_no='$_REQUEST[serial_no]' order by serial_no desc") or die (mysqli_error($this->link)); 
	 return $result;
}


////////////////////////////////
public function ValidateSerialnonasaka() {
 		$today=date("Y-m-d");
	 	$result = mysqli_query($this->link,"SELECT distinct(serial_no),model FROM warranty_data_wp where serial_no='$_REQUEST[serial_no]' order by serial_no desc") or die (mysqli_error($this->link)); 
	 return $result;
}
///////////////////////////////////////////  Add complaint of Product (app) /////////////////////////////////////////////////////
		public function  ProComplaint($customer_id,$productid,$modelid,$serialno,$des){
			
//complaint counter update , call_type, repeat flag , pending call check SEP12 2018 SHIKHAR//// 	

     $sql_city="select cityid from customer_master where id='$customer_id'";
     $rs_sql=mysqli_query($this->link,$sql_city) or die(mysqli_error($this->link));
     $row_sql=mysqli_fetch_assoc($rs_sql);
     $city=$row_sql['cityid'];
             
			$product_type_query =mysqli_query($this->link,"select productcode from model_master where modelcode = '$modelid'");			
         $product_code_array=mysqli_fetch_array($product_type_query);
			$product_code = $product_code_array['productcode'];
			$today=date("Y-m-d");
			$today_time=date("H:i:s");
			$error_msg = "ER0";
			
			$product_name_query =mysqli_query($this->link,"select productname from product_master where productcode = '$product_code'") or die(mysqli_error($this->link));
			$product_name_array=mysqli_fetch_array($product_name_query);
			$product_name =$product_name_array['productname'];
		
			
			if($product_code == "WP" || $product_code == "AMC"){
				
				
				
				
 $res_dup=mysqli_query($this->link,"select serial_no from complaints_master_wp where serial_no='$serialno'");
if (mysqli_num_rows($res_dup)>0){ $repeatflag="Y";} else {$repeatflag="";}

$jobdup=mysqli_query($this->link,"select serial_no from complaints_master_wp where serial_no='$serialno' and status!='6' and serial_no!='ZZZZZZZZZZ'");
if (mysqli_num_rows($jobdup)==0){				
				
				
				//$ticket_id_query = mysqli_query($this->link,"select max(cnt) from complaints_master_wp");
				//$ticket_id_arr = mysqli_fetch_array($ticket_id_query);
				//$code_id=$ticket_id_arr[0]+1;
				//$ticket_id = "NK".$code_id;
				
				
				
					if($serialno!=''){
						$cat_call=mysqli_query($this->link,"select * from warranty_data_wp where serial_no='$serialno'  order by sno desc")or die(mysqli_error($this->link));
		  $rowcat=mysqli_fetch_array($cat_call);
	if (mysqli_num_rows($cat_call)>0){ 
	$indb*=1;
	$model=$rowcat[model];
	$sql_chk="select date_intallation from installation_master where serial_no='$serialno'";
	$rs_chk=mysqli_query($this->link,$sql_chk) or die(mysqli_error($this->link));
	if(mysqli_num_rows($rs_chk)>0) {
	$row_chk=mysqli_fetch_assoc($rs_chk);
	$start_date=strtotime($row_chk[date_intallation]);
	
	//$start_date='2018-07-27';
   $enddate=strtotime('+1 years',$start_date);
   $enddate=date('Y-m-d',($enddate));  
  
   if($enddate >= $today){
     $warranty="IN";
     }}else {  
     $sql_chk="select amc_end_date from amc where serial_no='$serialno'";
	$rs_chk=mysqli_query($this->link,$sql_chk) or die(mysqli_error($this->link));
	if(mysqli_num_rows($rs_chk)>0){
	$row_chk=mysqli_fetch_assoc($rs_chk);
	$enddate=$row_chk[amc_end_date];

	if($enddate >= $today){
     $warranty="IN";
     }}else{
	// check end date if end date is greater then today date
	//if MFG Code is added and mfg code is grater then warranty End date concider MFG date as warranty end date
	$enddate=$rowcat[end_date];
	if($rowcat[end_date] > $enddate ){$finalenddate=$rowcat[end_date];} else {$finalenddate=$enddate;}
	if($finalenddate >= $today){
	$warranty="IN";
	}else {
		$indb*=0;
	$warranty="OUT";
		}}
	
	}}
	$scm=rand(1111,9999);
							
							
							$sql = mysqli_query($this->link,"insert into complaints_master_wp set problem_detail='water is not coming',cityid='$city',location_id='139',SCM='$scm',repeat_call='$repeatflag',call_type='customer_app',cnt = '$code_id',modelid ='$modelid' ,warranty_status='$warranty', serial_no='$serialno'  ,customer_id='$customer_id'  ,productid='Water Purifier' ,open_date='$today',open_time='$today_time',call_attend_date='$today', call_attend_time='$today_time',status='0', updated_by='c_app',userid='c_app'");
							$error_msg .= "ER2 ".mysqli_error($this->link);
							$check = mysqli_affected_rows();
							$jid=mysqli_insert_id();
		//$j_id=$jid-2259543;
		$jobno="NK".$jid;
		$jobupd=mysqli_query($this->link,"update complaints_master_wp set job_no=CONCAT('NK',id) where job_no=''") or die(mysqli_error($this->link));
		//echo $jobno;
		//$sql_serial="insert into complaint_logged_serial set job_no='$jobno',serial_no='$_POST[serial_no]',userid='$_SESSION[userid]',customer_id='$_POST[cid]'";
		//mysqli_query($this->link,$sql_serial);
							
							
							
							}}}else {
							
							
									
								
				
			
				$ticket_id_query = mysqli_query($this->link,"select max(cnt) from complaints_master");
				$ticket_id_arr = mysqli_fetch_array($ticket_id_query);
				$code_id=$ticket_id_arr[0]+1;
				$ticket_id = "OPG".$code_id;
				
				
				
					if($serialno!=''){
						
						$res_dup=mysqli_query($this->link,"select serial_no from complaints_master where serial_no='$serialno'");
       if (mysqli_num_rows($res_dup)>0){ $repeatflag="Y";} else {$repeatflag="";}
       $jobdup=mysqli_query($this->link,"select serial_no from complaints_master where serial_no='$serialno' and status!='6' and serial_no!='ZZZZZZZZZZ'");
       if (mysqli_num_rows($jobdup)==0){
                 						
						
						
						$warranty_data = mysqli_query($this->link,"select start_date, end_date from warranty_data_battery where serial_no='$serialno'");
						if(mysqli_num_rows($warranty_data) > 0){
							if($warranty_data["end_date"] >= $today) {
								$warranty="IN";
								}else {
								$warranty="OUT";
								}
								
								
								$scm=rand(1111,9999);
								
								
								$sql = mysqli_query($this->link,"insert into complaints_master set battey_type='$product_name',problem_detail='Low Back UP',cityid='$city',location_id='139',SCM='$scm',repeat_call='$repeatflag',call_type='customer_app',cnt = '$code_id',modelid ='$modelid' , warranty_status='$warranty', serial_no='$serialno'  ,customer_id='$customer_id'  ,productid='$product_name' ,description='$des',open_date='$today',open_time='$today_time',call_attend_date='$today', call_attend_time='$today_time',status='0', updated_by='c_app',userid='c_app' ");
								$error_msg .= "ER6 ".mysqli_error($this->link);
								$check = mysqli_affected_rows();
								$jid=mysqli_insert_id();
                        		if($today<"2016-09-01")
		{
		$result_code=mysqli_query($this->link,"select max(id) as a from complaints_master where open_date='2016-05-31'");
		$arr_result2=mysqli_fetch_array($result_code);
		$code_id=$jid-$arr_result2[a];

		$y_query=mysqli_query($this->link,"Select * from year_code_master where year='$today_y'");
		$row_y=mysqli_fetch_array($y_query);
		$m_code=mysqli_query($this->link,"Select * from month_code_master where month='$today_m'");
		$row_m=mysqli_fetch_array($m_code);
		$pad=str_pad($code_id,6,"0",STR_PAD_LEFT);
		$jobno=$row_y[code].$row_m[code].$pad;
		$jobupd=mysqli_query($this->link,"update complaints_master set job_no=CONCAT('$row_y[code]','$row_m[code]',LPAD((id-$arr_result2[a]),6,'0')) where id='$jid'") or die(mysqli_error($this->link));
		}
		elseif($today>="2016-09-01")
		{
		$jobno="OPG".$jid;
		$jobupd=mysqli_query($this->link,"update complaints_master set job_no=CONCAT('OPG',id) where id='$jid'") or die(mysqli_error($this->link));
		}								
 								
								
							
					}
								
			}}	}	
		
		
		if($check > 0){
			$b["customer_id"] = $customer_id; 
			$b["ticketid"] =$ticket_id;   
			$b["productid"] = $productid; 
			$b["description"] = $des; 			
			$b["status"] = '1';  
			$b["warranty"]=$warranty;
  			}   else {             
  		if( mysqli_errno() == 1062) {                
   // Duplicate key - Primary Key Violation                    
			$b["customer_id"] = $customer_id; 
			$b["ticketid"] =$ticket_id;   
			$b["productid"] = $productid; 
			$b["description"] = $des; 
			$b["status"] = '2';  
    		 } else {                 
   // For other errors   
			$b["customer_id"] = $customer_id; 
			$b["ticketid"] =$ticket_id;   
			$b["productid"] = $productid; 
			$b["description"] = $des;
			$b["serial_no"]=$serialno; 
			$b["status"] = "0"; 
			$b["error"]=$error_msg; 
			$b["warranty"]=$warranty;           
      		 }                     
		  }     
			return $b; 
		}
//////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////// Add Product /////////////////////////////////////////////////////
public function  Addproduct($customerid,$modelid,$productname,$purchasedate,$img,$entry_date,$serial_no,$warranty_end_date){
$result_job=mysqli_query($this->link,"select id,customer_id,mobile,customer_name from customer_master where id = '$customerid' ") or die(mysqli_error($this->link));
if (mysqli_num_rows($result_job)>0)
{
$row_cust=mysqli_fetch_assoc($result_job);	
$sql_chk="select customer_id from product_registered where serial_no='$serial_no'";
$rs_chk=mysqli_query($this->link,$sql_chk) or die(mysqli_error($this->link));
if(mysqli_num_rows($rs_chk)>0) {
	$b["customer_id"] = $customerid;  
	$b["customer_name"]=$row_cust["customer_name"];
	$b["mobile"]=$row_cust["mobile"];
	$b["status"]="duplicate";
	
	}else {

$res =mysqli_query($this->link,"insert into product_registered set  customer_id='$customerid',product_id='$productname',model_id='$modelid', purchase_date= '$purchasedate' ,img='$img', entry_by='c_app',entry_date='$entry_date',serial_no='$serial_no',warranty_end_date='$warranty_end_date' ")or die(mysqli_error($this->link));
if(mysqli_affected_rows($res)>=0){
	$b["customer_id"] = $customerid;  
	$b["modelid"] = $modelid; 
	$b["productname"]=$productname;
	$b["purchase_date"]=$purchasedate;
	$b["img"]=$img;
	$b["status"] = '1';  
  }   else {             
  if( mysqli_errno() == 1062) {                
   // Duplicate key - Primary Key Violation                 
   
	$b["customer_id"] = $customerid;   
	$b["modelid"] = $modelid; 
	$b["productname"]=$productname;
	$b["purchase_date"]=$purchasedate;
	$b["img"]=$img;
	$b["status"] = '1';  
     } else {                 
   // For other errors                 
  
	$b["customer_id"] = $customerid;  
	$b["modelid"] = $modelid; 
	$b["productname"]=$productname;
	$b["purchase_date"]=$purchasedate;
	$b["img"]=$img; 
	$b["status"] = '0';             
       }                     
  } }    
	return $b; 
	}
}
////////////////////////////////////////////////////////////////////////////////////////////
 
public function Comp_Eng_attendence_new($longitu, $latitu, $atten_date, $atten_status, $location, $id,$eng_img){
 	$a=array();
	$data= explode(" ",$atten_date);
	
$chk_atten=mysqli_query($this->link,"select * from complete_eng_attendance where atten_date='$data[0]' and attendance_status='$atten_status' and updated_by='$id'")or die(mysqli_error($this->link));
if(mysqli_num_rows($chk_atten)==0){

$result=mysqli_query($this->link,"insert into complete_eng_attendance set longitude='$longitu',latitude='$latitu',attendance_date='$atten_date',atten_date='$atten_date',attendance_status='$atten_status',location='$location',eng_img='$eng_img',updated_by='$id'")or die(mysqli_error($this->link));
}
if($result) {  
   $a["status"]='1';
   $a["id"]=$id;        
return $a;         
} else {             
if( mysqli_errno() == 1062) {                
 // Duplicate key - Primary Key Violation   
 $a["status"]='1';
   $a["id"]=$id;               
 return $a;             
 } else {                 
 // For other errors 
 $a["status"]='0';
   $a["id"]=$id;                 
 return $a;             
 }                     
 }

}


//////////////////// Update Customer Master /////////////////////////////////////////////////////////////////////////////////
 public function  Custinfo($id,$customer_id,$name,$mobile,$address,$city,$email,$stateid){
$result = mysqli_query($this->link,"update customer_master set customer_name='$name',mobile='$mobile', address1='$address',cityid='$city',email='$email' ,stateid = '$stateid', update_by='c_app'  where customer_id='$customer_id'  or id ='$id' ")or die(mysqli_error($this->link));
if(mysqli_affected_rows()>=0){
	$b["id"] = $id; 	 
	$b["customer_id"] = $customer_id; 
	$b["customer_name"]=$name;
	$b["mobile"]=$mobile;   
	$b["address1"]=$address;
	$e= mysqli_fetch_array(mysqli_query($this->link,"SELECT city FROM  	city_master WHERE cityid = '$city'"));
	$b["city"] = $e["city"];
	$b["cityid"]=$city;  
	$b["email"]=$email;
	$b["stateid"]=$stateid;  
	$f= mysqli_fetch_array(mysqli_query($this->link,"SELECT state FROM  state_master WHERE stateid = '$b[stateid]' "));
	$b["state"] = $f["state"];
	$b["status"] = '1';  
  } 
  else {             
  if( mysqli_errno() == 1062) {                
   // Duplicate key - Primary Key Violation                 
   
	$b["id"] = $id; 	 
	$b["customer_id"] = $customer_id; 
	$b["customer_name"]=$name;
	$b["mobile"]=$mobile;   
	$b["address1"]=$address;
	$b["cityid"]=$city;  
	$b["email"]=$email;
	$b["stateid"]=$stateid;  
	$f= mysqli_fetch_array(mysqli_query($this->link,"SELECT state FROM  state_master WHERE stateid = '$b[stateid]' "));
	$b["state"] = $f["state"];                 
     } else {                 
   // For other errors                 
  
	$b["id"] = $id; 	 
	$b["customer_id"] = $customer_id; 
	$b["customer_name"]=$name;
	$b["mobile"]=$mobile;   
	$b["address1"]=$address;
	$b["cityid"]=$city;  
	$b["email"]=$email; 
	$b["stateid"]=$stateid;  
	$f= mysqli_fetch_array(mysqli_query($this->link,"SELECT state FROM  state_master WHERE stateid = '$b[stateid]' "));
	$b["state"] = $f["state"];
	$b["status"] = '0';              
       }                     
  }     
	return $b; 
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

 
 ////////////////// Update Complaint Master //////////////////
 public function storeUser($job_no,$status,$close_date,$fault1,$repair1,$part_consume1,$reason,$company_id,$divison_id,$range_id,$product_id,$model_id,$remark,$dop,$s_c_m,$path_img,$path_img1,$path_img2,$ei) { 
 $cls_dt=explode(" ",$close_date); 
$faultcode = mysqli_fetch_array(mysqli_query($this->link,"SELECT id FROM defect_detected_master where details='$fault1'"));
$repaircode = mysqli_fetch_array(mysqli_query($this->link,"SELECT id FROM solution_master where solution='$repair1'"));

$Call_status =mysqli_fetch_array(mysqli_query($this->link,"SELECT status FROM complaints_master where job_no='$job_no'"));
if($Call_status[status]=='2'){

if ($status == '2') {
$Pendinreason = mysqli_fetch_array(mysqli_query($this->link,"SELECT reasonid FROM reason_master where reason='$reason'"));
} else {
$closereason = mysqli_fetch_array(mysqli_query($this->link,"SELECT reasonid FROM close_reason_master where reason='$reason'"));
}
$result = mysqli_query($this->link,"update complaints_master set status='$status',close_date='$cls_dt[0]',close_time='$cls_dt[1]',modelid='$model_id',remark='$remark',app_part_consume='$part_consume1',defectdetect='$faultcode[id]',solutiongiven='$repaircode[id]',pending_remark='$Pendinreason[0]',closed_remark='$closereason[0]',img_path1='$path_img',img_path2='$path_img1',img_path3='$path_img2',dop='$dop',company_id='$company_id',S_C_M='$s_c_m',range_id='$range_id',division_id='$divison_id',productid='$product_id' where job_no='$job_no'")or die(mysqli_error($this->link));
if ($result) {             
return true;         
} else {             
if( mysqli_errno() == 1062) {                
 // Duplicate key - Primary Key Violation                 
 return true;             
 } else {                 
 // For other errors                 
 return false;             
 }                     
 }
}
else
{
return true;   
}

     
 }   
 ////////////////////////
 public function checkJobs($ei,$job_no)
{
$sel=mysqli_fetch_array(mysqli_query($this->link,"SELECT engg_assign, job_no FROM complaints_master where job_no='$job_no'"));

return $sel;
}
 /////////////////////// Update call History /////////////////////
 public function callHistory($job_no,$action,$status,$update_date,$ei) {
	 $name = mysqli_fetch_array(mysqli_query($this->link,"select username from user_master where userid='$ei'"));	 
	 if ($status == "Closed") {
	 $result= mysqli_query($this->link,"insert into call_history set job_no='$job_no',activity='$action',status='Technical Closed',outcome='$status',updated_by='$name[username]',assigned_to='$name[username]',update_date='$update_date'")or die(mysqli_error($this->link));
	 } else {
 $result= mysqli_query($this->link,"insert into call_history set job_no='$job_no',activity='$action',status='$status',outcome='$status',updated_by='$name[username]',assigned_to='$name[username]',update_date='$update_date'")or die(mysqli_error($this->link));
	 }
 if ($result) {             
return true;         
} else {             
if( mysqli_errno() == 1062) {                
 // Duplicate key - Primary Key Violation                 
 return true;             
 } else {                 
 // For other errors                 
 return false;             
 }                     
 }     
 }
 
}