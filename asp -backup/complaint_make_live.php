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
    $_REQUEST['mobileno']=base64_decode($_REQUEST['mobileno']);
//$srch_criteria = "where ( mobile = '".$_REQUEST['mobileno']."' or  alt_mobile  = '".$_REQUEST['mobileno']."')";
$srch_criteria = "where ( mobile = '".$_REQUEST['mobileno']."')";	
}
else if($_REQUEST['email_id']){
$srch_criteria = "where email = '".$_REQUEST['email_id']."'";
}
else if($_REQUEST['customer_id']){
$srch_criteria = "where customer_id = '".$_REQUEST['customer_id']."'";
}
else if($_REQUEST['imei_serial']){
	$sql_customer_id=mysqli_query($link1,"SELECT customer_id FROM jobsheet_data  where imei='".$_REQUEST['imei_serial']."' ");
	$job_cust = mysqli_fetch_assoc($sql_customer_id);
	$srch_criteria="where customer_id = '".$job_cust['customer_id']."'";
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
	}
    else{
		$usr_add="update customer_master set customer_name='"
                .ucwords($customer_name)."', address1='"
                .ucwords($address)."', pincode='"
                .$pincode."', cityid='"
                .$locationcity."', stateid='"
                .$locationstate."', email='"
                .$email."',  phone='"
                .$res_no."', alt_mobile='"
                .$phone2."', update_date='"
                .$today."', update_by='"
                .$_SESSION['asc_code']."',landmark='"
                .ucwords($landmark)."',type='"
                .$customer_type."',reg_name='"
                .ucwords($reg_name)."',gst_no='"
                .strtoupper($gst_no)."',mrg_date='"
                .$mrg_date."',dob_date='"
                .$dob_date."',custarea='"
                .$locationarea."'  where   customer_id='"
                .$custo_id."'";
		$res_add=mysqli_query($link1,$usr_add); 
		$cust_id=$custo_id;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// pick max count of job

	$cust_st = explode("~",getAnyDetails($locationstate,"zoneid,statecode","stateid","state_master",$link1));
	$statearea = 	$cust_st[0];
	$stcode=$cust_st[1];
    $data_counter_sql="SELECT job_counter  from date_counter  where  job_date ='".$today."'";
	$res_jobcount = mysqli_query($link1,$data_counter_sql);
	if(mysqli_num_rows($res_jobcount)>0){
		$row_jobcount = mysqli_fetch_assoc($res_jobcount);
		///// make job sequence
		$nextjobno = $row_jobcount['job_counter'] + 1;
		$jobno = $stcode."".$job_dt."".str_pad($nextjobno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$res_upd = mysqli_query($link1,"UPDATE date_counter set job_counter='".$nextjobno."' where job_date ='".$today."'");
	}
    else {
		$nextjobno=1;
		$job_dt=date("ymd");
		$jobno=$stcode."".$job_dt."".str_pad($nextjobno,4,0,STR_PAD_LEFT);
        $Inert_data_counter_sql="insert into date_counter set job_counter='".$nextjobno."',job_date ='".$today."'";
		$res_upd = mysqli_query($link1,$Inert_data_counter_sql);
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
	};
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

    $sql_image_upload="INSERT INTO image_upload_details SET job_no ='".$jobno."', img_url='".$img_file."', vid_url='".$vid_file."', upload_date='".$today."', location_code='".$_SESSION['asc_code']."', activity='Call Creation'";
    mysqli_query($link1,$sql_image_upload);
	if($img_file!="")
	{
		mysqli_query($link1, "INSERT INTO upload_history SET doc_no='".$jobno."', process='JOB CREATE', file_url='".$img_file."', create_by='".$_SESSION['userid']."', create_dt='".$datetime."', create_ip='".$ip."'");
	}
	if($vid_file!="")
	{
		mysqli_query($link1, "INSERT INTO upload_history SET doc_no='".$jobno."', process='JOB CREATE', file_url='".$vid_file."', create_by='".$_SESSION['userid']."', create_dt='".$datetime."', create_ip='".$ip."'");
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
    //// check if query is not executed
    if ($rep_location=='') {
		$flag = false;
		$error_msg = "Error details:- Assign Location is blank.please check " . mysqli_error($link1) . ".";
	}


	$prodname = getAnyDetails($product_name,"product_name","product_id","product_master",$link1);

	$vocname = getAnyDetails($voc1,"voc_desc","voc_code","voc_master",$link1); 
	$area = getAnyDetails($statearea,"zonename","zoneid","zone_master",$link1); 

	//$scm=rand(111111,999999);
	$scm=rand(1111,9999);

	//$hpcode="HC".$scm;
	$hpcode=$scm;

	$st="status='2', sub_status='2'";
//$sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$modelsplit[0]."', model='".$modelsplit[1]."', imei='".$imei_serial1."', open_date='".$today."', open_time='".$currtime."', warranty_status='".$st_wart."',warranty_days='".$wsd."', dop='".$pop_date."', dname='".ucwords($dealer_name)."', inv_no='".$invoice_no."',  call_type='".$call_type."', call_for='".$call_for."', customer_name='".ucwords($customer_name)."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".ucwords($address)."', cust_problem='".$voc1."', cust_problem2='".$array_voc2."', cust_problem3='".$voc3."', phy_cond='".$physical_cond."', created_by='".$_SESSION['userid']."', remark='".ucwords($remark)."', ".$st." ,ip='".$ip."',current_location='".$st_asp."',customer_id='".$cust_id."',entity_type='".$entity_type."',acc_rec='".$array_accprest."',area_type='".$arr_pin['area_type']."',pen_status='2',area='".$area."',partcode='".$row_part['partcode']."',h_code='".$hpcode."',custarea='".$locationarea."',installation_date ='".$install_date."',eng_id='".$eng_id1."'";
	if(($pop_date=='0000-00-00' || $pop_date=='') && $sold_unsold=='Sold'){
	    $flag = false;
		$error_msg = "Error jobsheet : DOP is blank or 0000-00-00" . mysqli_error($link1) . ".";
	}
	if($sold_unsold=='Unsold'){$warre_status = "IN";}else{$warre_status = $st_wart;}

	$sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$modelsplit[0]."', model='".$modelsplit[1]."', imei='".$imei_serial1."', open_date='".$today."', open_time='".$currtime."', warranty_status='".$warre_status."', dop='".$pop_date."', dname='".ucwords($dealer_name)."', inv_no='".$invoice_no."',  call_type='".$call_type."', call_for='".$call_for."', customer_name='".ucwords($customer_name)."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".ucwords($address)."', cust_problem='".$voc1."', cust_problem2='".$array_voc2."', cust_problem3='".$voc3."', phy_cond='".$physical_cond."', created_by='".$_SESSION['userid']."', remark='".ucwords($remark)."', ".$st." ,ip='".$ip."',current_location='".$rep_location."',customer_id='".$cust_id."',entity_type='".$entity_type."',acc_rec='".$array_accprest."',area_type='".$arr_pin['area_type']."',pen_status='2',area='".$area."',partcode='".$row_part['partcode']."',h_code='".$hpcode."',custarea='".$locationarea."',installation_date ='".$install_date."',eng_id='".$eng_id1."',mfd='".$mfd."',manufactured_expiry_date='".$mfd_ex."',warranty_days='".$wp_days."',sold_unsold='".$sold_unsold."'";
	$res_inst = mysqli_query($link1,$sql_inst);
	//// check if query is not executed

	if (!$res_inst) {
		$flag = false;
		$error_msg = "Error jobsheet : " . mysqli_error($link1) . ".";
	}
//print_r('dddddddd');exit;
		if($modelsplit[0]==''){
             $flag = false;
		     $error_msg = "please check. model is blank : " . mysqli_error($link1) . ".";
		}
	//// Product Register \\\\\
	//echo "select * from product_registered where serial_no='$serial_no'<br />";
	$usr_add3="INSERT INTO product_registered set serial_no='".$imei_serial1."', customer_id='".$cust_id."', product_id='".$product_name."', model_id='".$modelsplit[0]."', purchase_date='".$pop_date."', installation_date ='".$install_date."', warranty_end_date='".$warraty_date."', status='1',mobile_no='".$phone1."',brand_id='".$brand."',amc_no='".$amc_no."',amc_end_date='".$amc_exp_date."',job_no='".$jobno."'";;

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

    if ($flag) {;
		if($phone1!=''){

            $cust_name = cleanData($customer_name);
            $eng_contactno = getAnyDetails($eng_id1,"contactmo","userloginid","locationuser_master",$link1);
            $eng_name = getAnyDetails($eng_id1,"locusername","userloginid","locationuser_master",$link1);
            $sms_msg_qt = "Dear Su-Kam Customer your Call Number is ".$jobno.". Please keep Warranty Card & Bill ready to show to Service Engineer during his visit. Service will be Chargeable if documents are not produced. Your OTP is ".$hpcode." Please share the same with SE for Call Closure. For update contact us on WhatsApp no 9068906840";

//            $sendsms_qt = sendSMSByURL1($phone1,$sms_msg_qt);
            $sendsms_qt = "";
            $sms_msg="Dear".ucwords($cust_name).
                    ",Your  SR No ".$jobno." for product model "
                    .$modelsplit[1].
                    " has been Logged in Successfully On Dated ".$today.". Sukam";
            $template_id="75789";

            if($sms_resp){
                $res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$jobno."', ref_type='COMPLAINT LOGIN',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
            }
            else{
                $res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$jobno."', ref_type='COMPLAINT LOGIN',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
            }
        }
		#### END  SMS
		mysqli_commit($link1);
		////// return message
		$msg="You have successfully created a Job like <span class='red_small'> ".$jobno." </span> and Customer id is <span class='red_small'> ".$cust_id." </span>";
		$cflag="success";
		$cmsg="Success";
	}
    else {
        var_dump("else block");exit();
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
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">
    <script src="../js/bootstrap-select.min.js"></script>
    <script>
        $(document).ready(function(){
        $("#frm1").validate();
    });
        $(document).ready(function(){
            var spinner = $('#loader');
            $("#frm1").validate({
                submitHandler: function (form){
                    if(!this.wasSent){
                        this.wasSent = true;
                        $(':submit', form).val('Please wait...')
                            .attr('disabled', 'disabled')
                            .addClass('disabled');
                        spinner.show();
                        form.submit();
                    }else{
                        return false;
                    }
                }
            });
        });
        <?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){?>
        $(document).ready(function () {
            $('#pop_date').attr('readonly', true);
        });
        <?php }
        else{?>
        $(document).ready(function () {
            $('#pop_date').datepicker({
                format: "yyyy-mm-dd",
                endDate: "<?=$today?>",
                todayHighlight: false,
                autoclose: false,
            }).on('changeDate', function(ev){
                // getWarranty();
                getdate4();
            });
        });

	<?php }?>
 </script>
    <style>
        #modal_m {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 9999;

            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-box {
            width: 700px;
            max-width: 90vw;
            height: 500px;
            max-height: 90vh;

            background: #fff;
            border-radius: 16px;
            padding: 14px;

            display: flex;
            flex-direction: column;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #m_close {
            font-size: 22px;
            cursor: pointer;
            user-select: none;
        }
        .img-wrap {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .img-wrap img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;   /* 🔥 no distortion */
            aspect-ratio: 1 / 1;
        }
        #loader {
            display: none;
            position: fixed;
            z-index: 999999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.6);
        }
        #loader::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50px;
            height: 50px;
            margin: -25px 0 0 -25px;
            border: 6px solid #ccc;
            border-top: 6px solid #225702;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

    </style>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <link rel="stylesheet" href="../css/datepicker.css">
    <script src="../js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
</head>
<body onload="<?= ($row_customer['pincode'] != '' && $product_det['job_no'] == '')
        ? 'getmaploc('.$row_customer['pincode'].')'
        : '' ?>">
<div class="container-fluid">
  <div class="row content">
	<?php include("../includes/leftnavemp2.php"); ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-id-badge"></i> Enter Complaint Details</h2>
      <?php if($model_det[5]=="Y"){ ?>
      <h4 align="center" style="color:#F00">You are making a Complaint for OUT warranty model .</h4> 
      <?php } ?>

		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data" autocomplete="off" onsubmit="return really('save')" action="" method="post">
        <div class="panel-group">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
                <?php include('./component/asp_complaint_customer.php'); ?>
            </div>


        

            <div class="panel panel-info">
                <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
                <?php include "./component/asp_complaint_product.php"; ?>
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
						$vocpro="SELECT * FROM voc_master where   status='1' group by voc_desc";
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
							<select name="voc2[]" id="example-multiple-selected1"
                                    multiple="multiple" class="form-control scroll-select">
							<?php
							$vocpro="SELECT * FROM voc_master where  status='1' group by voc_desc";
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
							  <textarea name="remark" id="remark"  class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="text-transform: uppercase;resize:none;"><?=$ticket_det['remark']?></textarea>
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
<div id="loader"></div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
<script language="javascript" type="text/javascript">
    const BASE_URL="../includes/getAzaxFields.php";
    const SERIAL_VALIDATOR='../pagination/serial_validatord.php';
    const FIELD_WARRENTY="../includes/getField_warranty.php";

    /**
     * Fetches and loads the list of cities based on the selected state ID.
     *
     * Description:
     * When this function is called, it reads the selected value from the
     * element with ID `locationstate` (assumed to be a state selector).
     * It then sends an AJAX POST request to `BASE_URL`, passing the selected
     * state ID as `stateuser`. The server should return the HTML content
     * (e.g., <option> list or city markup), which will be inserted into the
     * element with ID `citydiv`.
     *
     * Dependencies:
     * - jQuery must be loaded.
     * - BASE_URL must be defined and point to the backend endpoint.
     *
     * Request Payload:
     * { stateuser: <selected_state_id> }
     *
     * Server Response:
     * Expected to return HTML content containing city data.
     *
     * Side Effects:
     * Updates the inner HTML of the `#citydiv` element.
     *
     * Example Usage:
     * get_citydiv(); // typically called on state dropdown change
     */
    function get_citydiv(){
        var name=$('#locationstate').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{stateuser:name},
            success:function(data){$('#citydiv').html(data);}
        });
    }

    /**
     * Handles dynamic loading of models and VOC fields based on user selections.
     *
     * Description:
     * --------------------------------------------------
     * 1. Brand Change Handler:
     *    - Triggered when the element with ID `brand` changes.
     *    - Reads the selected brand ID and the current product ID.
     *    - Sends an AJAX POST request to `BASE_URL` with:
     *          { brandModel: <brand_id>, product_id: <product_id> }
     *    - The server should return HTML containing model options.
     *    - Injects the returned HTML into `#modeldiv`.
     *    - Refreshes Bootstrap selectpicker dropdowns.
     *
     * 2. Product Change Handler:
     *    - Triggered when the element with ID `product_name` changes.
     *    - Clears the selected brand value.
     *    - Sends an AJAX POST request to:
     *          '../includes/getAzaxFields.php'
     *      with:
     *          { vocproduct: <product_id> }
     *    - The server should return HTML for VOC-related fields.
     *    - Injects the returned HTML into `#vocdiv`.
     *    - Calls `getmultivoc()` after updating the DOM.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded.
     * - Bootstrap Select (selectpicker) plugin must be included.
     * - BASE_URL must be defined.
     * - Function `getmultivoc()` must exist globally.
     *
     * Side Effects:
     * --------------------------------------------------
     * - Updates HTML inside #modeldiv and #vocdiv.
     * - Resets brand dropdown when product changes.
     *
     * Typical Usage:
     * --------------------------------------------------
     * Runs automatically once the DOM is ready.
     */
    $(document).ready(function(){
        $('#brand').change(function(){
            var brandid=$('#brand').val();
            var product_name=document.getElementById("product_name").value;
            $.ajax({
                type:'post',
                url:BASE_URL,
                data:{brandModel:brandid,product_id:product_name},
                success:function(data){
                    console.log(data);
                    $('#modeldiv').html(data);
                    $('.selectpicker').selectpicker('refresh');
                }
            });
        });
        $('#product_name').change(function(){
            document.getElementById("brand").value="";
            var product_name=document.getElementById("product_name").value;
            $.ajax({
                type:'post',
                url:BASE_URL,
                data:{vocproduct:product_name},
                success:function(data){
                    $('#vocdiv').html(data);
                    getmultivoc();
                }
            });
        });
    });

    /**
     * Adds a specified number of days to a given date.
     *
     * Description:
     * Creates a new Date object from the provided `date`, adds the given
     * number of days to it, and returns the updated Date object.
     * The original date value is not modified.
     *
     * Parameters:
     * @param {Date|string|number} date
     *        The base date. Can be a Date object, a valid date string,
     *        or a timestamp accepted by the JavaScript Date constructor.
     *
     * @param {number} days
     *        Number of days to add. Can be negative to subtract days.
     *
     * Returns:
     * @returns {Date}
     *          A new Date object with the added days.
     *
     * Example:
     * const result = addDays('2026-02-17', 5);
     * // → returns Date representing 2026-02-22
     *
     * Notes:
     * - Handles month/year rollover automatically.
     * - Time portion of the original date is preserved.
     */
    function addDays(date, days) {
        const newDate = new Date(date);
        newDate.setDate(newDate.getDate() + days);
        return newDate;
    }
    /**
     * Returns warranty duration (in days) based on the provided warranty slab code.
     *
     * Description:
     * Maps a warranty slab code (numeric or alphabetic) to the corresponding
     * warranty period in days. The function checks the slab code and returns
     * the predefined number of days associated with that code.
     *
     * Parameters:
     * @param {number|string} wslabcode
     *        Warranty slab identifier (e.g., 0–9, 'A', 'B', 'C', etc.).
     *
     * Returns:
     * @returns {number}
     *          Warranty duration in days. Returns 0 if the slab represents
     *          no warranty or zero-day warranty.
     *
     * Example:
     * getWarrantySlab(1);   // → 365
     * getWarrantySlab('A'); // → 638
     *
     * Notes:
     * - Supports both numeric and string slab codes.
     * - Codes 'N' and 'W' return 0 days.
     * - Ensure comparison operators use === instead of = to avoid assignment bugs.
     */
    function getWarrantySlab(wslabcode){
        var mfg_exdate = "";
        if(wslabcode=0) {
            mfg_exdate=183;
        }
        else if(wslabcode=1){
            mfg_exdate=365;
        }
        else if(wslabcode=2){
            mfg_exdate=457;
        }
        else if(wslabcode=3){
            mfg_exdate=547;
        }
        else if(wslabcode=4){
            mfg_exdate=730;
        }
        else if(wslabcode=5){
            mfg_exdate=914;
        }
        else if(wslabcode=6){
            mfg_exdate=1095;
        }
        else if(wslabcode=7){
            mfg_exdate=1278;
        }
        else if(wslabcode=8){
            mfg_exdate=1460;
        }
        else if(wslabcode=9){
            mfg_exdate=1825;
        }
        else if(wslabcode='N'){
            mfg_exdate=0;
        }
        else if(wslabcode='W'){
            mfg_exdate=0;
        }
        else if(wslabcode='A'){
            mfg_exdate=638;
        }
        else if(wslabcode='B'){mfg_exdate=225;}
        else if(wslabcode='C'){mfg_exdate=1521;}
        else if(wslabcode='D'){mfg_exdate=241;}
        else if(wslabcode='E'){mfg_exdate=200;}
        else if(wslabcode='F'){mfg_exdate=250;}
        else if(wslabcode='G'){mfg_exdate=2190;}
        else if(wslabcode='H'){mfg_exdate=3285;}
        else if(wslabcode='I'){mfg_exdate=2737;}
        else if(wslabcode='J'){mfg_exdate=1187;}
        else if(wslabcode='P'){mfg_exdate=1825;}
        else if(wslabcode='X'){mfg_exdate=2650;}
        else if(wslabcode='Y'){mfg_exdate=272;}
        else if(wslabcode='Z'){mfg_exdate=300;}
        return mfg_exdate;
    }

    /**
     * Checks whether the entered serial/IMEI already exists or is pending.
     *
     * Description:
     * Reads the value from the input field with ID `imei_serial1` and sends
     * it to the server via an AJAX POST request to `BASE_URL`.
     * The server should validate the serial and return a non-empty response
     * if the serial is found or pending.
     *
     * Behaviour:
     * - If the response is NOT empty:
     *      • Clears the serial input field
     *      • Reloads the current page
     * - If the response is empty:
     *      • No action is taken (serial assumed valid)
     *
     * Dependencies:
     * - jQuery must be loaded
     * - BASE_URL must be defined and point to the validation endpoint
     *
     * Request Payload:
     * { checkserialpending: <serial_number> }
     *
     * Side Effects:
     * - Modifies the value of `#imei_serial1`
     * - Reloads the browser page when duplicate/pending serial is detected
     *
     * Example Usage:
     * checkSerialdeatil(); // typically called on input blur or form submit
     */
    function checkSerialdeatil(){
        var mm_serial=document.getElementById('imei_serial1').value;
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{checkserialpending:mm_serial},
            success:function(data){
                if(data!=''){
                    document.getElementById('imei_serial1').value='';
                    location.reload();
                }
            }
        });
    }

    /**
     * Parses a product serial/IMEI string and extracts manufacturing details,
     * warranty slab, and calculated warranty expiry date.
     *
     * Description:
     * --------------------------------------------------
     * Reads the serial number from input field `#imei_serial1` and decodes
     * multiple product attributes using fixed substring positions:
     *
     * Extracted fields:
     * - Plant ID (factory code)
     * - Segment code
     * - Packing line code
     * - Brand code
     * - Model code
     * - Warranty slab code
     * - Warranty value
     * - Manufacturing day, month, and encoded year
     *
     * The encoded month (A–L) is converted to numeric month (01–12).
     * The encoded year (Y3–Y9) is converted to full year (2023–2030).
     *
     * The function then:
     * 1. Builds the Manufacturing Date (YYYY-MM-DD).
     * 2. Fetches warranty duration (days) using `getWarrantySlab()`.
     * 3. Calculates the warranty expiry date using `addDays()`.
     * 4. Updates the following fields in the DOM:
     *      - #mfd     → Manufacturing date
     *      - #mfd_ex  → Warranty expiry date
     *      - #wp_days → Warranty period in days
     * 5. Calls `chkModelSno()` to validate the model against the serial.
     *
     * Dependencies:
     * --------------------------------------------------
     * - Function `getWarrantySlab(code)`
     * - Function `addDays(date, days)`
     * - Function `chkModelSno(model, serial, extra)`
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates multiple input fields in the page.
     * Performs serial-based product validation.
     *
     * Example Usage:
     * --------------------------------------------------
     * getSerialdeatils();   // usually triggered after serial input
     *
     * Notes:
     * --------------------------------------------------
     * - Assumes the serial format follows a strict fixed-length structure.
     * - Incorrect or short serial values may produce invalid dates.
     */
    function getSerialdeatils(){
        var mm_serial=document.getElementById('imei_serial1').value;
        var myString=mm_serial;
        var myNewString=myString.substr(1); ////1 D L1 B1 M01 W6 01 E Y3 0001   1DL1B1M01W601EY30001
        var plantid = myString.substr(0,1);////factory code
        var segment_code = myString.substr(1,1); /////segment code
        var pck_linecode = myString.substr(2,2);/////packing line code
        var brand_name = myString.substr(4,2);/////brand name
        var mm_model = myString.substr(6,3); ///// model code
        var ws_slab = myString.substr(10,1);/////// get warranty slab
        var warranty = myString.substr(9,2); //////warranty
        var mm_day = myString.substr(11,2); //////MF D
        var mm_month = myString.substr(13,1); //////MF M
        var mm_year = myString.substr(14,2); //////warranty
        if(mm_year=="Y3" ){var mm_yearn='2023';}
        else if(mm_year=="Y4" ){var mm_yearn='2024';}
        else if(mm_year=="Y5" ){var mm_yearn='2025';}
        else if(mm_year=="Y6" ){var mm_yearn='2026';}
        else if(mm_year=="Y7" ){var mm_yearn='2027';}
        else if(mm_year=="Y8" ){var mm_yearn='2029';}
        else if(mm_year=="Y9" ){var mm_yearn='2030';}
        else {var mm_yearn='';}
        if(mm_month=="A" || mm_month=="a"){var mm_monthn='01';}
        else if(mm_month=="B" || mm_month=="b"){var mm_monthn='02';}
        else if(mm_month=="C" || mm_month=="c"){var mm_monthn='03';}
        else if(mm_month=="D" || mm_month=="d"){var mm_monthn='04';}
        else if(mm_month=="E" || mm_month=="e"){var mm_monthn='05';}
        else if(mm_month=="F" || mm_month=="f"){var mm_monthn='06';}
        else if(mm_month=="G" || mm_month=="g"){var mm_monthn='07';}
        else if(mm_month=="H" || mm_month=="h"){var mm_monthn='08';}
        else if(mm_month=="I" || mm_month=="i"){var mm_monthn='09';}
        else if(mm_month=="J" || mm_month=="j"){var mm_monthn='10';}
        else if(mm_month=="K" || mm_month=="k"){var mm_monthn='11';}
        else if(mm_month=="L" || mm_month=="l"){var mm_monthn='12';}
        else {var mm_monthn='';}
        ///////////////// MFD Date \\\\\\\\\\\\\\\\
        var mm_mfd = mm_yearn+'-'+mm_monthn+'-'+mm_day;
        var ws_days = getWarrantySlab(ws_slab);
        document.getElementById("mfd").value=mm_mfd;
        const futureDate = addDays(mm_mfd, ws_days);
        if(futureDate!='Invalid Date'){
            const formattedDate = futureDate.toISOString().split('T')[0];
            document.getElementById("mfd_ex").value=formattedDate;
        }else{
            const formattedDate = "";
            document.getElementById("mfd_ex").value="";
        }
        document.getElementById("wp_days").value=ws_days;
        chkModelSno(mm_model,mm_serial,'');
    };

    /**
     * Fetches the Date of Purchase (DOP) for a given serial number
     * and updates the POP date field in the UI.
     *
     * Description:
     * --------------------------------------------------
     * Sends a GET request to the `SERIAL_VALIDATOR` endpoint with the
     * provided serial number. The server is expected to return a JSON
     * response containing a status flag and a message object that may
     * include the `dop` (date of purchase).
     *
     * Behaviour:
     * - If `serialNo` is empty → function exits immediately.
     * - Shows a loading indicator (`#loader`) while the request is running.
     * - If the response contains a valid DOP:
     *      • Sets the value of `#pop_date`
     *      • Makes the field read-only
     * - If no DOP is returned:
     *      • Clears the `#pop_date` field
     * - Hides the loader once the request completes (success or failure).
     *
     * Parameters:
     * @param {string} serialNo
     *        The product serial number to validate and fetch DOP for.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global constant `SERIAL_VALIDATOR` must contain the API URL
     * - DOM elements required:
     *      #loader   → loading indicator
     *      #pop_date → purchase date input field
     *
     * Expected API Response Example:
     * {
     *   "status": true,
     *   "message": {
     *      "dop": "2025-11-20"
     *   }
     * }
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates the value and readonly state of `#pop_date`
     * Shows and hides the loader element.
     *
     * Example Usage:
     * --------------------------------------------------
     * fetchSerialDOP('ABC123456789');
     */
    function fetchSerialDOP(serialNo) {
        if (serialNo === '') return;
        $('#loader').show();
        $.ajax({
            url: SERIAL_VALIDATOR,
            type: 'GET',
            dataType: 'json',
            data: { serial_no: serialNo },
            success: function (res) {
                if (res.status === true && res.message.dop) {
                    $('#pop_date').val(res.message.dop);
                    $('#pop_date').attr('readonly', true);
                }else{
                    $('#pop_date').val("");
                }
            },
            error: function () {

            },
            complete: function () {
                $('#loader').hide();
            }
        });
    }

    /**
     * Validates a model against its serial number and fetches related warranty data.
     *
     * Description:
     * --------------------------------------------------
     * If a model value is provided, this function builds a request payload
     * and sends it to the backend warranty endpoint (`FIELD_WARRENTY`)
     * using the helper function `xmlhttpPost()`.
     *
     * The request includes:
     *   - action  → "chkModelSno"
     *   - value   → model code
     *   - value2  → serial number
     *   - target  → optional index/identifier
     *
     * The server response is handled by the callback function `displayModel`.
     *
     * Parameters:
     * @param {string} val
     *        Model code to validate.
     *
     * @param {string} val2
     *        Serial number associated with the model.
     *
     * @param {string|number} ind
     *        Optional index or target identifier used by the callback.
     *
     * Dependencies:
     * --------------------------------------------------
     * - Global constant `FIELD_WARRENTY`
     *      → "../includes/getField_warranty.php"
     * - Function `xmlhttpPost(url, data, callbackName)`
     * - Callback function `displayModel(response)`
     *
     * Returns:
     * --------------------------------------------------
     * @returns {boolean|undefined}
     *          Returns false if request is sent (to prevent default actions).
     *
     * Example Usage:
     * --------------------------------------------------
     * chkModelSno('M01', '1DL1B1M01W601EY30001', '');
     *
     * Side Effects:
     * --------------------------------------------------
     * Sends an asynchronous request to the warranty backend
     * and updates the UI through the `displayModel` callback.
     */
    function chkModelSno(val,val2,ind){
        var indx=ind;
        if(val!=""){
            var strSubmit = "action=chkModelSno&value="+val+"&value2="+val2+"&target="+ind;
            var strURL = FIELD_WARRENTY;
            var strResultFunc="displayModel";
            xmlhttpPost(strURL,strSubmit,strResultFunc);
            return false;
        }
    }

    /**
     * Processes the server response for model/serial validation and updates
     * product, brand, model, and purchase date fields in the UI.
     *
     * Description:
     * --------------------------------------------------
     * Receives a caret-separated (^) response string from the backend,
     * typically returned by the `chkModelSno()` → `xmlhttpPost()` request.
     *
     * Expected response format (index-based):
     *   [0] model_id
     *   [1] model_html
     *   [2] warranty_period
     *   [3] index / target identifier
     *   [4] product_id
     *   [5] brand_id
     *   [6] product_html
     *   [7] brand_html
     *   [8] (unused / optional)
     *   [9] purchase_date (YYYY-MM-DD or "0000-00-00")
     *
     * Behaviour:
     * --------------------------------------------------
     * - If a valid model_id exists:
     *      • Updates #productdiv with product HTML + hidden product input
     *      • Updates #selectedbrand with brand HTML + hidden brand input
     *      • Updates #modeldiv with model HTML + hidden model fields
     *
     * - If purchase date exists and is valid:
     *      • Sets #pop_date value
     *      • Calls `getdate4()` for dependent date calculations
     *      • Makes the date field readonly and disables pointer interaction
     *
     * - If purchase date is empty or "0000-00-00":
     *      • Clears #pop_date
     *      • Re-enables editing
     *
     * Parameters:
     * @param {string} result
     *        Caret-separated response string from the server.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Function `getdate4()` must exist
     * - DOM elements required:
     *      #productdiv
     *      #selectedbrand
     *      #modeldiv
     *      #pop_date
     *
     * Side Effects:
     * --------------------------------------------------
     * Dynamically replaces multiple DOM sections.
     * Locks or unlocks purchase date input.
     *
     * Example Usage:
     * --------------------------------------------------
     * displayModel(serverResponseString);
     */
    function displayModel(result){
        var res1=result.split("^");
        var indx=res1[3];
        if(res1[0]!=""){
            $('#productdiv').html(res1[6]+'<input type="hidden" name="product_name" id="product_name" value="'+res1[4]+'"/>');
            $('#selectedbrand').html(res1[7]+'<input type="hidden" name="brand" id="brand" value="'+res1[5]+'"/>');
            $('#modeldiv').html(res1[1]+'<input type="hidden" name="modelid" id="modelid" value="'+res1[0]+'"/>' +
                '<input type="hidden" name="modelwp" id="modelwp" value="'+res1[2]+'~'+res1[3]+'"/>');
            if(res1[9]!=""){
                if(res1[9]!='' && res1[9]!='0000-00-00'){
                    document.getElementById('pop_date').value=res1[9];
                    getdate4();
                    $('#pop_date').attr('readonly', true);
                    document.getElementById("pop_date").style.pointerEvents = "none";
                }
                else{
                    document.getElementById('pop_date').value="";
                    $('#pop_date').attr('readonly', false);
                    document.getElementById("pop_date").style.pointerEvents = "";
                }
            }
        }
        else{
            //alert("Please check -- In-valid serial No");
            // location.reload();
        }
    }

    /**
     * Fetches and loads multiple VOC (Voice of Customer / issue category)
     * options based on the selected product.
     *
     * Description:
     * --------------------------------------------------
     * Reads the selected product ID from `#product_name` and sends it
     * via an AJAX POST request to `BASE_URL` with parameter:
     *      { vocproductmulti: product_name }
     *
     * If the server returns a non-empty response:
     *      • Injects the returned HTML into `#mutlivoc`
     *      • Calls `reCallSelect()` to reinitialize/refresh the select UI
     *
     * Parameters:
     * --------------------------------------------------
     * None (reads value directly from DOM).
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global constant `BASE_URL` must be defined
     * - Function `reCallSelect()` must exist (used to refresh dropdown UI)
     * - DOM elements required:
     *      #product_name
     *      #mutlivoc
     *
     * Expected Server Response:
     * --------------------------------------------------
     * HTML markup containing multi-select VOC options.
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates the inner HTML of `#mutlivoc`
     * Reinitializes select components.
     *
     * Example Usage:
     * --------------------------------------------------
     * getmultivoc();   // typically triggered after product selection
     */
    function getmultivoc(){
        var product_name=document.getElementById("product_name").value;
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{vocproductmulti:product_name},
            success:function(data){
                if(data!=""){
                    $('#mutlivoc').html(data);
                    reCallSelect();
                }
            }
        });
    };

    /**
     * Calculates the warranty expiry date based on purchase date,
     * model warranty period, and customer type.
     *
     * Description:
     * --------------------------------------------------
     * Reads the purchase date from `#pop_date` and the warranty
     * period from hidden field `#modelwp`.
     *
     * The `#modelwp` value is expected in the format:
     *      "customerDays~dealerDays"
     *
     * Behaviour:
     * - If `customer_type` is "Dealer"
     *      → uses dealer warranty days (second value)
     * - Otherwise
     *      → uses customer warranty days (first value)
     *
     * The function then:
     * 1. Adds the selected warranty days to the purchase date
     *    (minus 1 day so the purchase day counts).
     * 2. Formats the calculated expiry date as YYYY-MM-DD.
     * 3. Updates the expiry date field `#warraty_date`.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Required DOM elements:
     *      #pop_date        → purchase date input (YYYY-MM-DD)
     *      #modelwp         → warranty days string ("X~Y")
     *      #customer_type   → dropdown determining which warranty to use
     *      #warraty_date    → output expiry date field
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates the value of `#warraty_date`.
     * Writes debug information to the browser console.
     *
     * Example Usage:
     * --------------------------------------------------
     * getdate4();   // typically called after POP date or model selection
     *
     * Notes:
     * --------------------------------------------------
     * - Assumes purchase date is valid and not empty.
     * - Warranty string must contain "~" separator.
     */
    // function getdate4() {
    //     var start_date = new Date($('#pop_date').val());
    //     console.log(start_date);
    //     //alert(start_date);
    //     var model_wp =  document.getElementById('modelwp').value;
    //     console.log(model_wp+"model_wp");
    //     var customer_type =  document.getElementById('customer_type').value;
    //     console.log(customer_type+"customer_wp");
    //     var modelsplit=model_wp.split("~");
    //     if(customer_type =='Dealer'){
    //         var wday = parseInt(modelsplit[1]);
    //     }else{
    //         var wday= parseInt(modelsplit[0]);
    //     }
    //     var end_date = new Date(start_date);
    //     console.log("end_date"+end_date);
    //     end_date.setDate(start_date.getDate() +  parseInt(wday) - parseInt(1));
    //     datecc=end_date.getFullYear() + '-' + ("0" + (end_date.getMonth() + 1)).slice(-2) + '-' + ("0" + end_date.getDate()).slice(-2);
    //     console.log("datecc"+datecc)
    //     $("#warraty_date").val(datecc);
    // }

    function getdate4() {
        document.getElementById("warranty_status").value = "IN";
        var start = $('#pop_date').val();
        if(!start) return;

        // create date object
        var d = new Date(start);

        // add 1 year
        d.setFullYear(d.getFullYear() + 1);

        // OPTIONAL: agar purchase day count karna hai to -1 day
        // d.setDate(d.getDate() - 1);

        var yyyy = d.getFullYear();
        var mm = ('0' + (d.getMonth()+1)).slice(-2);
        var dd = ('0' + d.getDate()).slice(-2);
        $('#warraty_date').val(yyyy + '-' + mm + '-' + dd);
        $('#install_date').val(yyyy + '-' + mm + '-' + dd);
    }

    /**
     * Fetches accessory options for the selected model and initializes
     * the multi-select dropdown UI.
     *
     * Description:
     * --------------------------------------------------
     * Reads the model identifier from the hidden/input field `#modelid`.
     * The value may contain multiple parts separated by "~", where the
     * first part represents the actual model ID used for the request.
     *
     * Sends an AJAX POST request to `BASE_URL` with:
     *      { model: <model_id> }
     *
     * On successful response:
     *      • Injects the returned HTML (accessory options) into `#accdiv`
     *      • Initializes the multiselect plugin on
     *        `#example-multiple-selected2` with:
     *            - Select-all option enabled
     *            - Filtering enabled
     *            - Fixed button width (200px)
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Multiselect plugin must be included
     * - Global `BASE_URL` must be defined
     * - Required DOM elements:
     *      #modelid
     *      #accdiv
     *      #example-multiple-selected2
     *
     * Expected Server Response:
     * --------------------------------------------------
     * HTML markup containing accessory <option> elements or a full select.
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates the accessory container HTML.
     * Re-initializes the multiselect UI component.
     *
     * Example Usage:
     * --------------------------------------------------
     * getAccessory();   // typically called after model selection
     */
    function getAccessory(){
        var model_id=$('#modelid').val();
        var modelcode = model_id.split("~");
        $.ajax({
            type:'post',
            url:BASE_URL,
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
    }

    /**
     * Determines whether a product is under warranty (IN) or out of warranty (OUT)
     * based on purchase date, model warranty period, and customer type.
     *
     * Description:
     * --------------------------------------------------
     * Reads:
     *   - Purchase date from `#pop_date`
     *   - Warranty period string from hidden field `#modelwp`
     *       (expected format: "customerDays~dealerDays")
     *   - Customer type from `#customer_type`
     *
     * Warranty days selection:
     *   - If customer type is "Dealer" → uses dealer warranty days
     *   - Otherwise → uses customer warranty days
     *
     * Special Rule:
     *   - If PHP flag `<?=$model_det[5]?>` equals "Y"
     *       → Warranty is forced to OUT
     *       → Displays warning message in `#errmsg`
     *
     * Otherwise:
     *   - Calculates the difference (in days) between today's date
     *     (`<?=$today?>`) and the purchase date using `date_difference()`.
     *   - If elapsed days ≤ allowed warranty days → status = "IN"
     *   - Else → status = "OUT"
     *
     * Updates:
     *   - #warranty_status
     *   - #warranty_status1
     *   - #errmsg (only for forced OUT models)
     *
     * Dependencies:
     * --------------------------------------------------
     * - Function `date_difference(currentDate, purchaseDate)`
     * - PHP variables:
     *      <?=$today?>        → must output today's date (YYYY-MM-DD)
     *      <?=$model_det[5]?> → model OUT-warranty flag ("Y"/"N")
     *
     * Required DOM elements:
     *      #pop_date
     *      #modelwp
     *      #customer_type
     *      #warranty_status
     *      #warranty_status1
     *      #errmsg
     *
     * Side Effects:
     * --------------------------------------------------
     * Sets warranty status fields and may show an error message.
     *
     * Example Usage:
     * --------------------------------------------------
     * getWarranty();   // typically called after POP date or model selection
     */
    function getWarranty(){
        var sel_pop = $('#pop_date').val();

        var model_wp =  document.getElementById('modelwp').value;
        var customer_type =  document.getElementById('customer_type').value;
        var post_wsd=model_wp.split("~");
        console.log(post_wsd);
        if(customer_type =='Dealer'){
            var tat_warrty = parseInt(post_wsd[1]);
        }else{
            var tat_warrty = parseInt(post_wsd[0]);
        }
        if("<?=$model_det[5]?>" == "Y"){
            document.getElementById("warranty_status").value = "OUT";
            document.getElementById("errmsg").innerHTML = "You are making a job for OUT warranty model. <br/>";
        }
        else{
            /////calculate days
            var diffDays = date_difference("<?=$today?>", sel_pop);
            if(diffDays <= tat_warrty){
                document.getElementById("warranty_status").value = "IN";
                document.getElementById("warranty_status1").value = "IN";
            }else{
                document.getElementById("warranty_status").value = "OUT";
                document.getElementById("warranty_status1").value = "OUT";
            }
        }
    }

    /**
     * Validates an uploaded video file to ensure it is an MP4 and within size limits.
     *
     * Description:
     * --------------------------------------------------
     * Checks the selected file from the input element (ID passed in `nam`).
     * Validation rules:
     *
     * 1. Allowed file type:
     *      - mp4 only
     *
     * 2. Maximum file size:
     *      - 10 MB (10,240,000 bytes)
     *
     * If validation fails:
     *      • Displays an error message in element `#errmsg{ind}`
     *      • Clears the file input value
     *      • Returns false
     *
     * If validation succeeds:
     *      • Returns true
     *
     * Parameters:
     * @param {string} nam
     *        ID of the file input element to validate.
     *
     * @param {string|number} ind
     *        Index used to identify the error message container
     *        (expects element with ID: "errmsg" + ind).
     *
     * Dependencies:
     * --------------------------------------------------
     * - DOM element `errmsg{ind}` must exist.
     *
     * Returns:
     * --------------------------------------------------
     * @returns {boolean}
     *          true  → file valid
     *          false → file invalid
     *
     * Example Usage:
     * --------------------------------------------------
     * validateVid('upload_video', 1);
     *
     * Side Effects:
     * --------------------------------------------------
     * Clears the file input on invalid selection.
     * Updates the corresponding error message container.
     */
    function validateVid(nam,ind){
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
        else if(file.size > 10240000){
            err_msg = "<strong>File size should be under 10 MB.<br/></strong>";
            document.getElementById("errmsg"+ind).innerHTML = err_msg;
            document.getElementById(nam).value = '';
            return false;
        }
        return true;
    }

    /**
     * Validates an uploaded file for allowed type and size,
     * displays an error message if invalid, and triggers image preview.
     *
     * Description:
     * --------------------------------------------------
     * Checks the selected file from the input element whose ID is passed
     * via `nam`. Validation rules:
     *
     * 1. Allowed file types:
     *      - jpeg / jpg
     *      - png
     *      - pdf
     *
     * 2. Maximum file size:
     *      - 3 MB (3072000 bytes)
     *
     * If validation fails:
     *      • Displays an error message inside element `#errmsg{ind}`
     *      • Clears the file input value
     *      • Returns false
     *
     * If validation succeeds:
     *      • Calls `imagepreview()` to display preview
     *      • Returns true
     *
     * Parameters:
     * @param {string} nam
     *        ID of the file input element to validate.
     *
     * @param {string|number} ind
     *        Index used to locate the error message container
     *        (expects element with ID: "errmsg" + ind).
     *
     * Dependencies:
     * --------------------------------------------------
     * - Function `imagepreview()` must exist.
     * - DOM element `errmsg{ind}` must exist for error display.
     *
     * Returns:
     * --------------------------------------------------
     * @returns {boolean}
     *          true  → file valid
     *          false → file invalid
     *
     * Example Usage:
     * --------------------------------------------------
     * validateImage('handset_img', 1);
     *
     * Side Effects:
     * --------------------------------------------------
     * Clears file input on invalid selection.
     * Updates error message container.
     * May trigger preview rendering.
     */
    function validateImage(nam,ind){
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
        imagepreview();
        return true;
    }

    /**
     * Initializes datepicker widgets for multiple date input fields
     * once the DOM is fully loaded.
     *
     * Description:
     * --------------------------------------------------
     * Attaches Bootstrap Datepicker controls to the following fields:
     *
     * 1. #pop_date (Purchase Date)
     *    - Format: YYYY-MM-DD
     *    - Highlights today's date
     *    - Auto-closes after selection
     *
     * 2. #install_date (Installation Date)
     *    - Format: YYYY-MM-DD
     *    - Start date restricted to today's date (PHP variable <?=$today?>)
     *    - End date restricted to today's date
     *    - Today's date disabled from manual selection
     *    - Highlights today
     *    - Auto-closes after selection
     *
     * 3. #dob_date (Date of Birth)
     *    - Format: YYYY-MM-DD
     *    - Cannot select future dates (endDate = today)
     *    - Highlights today
     *    - Auto-closes after selection
     *
     * 4. #mrg_date (Marriage Date)
     *    - Format: YYYY-MM-DD
     *    - Cannot select future dates
     *    - Highlights today
     *    - Auto-closes after selection
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Bootstrap Datepicker plugin must be included
     * - PHP variable <?=$today?> must output a valid date string (YYYY-MM-DD)
     *
     * Side Effects:
     * --------------------------------------------------
     * Converts standard input fields into interactive calendar pickers.
     *
     * Example Usage:
     * --------------------------------------------------
     * Runs automatically on page load.
     */
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

    /**
     * Updates warranty status and VOC display based on the selected call type.
     *
     * Description:
     * --------------------------------------------------
     * Reads the value from the `#call_for` dropdown to determine the type
     * of service request (e.g., Installation, Reinstallation, Service, etc.).
     *
     * Behaviour:
     * - If the call type is "Reinstallation":
     *      • Sets `#warranty_status` to "VOID"
     * - Otherwise:
     *      • Restores `#warranty_status` from hidden/input field `#warranty_status1`
     *
     * - If the call type is "Reinstallation" OR "Installation":
     *      • Hides the VOC section (`#vocdisplay`)
     * - Otherwise:
     *      • Shows the VOC section
     *
     * Dependencies:
     * --------------------------------------------------
     * Required DOM elements:
     *   - #call_for
     *   - #warranty_status
     *   - #warranty_status1
     *   - #vocdisplay
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates warranty status field value.
     * Shows or hides the VOC display section.
     *
     * Example Usage:
     * --------------------------------------------------
     * reinsallationfun();   // typically called on change of call_for dropdown
     */
    function reinsallationfun(){
        var call_for=$('#call_for').val();
        /*if(call_for=="Reinstallation"){
            document.getElementById("warranty_status").value = "VOID";
        }else{
            document.getElementById("warranty_status").value= document.getElementById("warranty_status1").value ;
        }
        if (call_for=="Reinstallation" || call_for=="Installation" ){
            document.getElementById("vocdisplay").style.display = "none";
        }else{
            document.getElementById("vocdisplay").style.display="";
        }*/
    }

    /**
     * Fetches mapped service/location data based on pincode, product,
     * brand, and state, then updates the location dropdown in the UI.
     *
     * Description:
     * --------------------------------------------------
     * Reads values from the following fields:
     *   - #pincode        → user pincode
     *   - #product_name   → selected product
     *   - #brand          → selected brand
     *   - #locationstate  → selected state
     *
     * Sends these values via an AJAX POST request to `BASE_URL`
     * with the payload:
     *   { RVLocpin, product7, brand7, state7 }
     *
     * If the server returns a non-empty response:
     *   • Injects the returned HTML into `#loc_pincode`
     *   • Calls `makeSelect()` to initialize/refresh the dropdown UI
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Function `makeSelect()` must exist (for select UI setup)
     *
     * Request Payload Example:
     * {
     *   RVLocpin: "110001",
     *   product7: "TV",
     *   brand7: "Sony",
     *   state7: "Delhi"
     * }
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates HTML inside `#loc_pincode`
     * Reinitializes select UI via `makeSelect()`
     *
     * Example Usage:
     * --------------------------------------------------
     * getmaploc();  // typically triggered after pincode or product selection
     */
    function getmaploc(){
        var pincode=$('#pincode').val();
        var prd7=$('#product_name').val();
        var brd7=$('#brand').val();
        var sat7=$('#locationstate').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{RVLocpin:pincode,product7:prd7,brand7:brd7,state7:sat7},
            success:function(data){
                if(data!=""){
                    $('#loc_pincode').html(data);
                    makeSelect();
                }
            }
        });
    };

    /**
     * Validates whether the selected brand is allowed for the current
     * representative/location and resets the selection if not permitted.
     *
     * Description:
     * --------------------------------------------------
     * Reads:
     *   - Selected brand from `#brand`
     *   - Current representative/location from `#rep_location`
     *
     * Sends an AJAX POST request to `BASE_URL` with:
     *      { brandmap: <brand>, rep_location: <location> }
     *
     * Behaviour:
     * - If the server response is `0`
     *      → Shows an alert stating the location has no rights for the brand
     *      → Clears the brand selection
     * - Otherwise
     *      → No action (brand is allowed)
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Required DOM elements:
     *      #brand
     *      #rep_location
     *
     * Expected Server Response:
     * --------------------------------------------------
     * 0  → brand not permitted for this location
     * 1 (or any non-zero value) → permitted
     *
     * Side Effects:
     * --------------------------------------------------
     * May reset the brand dropdown value.
     * Displays an alert message for unauthorized selections.
     *
     * Example Usage:
     * --------------------------------------------------
     * getmapbrand();   // typically triggered on brand change
     */
    function getmapbrand(){
        var brand=$('#brand').val();
        var curent_loc=$('#rep_location').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{brandmap:brand,rep_location:curent_loc},
            success:function(data){
                if(data==0){
                    alert("This Location have no rights for This Brand");
                    document.getElementById('brand').value = '';
                }else{}
            }
        });
    };

    /**
     * Validates whether the selected product is permitted for the current
     * representative/location and clears the selection if unauthorized.
     *
     * Description:
     * --------------------------------------------------
     * Reads:
     *   - Selected product ID from `#product_name`
     *   - Current representative/location from `#rep_location`
     *
     * Sends an AJAX POST request to `BASE_URL` with:
     *      { productmap: <product>, rep_location: <location> }
     *
     * Behaviour:
     * - If the server response equals `0`
     *      → Shows an alert that the location has no rights for this product
     *      → Clears the product selection field
     * - Otherwise
     *      → No action (product is allowed)
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Required DOM elements:
     *      #product_name
     *      #rep_location
     *
     * Expected Server Response:
     * --------------------------------------------------
     * 0  → product not permitted for this location
     * non-zero → permitted
     *
     * Side Effects:
     * --------------------------------------------------
     * May reset the product dropdown value.
     * Displays an alert message for unauthorized selections.
     *
     * Example Usage:
     * --------------------------------------------------
     * getmapproduct();   // typically triggered on product change
     */
    function getmapproduct(){
        var prod=$('#product_name').val();
        var curent_loc=$('#rep_location').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{productmap:prod,rep_location:curent_loc},
            success:function(data){
                if(data==0){
                    alert("This Location have no rights for This Product");
                    document.getElementById('product_name').value = '';
                }else{}
            }
        });
    };

    /**
     * Fetches and displays the list of brands available for a selected product,
     * filtered by the user's accessible brands.
     *
     * Description:
     * --------------------------------------------------
     * Sends an AJAX POST request to `BASE_URL` with:
     *   - RVprdwisebrand → list of brands accessible to the user (PHP variable)
     *   - prd            → selected product identifier
     *
     * The server should return HTML containing the filtered brand options.
     * If a response is received, it replaces the content inside `#selectedbrand`.
     *
     * Parameters:
     * @param {string|number} prd
     *        Selected product ID/code used to filter available brands.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - PHP variable `<?=$access_brand;?>` must output accessible brand IDs
     * - Required DOM element:
     *      #selectedbrand → container where brand dropdown HTML is inserted
     *
     * Expected Server Response:
     * --------------------------------------------------
     * HTML markup for brand dropdown/options filtered by product.
     *
     * Side Effects:
     * --------------------------------------------------
     * Replaces the inner HTML of `#selectedbrand`.
     *
     * Example Usage:
     * --------------------------------------------------
     * getprdwisebrand('TV01');
     */
    function getprdwisebrand(prd){
        var access_brand = "<?=$access_brand;?>";
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{RVprdwisebrand:access_brand,prd:prd},
            success:function(data){
                if(data){
                    $('#selectedbrand').html(data);
                }
                else{}
            }
        });
    };

    /**
     * Re-fetches and resets model options based on the currently selected
     * product and brand, then reinitializes the select UI.
     *
     * Description:
     * --------------------------------------------------
     * Reads:
     *   - Product ID from `#product_name`
     *   - Brand ID from `#brand`
     *
     * Sends an AJAX POST request to `BASE_URL` with:
     *      { brandModel: <brand_id>, product_id: <product_id> }
     *
     * The server is expected to process the request and return updated
     * model-related data (even though the response is not directly used here).
     * After the request completes successfully, the function calls
     * `makeSelect()` to refresh or rebuild the dropdown/select UI.
     *
     * Parameters:
     * --------------------------------------------------
     * None (values are read directly from DOM).
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Function `makeSelect()` must exist for dropdown initialization
     * - Required DOM elements:
     *      #product_name
     *      #brand
     *
     * Side Effects:
     * --------------------------------------------------
     * Sends a backend request and refreshes select components.
     *
     * Example Usage:
     * --------------------------------------------------
     * resetProdModel();   // typically called after product/brand reset
     */
    function resetProdModel(){
        var product_name = document.getElementById('product_name').value;
        var brandid = document.getElementById('brand').value;
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{brandModel:brandid,product_id:product_name},
            success:function(data){makeSelect();}
        });
    }

    /**
     * Fetches state/location mapping based on the entered pincode
     * and updates related location fields in the UI.
     *
     * Description:
     * --------------------------------------------------
     * Reads the pincode value from `#pincode` and sends an AJAX POST
     * request to `BASE_URL` with:
     *      { Locpinstate: <pincode>, cmLocSt: '1' }
     *
     * On successful response:
     *      • Inserts the returned HTML into `#loc_pincodestate`
     *      • Calls `get_pincity()` to load city options for the pincode
     *      • Calls `get_assign_location()` to assign the service location
     *
     * Parameters:
     * --------------------------------------------------
     * None (pincode is read directly from the DOM).
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Required DOM elements:
     *      #pincode
     *      #loc_pincodestate
     * - Functions must exist:
     *      get_pincity()
     *      get_assign_location()
     *
     * Expected Server Response:
     * --------------------------------------------------
     * HTML markup representing the mapped state/location data.
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates state container HTML and triggers additional
     * location-related data loading functions.
     *
     * Example Usage:
     * --------------------------------------------------
     * getmapinstate();   // typically triggered after pincode entry/change
     */
    function getmapinstate() {
        var pincode=$('#pincode').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{Locpinstate:pincode,cmLocSt:'1'},
            success:function(data){
                $('#loc_pincodestate').html(data);
                get_pincity();
                get_assign_location();
            }
        });
    };

    /**
     * Fetches and assigns the service location mapped to a given pincode
     * and updates the location container in the UI.
     *
     * Description:
     * --------------------------------------------------
     * Reads the pincode value from `#pincode` and sends an AJAX POST
     * request to `BASE_URL` with:
     *      { assignpincode: <pincode> }
     *
     * If the server returns a non-empty response:
     *      • Injects the returned HTML into `#loc_pincode`
     *        (typically a location dropdown or assigned service center info)
     *
     * Parameters:
     * --------------------------------------------------
     * None (pincode is read directly from the DOM).
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Required DOM elements:
     *      #pincode
     *      #loc_pincode
     *
     * Expected Server Response:
     * --------------------------------------------------
     * HTML markup representing the assigned location options/details.
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates the content of `#loc_pincode`.
     *
     * Example Usage:
     * --------------------------------------------------
     * get_assign_location();   // typically triggered after pincode selection/change
     */
    function get_assign_location() {
        var pincode=$('#pincode').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{assignpincode:pincode},
            success:function(data){
                if(data!=""){
                    $('#loc_pincode').html(data);
                }
            }
        });
    };

    /**
     * Fetches and updates engineer/service assignment based on the selected
     * representative location and pincode.
     *
     * Description:
     * --------------------------------------------------
     * Reads:
     *   - Representative location code from `#rep_location`
     *   - Pincode from `#pincode`
     *
     * Sends an AJAX POST request to `BASE_URL` with:
     *      { assignpincode1: <pincode>, loc_code: <location_code> }
     *
     * If the server returns a non-empty response:
     *      • Updates the HTML inside `#loc_pincode`
     *        (typically containing assigned engineer/location details)
     *
     * Parameters:
     * --------------------------------------------------
     * None (values are read directly from the DOM).
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Global `BASE_URL` must be defined
     * - Required DOM elements:
     *      #rep_location
     *      #pincode
     *      #loc_pincode
     *
     * Expected Server Response:
     * --------------------------------------------------
     * HTML markup representing engineer assignment or location info.
     *
     * Side Effects:
     * --------------------------------------------------
     * Replaces the content of `#loc_pincode`.
     *
     * Example Usage:
     * --------------------------------------------------
     * eng_assign();   // typically triggered after rep location selection
     */
    function eng_assign() {
        var val=$('#rep_location').val();
        var pincode=$('#pincode').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{assignpincode1:pincode,loc_code:val},
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
            url:BASE_URL,
            data:{Locpincity:pincode,cmLocSt:'2'},
            success:function(data){
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
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{Locpinarea:pincode,cmLocSt:'3',city_id:cityId},
            success:function(data){
                if(data!=""){
                    $('#Areadiv').html(data);
                }
            }
        });
    };

    function get_cityArea() {
        var locationcity=$('#locationcity').val();
        var enterpin = $('#pincode').val();
        $.ajax({
            type:'post',
            url:BASE_URL,
            data:{Locpinareacity:locationcity,enter_pin:enterpin},
            success:function(data){
                $('#Areadiv').html(data);
            }
        });
    };

    /**
     * Extracts the pincode from the selected location-area value
     * and populates it into the pincode input field.
     *
     * Description:
     * --------------------------------------------------
     * Reads the value from `#locationarea`, which is expected to be
     * in a tilde-separated format such as:
     *
     *      "AreaName~Pincode"
     *
     * The function splits the value using "~" and assigns the second
     * part (index 1) as the pincode, updating the `#pincode` field.
     *
     * Parameters:
     * --------------------------------------------------
     * None (value is read directly from the DOM).
     *
     * Dependencies:
     * --------------------------------------------------
     * Required DOM elements:
     *      #locationarea → select/input containing area~pincode value
     *      #pincode      → input field to receive the extracted pincode
     *
     * Side Effects:
     * --------------------------------------------------
     * Updates the value of the `#pincode` input field.
     *
     * Example Usage:
     * --------------------------------------------------
     * findpicode();   // typically triggered after location area selection
     *
     * Notes:
     * --------------------------------------------------
     * Assumes the location value always contains "~".
     * If the format is invalid, the pincode may become undefined.
     */
    function findpicode(){
        var locationarea=$('#locationarea').val();
        var post_area=locationarea.split("~");
        document.getElementById("pincode").value=post_area[1];
    }

    /**
     * Calculates the absolute difference in days between two dates.
     *
     * Description:
     * --------------------------------------------------
     * Accepts two date strings in the format "YYYY-MM-DD".
     * Converts both into JavaScript Date objects and computes the
     * absolute number of days between them.
     *
     * The calculation ignores time components and returns only the
     * rounded day difference.
     *
     * Parameters:
     * @param {string} enddate
     *        Ending date in "YYYY-MM-DD" format.
     *
     * @param {string} startdate
     *        Starting date in "YYYY-MM-DD" format.
     *
     * Returns:
     * @returns {number}
     *          Absolute difference between the two dates in days.
     *
     * Example Usage:
     * --------------------------------------------------
     * date_difference('2026-02-17', '2026-02-10'); // → 7
     *
     * Dependencies:
     * --------------------------------------------------
     * None (pure JavaScript).
     *
     * Notes:
     * --------------------------------------------------
     * - Assumes valid "YYYY-MM-DD" formatted inputs.
     * - Result is always non-negative (absolute difference).
     */
    function date_difference(enddate,startdate){
        var end_date = (enddate).split("-");
        var start_date = (startdate).split("-");
        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var firstDate = new Date(start_date[0], start_date[1], start_date[2]);
        var secondDate = new Date(end_date[0], end_date[1], end_date[2]);
        var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));
        return diffDays;
    }

    /**
     * Initializes multiselect dropdown plugins for predefined select elements
     * once the DOM is fully loaded.
     *
     * Description:
     * --------------------------------------------------
     * Applies the multiselect plugin to:
     *
     * 1. #example-multiple-selected1
     * 2. #example-multiple-selected2
     *
     * Configuration used for both:
     *   - includeSelectAllOption → adds a "Select All" option
     *   - enableFiltering        → enables search/filter inside dropdown
     *   - buttonWidth            → sets dropdown button width to 200px
     *
     * This setup ensures that both select elements are rendered as enhanced
     * multi-selection dropdowns when the page loads.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Bootstrap Multiselect (or compatible plugin) must be included
     * - Both DOM elements must exist before initialization
     *
     * Side Effects:
     * --------------------------------------------------
     * Converts the standard `<select>` elements into searchable multiselect UI.
     *
     * Example Usage:
     * --------------------------------------------------
     * Runs automatically on page load.
     */
    $(document).ready(function() {
        $('#example-multiple-selected1').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            buttonWidth:"200"
        });
        $('#example-multiple-selected2').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            buttonWidth:"200"
        });
    });

    /**
     * Initializes or re-initializes the multiselect dropdown for the
     * element `#example-multiple-selected1`.
     *
     * Description:
     * --------------------------------------------------
     * Applies the multiselect plugin to the target select element with:
     *   - includeSelectAllOption → adds a "Select All" option
     *   - enableFiltering        → enables search/filter inside dropdown
     *   - buttonWidth            → sets dropdown button width to 200px
     *
     * This function is typically called after dynamically loading or
     * updating the select options (e.g., via AJAX) so the multiselect UI
     * renders correctly.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Bootstrap Multiselect (or compatible plugin) must be included
     * - DOM element `#example-multiple-selected1` must exist
     *
     * Side Effects:
     * --------------------------------------------------
     * Converts the standard `<select>` element into an enhanced
     * searchable multi-selection dropdown.
     *
     * Example Usage:
     * --------------------------------------------------
     * reCallSelect();   // call after updating multi-select options
     */
    function reCallSelect(){
        $('#example-multiple-selected1').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            buttonWidth:"200"
        });
    }

    /**
     * Initializes or re-initializes Bootstrap Select dropdowns
     * with predefined configuration options.
     *
     * Description:
     * --------------------------------------------------
     * Applies the Bootstrap Select (`selectpicker`) plugin to all
     * elements with the class `.selectpicker`.
     *
     * Enabled features:
     *   - liveSearch   → allows users to search inside the dropdown
     *   - showSubtext  → displays subtext for dropdown options (if provided)
     *
     * This function is typically called after dynamically inserting
     * or updating `<select>` elements in the DOM so they render correctly
     * with the selectpicker UI.
     *
     * Dependencies:
     * --------------------------------------------------
     * - jQuery must be loaded
     * - Bootstrap Select plugin must be included
     * - Elements must have class `.selectpicker`
     *
     * Side Effects:
     * --------------------------------------------------
     * Converts standard `<select>` elements into enhanced searchable dropdowns.
     *
     * Example Usage:
     * --------------------------------------------------
     * makeSelect();   // call after AJAX updates or page load
     */
    function makeSelect(){
        $('.selectpicker').selectpicker({
            liveSearch: true,
            showSubtext: true
        });
    }

    /**
     * Toggles visibility of purchase date and warranty status fields
     * based on whether the product is marked as Sold or Unsold.
     *
     * Description:
     * --------------------------------------------------
     * Checks the provided status value:
     *
     * - If value is "Sold"
     *      → Shows the purchase date section (`#dop_date1`)
     *      → Shows the warranty status section (`#warr_sts`)
     *
     * - If value is "Unsold"
     *      → Hides the purchase date section
     *      → Hides the warranty status section
     *
     * Parameters:
     * @param {string} val
     *        Product sale status ("Sold" or "Unsold").
     *
     * Dependencies:
     * --------------------------------------------------
     * Required DOM elements:
     *      #dop_date1   → purchase date container
     *      #warr_sts    → warranty status container
     *
     * Side Effects:
     * --------------------------------------------------
     * Changes the CSS display property of the related sections.
     *
     * Example Usage:
     * --------------------------------------------------
     * sold_unsold1('Sold');
     * sold_unsold1('Unsold');
     */
    function sold_unsold1(val){
        if(val=='Sold'){
            document.getElementById("dop_date1").style.display = "";
            document.getElementById("warr_sts").style.display = "";
        }
        else if(val=='Unsold'){
            document.getElementById("dop_date1").style.display = "none";
            document.getElementById("warr_sts").style.display = "none";
        }
    }
</script>
</html>