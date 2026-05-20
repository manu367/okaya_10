<?php
error_reporting(E_All);
require_once("../includes/config.php");

/////get status//
@extract($_POST);
$browserid=session_id();
	//////  if we want to Add new po
   if ($_POST['upd']=='Process'){
   ////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
   //// pick max count of grn
    
								$data_sql1="select from_location,to_location,challan_no,doc_type,remark,ref_challan_no,ref_challan_date,type from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."' and challan_no='".$_POST['challan_no']."' group by challan_no ";							
								$data_res1=mysqli_query($link1,$data_sql1);
								while($data_row1=mysqli_fetch_assoc($data_res1)){ 
								
   
		$res_grncount = mysqli_query($link1,"SELECT fy,grn_counter from invoice_counter where location_code='".$data_row1['to_location']."'");
		$row_grncount = mysqli_fetch_assoc($res_grncount);
	///// make grn sequence
		$nextgrnno = $row_grncount['grn_counter'] + 1;
		$grnno = "LP".substr($data_row1['to_location'],3)."/".$row_grncount['fy'].str_pad($nextgrnno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$upd = mysqli_query($link1,"UPDATE invoice_counter set grn_counter='".$nextgrnno."' where location_code='".$data_row1['to_location']."'");
		//// check if query is not executed
		if (!$upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
	//////////////////////////////////////////
		$from_details  = explode('~' ,getAnyDetails($data_row1['from_location'],"address,gst_no,state,name","id","vendor_master",$link1));
        $to_details  = explode("~",getAnyDetails($data_row1['to_location'],"locationaddress,gstno,stateid,deliveryaddress","location_code" ,"location_master",$link1));
 
			/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////
 		$grn_master="insert into grn_master set location_code ='".$data_row1['to_location']."', party_code ='".$data_row1['from_location']."',receive_date='".$today."' , entry_date_time='".$datetime."' , status='2' , grn_no='".$grnno."', grn_type='LOCAL PURCHASE' , remark='".$data_row1['remark']."',comp_code='".$data_row1['to_location']."',update_by='".$_SESSION['userid']."',ip_address='".$_SERVER['REMOTE_ADDR']."',p_type='".$data_row1['type']."',ref_challan_no='".$data_row1['ref_challan_no']."',ref_challan_date='".$data_row1['ref_challan_date']."' ";
		$result5=mysqli_query($link1,$grn_master);
		//// check if query is not executed
		if (!$result5) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		////////////// insert data into billing master TABLE ///////////////////////////////////////////////
		$sgst_final_val=0;
      $cgst_final_val=0;
     $igst_final_val=0;
    $basic_cost=0;
 	 	$bill_master="insert into billing_master set from_location ='".$data_row1['from_location']."', to_location ='".$data_row1['to_location']."' ,party_name ='".$from_details[3]."' ,  challan_no='".$grnno."' ,sale_date='".$today."', entry_date='".$date."' , status='2' , from_gst_no='".$from_details[1]."' , to_gst_no = '".$to_details[1]."' , from_stateid = '".$from_details[2]."'  , to_stateid= '".$to_details[2]."'   ,po_type= 'LOCAL PURCHASE', from_addrs='".$from_details[0]."', disp_addrs='".$from_details[0]."', to_addrs='".$to_details[0]."', deliv_addrs='".$to_details[3]."',p_type='".$data_row1['type']."',ref_challan_no='".$data_row1['ref_challan_no']."',ref_challan_date='".$data_row1['ref_challan_date']."'";
		$result6=mysqli_query($link1,$bill_master);
		//// check if query is not executed
		if (!$result6) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}
		///// Insert in item data by picking each data row one by one
		       $data_tem1="select partcode,qty,price,challan_no,value,qc_fail_qty from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'";							
								$data_tem_reus=mysqli_query($link1,$data_tem1);
								while($data_tem_item=mysqli_fetch_assoc($data_tem_reus)){   
	    	// checking row value of product and qty should not be blank
			
			if($data_tem_item['partcode']!='' && $data_tem_item['qty']!='' && $data_tem_item['price']!='' && $grnno==$data_row1['challan_no']) {
			
			$partdet = explode("~",getAnyDetails($data_tem_item['partcode'] , "hsn_code,part_name,product_id,brand_id,model_id" ,"partcode", "partcode_master" ,$link1));
		  	$tax_info = mysqli_fetch_array(mysqli_query($link1, "select cgst,sgst, igst from tax_hsn_master where  hsn_code = '".$partdet[0]."' ")) ;	
			/////////// insert  GRN data
	    	$query2="insert into grn_data set   grn_no  ='".$grnno."' ,product_id ='".$partdet[2]."', brand_id ='".$partdet[3]."', model_id ='".$partdet[4]."',hsn_code='".$partdet[0]."', partcode='".$data_tem_item['partcode']."', part_name='".$partdet[1]."', shipped_qty='".$data_tem_item['qty']."', okqty='".$data_tem_item['qty']."'   ,price = '".$data_tem_item['price']."' , amount = '".$data_tem_item['value']."'  ,type='LOCAL PURCHASE' ,p_type='".$data_row1['type']."',ref_challan_no='".$data_row1['ref_challan_no']."',ref_challan_date='".$data_row1['ref_challan_date']."',qc_fail_qty='".$data_tem_item['qc_fail_qty']."' ";
		 	$result = mysqli_query($link1, $query2);
		   	//// check if query is not executed
		   	if (!$result) {
	        	$flag = false;
              	$error_msg = "Error details4: " . mysqli_error($link1) . ".";
			}		   
		   	/////////// insert  BILLING PRODUCT data
		   	if($from_details[2] == $to_details[2] ){
		   		$cgstamt = ($tax_info['cgst']*$data_tem_item['value'])/100;
		   		$sgstamt = ($tax_info['sgst']*$data_tem_item['value'])/100;
		        $toa_val=$cgstamt+$sgstamt+$data_tem_item['value'];
		  		$bill_data="insert into billing_product_items set  from_location='".$data_row1['from_location']."'  ,to_location='".$data_row1['to_location']."' , challan_no  ='".$grnno."' ,product_id ='".$partdet[2]."', brand_id ='".$partdet[3]."', model_id ='".$partdet[4]."', partcode ='".$data_tem_item['partcode']."', part_name='".$partdet[1]."', hsn_code= '".$partdet[0]."' ,cgst_per= '".$tax_info['cgst']."' ,sgst_per= '".$tax_info['sgst']."' , cgst_amt= '".$cgstamt ."' , sgst_amt= '".$sgstamt."'  , type='LOCAL PURCHASE' , price='".$data_tem_item['price']."' ,value ='". $toa_val."' , item_total = '".$toa_val."' ,qty ='".$data_tem_item['qty']."' ,okqty='".$data_tem_item['qty']."' ,p_type='".$data_row1['type']."',ref_challan_no='".$data_row1['ref_challan_no']."',ref_challan_date='".$data_row1['ref_challan_date']."',qc_fail_qty='".$data_tem_item['qc_fail_qty']."'";
		 		$result3 = mysqli_query($link1, $bill_data);
		  		//// check if query is not executed
		   		if (!$result3) {
	         		$flag = false;
              		$error_msg = "Error details5: " . mysqli_error($link1) . ".";
				}	    
			}
		   	else{
		   		$igstamt = ($tax_info['igst']*$data_tem_item['value'])/100;
				$toa_val=$igstamt+$data_tem_item['value'];
		  		$bill_data="insert into billing_product_items set  from_location='".$data_row1['from_location']."'  ,to_location='".$data_row1['to_location']."' , challan_no  ='".$grnno."' ,product_id ='".$partdet[2]."', brand_id ='".$partdet[3]."', model_id ='".$partdet[4]."', partcode ='".$partcode[$k]."', part_name='".$data_tem_item['partcode']."', hsn_code= '".$partdet[0]."' ,igst_per= '".$tax_info['igst']."'  , igst_amt= '".$igstamt ."'  , type='LOCAL PURCHASE' , price='".$data_tem_item['price']."' ,value ='".$toa_val."' , item_total = '".$toa_val."' ,qty ='".$data_tem_item['qty']."' ,okqty='".$data_tem_item['qty']."',p_type='".$data_row1['type']."',ref_challan_no='".$data_row1['ref_challan_no']."',ref_challan_date='".$data_row1['ref_challan_date']."' ,qc_fail_qty='".$data_tem_item['qc_fail_qty']."'";
		 		$result3 = mysqli_query($link1, $bill_data);
		  		//// check if query is not executed
		   		if (!$result3) {
	         		$flag = false;
              		$error_msg = "Error details6: " . mysqli_error($link1) . ".";
				}		   		   
			}
		   	/////////////////////// check whether partcode and location code exist in client inventory or not //////////////////////
			$check = mysqli_query($link1 , "select location_code , partcode from client_inventory where location_code = '".$data_row1['to_location']."'  and partcode = '".$data_tem_item['partcode']."' ");
			if(mysqli_num_rows($check)>0){ 
				////////////// update  okqty in client inventory table //////////////////////////////////////////////////////////	 
	   			$client   = mysqli_query($link1 , " update  client_inventory set okqty=okqty+'".$data_tem_item['qty']."',qc_fail_qty=qc_fail_qty+'".$data_tem_item['qc_fail_qty']."' where partcode = '".$data_tem_item['partcode']."' and  location_code = '".$data_row1['to_location']."' "	);	   
			}
			else {
				////////////// insert  okqty in client inventory table //////////////////////////////////////////////////////////	 
	  			$client   = mysqli_query($link1 , " insert into  client_inventory set okqty=okqty+'".$data_tem_item['qty']."',qc_fail_qty=qc_fail_qty+'".$data_tem_item['qc_fail_qty']."' , partcode = '".$data_tem_item['partcode']."' ,  location_code = '".$data_row1['to_location']."',  	updatedate = '".$datetime."' ");	   
			}
			//// check if query is not executed
		   	if (!$client) {
	        	$flag = false;
               	$error_msg = "Error details7: " . mysqli_error($link1) . ".";
			}			 
			/////////////////// insert in stock ledger////				 
			$flag=stockLedger($grnno,$today,$data_tem_item['partcode'],$data_row1['from_location'],$data_row1['to_location'],"IN","OK","Local Purchase","Receive",$data_tem_item['qty'],$data_tem_item['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);   
			}// close if loop of checking row value of product and qty challan
			
			 else {
			    $flag = false;
				$error_msg = "Challan partcode condistion check ".$data_row1['challan_no'];
			       }// close if loop of checking row value of product and qty should not be blank
		$cgst_final_val=$cgst_final_val+$cgstamt;
		$sgst_final_val=$cgst_final_val+$sgstamt;
		$igst_final_val=$cgst_final_val+$igstamt;
		$basic_cost=$basic_cost+$data_tem_item['value'];
		$inv_tot_cost=$inv_tot_cost+$$toa_val;
		
		}/// close for loop
		
		//////////////////////////////
			$wh_billin2="update billing_master set basic_cost='".$basic_cost."', total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."',status='4' where challan_no='".$grnno."' and to_location ='".$data_row1['to_location']."'";
			$wh_billing_qry=mysqli_query($link1,$wh_billin2);
			//// check if query is not executed
			if (!$wh_billing_qry) {
				$flag = false;
				$error_msg = "Billing data not upadte " . mysqli_error($link1) . ".";
			}
			//////////////////////////////
			$grn_master2="update grn_master set cost='".$inv_tot_cost."',status='4' where grn_no='".$grnno."' and location_code ='".$data_row1['to_location']."'";
			$grn_billing_qry=mysqli_query($link1,$grn_master2);
			//// check if query is not executed
			if (!$grn_billing_qry) {
				$flag = false;
				$error_msg = "GSR data not upadte " . mysqli_error($link1) . ".";
			}
		
		////// insert in location account ledger
		$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$_SESSION['asc_code']."',entry_date='".$today."',remark='".$data_row1['remark']."', transaction_type = 'LOCAL PURCHASE',transaction_no='".$grnno."',month_year='".date("m-Y")."',crdr='DR',amount='".$toa_val."'");
		if(!$res_ac_ledger){
			$flag = false;
			$error_msg = "Error details8: " . mysqli_error($link1) . ".";
		}
		////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $grnno, "LOCAL PURCHASE", "ADD", $ip, $link1, $flag);
		///// check both master and data query are successfully executed
		
   
								} /////close while loop
					/////// final delete all data from tem 
					 
					$result_temp=mysqli_query($link1,"delete from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
					//// check if query is not executed
						if (!$result_temp) {
	 		 		   $flag = false;
       				   $error_msg = "temp data not delete: " . mysqli_error($link1) . ".";
   						 }
					 			
                             
   if ($flag) {
        	mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
        	$msg = "Local Purchase  is successfully placed with ref. no.".$grnno;
    	} else {
		
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again." .$error_msg ;
			mysqli_autocommit($link1, true);
			$result_temp=mysqli_query($link1,"delete from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
			
		} 
		
    	mysqli_close($link1);
	   	///// move to parent page
  		header("location:grn_local.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
   
   }
   //// if user hit cancel button
	if($_POST['cancel']=='Cancel'){
	mysqli_autocommit( $link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"delete from temp_disp_upd where  userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Temp data not delete:";
	}
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "All Excel Uploaded Data has been deleted.";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	}
	mysqli_close($link1);
	///// move to parent page
	header("location:grn_local.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  
    exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
<!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
	$(document).ready(function(){
		$("#frm2").validate();
	});
	$(document).ready(function(){
    
	///// Search Show and Remove (use true and false)
		$('#myTable').dataTable( {
		  "searching": false
		} );
	});	
 </script>
 
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/common_js.js"></script>


</head>
<body>
	<div class="container-fluid">
 		<div class="row content">
		<?php 
    	include("../includes/leftnavemp2.php");
    	?>
   		<div class="<?=$screenwidth?> tab-pane fade in active">
      		
   			<div class="panel-group">
			  
		 <h2 align="center"><i class="fa fa-upload"></i> Local Purchase Details</h2>
      <h4 align="center" style="color:#060">Step 1 is completed (Excel file is uploaded) .</h4>
      <h4 align="center" style="color:#FF9900">Step 2 Please Go for next process or cancel uploaded data.</h4>
      <h4 align="center" style="color:#FF0000">Do Not Refersh while process is being execute.</h4>
	  <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
          </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
   			<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    			<div class="panel panel-info table-responsive">
    			<div class="panel panel-info table-responsive">
      				<div class="panel-heading">Uploded Information</div>
      					<div class="panel-body">
       						<table class="table table-bordered" width="100%"  id="myTable">
            					<thead>
                                	<tr class="<?=$tableheadcolor?>"> 
              							<td width="4%">S.No</td>
										<td width="20%">From Location Name</td>
                                        <td width="20%">To Location </td>
              							<td width="20%">Partcode Name</td>
                                        <td width="6%">Qty</td>
                                        <td width="10%">Price</td>
                                        <td width="10%">Reference Challan</td>
										 <td width="10%">Type</td>
            						</tr>
            					</thead>
            					<tbody>
            					<?php
								$i=1;
								$data_sql="select challan_no,from_location,to_location,partcode,qty,price,ref_challan_no,type from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'";
								$data_res=mysqli_query($link1,$data_sql);
								while($data_row=mysqli_fetch_assoc($data_res)){
								?>
              						<tr><input type="hidden" name="challan_no" id="challan_no" class="form-control" value="<?php echo $data_row['challan_no']; ?>"  readonly/>
                						<td><?=$i?></td>
										<td><?php echo getAnyDetails($data_row["from_location"],"name","id","vendor_master",$link1)."(".$data_row['from_location'].")";?></td>
										<td><?php echo getAnyDetails($data_row["to_location"],"locationname","location_code","location_master",$link1)."(".$data_row['to_location'].")";?></td>
                						<td ><?php echo getAnyDetails($data_row["partcode"],"part_name","partcode","partcode_master",$link1)."(".$data_row['partcode'].")";?></td>
              							<td><?php echo $data_row['qty']; ?></td>
              							
              							<td><?php echo $data_row['price']; ?></td>   
										<td><?php echo $data_row['ref_challan_no']; ?></td>   
              							
              						
			  							<td><?php echo $data_row['type']; ?></td>           
                					</tr>
            					<?php
									//$total+= $data_row['total_cost'];
									$i++;
								}
								?>
                                   
              				
            					</tbody>
          					</table>
							<div style="text-align:center;"><input type="submit" class="btn btn-success" name="upd" id="upd" value="Process" title="Process">&nbsp;
                                       
                   							<input type="submit" class="btn btn-danger" name="cancel" id="cancel" value="Cancel" title="Cancel Uploaded Data" onClick="return myConfirm();"></div>
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
</html>

