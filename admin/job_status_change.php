<?php
require_once("../includes/config.php");
////decode post job no.
$jobno = base64_decode($_POST['postjobno']);
if($_POST['savejob']=="Save" && $jobno!=''){
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
	/////// fetch current job details
	$job_details = mysqli_fetch_assoc(mysqli_query($link1,"select * from jobsheet_data where job_no='".$jobno."'"));

	
	////////////  frst update status and substatus in jobsheet data table /////////////////////////////////
	if($_POST['jobstatus'] == "1"){
	$upd_jobsheet =  mysqli_query($link1,"update jobsheet_data set vistor_app_by='',vistor_date='',current_location='',doa_rej_rmk='',eng_id='',status = '".$_REQUEST['jobstatus']."' , sub_status = '".$_REQUEST['jobstatus']."'  , remark = '".$_POST['rep_remark']."'   where job_no = '".$jobno."'  ");
	}else {
	$upd_jobsheet =  mysqli_query($link1,"update jobsheet_data set status = '".$_REQUEST['jobstatus']."' , sub_status = '".$_REQUEST['jobstatus']."'  , remark = '".$_POST['rep_remark']."'   where job_no = '".$jobno."'  ");	
		
		}
	//// check if query is not executed
		if (!$upd_jobsheet) {
			 $flag = false;		
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
	
	########## End 	
	
	### Seconfd insert into call history table  /////////////////////////////////////////////////
	if($_POST['jobstatus'] == "1") { $outcome = "Job Create" ;} else if ($_POST['jobstatus'] == "12") {$outcome = "Cancel" ;} else if ($_POST['jobstatus'] == "10") {$outcome = "Handover to Customer" ;}  else{$outcome = "" ;}
		
		$flag = callHistory($jobno,$_SESSION['userid'],$_POST['jobstatus'],"Job Status Change",$outcome,$_SESSION['userid'],"",$_POST['rep_remark'],$travelkm,$travel,$ip,$link1,$flag);
	
	########## End 
	
	### thirdly update status cancel  in SFR Transcation table////////////////////////////
	if ($job_details['status'] == "4") { ////// start of if//////////
		$upd_sfr =  mysqli_query($link1,"update sfr_transaction set status = '12'  where job_no = '".$jobno."'  ");		
			//// check if query is not executed
			if (!$upd_sfr) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}

	########## End 
	///////////////     get challan no from sfr transcation table so that we can change status in sfr challan table/////////////////////////////////
	$challan_no  =  getAnyDetails($jobno,"challan_no" ,"job_no" ,"sfr_transaction" ,$link1);
	
	//////////// fourthly update status cancel  in  sfr_challan table /////////////////////////////
	$upd_sfrchallan =  mysqli_query($link1,"update sfr_challan set status = '12'  where challan_no = '".$challan_no."'  ");
		
			//// check if query is not executed
			if (!$upd_sfrchallan) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
			
			//////////// fourthly update status cancel  in  sfr_challan table /////////////////////////////
	$upd_sfrbin =  mysqli_query($link1,"update sfr_bin set status = '12'  where challan_no = '".$challan_no."'  ");
		
			//// check if query is not executed
			if (!$upd_sfrbin) {
				 $flag = false;
				 $error_msg = "Error details11: " . mysqli_error($link1) . ".";
			}
			
			////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"SFR","Job Status Change",$ip,$link1,$flag);

	}	/// end of if //////////////////////////////////
	########## End 
	
	### next  update status cancel  in auto_part_request table	
	else if($job_details['status'] == "3"){	
			$upd_auotpart =  mysqli_query($link1,"update auto_part_request set status = '12'  where job_no = '".$jobno."'  ");		
			//// check if query is not executed
			if (!$upd_auotpart) {
				 $flag = false;
				 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
			}
	
	/////  condition to check whether jobno status in po_item table has status 1, if status is 1 then only u can update status cancel //////////////////////////
			$po_itmesinfo	 = explode("~",getAnyDetails($jobno,"status,po_no" ,"job_no" ,"po_items",$link1))	;
			
			if($po_itmesinfo[0] == '1') {						
			$upd_poitems =  mysqli_query($link1,"update po_items set status = '12'  where job_no = '".$jobno."'  "	);	
			//// check if query is not executed
			if (!$upd_poitems) {
				 $flag = false;
				 $error_msg = "Error details6: " . mysqli_error($link1) . ".";
			}

			////////// update status cancel in po_master table/////////////////////////////////////
			$upd_po_master =  mysqli_query($link1,"update po_master set status = '12'  where po_no = '".$po_itmesinfo[1]."'  "	);	
			//// check if query is not executed
			if (!$upd_po_master) {
				 $flag = false;
				 $error_msg = "Error details7: " . mysqli_error($link1) . ".";
			}
			////// insert in activity table////
				$flag = dailyActivity($_SESSION['userid'],$jobno,"PO","Job Status Change",$ip,$link1,$flag);
	
		}
	}/// end of  if ////////////////	
		########## next update status cancel in estimate master table ////////////////////	
		else if($job_details['status'] == "5"){	
			$upd_estimate =  mysqli_query($link1,"update estimate_master set status = '12'  where job_no = '".$jobno."'  "	);	
			//// check if query is not executed
			if (!$upd_estimate) {
				 $flag = false;
				 $error_msg = "Error details5: " . mysqli_error($link1) . ".";
			}
				////// insert in activity table////
				$flag = dailyActivity($_SESSION['userid'],$jobno,"EP","Job Status Change",$ip,$link1,$flag);
		}
		else{
		//// nothing to do  
		}		
	///// check if all query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Job Status  has been changed successfully.";
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	mysqli_close($link1);
   ///// move to parent page
   header("location:admin_jobstatus_change.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}
?>