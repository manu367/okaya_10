<?php
require_once("../includes/config.php");
if($_POST['upd']=='Generate'){

///////////////////  update by priya on 13 august to block multiple entry ////////////////////////////////////////////////////////////////////////////////////////////////
$messageIdentclaim = md5($_REQUEST['location_code'] . $_REQUEST['claim_month'] );
//and check it against the stored value:
    $sessionMessageIdentclaim = isset($_SESSION['messageIdentclaim'])?$_SESSION['messageIdentclaim']:'';

    if($messageIdentclaim!=$sessionMessageIdentclaim){//if its different:          
        //save the session var:
            $_SESSION['messageIdentclaim'] = $messageIdentclaim;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

mysqli_autocommit($link1, false);
$flag = true;
$err_msg = "";
/* 	$claim_sql = "SELECT * FROM job_claim_appr  where action_by = '".$_REQUEST['location_code']."'  and  app_status='Y' and  ".$daterange." ";
	$claim_res = mysqli_query($link1,$claim_sql);
	$claimdata_row = mysqli_fetch_assoc($claim_res);*/
	
	$month = $_POST['month'];
	$year   = $_POST['year'];
	$locationname  = $_POST['location_code'];
//echo "select claim_mnth , location_code from claim_invoice where claim_mnth LIKE '%".$_POST['claim_month']."%' and location_code = '".$locationname."'  and status != '5' ";
  $numcount	= mysqli_query($link1, "select claim_mnth , location_code from claim_invoice where claim_mnth LIKE '%".$_POST['claim_month']."%' and location_code = '".$locationname."'  and status != '5' and po_type='ENTITY TRAVEL CLAIM' ");
  
  
 if (mysqli_num_rows($numcount) == 0) {
	
	
		$sql_invcount = "SELECT * FROM invoice_counter where location_code='".$_REQUEST['location_code']."'";
		$res_invcount = mysqli_query($link1,$sql_invcount)or die("error1".mysqli_error($link1));
		$row_invcount = mysqli_fetch_array($res_invcount);
		$next_invno = $row_invcount['inv_counter']+1;
		/////update next counter against invoice
		$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_REQUEST['location_code']."'");
		/// check if query is execute or not//
		if(!$res_upd){
			$flag = false;
			$error_msg = "Error1". mysqli_error($link1) . ".";
		}
		///// make invoice no.
		$invoice_no = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
		
		
			$fromlocdet = explode("~",getAnyDetails($_REQUEST['location_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
			$map_loc=$_REQUEST['map_location_code'];
	////// Claim invoice address
	$tolocdet = explode("~",getAnyDetails($map_loc,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
		
			//////intialize tax variables
	$sgst_final_val=0;
	$cgst_final_val=0;
	$igst_final_val=0;
	$basic_cost=0;
	$total_qty = 0;
	$total_reqqty = 0;
	$total_procqty = 0;
	/////// get po items details	
	//echo "jitu";
	
	for ($i=1;$i<$_POST['row_count'];$i++){
		
		 $model_cat="model_cat".$i;
		 $rep_lvl="rep_lvl".$i;
		 $count_lvl="count_lvl".$i;
		 $level_price="level_price".$i;
		 $cost_avl="cost_avl".$i;
		 	$res_tax = mysqli_query($link1,"SELECT id,sgst,igst,cgst FROM tax_hsn_master where hsn_code='76767674'");
        		$row_tax = mysqli_fetch_assoc($res_tax) ;
				if($row_tax['id']==""){
					$flag=false;
					$error_msg="Tax not found in HSN TAX MASTER";
				}
				
					$cgst_per=0;
				$cgst_val=0;
				
				$sgst_per=0;
				$sgst_val=0;
				
				$igst_per=0;
				$igst_val=0;
				
				$tot_val=0;
				//// check if dispatcher and receiver belongs to same state then tax should be apply as SGST&CGST (In india) 
				if($_POST['gst']!=""){
				if($fromlocdet['5'] == $tolocdet['5']){
				//----------------------------- CGST & SGST Applicable----------------------//
				
						$cgst_per = $row_tax['cgst'];
						$sgst_per = $row_tax['sgst'];
					
					/////// calculate cgst and sgst	
					$cgst_val = ($cgst_per * $_POST[$cost_avl] ) / 100;
					$cgst_final_val = $cgst_final_val + $cgst_val;
					
					$sgst_val = ($sgst_per * $_POST[$cost_avl]) / 100;
					$sgst_final_val = $sgst_final_val + $sgst_val;

					$basic_cost = $basic_cost + $_POST[$cost_avl] ;	
					$tot_val = $_POST[$cost_avl] + $cgst_val + $sgst_val;	
				}else{//// check if dispatcher and receiver belongs to different state then tax should be apply as IGST (In india) 
					//----------------------------- IGST Applicable----------------------//
					
						$igst_per = $row_tax['igst'];
				
					/////// calculate igst
					$igst_val = ($igst_per * $_POST[$cost_avl]) / 100;
					$igst_final_val = $igst_final_val + $igst_val;
				
					$basic_cost = $basic_cost + $_POST[$cost_avl];
					$tot_val = $_POST[$cost_avl] + $igst_val;
				}
				}
				else {
					
											$cgst_per = 0;
						$sgst_per = 0;
						
					$igst_per =0;
					/////// calculate cgst and sgst	
					$cgst_val = ($cgst_per * $_POST[$cost_avl] ) / 100;
					$cgst_final_val = $cgst_final_val + $cgst_val;
					
					$sgst_val = ($sgst_per * $_POST[$cost_avl]) / 100;
					$sgst_final_val = $sgst_final_val + $sgst_val;

				
					$tot_val = $_POST[$cost_avl] + $cgst_val + $sgst_val;
						$igst_per = 0;
				
					/////// calculate igst
					$igst_val = ($igst_per * $_POST[$cost_avl]) / 100;
					$igst_final_val = $igst_final_val + $igst_val;
				
					$basic_cost = $basic_cost + $_POST[$cost_avl];
					$tot_val = $_POST[$cost_avl] + $igst_val;
					
					}
					
	
	 
		 $sql_claim = "INSERT INTO claim_invoice set location_code='".$_REQUEST['location_code']."',claim_no ='".$invoice_no."',level ='".$_POST[$rep_lvl]."',tot_lvl='".$_POST[$count_lvl]."',price='".$_POST[$level_price]."',value='".$_POST[$cost_avl]."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',total_cost ='".$tot_val."',claim_date ='".$today."',claim_mnth='".$_POST['claim_month']."',cat='".$_POST[ $model_cat]."' , status = '4' ";
				$res_claim = mysqli_query($link1,$sql_claim);
				//// check if query is not executed
				if (!$res_claim) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
				
					/////// total invoice amount
			
				$inv_tot_cost = $basic_cost + $cgst_final_val + $sgst_final_val + $igst_final_val;
		
		}
		
		
		
		
	//--------------------------------- inserting in billing_master------------------------------//
	$sql_billmaster = "INSERT INTO billing_master set from_location='".$_REQUEST['location_code']."', to_location='".$map_loc."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$tolocdet[8]."',
party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',
logged_by='".$_SESSION['userid']."',billing_rmk='Against Travel Claim',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,bill_to='".$tolocdet[0]."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='4',document_type='DC',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."',po_type='ENTITY TRAVEL CLAIM' ,claim_month='".$_POST['claim_month']."' "; 
	$res_billmaster = mysqli_query($link1,$sql_billmaster);
	//// check if query is not executed
	if (!$res_billmaster) {
    	$flag = false;
    	$error_msg = "Error details6: " . mysqli_error($link1) . ".";
		
		
	}

	 $narreation= "Travel Claim" ."-".$_POST['claim_month_eg'];
	///////////////////////////////update Claim TAble against job///////////////////
	//echo "UPDATE job_claim_appr set claim_no='".$invoice_no."'  where action_by = '".$_REQUEST['location_code']."'  and  app_status='Y' and hand_date like '%".$_POST['claim_month']."%'";
		$res_job_claim = mysqli_query($link1,"UPDATE job_claim_appr set et_claim_no='".$invoice_no."'  where action_by = '".$_REQUEST['location_code']."'  and  app_status='Y' and hand_date like '%".$_POST['claim_month']."%'");
	if(!$res_job_claim){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}
	
	///// update credit limit of receiver
	$res_cr = mysqli_query($link1,"UPDATE current_cr_status set claim_amt  = claim_amt + '".$inv_tot_cost."' where location_code='".$_REQUEST['location_code']."'");
	if(!$res_cr){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}
	////// insert in location account ledger
//	echo "INSERT INTO location_account_ledger set location_code='".$_REQUEST['location_code']."',entry_date='".$today."',remark='".$invoice_no."', transaction_type = 'Monthly Claim',month_year='".date("m-Y")."',crdr='CR',amount='".$inv_tot_cost."'";
	$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$_REQUEST['location_code']."',entry_date='".$today."',remark='".$invoice_no."', transaction_type = '".$narreation."',month_year='".$_POST['claim_month_eg']."',crdr='CR',amount='".$inv_tot_cost."',transaction_no ='".$invoice_no."'");
	if(!$res_ac_ledger){
		$flag = false;
		$error_msg = "Error details8: " . mysqli_error($link1) . ".";
	}
}
else {
		$msg = "Request could not be processed. Claim is already generated for this month";
		$cflag = "danger";
		$cmsg = "Failed";
header("location:claim_generate_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;

}
if ($flag) {
        mysqli_commit($link1);
        $msg = "Claim has been  generated";
		$cflag = "success";
		$cmsg = "Success";
    } 
	
	else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$error_msg;
		$cflag = "danger";
		$cmsg = "Failed";
	} 
    mysqli_close($link1);
}else {
mysqli_rollback($link1);
	$cflag = "danger";
		$cmsg = "Failed";
			$msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";

}
	   ///// move to parent page
 header("location:claim_generate_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
   exit;
   }
?>
