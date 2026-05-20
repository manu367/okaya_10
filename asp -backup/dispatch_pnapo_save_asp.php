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
	$pomaster = mysqli_fetch_assoc(mysqli_query($link1,"SELECT from_code,to_code,potype FROM po_master where po_no='".$docid."'"));
	//// check selected document type
	if($_POST['doc_type']=="INV"){
		//////pick max counter of INVOICE
		$sql_invcount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";
		$res_invcount = mysqli_query($link1,$sql_invcount)or die("error1".mysqli_error($link1));
		$row_invcount = mysqli_fetch_array($res_invcount);
		$next_invno = $row_invcount['inv_counter']+1;
		/////update next counter against invoice
		$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");
		/// check if query is execute or not//
		if(!$res_upd){
			$flag = false;
			$error_msg = "Error1". mysqli_error($link1) . ".";
		}
		///// make invoice no.
		$invoice_no = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);	
		$doctype_flag *= 1;
	}else if($_POST['doc_type']=="DC"){
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
		$doctype_flag *= 1;
	}else{
		$doctype_flag *= 0;
	}
	///// check if any one doctype is selected
	if($doctype_flag==1){
	////// get basic details of both parties
	////// PO dispatcher
	$fromlocdet = explode("~",getAnyDetails($pomaster['to_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
	////// PO receiver
	$tolocdet = explode("~",getAnyDetails($pomaster['from_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
	
	//////intialize tax variables
	$sgst_final_val=0;
	$cgst_final_val=0;
	$igst_final_val=0;
	$basic_cost=0;
	$total_qty = 0;
	$total_reqqty = 0;
	$total_procqty = 0;
	/////// get po items details
	$podata_sql = "SELECT * FROM po_items where po_no='".$docid."'";
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
				$cgst_per=0;
				$cgst_val=0;
				
				$sgst_per=0;
				$sgst_val=0;
				
				$igst_per=0;
				$igst_val=0;
				
				$tot_val=0;
				//// check if dispatcher and receiver belongs to same state then tax should be apply as SGST&CGST (In india) 
				if($fromlocdet['5'] == $tolocdet['5']){
				//----------------------------- CGST & SGST Applicable----------------------//
					if($_POST['doc_type']=='INV'){
						$cgst_per = $row_tax['cgst'];
						$sgst_per = $row_tax['sgst'];
					}else{
						$cgst_per = "0";
						$sgst_per = "0";
					}
					/////// calculate cgst and sgst	
					$cgst_val = ($cgst_per * $linetotal) / 100;
					$cgst_final_val = $cgst_final_val + $cgst_val;
					
					$sgst_val = ($sgst_per * $linetotal) / 100;
					$sgst_final_val = $sgst_final_val + $sgst_val;

					$basic_cost = $basic_cost + $linetotal;	
					$tot_val = $linetotal + $cgst_val + $sgst_val;	
				}else{//// check if dispatcher and receiver belongs to different state then tax should be apply as IGST (In india) 
					//----------------------------- IGST Applicable----------------------//
					if($_POST['doc_type']=='INV'){
						$igst_per = $row_tax['igst'];
					}else{
						$igst_per = "0";
					}
					/////// calculate igst
					$igst_val = ($igst_per * $linetotal) / 100;
					$igst_final_val = $igst_final_val + $igst_val;
				
					$basic_cost = $basic_cost + $linetotal;
					$tot_val = $linetotal + $igst_val;
				}
				//--------------------------------- inserting in  billing_product_items------------------------------//
     			$sql_billdata = "INSERT INTO billing_product_items set from_location='".$pomaster['to_code']."', to_location='".$pomaster['from_code']."',challan_no='".$invoice_no."',request_no='".$docid."',job_no='".$podata_row['job_no']."',type='".$pomaster['potype']."', hsn_code='".$row_part['hsn_code']."',partcode='".$podata_row['partcode']."',part_name='".$row_part['part_name']."',qty='".$_POST[$post_dispqty]."',okqty='".$_POST[$post_dispqty]."',price='".$_POST[$post_price]."',uom='PCS',value='".$linetotal."',basic_amt='".$linetotal."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',item_total='".$tot_val."',stock_type='okqty'";
				$res_billdata = mysqli_query($link1,$sql_billdata);
				//// check if query is not executed
				if (!$res_billdata) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
				//----------------------------- po data upadate------------------------//
				if(($_POST[$post_dispqty]+$podata_row['processed_qty']) == $podata_row['qty']){ $linestatus = 2;}else{ $linestatus = 6;}
     			$sql_poitems = "UPDATE po_items set processed_qty = processed_qty + '".$_POST[$post_dispqty]."', process_challan='".$invoice_no."',status='".$linestatus."' where id='".$podata_row['id']."' ";
				$res_poitems = mysqli_query($link1,$sql_poitems);
				//// check if query is not executed
				if (!$res_poitems) {
					$flag = false;
					$error_msg = "Error details4: " . mysqli_error($link1) . ".";
				}
  				//----------------------------- inventory upadate------------------------//
    			$sql_invt = "UPDATE client_inventory set okqty = okqty - '".$_POST[$post_dispqty]."',updatedate='" . $datetime . "' where partcode='".$podata_row['partcode']."' and location_code='".$pomaster['to_code']."' and  okqty >= '".$_POST[$post_dispqty]."'";
   				$res_invt = mysqli_query($link1,$sql_invt);
			  	//// check if query is not executed
			  	if (!$res_invt) {
					$flag = false;
					$error_msg = "Error details5: " . mysqli_error($link1) . ".";
				}
				
			//////////////////////// for IN transit QTY update///////////////////////////////// 
			  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$podata_row['partcode']."' and location_code='".$pomaster['from_code']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set  	in_transit= in_transit+'".$_POST[$post_dispqty]."',updatedate='".$datetime."' where partcode='".$podata_row['partcode']."' and location_code='".$pomaster['from_code']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$pomaster['from_code']."',partcode='".$podata_row['partcode']."',in_transit='".$_POST[$post_dispqty]."',updatedate='".$datetime."'");
		  }	
				
				
				
				///// entry in stock ledger
				if($_POST[$post_dispqty]>0){
					$flag = stockLedger($invoice_no,$today,$podata_row['partcode'],$_SESSION['asc_code'],$pomaster['from_code'],"OUT","OK","PO Dispatch","Process",$_POST[$post_dispqty],$_POST[$post_price],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				}
				//// update substatus of jobsheet data 
				if($podata_row['job_no']!="" && $_POST[$post_dispqty]>0){
					$result2 = mysqli_query($link1," update jobsheet_data set sub_status = '32'  where job_no ='".$podata_row['job_no']."'");
					//// check if query is not executed
					if (!$result2) {
						$flag = false;
						$err_msg = "Error details5.0: " . mysqli_error($link1) . ".";
					}
					///// entry in call/job  history
					$flag = callHistory($podata_row['job_no'],$_SESSION['asc_code'],"32","PNA PO Processed","Part Processed",$_SESSION['userid'],"","",$ip,$link1,$flag);
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
 	echo $sql_billmaster = "INSERT INTO billing_master set from_location='".$pomaster['to_code']."', to_location='".$pomaster['from_code']."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$tolocdet[8]."',
party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',po_no='".$docid."',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',
docket_no='".$_POST['docket_no']."',courier='".$_POST['courier_name']."',
logged_by='".$_SESSION['userid']."',billing_rmk='Against ".$pomaster['potype']."',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,bill_to='".$tolocdet[0]."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='2',document_type='".$_POST['doc_type']."',finvoice_no='".$_POST['finvoice_no']."',po_type='".$pomaster['potype']."',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."'"; 
	$res_billmaster = mysqli_query($link1,$sql_billmaster);
	//// check if query is not executed
	if (!$res_billmaster) {
    	$flag = false;
    	$error_msg = "Error details6: " . mysqli_error($link1) . ".";
	}
	///// update credit limit of receiver
	$res_cr = mysqli_query($link1,"UPDATE current_cr_status set credit_bal = credit_bal - '".$inv_tot_cost."', total_credit_limit = total_credit_limit - '".$inv_tot_cost."' where location_code='".$pomaster['from_code']."'");
	if(!$res_cr){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}
	
	
	
	
	////// insert in location account ledger
	$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$pomaster['from_code']."',entry_date='".$today."',remark='".$invoice_no."', transaction_type = 'PO Process',month_year='".date("m-Y")."',crdr='DR',amount='".$inv_tot_cost."'");
	if(!$res_ac_ledger){
		$flag = false;
		$error_msg = "Error details8: " . mysqli_error($link1) . ".";
	}
	////// update status in po master
	if($total_reqqty == $total_procqty && $total_reqqty !=0 && $total_procqty !=0){ $status = 2;}else{ $status = 6;}
	$res_pomaster = mysqli_query($link1,"UPDATE po_master set status='".$status."' where po_no='".$docid."'");
	if(!$res_pomaster){
		$flag = false;
		$error_msg = "Error details9: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$invoice_no,"PO Dispatch ","Dispatch",$ip,$link1,$flag);
	}else{
		$flag = false;
		$error_msg = "Error details10: Document type is not selected.";
	}
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
header("location:invoice_list_p2c.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&status=1");
	exit;   
	 }

}
?>
