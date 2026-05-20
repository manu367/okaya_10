<?php
require_once("../includes/config.php");
$docid=base64_decode($_POST['refid']);
////// after hitting dispatch button
if($_POST['dispatchpo']=="Dispatch" && $docid!='' && $_SESSION['asc_code']!=''){

////////////////////  update by priya on 20 july to block multiple entry ///////////////////////////////////////////////////////////////////////////////////////
$messageIdent_whdispatch = md5($_SESSION['asc_code'] .$docid .$_POST['dispatchpo']);
	//and check it against the stored value:
   	$sessionMessageIdent_whdispatch = isset($_SESSION['messageIdent_whdispatch'])?$_SESSION['messageIdent_whdispatch']:'';
	if($messageIdent_whdispatch!=$sessionMessageIdent_whdispatch){//if its different:          
				//save the session var:
		$_SESSION['messageIdent_whdispatch'] = $messageIdent_whdispatch;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
	$doctype_flag = 1;
	//// pick po master details

	//// check selected document type

		//////pick max counter of INVOICE
		$sql_dccount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";
		$res_dccount = mysqli_query($link1,$sql_dccount)or die("error1".mysqli_error($link1));
		$row_dccount = mysqli_fetch_array($res_dccount);
		$next_dcno = $row_dccount['stn_counter']+1;
		/////update next counter against invoice
		$res_upd = mysqli_query($link1,"UPDATE invoice_counter set stn_counter = '".$next_dcno."' where location_code='".$_SESSION['asc_code']."'");
		/// check if query is execute or not//
		if(!$res_upd){
			$flag = false;
			$error_msg = "Error1". mysqli_error($link1) . ".";
		}
		///// make invoice no.
		$invoice_no = $row_dccount['stn_series']."".$row_dccount['fy']."".str_pad($next_dcno,4,"0",STR_PAD_LEFT);
		
	///// check if any one doctype is selected

	////// get basic details of both parties
	////// PO dispatcher
	$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
	////// PO receiver
	$tolocdet = explode("~", getAnyDetails($docid,"locusername,userloginid","userloginid","locationuser_master",$link1));
	
	//////intialize tax variables
	$sgst_final_val=0;
	$cgst_final_val=0;
	$igst_final_val=0;
	$basic_cost=0;
	$total_qty = 0;
	$total_reqqty = 0;
	$total_procqty = 0;
	/////// get po items details
	$podata_sql = "SELECT * FROM part_demand where eng_id='".$docid."'";
	$podata_res = mysqli_query($link1,$podata_sql);
	while($podata_row = mysqli_fetch_assoc($podata_res)){
		////initialize post variables
		$post_dispqty = "disp_qty".$podata_row['id'];
		$post_price = "price".$podata_row['id'];
		///// if post dispatch qty is more than zero
		if($_POST[$post_dispqty] > 0){
			/////check inventory again
			$avlqty = getInventory($_SESSION['asc_code'],$podata_row['partcode'],"okqty",$link1);
			if($avlqty >= $_POST[$post_dispqty]){
				////////check hsn code of part
				$res_part = mysqli_query($link1,"SELECT hsn_code,part_name FROM partcode_master where partcode='".$podata_row['partcode']."' and status='1'");
				$row_part = mysqli_fetch_assoc($res_part) ;
				if($row_part['hsn_code'] == ""){
					$flag=false;
					$error_msg="HSN Code not found in partcode master";
				}
        		//  get tax on HSN Code
				$res_tax = mysqli_query($link1,"SELECT id,sgst,igst,cgst FROM tax_hsn_master where hsn_code='".$row_part['hsn_code']."'");
        		$row_tax = mysqli_fetch_assoc($res_tax) ;
				if($row_tax['id']==""){
					$flag=false;
					$error_msg="Tax not found in HSN TAX MASTER".$row_part['hsn_code'];
				}
		  		///// calculate line total
				$linetotal = $_POST[$post_price] * $_POST[$post_dispqty];	
				////// initialize line tax variables
			
				
				$tot_val=0;
			
		$basic_cost = $basic_cost + $linetotal;	
					$tot_val = $linetotal ;
			
				//--------------------------------- inserting in  billing_product_items------------------------------//
     			$sql_billdata = "INSERT INTO stn_items set from_location='".$_SESSION['asc_code']."', to_location='".$docid."',challan_no='".$invoice_no."',job_no='".$podata_row['job_no']."',type='ISSUE-TO-ENG', hsn_code='".$row_part['hsn_code']."',partcode='".$podata_row['partcode']."',part_name='".$row_part['part_name']."',qty='".$_POST[$post_dispqty]."',okqty='".$_POST[$post_dispqty]."',price='".$_POST[$post_price]."',uom='PCS',value='".$linetotal."',basic_amt='".$linetotal."',item_total='".$tot_val."'";
				$res_billdata = mysqli_query($link1,$sql_billdata);
				//// check if query is not executed
				if (!$res_billdata) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
				//----------------------------- po data upadate------------------------//
				if(($_POST[$post_dispqty]) == $podata_row['qty']){ $linestatus = 2;}else{ $linestatus = 6;}
     			$sql_poitems = "UPDATE part_demand set remark ='".$invoice_no."',status='".$linestatus."' where id='".$podata_row['id']."' ";
				$res_poitems = mysqli_query($link1,$sql_poitems);
				//// check if query is not executed
				if (!$res_poitems) {
					$flag = false;
					$error_msg = "Error details4: " . mysqli_error($link1) . ".";
				}
  				//----------------------------- inventory upadate------------------------//
    			$sql_invt = "UPDATE client_inventory set okqty = okqty - '".$_POST[$post_dispqty]."', mount_qty =mount_qty +'".$_POST[$post_dispqty]."',updatedate='" . $datetime . "' where partcode='".$podata_row['partcode']."' and location_code='".$_SESSION['asc_code']."' and  okqty >= '".$_POST[$post_dispqty]."'";
   				$res_invt = mysqli_query($link1,$sql_invt);
			  	//// check if query is not executed
			  	if (!$res_invt) {
					$flag = false;
					$error_msg = "Error details5: " . mysqli_error($link1) . ".";
				}
				
			//////////////////////// for IN transit QTY update///////////////////////////////// 
			  if(mysqli_num_rows(mysqli_query($link1,"select partcode from user_inventory where partcode='".$podata_row['partcode']."' and location_code='".$_SESSION['asc_code']."' and  	locationuser_code ='".$docid."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result_inv=mysqli_query($link1,"update user_inventory set  okqty = okqty + '".$_POST[$post_dispqty]."',updatedate='".$datetime."' where partcode='".$podata_row['partcode']."' and locationuser_code='".$docid."' and location_code='".$_SESSION['asc_code']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result_inv=mysqli_query($link1,"insert into user_inventory     set location_code='".$_SESSION['asc_code']."',partcode='".$podata_row['partcode']."',okqty='".$_POST[$post_dispqty]."',updatedate='".$datetime."',locationuser_code='".$docid."'");
		  }	
				
					  	if (!$result_inv) {
					$flag = false;
					$error_msg = "Error details5: " . mysqli_error($link1) . ".";
				}
				
				///// entry in stock ledger
				if($_POST[$post_dispqty]>0){
					$flag = stockLedger($invoice_no,$today,$podata_row['partcode'],$_SESSION['asc_code'],$docid,"OUT","OK","Issue To Eng","Againt Part Dimand",$_POST[$post_dispqty],$_POST[$post_price],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
						$flag=stockLedger($invoice_no,$today,$podata_row['partcode'],$_SESSION['asc_code'],$docid,"IN","OK","Recieve Stock From Location","Againt Part Dimand",$_POST[$post_dispqty],$_POST[$post_price],$docid,$today,$currtime,$ip,$link1,$flag);   
					
				}
				//// update substatus of jobsheet data 
				if($podata_row['job_no']!="" && $_POST[$post_dispqty]>0){
				        $rs_job=mysqli_query($link1,"select status from jobsheet_data where job_no ='".$podata_row['job_no']."'") or die(mysqli_error());	
				$row_job=mysqli_fetch_assoc($rs_job);	
				if($row_job['status']=='3'){	
					$result2 = mysqli_query($link1," update jobsheet_data set sub_status = '32'  where job_no ='".$podata_row['job_no']."'");
					//// check if query is not executed
					if (!$result2) {
						$flag = false;
						$err_msg = "Error details5.0: " . mysqli_error($link1) . ".";
					}
					///// entry in call/job  history
					$flag = callHistory($podata_row['job_no'],$_SESSION['asc_code'],"32","Part Issue","Part Processed",$_SESSION['userid'],"","","","",$ip,$link1,$flag);
					}
				}
				/////// total invoice amount
				$inv_tot_cost = $basic_cost + $cgst_final_val + $sgst_final_val + $igst_final_val;
				$total_qty += $_POST[$post_dispqty];
				$total_reqqty += $podata_row['qty'];
				$total_procqty += $podata_row['processed_qty'];
			}///close inventory check if
		}/// close post dispatch qty if
	}///// close item while loop
	//// check dispatch qty should not be zero
	if($total_qty == 0){
		$flag = false;
		$error_msg = "Error details5.1: You are dispatch 0 qty";
	}
	//--------------------------------- inserting in billing_master------------------------------//
 	echo $sql_billmaster = "INSERT INTO stn_master set from_location='".$_SESSION['asc_code']."', to_location='".$docid."',
party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',po_type= 'ISSUE-TO-ENG',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',
docket_no='".$_POST['docket_no']."',courier='".$_POST['courier_name']."',
logged_by='".$_SESSION['userid']."',billing_rmk='Against Part Demend',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='4',document_type='DC',finvoice_no='".$_POST['finvoice_no']."',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."'"; 
	$res_billmaster = mysqli_query($link1,$sql_billmaster);
	//// check if query is not executed
	if (!$res_billmaster) {
    	$flag = false;
    	$error_msg = "Error details6: " . mysqli_error($link1) . ".";
	}

	
	
	

	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$invoice_no,"PO Dispatch ","Dispatch",$ip,$link1,$flag);

	///// check if all query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "PO is processed successfully.";
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	mysqli_close($link1);
   ///// move to parent page
    header("location:invoice_list_p2c.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
	}
	else {
        //you've sent this already!
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
header("location:assgin_part_user.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&status=1");
	exit;   
	 }

}
?>
