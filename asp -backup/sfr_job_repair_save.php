<?php
require_once("../includes/config.php");
////decode post job no.
$jobno = base64_decode($_POST['postjobno']);
if($_POST['savejob']=="Save" && $jobno!='' && $_SESSION['asc_code']!=''){
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
	/////// fetch current job details
	$job_details = mysqli_fetch_assoc(mysqli_query($link1,"select * from jobsheet_data where job_no='".$jobno."'"));
	///////// save the job details
	//// update status and repair remark in job sheet
	$res_jobsheet = mysqli_query($link1,"UPDATE jobsheet_data set sub_status='".$_POST['jobstatus']."' where job_no='".$jobno."'");
	//// check if query is not executed
	if (!$res_jobsheet) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	### Start SFR CASE ########## if repair status select as SFR
	if($_POST['jobstatus'] == "4"){
	///////////////  update by priya on 1 august to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_sfrl = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
   		$sessionMessageIdent_sfrl = isset($_SESSION['messageIdent_sfrl'])?$_SESSION['messageIdent_sfrl']:'';
		if($messageIdent_sfrl!=$sessionMessageIdent_sfrl){//if its different:          
				//save the session var:
			$_SESSION['messageIdent_sfrl'] = $messageIdent_sfrl;
   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//// inser SFR details in sfr bin table
		$res_sfrbin = mysqli_query($link1,"INSERT INTO sfr_bin set location_code='".$_SESSION['asc_code']."', to_location='".$_POST['send_for']."', job_no='".$jobno."', imei='".$job_details['imei']."', model_id='".$job_details['model_id']."', partcode='".$job_details['partcode']."', qty='1', entry_date='".$today."', status='4'");
		//// check if query is not executed
		if (!$res_sfrbin) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Esclated","Send For Repair",$_SESSION['userid'],$job_details['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"SFR",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed-1: " . mysqli_error($link1) . ".";
		}
	}
	########## End SFR CASE
	
	### Start PNA CASE ########## if repair status select as PNA
	else if($_POST['jobstatus'] == "43"){
	///////////////  update by priya on 1 august to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pnal = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pnal = isset($_SESSION['messageIdent_pnal'])?$_SESSION['messageIdent_pnal']:'';
		if($messageIdent_pnal!=$sessionMessageIdent_pnal){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pnal'] = $messageIdent_pnal;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
		$pna_partarr = $_POST['pending_part'];
		foreach($pna_partarr as $k => $val){ 
			//// insert PNA details in auto part requset table
			$expld_pnapart = explode("~",$pna_partarr[$k]);
			$res_autopartreq = mysqli_query($link1,"INSERT INTO auto_part_request set location_code='".$_SESSION['asc_code']."', to_location='', job_no='".$jobno."', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."', model_id='".$job_details['model_id']."', partcode='".$expld_pnapart[0]."', part_category='".$expld_pnapart[1]."' , qty='1', status='3', request_date='".$today."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
		}/////end foreach loop
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job PNA","Part Not Available",$_SESSION['userid'],$job_details['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"PNA",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed -2: " . mysqli_error($link1) . ".";
		}
	}
	########## End PNA CASE
	
	### Start EP CASE ########## if repair status select as EP
	else if($_POST['jobstatus'] == "45"){
		////pick max count of a location
		$res_maxcount = mysqli_query($link1,"SELECT COUNT(eid) as maxcnt FROM estimate_master where location_code='".$_SESSION['asc_code']."'");
		$row_maxcount = mysqli_fetch_assoc($res_maxcount);
		//// next estimate no.
		$next_no = $row_maxcount['maxcnt']+1;
		$estimate_no = $jobno."E".$next_no;
		///// get addressess for the parties
		$location_addrs = getAnyDetails($_SESSION['asc_code'],"locationaddress","location_code","location_master",$link1);
		////// insert in estimate master
		$res_estimaster = mysqli_query($link1,"INSERT INTO estimate_master set estimate_no='".$estimate_no."', estimate_date='".$today."', location_code='".$_SESSION['asc_code']."', from_address='".$location_addrs."', to_address='".$job_details['address']."', estimate_amount='".$_POST['ep_new_es']."' , entry_by='".$_SESSION['userid']."', entry_ip='".$_SERVER['REMOTE_ADDR']."', status='5'");
		//// check if query is not executed
		if (!$res_estimaster) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		///// Insert in item data by picking each data row one by one
		
		/////initialize post array variables
		$ep_partarr = $_POST['esti_part'];
		$ep_hsncode = $_POST['ep_hsn_code'];
		$ep_basicamt = $_POST['ep_cost'];
		$ep_taxper = $_POST['ep_taxper'];
		$ep_taxamt = $_POST['ep_taxamt'];
		$ep_totamt = $_POST['ep_totalamt'];
		foreach($ep_partarr as $k => $val){
			///// get addressess for the parties
			$partdetail = getAnyDetails($ep_partarr[$k],"part_name","partcode","partcode_master",$link1); 
			//// insert in estimate data
			$res_estidata = mysqli_query($link1,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='".$ep_partarr[$k]."', hsn_code='".$ep_hsncode[$k]."', part_name='".$partdetail."', basic_amount='".$ep_basicamt[$k]."', tax_per='".$ep_taxper[$k]."', tax_amt='".$ep_taxamt[$k]."' , tax_name='', total_amount='".$ep_totamt[$k]."'");
			//// check if query is not executed
			if (!$res_estidata) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
		}/////end foreach loop
		//// check if any service charge is applicable then we have to insert one more entry in estimate items
		$res_servcharge = mysqli_query($link1,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='SERV001', hsn_code='".$_POST['ser_tax_hsn']."', part_name='Service Charge', basic_amount='".$_POST['ser_charge']."', tax_per='".$_POST['ser_tax_per']."', tax_amt='".$_POST['ser_tax_amt']."' , tax_name='', total_amount='".$_POST['total_ser_tax_amt']."'");
		//// check if query is not executed
		if (!$res_servcharge) {
			 $flag = false;
			 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job EP","Parts Estimation",$_SESSION['userid'],$job_details['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"EP",$ip,$link1,$flag);
	}
	########## End EP CASE
	
	### Start Repair Done CASE ########## if repair status select as Repair Done
	else if($_POST['jobstatus'] == "416"){
	///////  update by priya 0n 1 august to block multiple entry ///////////////////////////////////////////////////
	$messageIdent_conl = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
			//and check it against the stored value:
			$sessionMessageIdent_conl = isset($_SESSION['messageIdent_conl'])?$_SESSION['messageIdent_conl']:'';
			if($messageIdent_conl!=$sessionMessageIdent_conl){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_conl'] = $messageIdent_conl;
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/////initialize post array variables
		$rd_faultarr = $_POST['fault_code'];
		$rd_repairarr = $_POST['repair_code'];
		$rd_repairlvlarr = $_POST['repair_level'];
		$rd_partarr = $_POST['part'];
		$rd_partpricearr = $_POST['part_price'];
		foreach($rd_faultarr as $k => $val){
			////// insert in repair details
			if($rd_partarr[$k]){ $part_replc = "Y";}else{ $part_replc = "N";}
			$partsplit = explode("^",$rd_partarr[$k]);
			
			$res_reapirdata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."', eng_id ='".$_SESSION['userid']."', bin_id='' , status='6', remark='".$_POST['rep_remark']."', fault_code='".$rd_faultarr[$k]."', rep_lvl='".$rd_repairlvlarr[$k]."', part_repl='".$part_replc."', repair_code='".$rd_repairarr[$k]."', partcode='".$partsplit[0]."', part_qty='1', part_cost='".$partsplit[1]."',close_date='".$today."'");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			//// update inventory as user consume part
			
			
			/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($job_details['warranty_status']=='IN'){
			$res_invt = mysqli_query($link1,"UPDATE client_inventory set okqty = okqty-'1', faulty = faulty+'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and okqty >0");
			//// check if query is not executed
			if (!$res_invt) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
			$res_p2cdata = mysqli_query($link1,"INSERT INTO part_to_credit set job_no ='".$jobno."', imei='".$job_details['imei']."',from_location='".$_SESSION['asc_code']."', partcode='".$partsplit[0]."', qty='1', price='".$rd_partpricearr[$k]."',cost='".$rd_partpricearr[$k]."',consumedate='".$today."',model_id='".$job_details['model_id']."',status ='1', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."',type='P2C'");
								if (!$res_p2cdata) {
				// $flag = false;
				 $error_msg = "Error details21: " . mysqli_error($link1) . ".";
			}	
			} else {
						
						$res_invt = mysqli_query($link1,"UPDATE client_inventory set okqty = okqty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
						
						}
			///// entry in stock ledger
			$flag = stockLedger($jobno,$today,$partsplit[0],$_SESSION['asc_code'],$job_details['customer_name'],"OUT","OK","JOB REPAIR","Repair Done","1",$partsplit[1],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
		}/////end foreach loop
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Repair Done","Repair Done",$_SESSION['userid'],$job_details['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"Repair Done",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed -3: " . mysqli_error($link1) . ".";
		}
	}
	########## End Repair Done CASE
	
	### Start Replacement CASE ########## if repair status select as Replacement
	else if($_POST['jobstatus'] == "48"){
	///////////////////////////  update by priya 0n 1 august to block multiple job creation  ////////////////////////////////////////////
	$messageIdent_replacement = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
   		$sessionMessageIdent_replacement = isset($_SESSION['messageIdent_replacement'])?$_SESSION['messageIdent_replacement']:'';
		if($messageIdent_replacement!=$sessionMessageIdent_replacement){//if its different:          
					//save the session var:
			$_SESSION['messageIdent_replacement'] = $messageIdent_replacement;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if($_POST['fault_code_replace']!='' && $_POST['repair_code_replace']!='' && $_POST['replace_model']!='' && $_POST['rep_part']!='' && $_POST['new_imei1']!=''){
			$expld_repcode = explode("~",$_POST['repair_code_replace']);
			////// insert in repair details
			$res_replcedata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."', eng_id ='".$_SESSION['userid']."', bin_id='' , status='6', remark='".$_POST['rep_remark']."', fault_code='".$_POST['fault_code_replace']."', rep_lvl='".$expld_repcode[1]."', part_repl='Y', repair_code='".$expld_repcode[0]."', partcode='".$_POST['rep_part']."', part_qty='1', part_cost='0.00', replace_imei1='".$_POST['new_imei1']."', replace_imei2='".$_POST['new_imei2']."', replace_serial='',close_date='".$today."'");
			//// check if query is not executed
			if (!$res_replcedata) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			//// update inventory as user consume part
			$res_invt = mysqli_query($link1,"UPDATE client_inventory set okqty = okqty-'1', faulty = faulty+'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$_POST['rep_part']."' and okqty >0");
			//// check if query is not executed
			if (!$res_invt) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
			///// entry in stock ledger
			$flag = stockLedger($jobno,$today,$_POST['rep_part'],$_SESSION['asc_code'],$job_details['customer_name'],"OUT","OK","JOB REPAIR","Replacement","1","0.00",$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
		}
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Replacement","Replacement",$_SESSION['userid'],$job_details['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"Replacement",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed-4 : " . mysqli_error($link1) . ".";
		}
	}
	########## End Replacement CASE
	//////////////////////////////// RWR CASE//////////////////////////////////////
	//////////////////  updated by priya on 5 June  /////////////////////////////////////////////////////////////
	else if($_POST['jobstatus'] == "411"){
	
	//// inser SFR Repair Bin details in sfr bin table
		$rep_sfrbin = mysqli_query($link1,"INSERT INTO sfr_repaired_bin set location_code='".$_SESSION['asc_code']."', to_location='".$job_details['location_code']."', job_no='".$job_details['job_no']."', imei='".$job_details['imei']."', model_id='".$job_details['model_id']."',  	part_id='".$job_details['partcode']."', qty='1', entry_date='".$today."', status='417' ,type = 'RWR' ");
		//// check if query is not executed
		if (!$rep_sfrbin) {
			 $flag = false;
			 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
		
		
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"RWR","RWR",$_SESSION['userid'],$job_details['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"RWR at ".$_SESSION['asc_code'],"RWR",$ip,$link1,$flag);
	}
	########## Otherwise go to default case
	else{
		//// nothing to do
		$error_msg = "Nothing to do.";
	}
	///// check if all query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Repair action has been taken successfully.";
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	mysqli_close($link1);
   ///// move to parent page
    header("location:sfr_job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}
?>