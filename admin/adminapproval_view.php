<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po to vendor details
$job_sql="SELECT * FROM supplier_po_master where system_ref_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
@extract($_POST);
if($_POST){
if($_POST['save']=='Approve'){
    $fromlocdet = explode("~",getAnyDetails($job_row['user_code'],"name,bill_address,ship_address ,address ,city,state,pincode,email,gst_no","id","vendor_master",$link1));
    $tolocdet = explode("~",getAnyDetails($job_row['location_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,locationtype","location_code","location_master",$link1));
    $ship_add = explode("~",getAnyDetails($job_row['comp_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,locationtype","location_code","location_master",$link1));
    mysqli_autocommit($link1, false);
    $flag = true;
    $upd_status="update  supplier_po_master set cs_appr_by='".$_SESSION['userid']."',cs_appr_date='".$today."',cs_appr_remark='".$remark."', status='9' where system_ref_no='".$docid."' ";
      $result=mysqli_query($link1,$upd_status);
      $sql_invcount = "SELECT * FROM invoice_counter where location_code='".$job_row['location_code']."'";
			$res_invcount = mysqli_query($link1,$sql_invcount)or die("error1".mysqli_error($link1));
			$row_invcount = mysqli_fetch_array($res_invcount);
			$next_invno = $row_invcount['inv_counter']+1;
			$invoice_no = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
			$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$job_row['location_code']."'");
			if(!$res_upd){
				$flag = false;
				$error_msg = "Error1". mysqli_error($link1) . ".";
				}
				$sgst_final_val=0;
				$cgst_final_val=0;
				$igst_final_val=0;
				$basic_cost=0;
				$total_qty = 0;
				$total_reqqty = 0;
				$total_procqty = 0;
                $sql_bill="SELECT * FROM supplier_po_data where system_ref_no='".$docid."'";
                $rs_bill=mysqli_query($link1,$sql_bill) or die(mysqli_error());
                while($row_bill=mysqli_fetch_assoc($rs_bill)){
                    $part1 = "part".$row_bill['id'];
                    $post_dispqty1 = "qty".$row_bill['id'];				
					$part_code1=explode("~",getAnyDetails($_POST[$part1],"partcode,location_price,l3_price,product_id,brand_id,hsn_code,part_name","partcode","partcode_master",$link1));
					$post_price=$part_code1[2];
						if($_POST[$post_dispqty1]> 0){
								if($part_code1[5]== ""){
									$flag=false;
									$error_msg="HSN Code not found in partcode master".$_POST[$part1];
								}
								$res_tax = mysqli_query($link1,"SELECT id,sgst,igst,cgst FROM tax_hsn_master where hsn_code='".$part_code1[5]."'");
								$row_tax = mysqli_fetch_assoc($res_tax) ;
								if($row_tax['id']==""){
									$flag=false;
									$error_msg="Tax not found in HSN TAX MASTER"." ".$_POST[$part1];
								}
								$linetotal = $post_price * $_POST[$post_dispqty1];
								$cgst_per=0;
								$cgst_val=0;
								$sgst_per=0;
								$sgst_val=0;
								$igst_per=0;
								$igst_val=0;
								$tot_val=0;
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
								$sql_billdata = "INSERT INTO billing_product_items set from_location='".$job_row['user_code']."', to_location='".$job_row['location_code']."',challan_no='".$invoice_no."',request_no='".$docid."',type='GRN', hsn_code='".$part_code1[5]."',partcode='".$part_code1[0]."',part_name='".$part_code1[6]."',qty='".$_POST[$post_dispqty1]."',price='".$post_price."',uom='PCS',value='".$linetotal."',sale_date='".$today."',basic_amt='".$linetotal."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',item_total='".$tot_val."',stock_type='okqty' , product_id ='".$part_code1[3]."' , brand_id = '".$part_code1[4]."'  ";

								//echo $sql_billdata."<br><br>";

								$res_billdata = mysqli_query($link1,$sql_billdata);

								//// check if query is not executed
								if (!$res_billdata) {
									$flag = false;
									$error_msg = "Error details3: " . mysqli_error($link1) . ".";
								}
									 $upd_data="update  supplier_po_data set  status='9'  , update_date  ='".$today."',partcode='".$part_code1[0]."' ,product_id='".$part_code1[3]."',brand_id='".$part_code1[4]."' ,qty='".$_POST[$post_dispqty1]."',cost='".$linetotal."',total_cost='".$tot_val."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."' ,req_qty='".$_POST[$post_dispqty1]."'  where partcode='".$row_bill['partcode']."' ";

								//echo $upd_data."<br><br>";

								$result2=mysqli_query($link1,$upd_data);

								//// check if query is not executed
								if (!$result2) {
									 $flag = false;
									$error_msg = "Error details1: " . mysqli_error($link1) . ".";
								}


								/////// total invoice amount
								$inv_tot_cost = $basic_cost + $cgst_final_val + $sgst_final_val + $igst_final_val;
								$total_qty +=$_POST[$post_dispqty1];
								$total_reqqty += $_POST[$post_dispqty1];
								$total_procqty += $_POST[$post_dispqty1];

						}/// close post dispatch qty if
       			}////// end of for loop	
					//// check dispatch qty should not be zero

						//--------------------------------- inserting in billing_master------------------------------//
						$sql_billmaster = "INSERT INTO billing_master set from_location='".$job_row['user_code']."', to_location='".$job_row['location_code']."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$tolocdet[8]."',party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',po_no='".$docid."',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',logged_by='".$_SESSION['userid']."',billing_rmk='Against PO To Vendor',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,bill_to='".$tolocdet[0]."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='4',document_type='".$_POST['doc_type']."',po_type='GRN',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."',fright_charge ='".$job_row['f_amt']."'";

						//echo $sql_billmaster."<br><br>";

						$res_billmaster = mysqli_query($link1,$sql_billmaster);

						//// check if query is not executed
						if (!$res_billmaster) {
							$flag = false;
							$error_msg = "Error details6: " . mysqli_error($link1) . ".";
						}

					/////////////// update reject status in master table////////////////////////////////////////
					$upd_status_amt="update  supplier_po_master  	set total_sgst_amt='".$cgst_final_val."',total_cgst_amt='".$sgst_final_val."',total_igst_amt='".$igst_final_val."',total_amt='".$inv_tot_cost."',actual_amt='".$basic_cost."',grand_amt ='".$inv_tot_cost."' where system_ref_no='".$docid."' ";

					//echo $upd_status_amt."<br><br>";

					$result1=mysqli_query($link1,$upd_status_amt);

					//// result1 if query is not executed
					if (!$result1) {
						 $flag = false;
						 $error_msg = "Error details7: " . mysqli_error($link1) . ".";
					}
						////// get basic details of both parties
					////// PO dispatcher

				//initialize variables


						////// insert in activity table////
						$flag = dailyActivity($_SESSION['userid'],$invoice_no,"GRN  ","GRN",$ip,$link1,$flag);

						/////////////////// Code for gate entry details table data enter /////////////////////
						$pono = $docid;
						//// Make System generated request no for  gate entry.//////
						$max_sno="select max(sno) as no from gate_entry_detail where location_code='".$job_row['location_code']."'";

						//echo $max_sno."<br><br>";

						$rs3=mysqli_query($link1,$max_sno);
						$row3=mysqli_fetch_array($rs3);
						if($row3['no']>0){
							$req_no=$row3['no']+1;
						}else{
							$req_no= 1;
						}
						$pad=str_pad($req_no,3,"0",STR_PAD_LEFT);
						$request_no="GE".substr($job_row['location_code'],3)."/".substr(date("Y"),2,2)."/".$pad;
						/////////////////////////// insert into master table//////////////////////////////////////////////////////////

						//echo "insert into gate_entry_detail set location_code='".$job_row['location_code']."',entry_date='".$today."',entry_time='".$time."',request_no='".$request_no."',po_no='".$pono."',comp_code='".$_SESSION['asc_code']."',logged_by='".$_SESSION['userid']."' , entry_status = '13' "."<br><br>";

						$master_query= mysqli_query($link1,"insert into gate_entry_detail set location_code='".$job_row['location_code']."',entry_date='".$today."',entry_time='".$time."',request_no='".$request_no."',po_no='".$pono."',comp_code='".$_SESSION['asc_code']."',logged_by='".$_SESSION['userid']."' , entry_status = '13' ");

						///////// get last insert id////////////////////////////////////////	
						$ins_id=mysqli_insert_id($link1);
						////// check query is executed or not /////////////////////
						if (!$master_query) {
							$flag = false;
							$error_msg = "Error details1: " . mysqli_error($link1) . ".";
						}
						///////// update flag  and status in  supplier master table

						//echo "update supplier_po_master set status= '1' ,gate_entry_flag='Y' where system_ref_no='".$pono."' "."<br><br>";

						$upd_flag = mysqli_query($link1,"update supplier_po_master set status= '1' ,gate_entry_flag='Y' where system_ref_no='".$pono."' " );

						/////////////// check if query is executed or not/////////////////////
						if (!$upd_flag) {
							$flag = false;
							$error_msg =  "Error details3: " . mysqli_error($link1) . ".";
						}
						///////// update status in  supplier data table

						//echo "update supplier_po_data set status= '1',update_date='".$today."' where system_ref_no='".$pono."' "."<br><br>";

						$upd_st = mysqli_query($link1,"update supplier_po_data set status= '1',update_date='".$today."' where system_ref_no='".$pono."' " );

						/////////////// check if query is executed or not/////////////////////
						if (!$upd_st) {
							$flag = false;
							$error_msg =  "Error details4: " . mysqli_error($link1) . ".";
						}	
						//////////////////// end of the date /////////////////////////////////////////////////

		////// insert in activity table////
		
		$flag=dailyActivity($_SESSION['asc_code'],$docid,"Vender PO","Approved",$_SERVER['REMOTE_ADDR'],$link1,$flag);	
		
		$msg = "Approved by Admin";
		
	if ($flag) {
        mysqli_commit($link1);  
		$cflag = "success";
		$cmsg = "Success";  
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
    header("location:appr_po_admin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
	
	
}

if ($_POST['reject']=='Reject') {
////// initialize parameter ////////////////////////////////
	mysqli_autocommit($link1, false);
	$flag = true;
	/////////////// update reject status in master table////////////////////////////////////////
	 $upd_status="update  supplier_po_master set cs_appr_by='".$_SESSION['userid']."',cs_appr_date='".$today."',cs_appr_remark='".$remark."', status='5' where system_ref_no='".$docid."' ";
    $result=mysqli_query($link1,$upd_status);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	/////////// update reject status in data table///////////////////////////////////////
	$upd_data="update  supplier_po_data set  status='5'  , update_date ='".$today."' where system_ref_no='".$docid."' ";
    $result2=mysqli_query($link1,$upd_data);
	
	
	//// check if query is not executed
	if (!$result2) {
	     $flag = false;
       $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
	
	$flag=dailyActivity($_SESSION['asc_code'],$docid,"Vender PO","Rejected",$_SERVER['REMOTE_ADDR'],$link1,$flag);	
	$msg = "Rejected by Admin";
		if ($flag) {
        mysqli_commit($link1);  
		$cflag = "success";
		$cmsg = "Success";  
    } else {
		mysqli_rollback($link1);
			$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	   ///// move to parent page
 header("location:appr_po_admin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
       include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-ship"></i> PO to Vendor Approval</h2>
      <h4 align="center">PO No.- <?=$docid?></h4>
	  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['party_name'],"name","id","vendor_master",$link1);?></td>
                <td width="20%"><label class="control-label">Bill From</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['location_code'],"locationname","location_code","location_master",$link1);?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Bill To</label></td>
                <td><?php echo getAnyDetails($job_row['bill_to'],"locationname","location_code","location_master",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php  echo getdispatchstatus($job_row["status"])?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $job_row['system_ref_no'];?></td>
                <td><label class="control-label">PO  Date.</label></td>
                <td><?php echo dt_format($job_row['entry_date']);?></td>
              </tr>
			     <tr>
                <td><label class="control-label">Type.</label></td>
                <td><?php echo $job_row['voucher_type'];?></td>
                <td><label class="control-label">Document Type.</label></td>
                <td><?=$job_row['ship_type']?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Approve/Reject Date.</label></td>
                <td><?php echo dt_format($job_row['cs_appr_date']);?></td>
                <td><label class="control-label">Approve/Reject Remarks.</label></td>
                <td><?=$job_row['cs_appr_remark']?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive" >
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Items Details</div>
         <div class="panel-body">
       <table class="table table-bordered table-hover" width="100%">
            <thead>
              <tr>
                <th width="3%" style="text-align:center">#</th>
                <th width="8%" style="text-align:center">Product</th>
                <th width="8%" style="text-align:center">Brand</th>
                <th width="10%" style="text-align:center">Model</th>
                <th width="14%" style="text-align:center">Partcode</th>
                <th width="7%" style="text-align:center">Qty</th>
       
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM supplier_po_data where system_ref_no='".$job_row['system_ref_no']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row['partcode'],"part_name","partcode","partcode_master",$link1));
			?>
              <tr align="left">
                <td><?=$i?></td>
                <td><?=$proddet[0];?></td>
                <td><?=$brand[0];?></td>
                <td><?=$model[0];?></td>
                <td><select name="part<?=$podata_row['id']?>" class="form-control required"  id="part<?=$podata_row['id']?>">
                 <?php 
                 $cus_query="SELECT * FROM alt_part_map where status = '1' and partcode='".$podata_row['partcode']."' order by partcode";
                 $check_cust=mysqli_query($link1,$cus_query);

?>
                   <option value="<?=$podata_row['partcode']?>"><?=$part[0]?></option>
			<?php	while($br_cust = mysqli_fetch_array($check_cust)){

$part_alter=explode("~",getAnyDetails($br_cust['alter_partcode'],"part_name","partcode","partcode_master",$link1));

				?>
                          <option value="<?=$br_cust['alter_partcode']?>"><?php echo $part_alter['0']?></option>
                          <?php  }?>
                        </select>
                
                
                
                
                </td>
                <td align="right"><input type="text" value="<?=$podata_row['qty']?>" name="qty<?=$podata_row['id']?>" id="qty<?=$podata_row['id']?>"  class="form-control required" /><input type="hidden" value="INV" name="doc_type" id="doc_type" value="<?=$job_row['ship_type']?>"></td>
                
                </tr>
      
		<?php }	?>
			  	<?php if($job_row['status']=='7'){?>
			<tr><td  align="right"  colspan="4">Remark</td><td colspan="8"><textarea name="remark" id="remark"  class=" form-control" ><?=$job_row['cs_appr_remark']?></textarea></td></tr><?php }?>
			 <tr>
                <td width="100%" align="center" colspan="12">
				<?php if($job_row['status']=='7'){?>
				 <input title="save" type="submit" class="btn btn<?=$btncolor?>" id = "save"  name= "save" value="Approve" >
				 <input title="reject" type="submit" class="btn btn<?=$btncolor?>" id = "reject"  name= "reject" value="Reject" >
				<?php  }?>
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='appr_po_admin.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>