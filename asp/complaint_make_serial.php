<?php

require_once("../includes/config.php");
////get access ASP details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);

////get access brand details

$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
$access_asp = getAccessASP($_SESSION['asc_code'],$link1);
$tY=date("Y");
$tM=date("m");
	$td=date("d");
$val_y=substr($tY,2,2);
$job_dt=$val_y."".$tM."".$td;
////////////// update by jitender on dec 11 for repair and bounce type call for claim process ////////////////////////////////////////
if($_REQUEST['mobileno']){
$srch_criteria = "where ( mobile = '".$_REQUEST['mobileno']."' or  alt_mobile  = '".$_REQUEST['mobileno']."')";
}else if($_REQUEST['email_id']){
$srch_criteria = "where email = '".$_REQUEST['email_id']."'";
}else if($_REQUEST['customer_id']){
$srch_criteria = "where customer_id = '".$_REQUEST['customer_id']."'";
}else{
$srch_criteria="";
}
$sql_cust	= mysqli_query($link1, "select  *  from customer_master   ".$srch_criteria."   order by id desc");

$row_customer=mysqli_fetch_array($sql_cust);



////// final submit form ////

if($_POST['savejob']=='Save')
{	
	//barCheck($link1);
	@extract($_POST);
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg="";
	
	
	
$job_find="select job_no  from jobsheet_data where imei='".$imei_serial1."' and status not in ('6','10','12','11') order by job_id desc";
$result_find=mysqli_query($link1,$job_find);
$result_find_count=mysqli_num_rows($result_find);	
$result_find_arr=mysqli_fetch_array($result_find);	
$job_no_d=$result_find_arr[0];
if($result_find_count > 0){
	$flag = false;
	$error_msg = "Error details1: There is already a call open on this serial." . $result_find_arr['job_no'] . ".";
}
	
	
	

	//////////////////////////////customer details//////////////////////////////////////////
	$usr_srch="select mobile from customer_master where mobile='".$phone1."'";
	$result_usr=mysqli_query($link1,$usr_srch);
	$arr_usr=mysqli_fetch_array($result_usr);	
	if ($custo_id==""){	
		// also save customer details \\ 	
		$sel_uid="select max(max_id) from customer_master";
		$res_uid=mysqli_query($link1,$sel_uid);
		$arr_result2=mysqli_fetch_array($res_uid);
		$code_id=$arr_result2[0]+1;
		$pad=str_pad($code_id,5,"0",STR_PAD_LEFT);
		$customer_id="C".$stCode.$pad;

		$usr_add="insert into customer_master set  customer_id='".$customer_id."', customer_name='".ucwords($customer_name)."', address1='".ucwords($address)."', pincode='".$pincode."', cityid='".$locationcity."', stateid='".$locationstate."', email='".$email."',  phone='".$res_no."', mobile='".$phone1."', alt_mobile='".$phone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',max_id='".$code_id."',landmark='".ucwords($landmark)."',type='".$customer_type."',reg_name='".ucwords($reg_name)."',gst_no='".strtoupper($gst_no)."',mrg_date='".$mrg_date."',dob_date='".$dob_date."',custarea='".$locationarea."'";
		$res_add=mysqli_query($link1,$usr_add); 



		$cust_id=$customer_id;
	}else{
		$usr_add="update customer_master set customer_name='".ucwords($customer_name)."', address1='".ucwords($address)."', pincode='".$pincode."', cityid='".$locationcity."', stateid='".$locationstate."', email='".$email."',  phone='".$res_no."', alt_mobile='".$phone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',landmark='".ucwords($landmark)."',type='".$customer_type."',reg_name='".ucwords($reg_name)."',gst_no='".strtoupper($gst_no)."',mrg_date='".$mrg_date."',dob_date='".$dob_date."',custarea='".$locationarea."'  where   customer_id='".$custo_id."'";
		$res_add=mysqli_query($link1,$usr_add); 
		$cust_id=$custo_id;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// pick max count of job
	$cust_st = explode("~",getAnyDetails($locationstate,"zoneid,statecode","stateid","state_master",$link1)); 
	$statearea = 	$cust_st[0];
	$stcode=$cust_st[1];

	$res_jobcount = mysqli_query($link1,"SELECT job_counter  from date_counter  where  job_date ='".$today."'");
	if(mysqli_num_rows($res_jobcount)>0){
		$row_jobcount = mysqli_fetch_assoc($res_jobcount);
		///// make job sequence
		$nextjobno = $row_jobcount['job_counter'] + 1;
		$jobno = $stcode."".$job_dt."".str_pad($nextjobno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$res_upd = mysqli_query($link1,"UPDATE date_counter set job_counter='".$nextjobno."' where job_date ='".$today."'");
	}else {
		$nextjobno=1;
		$job_dt=date("ymd");
		$jobno=$stcode."".$job_dt."".str_pad($nextjobno,4,0,STR_PAD_LEFT);
		$res_upd = mysqli_query($link1,"insert into date_counter set job_counter='".$nextjobno."',job_date ='".$today."'");
	}

	//// check if query is not executed
	if (!$res_upd) {
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}

	///// entry in job sheet data
	$modelsplit = explode("~",$modelid);
	if(is_array($voc2)){
		$array_voc2 = implode(",", $voc2);
	}
	///// model details
	if(is_array($acc_present)){
		$array_accprest = implode(",", $acc_present);
	}

	$mappin="select location_code,area_type  from location_pincode_access where pincode='".$pincode."'";
	$result_pin=mysqli_query($link1,$mappin);
	$arr_pin=mysqli_fetch_array($result_pin);
	############################################
	############################################
	$img_file = "";
	$vid_file = "";
	### image processor by Hemant - feb'2024 ###
	if(is_uploaded_file($_FILES['handset_img']['tmp_name']))
	{
		$ext = strtolower(pathinfo($_FILES["handset_img"]["name"], PATHINFO_EXTENSION));
		$allowed = array("png","jpg","jpeg","pdf");
		if(!in_array($ext, $allowed))
		{
			$flag = false;
			$error_msg = "Please upload a valid png, jpg or pdf file!";
		}
		else if($_FILES["handset_img"]["size"] > 3145728)
		{
			$flag = false;
			$error_msg = "Image/Pdf file size should be under 3 MB!";
		}
		else
		{
			$my = "../uploads/job/images/".date("Y-M");
			if(!is_dir($my)){
				$s = mkdir($my, 0777, 'R');
			}
			$file_name =$_FILES['handset_img']['name'];
			$file_tmp =$_FILES['handset_img']['tmp_name'];
			$targetPath = $my."/".time()."_".cleanFileName($file_name, 60);
			$img_upload = move_uploaded_file($file_tmp,$targetPath);
			if($img_upload)
			{
				$img_file = $targetPath;				
			}
		}
	}
	############################################
	### video processor by Hemant - feb'2024 ###
	if(is_uploaded_file($_FILES['att_video']['tmp_name']))
	{
		$ext = strtolower(pathinfo($_FILES["att_video"]["name"], PATHINFO_EXTENSION));
		$allowed = array("mp4");
		if(!in_array($ext, $allowed))
		{
			$flag = false;
			$error_msg = "Please upload a valid mp4 file!";
		}
		else if($_FILES["att_video"]["size"] > 10485760)
		{
			$flag = false;
			$error_msg = "Video file size should be under 10 MB!";
		}
		else
		{
			$my = "../uploads/job/videos/".date("Y-M");
			if(!is_dir($my)){
				$s = mkdir($my, 0777, 'R');
			}
			$file_name =$_FILES['att_video']['name'];
			$file_tmp =$_FILES['att_video']['tmp_name'];
			$targetPath = $my."/".time()."_".cleanFileName($file_name, 60);
			$vid_upload = move_uploaded_file($file_tmp,$targetPath);
			if($vid_upload)
			{
				$vid_file = $targetPath;
			}
		}
	}
	############################################
	mysqli_query($link1,"INSERT INTO image_upload_details SET job_no ='".$jobno."', img_url='".$img_file."', vid_url='".$vid_file."', upload_date='".$today."', location_code='".$_SESSION['asc_code']."'");
	if($img_file!="")
	{
		mysqli_query($link1, "INSERT INTO upload_history SET doc_no='".$jobno."', process='JOB CREATE', file_url='".$img_file."', create_by='".$_SESSION['userid']."', create_dt='".$datetime."', create_ip='".$ip."'");
	}
	if($vid_file!="")
	{
		mysqli_query($link1, "INSERT INTO upload_history SET doc_no='".$jobno."', process='JOB CREATE', file_url='".$vid_file."', create_by='".$_SESSION['userid']."', create_dt='".$datetime."', create_ip='".$ip."'");
	}	
	############################################
	############################################

	/*if($_SESSION['id_type']=='ASP'){
		$st_asp=$_SESSION['asc_code'];
	}else{
		$st_asp=$rep_location;
	}*/

		//$rep_location1 = str_replace(" ","",$rep_location);
	$st_asp1 = mysqli_fetch_array(mysqli_query($link1,"select location_code,device_token from locationuser_master where userloginid='".$rep_location."'"));
if($st_asp1['location_code']!=''){
	$st_asp=$st_asp1['location_code'];
	//$eng_id1= $rep_location;
}else{
	$st_asp=$rep_location;
	//$eng_id1= "";
}
$eng_id1=$assign_eng;	

	if($call_for=='Reinstallation'){
		$st_wart="VOID";
	}else{
		$st_wart=$warranty_status;
	}

	/*	 $unit_part=mysqli_query($link1,"select partcode from partcode_master where   model_id Like '%".$modelsplit[0]."%' and status='1' and  	part_category='UNIT'" )or die(mysqli_error($link1)); 
			$row_part = mysqli_fetch_array($unit_part);
			if($row_part['partcode']==""){
			 $flag = false;
			 $error_msg = "Partcode Not found in partcode master please check : " .$old_s['model_id']. ".";
			}*/



	$prodname = getAnyDetails($product_name,"product_name","product_id","product_master",$link1); 

	$vocname = getAnyDetails($voc1,"voc_desc","voc_code","voc_master",$link1); 
	$area = getAnyDetails($statearea,"zonename","zoneid","zone_master",$link1); 

	$scm=rand(111111,999999);

	$hpcode="HC".$scm;

	$st="status='2', sub_status='2'";
//$sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$modelsplit[0]."', model='".$modelsplit[1]."', imei='".$imei_serial1."', open_date='".$today."', open_time='".$currtime."', warranty_status='".$st_wart."',warranty_days='".$wsd."', dop='".$pop_date."', dname='".ucwords($dealer_name)."', inv_no='".$invoice_no."',  call_type='".$call_type."', call_for='".$call_for."', customer_name='".ucwords($customer_name)."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".ucwords($address)."', cust_problem='".$voc1."', cust_problem2='".$array_voc2."', cust_problem3='".$voc3."', phy_cond='".$physical_cond."', created_by='".$_SESSION['userid']."', remark='".ucwords($remark)."', ".$st." ,ip='".$ip."',current_location='".$st_asp."',customer_id='".$cust_id."',entity_type='".$entity_type."',acc_rec='".$array_accprest."',area_type='".$arr_pin['area_type']."',pen_status='2',area='".$area."',partcode='".$row_part['partcode']."',h_code='".$hpcode."',custarea='".$locationarea."',installation_date ='".$install_date."',eng_id='".$eng_id1."'";
	$sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$modelsplit[0]."', model='".$modelsplit[1]."', imei='".$imei_serial1."', open_date='".$today."', open_time='".$currtime."', warranty_status='".$st_wart."', dop='".$pop_date."', dname='".ucwords($dealer_name)."', inv_no='".$invoice_no."',  call_type='".$call_type."', call_for='".$call_for."', customer_name='".ucwords($customer_name)."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".ucwords($address)."', cust_problem='".$voc1."', cust_problem2='".$array_voc2."', cust_problem3='".$voc3."', phy_cond='".$physical_cond."', created_by='".$_SESSION['userid']."', remark='".ucwords($remark)."', ".$st." ,ip='".$ip."',current_location='".$st_asp."',customer_id='".$cust_id."',entity_type='".$entity_type."',acc_rec='".$array_accprest."',area_type='".$arr_pin['area_type']."',pen_status='2',area='".$area."',partcode='".$row_part['partcode']."',h_code='".$hpcode."',custarea='".$locationarea."',installation_date ='".$install_date."',eng_id='".$eng_id1."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',warranty_days='".$wp_days."'";
	$res_inst = mysqli_query($link1,$sql_inst);

	//// check if query is not executed

	if (!$res_inst) {

		$flag = false;

		$error_msg = "Error jobsheet : " . mysqli_error($link1) . ".";

	}
//print_r('dddddddd');exit;






	//// Product Register \\\\\
	//echo "select * from product_registered where serial_no='$serial_no'<br />";


	$usr_add3="INSERT INTO product_registered set serial_no='".$imei_serial1."', customer_id='".$cust_id."', product_id='".$product_name."', model_id='".$modelsplit[0]."', purchase_date='".$pop_date."', installation_date ='".$install_date."', warranty_end_date='".$warraty_date."', status='1',mobile_no='".$phone1."',brand_id='".$brand."',amc_no='".$amc_no."',amc_end_date='".$amc_exp_date."',job_no='".$jobno."'";
	$res_add3=mysqli_query($link1,$usr_add3);


	$job_sql=mysqli_query($link1,"SELECT max(job_count) as jobcount FROM jobsheet_data  where address='".ucwords($address)."' and open_date='".$today."' ");
	$job_det = mysqli_fetch_assoc($job_sql);

	$maxcount=$job_det['jobcount']+1;

	$jobresult=mysqli_query($link1,"update  jobsheet_data set job_count='".$maxcount."' where address='".ucwords($address)."' and open_date='".$today."' ");


	///// entry in call/job  history

	$flag = callHistory($jobno,$_SESSION['asc_code'],"1","Complaint Login","Complaint Login",$_SESSION['userid'],$warranty_status,ucwords($remark),"","",$ip,$link1,$flag);
	
	$flag = callHistory($jobno,$_SESSION['asc_code'],"2","Complaint Auto Assign","Complaint Assign",$_SESSION['userid'],$warranty_status,ucwords($remark),"","",$ip,$link1,$flag);

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed

	if ($flag) {

		
		##### SEND SMS
		if($phone1!=''){
			
		$cust_name = cleanData($customer_name);
	   
	   $sms_msg="Dear".ucwords($cust_name).",Your  SR No ".$jobno." for product model ".$modelsplit[1]." has been Logged in Successfully On Dated ".$today.". Sukam";
	   
	   $template_id="75789";
	   
	  			 //$sms_resp = sendSMSByURL($phone1,$sms_msg,$template_id);
				 if($sms_resp){
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$jobno."', ref_type='COMPLAINT LOGIN',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}else{
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$jobno."', ref_type='COMPLAINT LOGIN',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}
	   
		}
		#### END  SMS
		mysqli_commit($link1);

		////// return message

		$msg="You have successfully created a Job like <span class='red_small'> ".$jobno." </span> and Customer id is <span class='red_small'> ".$cust_id." </span>";

		$cflag="success";

		$cmsg="Success";

	} else {

		mysqli_rollback($link1);

		$cflag="danger";

		$cmsg="Failed";

		$msg = "Request could not be processed. Please try again. ".$error_msg;

	} 


	$loc_name = mysqli_query($link1,"SELECT  	locationname , contactno1  from location_master where location_code='".$st_asp."'");

	$row_loc = mysqli_fetch_assoc($loc_name);


	///// move to parent page
	$smk= base64_encode($msg);
	if($phone1!=''){
		// $sms_msg="Dear Customer ,your complaint has been registered and the reference no is . ".$jobno." and shall be attend  within 48 hours.If you are satisfied with our service, please share this Happy code ".$hpcode." with service executive. Our service executive ".$row_loc['locationname']." Mobile No ".$row_loc['contactno1']." shall attend your complaint shortly ";


		$sms_msg="Dear Customer, Your Call has been registered with Su-kam Job No. ".$jobno." . We will communicate the Service Engineer details shortly.";
	}
	$cust_name = cleanData($customer_name);
	$cust_addrs = cleanData($address);

	$sms_asp="To ASP ,Com No.:  ".$jobno."  Dated:  ".$today."  Customer Name: ".ucwords($cust_name)." Address:  ".ucwords($cust_addrs).", Mb. No:".$phone1." Alt. No:".$phone2." Product Name: ".$prodname." Date of Purchase:".$pop_date." Problem:".$vocname."";

	mysqli_close($link1);

	header("location:complaint_save_back.php?msg=".$smk."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".base64_encode($sms_msg)."&mobileno=".$phone1."&customer_id=".$customer_id."&imei_serial=".$imei_serial1."&email_id=".$email."&sms_asp=".base64_encode($sms_asp)."&contactno1=".$row_loc['contactno1']."&status=1");
	//exit;

}

////// get model details if post model id from previous page

if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

	if($_REQUEST['modelid']){

		$modelexpl = explode("~",$_REQUEST['modelid']);

		$model_code = $modelexpl[0];

		$model_name = $modelexpl[1];
		$model_wp = $modelexpl[2];

		$model_det = explode("~",getAnyDetails($modelexpl[0],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp,dwp","model_id","model_master",$link1));

	}else{



	}

}
//echo "SELECT * FROM product_registered  where serial_no='".$_REQUEST['imei_serial']."'";

$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where id='".$_REQUEST['id']."'"));



//	echo "SELECT current_location FROM jobsheet_data  where job_no='".$product_det['job_no']."'";
//echo "SELECT current_location FROM jobsheet_data where customer_id= '".$row_customer['customer_id']."'";
if($product_det['job_no']){
	//echo "SELECT current_location, cust_problem, cust_problem2 FROM jobsheet_data where job_no= '".$product_det['job_no']."'";
	$job_sql_t=mysqli_query($link1,"SELECT current_location, cust_problem, cust_problem2 FROM jobsheet_data where job_no= '".$product_det['job_no']."'");
	$job_det_t = mysqli_fetch_assoc($job_sql_t);
}





////// make voc array

$voc_arr = array();

$res_voc = mysqli_query($link1,"select voc_code, voc_desc from voc_master where 1");

while($row_voc = mysqli_fetch_assoc($res_voc)){

	$voc_arr[$row_voc['voc_code']] = $row_voc['voc_desc'];

}

//// calculate warranty
$model_det3 = explode("~",getAnyDetails($product_det['model_id'],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp","model_id","model_master",$link1));

if($product_det['purchase_date']!='' && $product_det['purchase_date']!='0000-00-00'){

	$days_diff = daysDifference($today,$product_det['purchase_date']);

	if($days_diff <= $model_det3[6]){

		$ws = "IN";

	}else{

		$ws = "OUT";

	}

}else{
	$ws = "";
}

?>

<!DOCTYPE html>

<html>

<head>

 <meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">

 <title><?=siteTitle?></title>

 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">

 <script src="../js/jquery.js"></script>

 <link href="../css/font-awesome.min.css" rel="stylesheet">

 <link href="../css/abc.css" rel="stylesheet">
<script src="../js/ajax.js"></script>
 <script src="../js/bootstrap.min.js"></script>

 <link href="../css/abc2.css" rel="stylesheet">

 <link rel="stylesheet" href="../css/bootstrap.min.css">

 <script>

	$(document).ready(function(){

        $("#frm1").validate();

    });

	<?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){?>

    $(document).ready(function () {

	  $('#pop_date').attr('readonly', true);

	});

	<?php }else{?>

	$(document).ready(function () {

		$('#pop_date').datepicker({

			format: "yyyy-mm-dd",

			endDate: "<?=$today?>",

			todayHighlight: false,

			autoclose: false,

		}).on('changeDate', function(ev){

    		

			getWarranty();

		});
		});

	<?php }?>

 </script>

 <script language="javascript" type="text/javascript">
 /////////// function to get city on the basis of state

 function get_citydiv(){

	  var name=$('#locationstate').val();

	  $.ajax({

	    type:'post',


		url:'../includes/getAzaxFields.php',

		data:{stateuser:name},

		success:function(data){

	    	$('#citydiv').html(data);
			//$('#pincode').val("");
	    }

	  });

 }

 /////////////




 /////////// function to get model on the basis of brand

  $(document).ready(function(){

	$('#brand').change(function(){

	  var brandid=$('#brand').val();
	   var product_name=document.getElementById("product_name").value;
	  // alert(product_name);
	 
	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{brandModel:brandid,product_id:product_name},

		success:function(data){

	    $('#modeldiv').html(data);

	    }

	  });

    });
$('#product_name').change(function(){

	
	  document.getElementById("brand").value="";
	   var product_name=document.getElementById("product_name").value;
	  // alert(product_name);
	 	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{vocproduct:product_name},

		success:function(data){

			$('#vocdiv').html(data);
			getmultivoc();
	    }

	  });
	

    });
  });
  
function addDays(date, days) {
  const newDate = new Date(date);
  newDate.setDate(newDate.getDate() + days);
  //var date = new Date('YYYY-mm-dd',newDate);
  return newDate;
}
	 
function getWarrantySlab(wslabcode){
	var mfg_exdate = ""; 
	if(wslabcode=0){mfg_exdate=183;}else if(wslabcode=1){mfg_exdate=365;}else if(wslabcode=2){mfg_exdate=457;}else if(wslabcode=3){mfg_exdate=547;}else if(wslabcode=4){mfg_exdate=730;}else if(wslabcode=5){mfg_exdate=914;}else if(wslabcode=6){mfg_exdate=1095;}else if(wslabcode=7){mfg_exdate=1278;}else if(wslabcode=8){mfg_exdate=1460;}else if(wslabcode=9){mfg_exdate=1825;}else if(wslabcode='N'){mfg_exdate=0;}else if(wslabcode='W'){mfg_exdate=0;}else if(wslabcode='A'){mfg_exdate=638;}else if(wslabcode='B'){mfg_exdate=225;}else if(wslabcode='C'){mfg_exdate=1521;}else if(wslabcode='D'){mfg_exdate=241;}else if(wslabcode='E'){mfg_exdate=200;}else if(wslabcode='F'){mfg_exdate=250;}else if(wslabcode='G'){mfg_exdate=2190;}else if(wslabcode='H'){mfg_exdate=3285;}else if(wslabcode='I'){mfg_exdate=2737;}else if(wslabcode='J'){mfg_exdate=1187;}else if(wslabcode='P'){mfg_exdate=1825;}else if(wslabcode='X'){mfg_exdate=2650;}else if(wslabcode='Y'){mfg_exdate=272;}else if(wslabcode='Z'){mfg_exdate=300;}
		return mfg_exdate;
} 
  
function getSerialdeatils(){
	var mm_serial = document.getElementById('imei_serial1').value;
    ////// check serial no.
    if(mm_serial!="")
	{
        $.ajax({
	        type:'post',
		    url:'../includes/get_validate_serial.php',
		    data:{serialno:mm_serial},
		    success:function(data){
                var resp = data.split("^");
	            document.getElementById("mfd").value=resp[10];
                document.getElementById("mfd_ex").value=resp[11];
                document.getElementById("wp_days").value=resp[2];
                $('#productdiv').html(resp[6]+'<input type="hidden" name="product_name" id="product_name" value="'+resp[4]+'"/>');
	            $('#selectedbrand').html(resp[7]+'<input type="hidden" name="brand" id="brand" value="'+resp[5]+'"/>');
	            $('#modeldiv').html(resp[1]+'<input type="hidden" name="modelid" id="modelid" value="'+resp[0]+'"/><input type="hidden" name="modelwp" id="modelwp" value="'+resp[2]+'~'+resp[3]+'"/>');
		        if(resp[9]!=""){
	                document.getElementById('pop_date').value=resp[9];
                    getdate4();
	            }
            }
	    });
    }
}
function getmultivoc(){
	
	    var product_name=document.getElementById("product_name").value;
	



	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{vocproductmulti:product_name},
		success:function(data){
		
	if(data!=""){
	    $('#mutlivoc').html(data);
		reCallSelect();
		}
	    }
	  });

	};
function getdate4() {


var start_date = new Date($('#pop_date').val());

  var model_wp =  document.getElementById('modelwp').value;
   var customer_type =  document.getElementById('customer_type').value;
 var modelsplit=model_wp.split("~");
 
  if(customer_type =='Dealer'){
  var wday = parseInt(modelsplit[1]);

}else{
var wday= parseInt(modelsplit[0]);
}


  var end_date = new Date(start_date);


  end_date.setDate(start_date.getDate() +  parseInt(wday) - parseInt(1));  
   
  $datecc=end_date.getFullYear() + '-' + ("0" + (end_date.getMonth() + 1)).slice(-2) + '-' + ("0" + end_date.getDate()).slice(-2);
                     
  $("#warraty_date").val($datecc);
          // $('#warranty_date').val(date);
   // document.getElementById('warranty_date').value = someFormattedDate;
   getWarranty();
}
  /////////// function to check DOA is eligible or not

  

  /////////// function to get model on the basis of brand

  function getAccessory(){

	//$('#modelid').change(function(){

	  var model_id=$('#modelid').val();

	  var modelcode = model_id.split("~");
	  
	 

	//  var calltype=$('#call_type').val();
	  
	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{model:modelcode[0]},

		success:function(data){
		

	    $('#accdiv').html(data);

		$('#example-multiple-selected2').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

	    });

	    }

	  });

    //});

  }

//// check warrantty on the basis of els status and pop selection

function getWarranty(){

	var sel_pop = $('#pop_date').val();

  var model_wp =  document.getElementById('modelwp').value;
 
  var customer_type =  document.getElementById('customer_type').value;

 
 var post_wsd=model_wp.split("~");
 
 if(customer_type =='Dealer'){

var tat_warrty = parseInt(post_wsd[1]);
}else{
var tat_warrty = parseInt(post_wsd[0]);
}

	

	////// check out warranty flag of this model

	if("<?=$model_det[5]?>" == "Y"){

		document.getElementById("warranty_status").value = "OUT";

		document.getElementById("errmsg").innerHTML = "You are making a job for OUT warranty model. <br/>";

	}else{

		/////calculate days

		var diffDays = date_difference("<?=$today?>", sel_pop);

		///// calculate warranty
	//alert(diffDays+""+post_wsd);
		if(diffDays <= tat_warrty){

			document.getElementById("warranty_status").value = "IN";
				document.getElementById("warranty_status1").value = "IN";
				

		
		}else{

			document.getElementById("warranty_status").value = "OUT";
				document.getElementById("warranty_status1").value = "OUT";

		}

	}

}

	 function validateVid(nam,ind)
	 {
		 var err_msg="";
		 document.getElementById("errmsg"+ind).innerHTML = err_msg;
		 var img1=document.getElementById(nam).value;
		 var file=document.getElementById(nam).files[0];
		 var t = file.type.split('/').pop().toLowerCase();
		 if(t!="mp4")
		 {
			 err_msg = "<strong>Please select a valid mp4 file!<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 else if(file.size > 10240000) //10 MB
		 {
			 err_msg = "<strong>File size should be under 10 MB.<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 return true;
	 }
	 function validateImage(nam,ind)
	 {
		 var err_msg="";
		 document.getElementById("errmsg"+ind).innerHTML = err_msg;
		 var img1=document.getElementById("handset_img").value;
		 var file = document.getElementById(nam).files[0];
		 var t = file.type.split('/').pop().toLowerCase();
		 if(t != "jpeg" && t != "jpg" && t != "png" && t != "pdf")
		 {
			 err_msg = "<strong>Please select a valid jpg, png or pdf file!<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 else if(file.size > 3072000)
		 {
			 err_msg = "<strong>File size should be under 3 MB.<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 return true;
	 }

$(document).ready(function () {
	$('#pop_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	
	$('#install_date').datepicker({
		format: "yyyy-mm-dd",
		startDate: "<?=$today?>",
		endDate: "<?=$today?>",
		datesDisabled: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
		$('#dob_date').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	$('#mrg_date').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});


});

  function reinsallationfun(){

 var call_for=$('#call_for').val();
 //alert(call_for);
 
 if(call_for=="Reinstallation"){
 document.getElementById("warranty_status").value = "VOID";
 }else{
   document.getElementById("warranty_status").value= document.getElementById("warranty_status1").value ;
 }
 
 if (call_for=="Reinstallation" || call_for=="Installation" ){
 
 document.getElementById("vocdisplay").style.display = "none";
 }else{
  document.getElementById("vocdisplay").style.display="";
 
 }

}
//// date difference

	 function getmaploc(){
	
	
	  var pincode=$('#pincode').val();
	  var prd7=$('#product_name').val();
	  var brd7=$('#brand').val();
	   var sat7=$('#locationstate').val();

	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{RVLocpin:pincode,product7:prd7,brand7:brd7,state7:sat7},
		success:function(data){
		
		if(data!=""){
			//alert(data);
			$('#loc_pincode').html(data);
		}
	    }
	  });

	};
	
	function getmapbrand(){
	
	  var brand=$('#brand').val();
	  var curent_loc=$('#rep_location').val();
	
	
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandmap:brand,rep_location:curent_loc},
		success:function(data){
	if(data==0){
	    alert("This Location have no rights for This Brand");
		document.getElementById('brand').value = '';
	}else{}
	    }
	  });
	
	};
	
	function getmapproduct(){
	
	  var prod=$('#product_name').val();
	  var curent_loc=$('#rep_location').val();
	
	
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{productmap:prod,rep_location:curent_loc},
		success:function(data){
	if(data==0){
	     alert("This Location have no rights for This Product");
		 document.getElementById('product_name').value = '';
	}else{}
	    }
	  });
	
	};
	
	function getprdwisebrand(prd){

	  var access_brand = "<?=$access_brand;?>";

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{RVprdwisebrand:access_brand,prd:prd},

		success:function(data){

			if(data){

				 //alert(data + ' - 3');

				 $('#selectedbrand').html(data);

				 resetProdModel();

			}else{

			

			}

	    }

	  });

	};

	////// reset product and brand //////
	function resetProdModel(){
		var product_name = document.getElementById('product_name').value;
		var brandid = document.getElementById('brand').value;
		
		$.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{brandModel:brandid,product_id:product_name},
			success:function(data){
			
			$('#modeldiv').html(data);
			}
		});
	}
	
	function getmapinstate() {
	
	  var pincode=$('#pincode').val();
	 	//alert(pincode);
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{Locpinstate:pincode,cmLocSt:'1'},
		success:function(data){
			//alert(data + ' getmapins ');
			$('#loc_pincodestate').html(data);
			get_pincity();
			get_assign_location();
	    }
	  });
	
	
	
};
function get_assign_location() {
	
	  var pincode=$('#pincode').val();

	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{assignpincode:pincode},
		success:function(data){
	
	//alert(data);
		if(data!=""){
	    $('#loc_pincode').html(data);
			
		}
	    }
	  });
		
};	
	
	
function get_pincity() {
	
	  var pincode=$('#pincode').val();

	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{Locpincity:pincode,cmLocSt:'2'},
		success:function(data){
	
	//alert(data);
		if(data!=""){
	    $('#citydiv').html(data);
			get_pincityArea();
		}
	    }
	  });
		
};

function get_pincityArea() {
	
	  var pincode=$('#pincode').val();

	var cityId = $('#locationcity').val();
	//alert(cityId);
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{Locpinarea:pincode,cmLocSt:'3',city_id:cityId},
		success:function(data){
	
	//alert(data);
		if(data!=""){
	    $('#Areadiv').html(data);
		}
	    }
	  });

};

function get_cityArea() {
	
	  var locationcity=$('#locationcity').val();
	var enterpin = $('#pincode').val();
	//alert(locationcity);
	
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{Locpinareacity:locationcity,enter_pin:enterpin},
		success:function(data){
			//alert(data);
			$('#Areadiv').html(data);
	    }
	  });

};

function findpicode(){
  var locationarea=$('#locationarea').val();
 
 var post_area=locationarea.split("~");
 
 document.getElementById("pincode").value=post_area[1];
 //getmaploc();
}

function date_difference(enddate,startdate){

	var end_date = (enddate).split("-");

	var start_date = (startdate).split("-");	

	var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds

	var firstDate = new Date(start_date[0], start_date[1], start_date[2]);

	var secondDate = new Date(end_date[0], end_date[1], end_date[2]);

	/////calculate days

	var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

	return diffDays;

}

 $(document).ready(function() {

	$('#example-multiple-selected1').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

            //enableFiltering: true

	});

	$('#example-multiple-selected2').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

            //enableFiltering: true

	});

 });
 function reCallSelect(){
 	$('#example-multiple-selected1').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

            //enableFiltering: true

	});

 }
  </script>

 <script type="text/javascript" src="../js/jquery.validate.js"></script>

 <script type="text/javascript" src="../js/common_js.js"></script>

  <!-- Include Date Picker -->

 <link rel="stylesheet" href="../css/datepicker.css">

 <script src="../js/bootstrap-datepicker.js"></script>

 <!-- Include multiselect -->

 <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>

 <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>

 <style type="text/css">

 .custom_label {

	 text-align:left;

	 vertical-align:middle

 }

 </style>
 <?php if($row_customer['pincode']!="" && $product_det['job_no']=="" ){?>
<body onLoad="getmaploc(<?=$row_customer['pincode']?>)" >
<?php } else {?>
<body>

<?php }?>
<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnavemp2.php");





    ?>
    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-id-badge"></i> Enter Complaint Details</h2>

      <?php if($model_det[5]=="Y"){ ?>
      <h4 align="center" style="color:#F00">You are making a Complaint for OUT warranty model .</h4> 
      <?php } ?>

		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data" autocomplete="off" onsubmit="return really('save')" action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>

              <div class="panel-body">

              	  <div class="form-group">
              	    <div class="col-md-6">
              	      <label class="col-md-6 custom_label">Customer Category <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <select name="customer_type" id="customer_type" class="form-control required" required>
                         
                          <?php



				$cus_query="SELECT * FROM customer_type where status = '1' order by customer_type";



				$check_cust=mysqli_query($link1,$cus_query);


if($row_customer['type']==""){?>
 <option value="">--Please Select--</option>
			<?php	while($br_cust = mysqli_fetch_array($check_cust)){



				?>
                          <option value="<?=$br_cust['customer_type']?>"<?php if($row_customer['type']==$br_cust['customer_type']){ echo "selected";}?>><?php echo $br_cust['customer_type']?></option>
                          <?php }} else{?>
						   <option value="<?=$row_customer['type']?>"><?php echo $row_customer['type']?></option><?php }?>
                        </select>
                      </div>
           	        </div>
              	    <div class="col-md-6"><label class="col-md-6 custom_label">Pin Code<span class="red_small">*</span></label>

                      <div class="col-md-6">
  <input name="pincode" type="text" class="digits form-control"  onKeyup="getmapinstate(this.value)" maxlength="6" id="pincode" value="<?=$row_customer['pincode']?>" >
  
  <span class="red_small">OR You can use state/city/area option to find pincode</span>
                      </div>

                    </div>

                </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="customer_name" id="customer_name" type="text" value="<?=ucwords($row_customer['customer_name']);?>" class="form-control required" />
						<input name="custo_id" id="custo_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>

                      </div>

                    </div>

                                      <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>

                      <div class="col-md-6" id="loc_pincodestate">

                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required >

                          <option value=''>--Please Select--</option>

                          <?php 

						 $state_query="select stateid, state from state_master where countryid='1' order by state";

						 $state_res=mysqli_query($link1,$state_query);

						 while($row_res = mysqli_fetch_array($state_res)){?>

						   <option value="<?=$row_res['stateid']?>"<?php if($row_customer['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>

						 <?php }  ?> 	

                        </select>               

                      </div>

                    </div>

                  </div>
				                    <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark </label>

                      <div class="col-md-6">

                        	<input name="landmark" id="landmark" type="text" class="form-control " value="<?=ucwords($row_customer['landmark']);?>"  /> 

                      </div>

                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>

                        <div class="col-md-6" id="citydiv">

                       <select name="locationcity" id="locationcity" class="form-control required" required >

                       <option value=''>--Please Select-</option>

                       <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_customer['stateid']."' and cityid='".$row_customer['cityid']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"<?php if($row_customer['cityid']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>

                      </div>

                    </div>


                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No<span class="red_small">(For SMS Update)</span> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>" <?php if($row_customer['mobile']!=''|| $_REQUEST['mobileno'] !="" ){?> readonly <?php }else{}?>>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Area <!---<span class="red_small">*</span>----></label>

                      <div class="col-md-6" id="Areadiv">

                     <select name="locationarea" id="locationarea" class="form-control" >

                       <option value=''>--Please Select-</option>
						<?php 
						$pin_area = "SELECT area,pincode FROM  pincode_master where cityid='".$row_customer['cityid']."' and pincode='".$row_customer['pincode']."'";
						$respin_area=mysqli_query($link1,$pin_area);
						while($rowpin_area = mysqli_fetch_array($respin_area)){
						?>
						<option value='<?php echo $rowpin_area['area']."~".$rowpin_area['pincode'];?>'<?php if($rowpin_area['area']."~".$rowpin_area['pincode']==$row_customer['custarea']){ echo "selected";}?>><?php echo $rowpin_area['area']?></option>
							<?php 
						}
						?>
					</select>
                  

                       </select>    

                      </div>

                    </div>

                  </div>

                
                  <div class="form-group">


					
					  <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=ucwords($row_customer['address1']);?></textarea>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>

                      <div class="col-md-6">

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$row_customer['email'];?>" >

                      </div>

                    </div>

                  </div>
				  
				  
                  <div class="form-group">
					  <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No/Dealar No </label>
                      <div class="col-md-6">
					  	<input name="phone2" type="text" class="digits form-control " id="phone2" maxlength="10" value="<?=$row_customer['alt_mobile'];?>" >
                      </div>
                    </div>
                   <div class="col-md-6"><label class="col-md-6 custom_label">Residence No</label>
                      <div class="col-md-6">
                        <input name="res_no" type="text" class="digits form-control" id="res_no" value="<?=$row_customer['phone']?>"  >
                      </div>
                    </div>
                  </div>
				  
 				<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">GST No</label>

                        <div class="col-md-6" id="citydiv">

                         <input name="gst_no" type="text" class=" form-control" id="gst_no" value="<?=$row_customer['gst_no']?>" >
                      </div>

                    </div>

                   <div class="col-md-6"><label class="col-md-6 custom_label">Registration Name</label>

                      <div class="col-md-6">

                        <input name="reg_name" type="text" class=" form-control" id="reg_name" value="<?=ucwords($row_customer['reg_name'])?>">

                      </div>

                    </div>

                  </div>
       
   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Birthday<!--<span class="small">(For SMS Update)</span>--></label>

                      <div class="col-md-6">

                     <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="dob_date"  id="dob_date" style="width:150px;" value="<?php if( $row_customer['dob_date']!='0000-00-00'  ){ echo $row_customer['dob_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Anniversary Date </label>

                      <div class="col-md-6">

                    <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="mrg_date"  id="mrg_date" style="width:150px;" value="<?php if($row_customer['mrg_date']!='0000-00-00'){ echo $row_customer['mrg_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                  </div>
				  
            		</div>
			    </div>


        

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>

              <div class="panel-body">
<div class="form-group">

                    <div class="col-md-6">
                      <label class="col-md-6 custom_label">Product <span class="red_small">*</span></label>

                      
<div class="col-md-6" id="productdiv">

                         <select name="product_name" id="product_name" class="form-control required" onChange="resetProdModel();getprdwisebrand(this.value);" required><!--getmapproduct(this.value);-->

                        

                          <?php
						  if($product_det['product_id']==''){?>
						    <option value=''>--Select Product--</option>

							<?php $dept_query="SELECT * FROM product_master where status = '1'   and product_id in (".$access_product.") order by product_name";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['product_id']?>"<?php if($sel_product == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>

						<?php }} else {?>	
                              <option value='<?=$product_det['product_id']?>'><?=getAnyDetails($product_det['product_id'],"product_name","product_id","product_master",$link1);?></option>
							  <?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label"><?php echo SERIALNO ?> </label>

                      <div class="col-md-6" >


    
    <input name="imei_serial1" id="imei_serial1" type="text" value="<?=$_REQUEST['imei_serial']?>" class="form-control required alphanumeric" minlength='10' maxlength="25" style="width:140px;float: left;" <?php if($product_det['serial_no']!=''){?> readonly <?php }else{}?> required />

	
				<input type="button" name="search_sr" id="search_sr" style="float: left;margin-top: 4px;border: 1px solid #225702; border-radius: 4px; background-color: #225702; color: #f8f3f6; font-weight: bold; margin-left:5px;" onClick="return getSerialdeatils();" value="Go!" />
                      </div>

                    </div>

                  </div>
              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Model <span class="red_small">*</span></label>




<div class="col-md-6" id="modeldiv">

                        <select name="modelid" id="modelid" class="form-control required" required>

                          <?php

						  	if($product_det['model_id']!=""){
							$model_det2 = explode("~",getAnyDetails($product_det['model_id'],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp,make_job,dwp","model_id","model_master",$link1));
echo $model_det2['7'];
						  		if($model_det2['7']!='N'){
						  ?>
							
                          <option value="<?=$product_det['model_id']."~".$model_det2['2']."~".$model_det2['6']."~".$model_det2['8'];?>"><?=$model_det2['2']?></option>

						  <?php
								} //// END IF COndition of Model Check
							}else if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

						  ?>
                                        <option value=''>--Select Model--</option>
                          <option value="<?=$model_code."~".$model_name."~".$model_det2['8']."~".$model_det2['8'];?>"><?=$model_det[2]?></option>

                          <?php }else{?>

                          <option value=''>--Select Model--</option>

                          <?php } ?>

                        </select>

                      </div>
                      
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>

                      <div class="col-md-6" id="selectedbrand">

                       <select name="brand" id="brand" class="form-control required" onChange="getmaploc();resetProdModel();" required> <!--getmapbrand(this.value);--->

                        <?php  if($product_det['brand_id']==''){?>
						    <option value=''>--Select Product--</option>
                          <?php

						  	

							$dept_query="SELECT * FROM brand_master where status = '1'  and brand_id in (".$access_brand.")   order by brand";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_brand == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

						<?php }} else {?>	
                              <option value='<?=$product_det['brand_id']?>'><?=getAnyDetails($product_det['brand_id'],"brand","brand_id","brand_master",$link1);?></option>
							  <?php }?>

                        </select>

                      </div>

                    </div>

                  </div>

              	

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Bill Purchase Date <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      <div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?php if($product_det['purchase_date']!=''){ echo $product_det['purchase_date'];?>  <?php }else{ echo "";}?>"   onChange="getdate4();"></div><div style="display:inline-block;float:left;"><?php if($product_det['purchase_date']=='') {?><i class="fa fa-calendar fa-lg"></i><?php }?></div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty End Date</label>

                      <div class="col-md-6">

                       <input name="warraty_date" id="warraty_date" type="text" value="<?=$product_det['warranty_end_date']?>"  class="form-control" readonly/>

                      </div>

                    </div>

                  </div>


                  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">Date Of Installation</label>

                      <div class="col-md-6">

            
  <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="install_date"  id="install_date" style="width:150px;" value="<?=$product_det['installation_date'];?>"   readonly  ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Call Type <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="call_for" id="call_for" class="form-control required"  onchange="reinsallationfun();"  required>

                          <option value=''>--Select Call Type--</option>
						
						   <option value='Repair'>Repair</option>
                          <option value='Installation'>Installation</option>
						<!--	<option value='PicknDrop'>Pick & Drop Service </option>
						    <option value='Reinstallation'>Re-installation</option>-->
						        <!--<option value='Demo'>Demo</option>-->
						 
						  <!-- <option value='Warehouse Stock Inspection (PDI)'>Warehouse Stock Inspection (PDI)</option
						  ><option value='Dealer Stock Set Repair'>Dealer Stock Set Repair</option>
						     <option value='Split AC I/W Maintenance Service'>Split AC I/W Maintenance Service</option>
							   <option value='Annual Maintenance Contract (AMC)'>Annual Maintenance Contract (AMC)</option>-->
					
						  <!--  <option value='Replacement Handling'>Replacement Handling</option>-->
							
							
						

                        </select>

                      </div>

                    </div>

                  </div>

                <!-- <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Number</label>

                      <div class="col-md-6">
						 <input name="amc_no" id="amc_no" type="text" value="<?=$amc_det['amcid']?>"  class="form-control"  <?php if($amc_det['amcid']!=''){?> readonly <?php }else{}?>/>
						  <input name="amc_day" id="amc_day" type="hidden" value="<?=$amc_det['amc_duration']?>"  class="form-control" />

                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Expiry Date </label>

                      <div class="col-md-6">

                      
					     <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="amc_exp_date"  id="amc_exp_date" style="width:150px;" value="<?=$amc_det['amc_end_date'];?>"  <?php if($amc_det['amc_end_date']!=''){?> readonly <?php }else{}?> ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                  </div> -->
				  
				                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty status<span class="red_small">*</span></label>

                      <div class="col-md-6">
					  
						 <input name="warranty_status" id="warranty_status" type="text" value="<?=$ws?>" class="form-control " readonly/>
						 <input name="warranty_status1" id="warranty_status1" type="hidden" value="<?=$ws?>" class="form-control " readonly/>
                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">Dealer Name </label>

                      <div class="col-md-6">

                         	<input name="dealer_name" id="dealer_name" type="text" value="" class="form-control"/>

                      </div>

                    </div>

                  </div> 
				   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Invoice No </label>

                      <div class="col-md-6">

                      <input name="invoice_no" id="invoice_no" type="text" value="" class="form-control"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Call source <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="call_type" id="call_type" class="form-control required" required>

                          <option value=''>--Select --</option>	
						    <option value='Customer Helpline'>Customer Helpline </option>
							   <option value='Dealer'>Dealer </option>
						   <option value='Distributor'>Distributor </option>
						     <option value='Direct Walkin'>Direct Walkin </option>
						   <option value='HO Escalation'>HO Escalation</option>
                      <!--    <option value='Social Media'>Social Media</option>
						  <option value='Web'>Web</option>-->
						
						<!--  <option value='SMS Feedback'>SMS Feedback</option>-->
						 
						  <!-- <option value='Customer'>Customer </option>-->
						

                        </select>

                      </div>

                    </div>

                  </div> 
				  
				  	   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Purchase From </label>

                      <div class="col-md-6">

                        <select name="entity_type" id="entity_type" class="form-control required" required>
                          <option value="Others">Others</option>
                          <?php



				$enty_query="SELECT * FROM entity_type where status_id = '1' order by name";



				$check_enty=mysqli_query($link1,$enty_query);



				while($br_entity = mysqli_fetch_array($check_enty)){



				?>
                          <option value="<?=$br_entity['id']?>"<?php if($_REQUEST['entity_type']==$br_entity['id']){ echo "selected";}?>><?php echo $br_entity['name']?></option>
                          <?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Accessory Required</label>

                      <div class="col-md-6" id="accdiv">

                       <select name="acc_present[]" id="example-multiple-selected2" multiple="multiple" class="form-control">
						<?php $acc_part = mysqli_fetch_assoc(mysqli_query($link1,"select partcode,part_name from partcode_master where model_id='".$model_code."' and part_category='ACCESSORY' and status='1'"));

							while($br_acc = mysqli_fetch_array($acc_part)){

						  ?>
<option value="<?=$br_acc['part_name']?>"><?php echo $br_acc['part_name']?></option>

						<?php }?>
                        </select>
                      </div>

                    </div>

                  </div> 
				  
				  
				  <div class="form-group">
					  <div class="col-md-6"><label class="col-md-6 custom_label">Assign Location </label>
                    <!--  <div class="col-md-6" id="loc_pincode">-->
						<div class="col-md-6" id="loc_pincode">	
					  <select name="rep_location" id="rep_location" class="form-control required" required>
                     
						 <option value="ASP">TEST ASP</option>
                      </select>
                      </div>
                    </div>
                   <div class="col-md-6"><label class="col-md-6 custom_label">MFD</label>
                      <div class="col-md-6">
								<input name="mfd" id="mfd" type="text" value=""  class="form-control" readonly/>
						        <input name="mfd_ex" id="mfd_ex" type="hidden" value=""  class="form-control" readonly/>
						  		<input name="wp_days" id="wp_days" type="hidden" value=""  class="form-control" readonly/>

                      </div>
                    </div>
                  </div>
				  
				  

              </div>

            </div>
            

            
            
            
            
            
            

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>

              <div class="panel-body">



                  <div class="form-group" id="vocdisplay" >

                    <div class="col-md-6"><label class="col-md-6 custom_label">VOC <span class="red_small">*</span></label>

                      <div class="col-md-6" id="vocdiv">
						
						<select  name='voc1' id='voc1' class='form-control required'  required>
						<option value=''>--Please Select--</option>     
						<?php
						$vocpro="SELECT * FROM voc_master where   status='1'";
						$row_res=mysqli_query($link1,$vocpro);
						while($vocrow = mysqli_fetch_array($row_res)){
						?>    
						<option value="<?php echo $vocrow['voc_code']; ?>" <?php if($vocrow['voc_code'] == $job_det_t['cust_problem']){ echo "selected"; } ?> > <?php echo $vocrow['voc_desc']; ?> </option>
						<?php
						}
						?>   
						</select>
                      </div>

                    </div>

                    <div class="col-md-6">

                    	<div class="col-md-6" id="mutlivoc">

                      
							<?php $vo22 = explode(",", $job_det_t['cust_problem2']); ?>
							<select name="voc2[]" id="example-multiple-selected1" multiple="multiple" class="form-control">
							<?php
							$vocpro="SELECT * FROM voc_master where  status='1'";
							$row_res=mysqli_query($link1,$vocpro);
							while($vocrow = mysqli_fetch_array($row_res)){
							?>
							<option value="<?php echo $vocrow['voc_code']; ?>" <?php for($i=0; $i<count($vo22); $i++){ if($vo22[$i] == $vocrow['voc_code']) { echo 'selected'; }}?>  ><?php echo $vocrow['voc_desc']; ?></option>
							<?php
							}
							?>
							</select>	
                        </div>

                      	<div class="col-md-6">

                            <input name="voc3" id="voc3" type="text" value="" class="form-control" placeholder="Enter Other VOC"/>

                      	</div>

                    </div>

                  </div>

				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Remark</label>
						  </div>
						  <div class="col-md-9">
							  <textarea name="remark" id="remark"  class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"><?=$ticket_det['remark']?></textarea>
						  </div>
					  </div>
				  </div>
				  
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Image Attachement<br><span style="color:#ff5f5f;font-weight:normal;">( png, jpg, pdf | max: 3 MB )</span></label>
						  </div>
						  <div class="col-md-5" style="">

							  <input type="file" name="handset_img" id="handset_img" onChange="return validateImage('handset_img','0');" class="form-control "  accept=".png,.jpg,.jpeg" style="margin-bottom:10px;">
							  <span id="errmsg0" class="red_small"></span>
						  </div>
					  </div>
				  </div>
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Video Attachement <br><span style="color:#ff5f5f;font-weight:normal;">( mp4 | max: 10MB )</span></label>
						  </div>
						  <div class="col-md-5" style="">
							  <input type="file" name="att_video" id="att_video" onChange="return validateVid('att_video','AV');" class="form-control" accept=".mp4" style="margin-bottom:10px;">
							  <span id="errmsgAV" class="red_small"></span>
						  </div>
					  </div>
				  </div>

                  <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='complaint_create.php?<?=$pagenav?>'">&nbsp;
						<input name="wsd" id="wsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
                      <input name="ticketno" id="ticketno" value="<?=base64_encode($ticket_det['ticket_no']);?>" type="hidden"/>
                      <input name="day_diff" id="day_diff" value="<?=$days_diference;?>" type="hidden"/>
                      
                      <input name="symptom" id="symptom" value="<?=$count['symp_code']?>" type="hidden"/>

                      <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

                    </div>

                  </div> 

              </div>

            </div><!-- end panal-->

        </div><!-- end panal group-->

        </form>

    </div><!--End col-sm-9-->

  </div><!--End row content-->

</div><!--End container fluid-->

<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>

</body>

</html>