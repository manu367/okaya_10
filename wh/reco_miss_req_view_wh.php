<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////

$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=="Request Reconciliation"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	$sql_invcount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";
		$res_invcount = mysqli_query($link1,$sql_invcount)or die("error1".mysqli_error($link1));
		$row_invcount = mysqli_fetch_array($res_invcount);
		$next_invno = $row_invcount['dn_counter']+1;
		/////update next counter against invoice
		$res_upd = mysqli_query($link1,"UPDATE invoice_counter set dn_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");
		/// check if query is execute or not//
		if(!$res_upd){
			$flag = false;
			$error_msg = "Error1". mysqli_error($link1) . ".";
		}
		///// make invoice no.
		$invoice_no = $row_invcount['inv_series']."".$row_invcount['fy']."".'DN'."".str_pad($next_invno,4,"0",STR_PAD_LEFT);	
	//////intialize tax variables
	$sgst_final_val=0;
	$cgst_final_val=0;
	$igst_final_val=0;
	$basic_cost=0;
	$total_qty = 0;
	$total_reqqty = 0;
	$total_procqty = 0;
	////////////////////////////////////Dispatch Details/////////////////////////////
	$sql_bill_data="select * from billing_master where  challan_no='".$po_no."'";
    $res_billData=mysqli_query($link1,$sql_bill_data)or die("error1".mysqli_error());
	$row_billData=mysqli_fetch_assoc($res_billData);
	
	////// PO dispatcher
	$fromlocdet = explode("~",getAnyDetails($row_billData['to_location'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
	////// PO receiver
	$tolocdet = explode("~",getAnyDetails($row_billData['from_location'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
	////// fetching data from data table//////////////////////////////////////////////////////////////////////////////////////////
    $sql_po_data="select * from billing_product_items where missing_reco='' and missing >0 and challan_no='".$po_no."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
    while($row_poData=mysqli_fetch_assoc($res_poData)){
	
	   	$res_part = mysqli_query($link1,"SELECT hsn_code,part_name FROM partcode_master where partcode='".$row_poData['partcode']."' and status='1'");
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
				$linetotal = $row_poData['price'] * $row_poData['missing'];	
				////// initialize line tax variables
				$cgst_per=0;
				$cgst_val=0;
				
				$sgst_per=0;
				$sgst_val=0;
				
				$igst_per=0;
				$igst_val=0;
				
				$tot_val=0;
				//// check if dispatcher and receiver belongs to same state then tax should be apply as SGST&CGST (In india) 
				if($row_billData['from_stateid'] == $row_billData['to_stateid']){
				//----------------------------- CGST & SGST Applicable----------------------//
					if($row_billData['document_type']=='INV'){
						$cgst_per = $row_poData['cgst_per'];
						$sgst_per = $row_poData['sgst_per'];
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
					if($row_billData['document_type']=='INV'){
						$igst_per = $row_poData['igst_per'];
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
     			$sql_billdata = "INSERT INTO billing_product_items set from_location='".$row_poData['to_location']."', to_location='".$row_poData['from_location']."',challan_no='".$invoice_no."',request_no='".$docid."',job_no='".$row_poData['job_no']."',type='".$row_poData['type']."', hsn_code='".$row_part['hsn_code']."',partcode='".$row_poData['partcode']."',part_name='".$row_part['part_name']."',qty='".$row_poData['missing']."',okqty='".$row_poData['missing']."',price='".$row_poData['price']."',uom='PCS',value='".$linetotal."',basic_amt='".$linetotal."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',item_total='".$tot_val."',stock_type='missing',opration_rmk ='".$row_billData['challan_no']."'";
				$res_billdata = mysqli_query($link1,$sql_billdata);
				//// check if query is not executed
				if (!$res_billdata) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
	
		 if($row_poData['missing']!="" && $row_poData['missing']!=0 && $row_poData['missing']!=0.00){
			    $req_miss_reco = mysqli_query($link1,"UPDATE billing_product_items set missing_reco = 'R' where id='".$row_poData['id']."'");
				if(!$req_miss_reco){
					$flag = false;
					$error_msg = "Error details7:req_miss_reco " . mysqli_error($link1) . ".";
				}
		  }
		  
		   $flag=stockLedger($invoice_no,$today,$row_poData['partcode'],$row_poData['to_location'],$row_poData['from_location'],"OUT","MISSING","Debit Note  Against Missing $row_billData[challan_no]","" ,$row_poData['missing'],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
		  
		  	$inv_tot_cost = $basic_cost + $cgst_final_val + $sgst_final_val + $igst_final_val;
	}
	
	
	//// close while loop  
		//--------------------------------- inserting in billing_master------------------------------//
	$sql_billmaster = "INSERT INTO billing_master set from_location='".$row_billData['to_location']."', to_location='".$row_billData['from_location']."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$tolocdet[8]."',
party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',

logged_by='".$_SESSION['userid']."',po_no='".$row_billData['challan_no']."',billing_rmk='Debit Note  Against Missing  ".$row_billData['challan_no']."',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,bill_to='".$tolocdet[0]."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='5',document_type='Debit Note',finvoice_no='".$_POST['finvoice_no']."',po_type='".$pomaster['potype']."',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."'";
	$res_billmaster = mysqli_query($link1,$sql_billmaster);
	//// check if query is not executed
	if (!$res_billmaster) {
    	$flag = false;
    	$error_msg = "Error details6: " . mysqli_error($link1) . ".";
	}
	
	
	///// update credit limit of receiver
	$res_cr = mysqli_query($link1,"UPDATE current_cr_status set credit_bal = credit_bal - '".$inv_tot_cost."', total_credit_limit = total_credit_limit - '".$inv_tot_cost."' where location_code='".$row_billData['from_location']."'");
	if(!$res_cr){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}
	
	
	
	
	////// insert in location account ledger
	$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$row_billData['from_location']."',entry_date='".$today."',remark='".$invoice_no."', transaction_type = 'Debit Note  Against Missing  ".$row_billData['challan_no']."',month_year='".date("m-Y")."',crdr='DR',amount='".$inv_tot_cost."'");
	if(!$res_ac_ledger){
		$flag = false;
		$error_msg = "Error details8: " . mysqli_error($link1) . ".";
	}
	//// Update status in  master table
 	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_no,"Missing Reconciliation Request","Request Generate",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Faulty Return Received for ".$po_no;
		$cflag="success";
		$cmsg="Success";
    } else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
   header("location:reco_mss_dmd_list_at_wh.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  exit;
 }
 }
 
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-book"></i>Missing Parts (Aginst PO) Reconciliation<br/><br />
   </h2>
      <div class="panel-group">
        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    
     
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Damage Items Information</div>
      <div class="panel-body">
        <table width="100%" height="104" class="table table-bordered">
          <thead>
          </thead>
          <tr class="<?=$tableheadcolor?>">
            <td width="3%">S.No</td>
            <td width="10%">Challan No.</td>
            <td width="10%">Job No.</td>
            <td width="15%">Part</td>
            <td width="8%">Price</td>
            <td width="7%"> Qty</td>
            <td width="9%">Value</td>
           
          </tr>
          <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_product_items where missing_reco='' and missing >0  and challan_no='".$po_no."' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
            <tr>
              <td><?=$i?></td>
              <td><?=$po_no;?></td>
              <td><?=$data_row['job_no'];?></td>
              <td><?=getAnyDetails($data_row['partcode'],"part_name","partcode" ,"partcode_master",$link1);?>-(<?=$data_row['partcode']?>)</td>
              <td><?=$data_row['price'];?></td>
              <td><?=$data_row['missing'];?></td>
              <td style="text-align:right"><?php $value=$data_row['missing']*$data_row['price']; echo $value;?></td>
             
            </tr>
            <?php
			$total+=$value;
			$i++;
			}
			?>
          </tbody>
        </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Receive</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>          
               <tr>
			   <td><label class="control-label">Total Amount</label></td>
                 <td>
                 <input type="text" name="tot_amt" id="tot_amt" class="number form-control required"   value="<?=$total;?>"  readonly/></td>
                   <td></td>
                 <td></td>
                   
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="Request Reco" value="Request Reconciliation" title="Request Reconciliation">&nbsp;
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='reco_mss_dmd_list_at_wh.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    </form>
</div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
