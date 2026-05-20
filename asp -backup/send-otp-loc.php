<?php 
//require_once("includes/dbconnect.php");
require_once("../includes/config.php");

$job_no = base64_decode($_REQUEST['refid']);

//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$job_no."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

$flag = true;

if($job_no != ""){
	$phone1 = $job_row['contact_no'];
	$sms_msg = "";
	if($job_row['btr_del_code']==""){

		$btr_del_code=rand(1111,9999);

		//, dispatch_date = '".$today."' 
		$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set btr_del_code = '".$btr_del_code."', m_job_date = '".$today."' where job_no='".$job_no."' and btr_del_code = '' ");
		/// check if query is execute or not//
		if(!$jobsheet_upd){
			$flag = false;
			$err_msg = "Error1". mysqli_error($link1) . ".";
		}
	    $sms_msg="Dear Customer, Your Call Closure Code is ".$btr_del_code." which is valid for 3 min. Thanks Su-Kam India (CANCRM)";
		//$sms_msg = "Dear Microtek Customer, We have dispatched replacement battery ".$job_row['job_no']." to you, pl provide Replacement Code ".$btr_del_code." after battery is delivered to you";
	}
	/*else{
		$sms_msg = "Dear Microtek Customer, We have dispatched replacement battery ".$job_row['job_no']." to you, pl provide Replacement Code ".$job_row['btr_del_code']." after battery is delivered to you";
	}*/

	//echo $sms_msg."<br><br>";

	if($flag){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		//CURLOPT_URL =>'http://www.smsjust.com/sms/user/urlsms.php?username=microtek&pass=saloni19&senderid=MtekIn&dest_mobileno='.$phone1.'&message='.urlencode($sms_msg).'&response=Y',
      CURLOPT_URL =>'http://foxxsms.net/sms//submitsms.jsp?user=CONDOUR&key=9ffa85dce5XX&mobile='.$phone1.'&message='.urlencode($sms_msg).'&senderid=CANCRM&accusage=1&entityid=1401565230000011667&tempid=1407173675343560848',	

		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		//CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		));
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_TIMEOUT_MS, 1000); 
		
		$response = curl_exec($curl);
				

		curl_close($curl);
		////// check whether response is ok or not so we will take last 10 character sub string from response it should be in today date format like YYYY_MM_DD
		$respdate = substr($response,-10);
				//print_r($respdate);exit;
		$res = "";
		if($respdate == date("Y_m_d")){
			$res = "1~".$response;
		}else{
			$res = "0~Bad URL";
		}

		$sms_resp = explode("~",$res);

		if($sms_resp[0]=="1"){
			//// insert into sms table


			//echo "INSERT INTO sms_send_response SET ref_no='".$job_no."', ref_type='REPL PICK UP OTP',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'"."<br><br>";

			$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$job_no."', ref_type='REPL PICK UP OTP',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
		}else{
			//// insert into sms table

			//echo "INSERT INTO sms_send_response SET ref_no='".$job_no."', ref_type='REPL PICK UP OTP',mobile_no='".$phone1."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'"."<br><br>";

			$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$job_no."', ref_type='REPL PICK UP OTP',mobile_no='".$phone1."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['asc_code']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
		}
	}
}

///// move to parent page
mysqli_close($link1);
header("location:job_list_repl_btr_sr_loc.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
exit;
################################################

