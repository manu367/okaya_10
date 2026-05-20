<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."' and from_location='".$_SESSION['asc_code']."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

@extract($_POST);
//////////////// upload excel file for IMEI upload
if($_POST['Submit']=="Upload")
{
	barCheck($link1);
	////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	if ($_FILES["fileupload"]["error"] > 0){
		$code=$_FILES["fileupload"]["error"];
	}
	else{
		move_uploaded_file($_FILES["fileupload"]["tmp_name"],"../ExcelExportAPI/upload_invoice/".$now.$_FILES["fileupload"]["name"]);
		$file="../ExcelExportAPI/upload_invoice/".$now.$_FILES["fileupload"]["name"];
		$fsave_name="".$now.$_FILES["fileupload"]["name"];
		//chmod ($file, 0755);
	}
	$filename=$file;
	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
	error_reporting(E_ALL ^ E_NOTICE);
 	$path = '../ExcelExportAPI/Classes/';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
    function __autoload($classe){
            $var = str_replace
            (
                '_', 
                DIRECTORY_SEPARATOR,
                $classe
            ) . '.php' ;
            require_once($var);
	}
 	$indentityType = PHPExcel_IOFactory::identify($filename);
    $object = PHPExcel_IOFactory::createReader($indentityType);
    $object->setReadDataOnly(true);
    $objPHPExcel = $object->load($filename);             
	$sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
	$highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
	$highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
	$indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
	$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
	///////////////////////// initialize parameter	
	$flag = 1;
	///// pick perticular part details
	$billing_part = mysqli_fetch_assoc(mysqli_query($link1,"select * from billing_product_items where id='".base64_decode($_POST['refsno'])."'"));
	//// check grn qty vs uploading qty
	if($billing_part['qty'] == ($highest-1)){
		//importing files to the database
		for($row =2 ;$row <= $highest;$row++){
			$imei1 = $sheet->getCellByColumnAndRow(0,$row)->getValue();
			$imei2 = $sheet->getCellByColumnAndRow(1,$row)->getValue();
			///// check serial no. in stock
			echo "select id,status from imei_details where imei1='".$imei1."'  and location_code='".$_SESSION['asc_code']."' and status ='1' ";
			$res = mysqli_query($link1,"select id,status from imei_details where imei1='".$imei1."'  and location_code='".$_SESSION['asc_code']."' and status ='1' ");
			$row_checkstock=mysqli_fetch_assoc($res);
			if($row_checkstock['id'] =='' ) {
				$flag = false;
				$error_msg = "Error details1:Stock is not availbale";
			}else{
				//inserting query into data base
				
				echo "update imei_details set status='3',dis_date='".$today."',challan_no='".$billing_part['challan_no']."' where location_code='".$_SESSION['asc_code']."' and ( imei1 ='".$imei1."' or imei2='".$imei2."' )";		
				$result = mysqli_query($link1,"update imei_details set status='3',dis_date='".$today."',challan_no='".$billing_part['challan_no']."' where location_code='".$_SESSION['asc_code']."' and  imei1 ='".$imei1."' ");
				//// check if query is not executed
				if (!$result) {
					$flag = false;
					$error_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
			//	///// update serial no. inventroy
			
			echo "INSERT INTO imei_history set imei1='".$imei1."',imei2='".$imei2."',partcode='".$billing_part['partcode']."',transaction_no='".$billing_part['challan_no']."',remark='Dispatched' ,location_code='".$_SESSION['asc_code']."' ";
			$result2 = mysqli_query($link1,"INSERT INTO imei_history set imei1='".$imei1."',imei2='".$imei2."',partcode='".$billing_part['partcode']."',transaction_no='".$billing_part['challan_no']."',remark='Dispatched' ,location_code='".$_SESSION['asc_code']."' "); 
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
			}
		}///// end of for loop
		/////// update attachment flag	
			$checkboxqty=mysqli_fetch_assoc(mysqli_query($link1,"select COUNT(id) as dispqty from imei_details where challan_no='".$billing_part['challan_no']."' and partcode='".$billing_part['partcode']."'"));	
		if($checkboxqty['dispqty'] == $billing_part['qty']){
			$result3 = mysqli_query($link1,"update billing_product_items set attach_file='".$fsave_name."',imei_attach='Y' where id='".$billing_part['id']."'");
			//// check if query is not executed
			if (!$result3) {
				$flag = false;
				$error_msg = "Error details4: " . mysqli_error($link1) . ".";
			}
		}
	}else{
		$flag = false;
        $error_msg = "Error details:5 Uploading qty does not match with invoice qty.";
	}
 	if ($flag) {

    	mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded.";
    } else {

		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. ".$error_msg;
	} 

    mysqli_close($link1);
	   ///// move to parent page
	header("Location:invoice_challan_view.php?refid=".base64_encode($docid)."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&doc_type=".$_REQUEST['doc_type']."&msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <style type="text/css">
	.modal-dialogTH{
		overflow-y: initial !important
	}
	.modal-bodyTH{
		max-height: calc(100vh - 212px);
		overflow-y: auto;
	}
	.modalTH {
	  width: 1000px;
	  margin: auto;
	}
 </style>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-book"></i> Invoices/Challans Details</h2><br/>
	   <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Party Information</div>
         <div class="panel-body">
         
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Billing To</label></td>
                <td width="30%">
				  <?php 
				  /// bill to party
				  echo $po_row['to_location'];?></td>
                <td width="20%"><label class="control-label">Billing From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getAnyDetails($po_row['from_location'],"locationname,cityid,stateid","location_code","location_master",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Billing Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo $po_row['logged_by'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo getdispatchstatus($po_row['status']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                     <td><?php echo getAnyDetails($po_row["to_stateid"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $po_row['deliv_addrs'];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="8%">HSN Code</th>
                <th style="text-align:center" width="8%">Bill Qty</th>
                <th style="text-align:center" width="8%">Price</th>                
                <th style="text-align:center" width="11%">Discount/<br>Unit</th>
                <th style="text-align:center" width="8%">Value After Discount</th>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                <th style="text-align:center" width="12%">SGST(%)</th>
                <th style="text-align:center" width="12%">SGST Amount</th>
                <th style="text-align:center" width="12%">CGST(%)</th>
                <th style="text-align:center" width="12%">CGST Amount</th>
                <?php }else{ ?>
                <th style="text-align:center" width="12%">IGST(%)</th>
                <th style="text-align:center" width="12%">IGST Amount</th>
                <?php }?>
                <th style="text-align:center" width="15%">Total</th>
                 <th style="text-align:center" width="15%">TAG /<?php echo SERIALNO ?></th>
               
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$total_qty=0;
			$podata_sql="SELECT * FROM billing_product_items where challan_no='".$docid."' and from_location='".$po_row['from_location']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$proddet=explode("~",getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,part_category","partcode","partcode_master",$link1));
			$make_partname = $proddet[0]."&nbsp;&nbsp;|&nbsp;&nbsp;".$model[0];
			$total_qty += $podata_row['qty'];
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$podata_row['partcode'].")"?></td>
                <td style="text-align:right"><?=$proddet[3]?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['discount_amt']?></td>
                <td style="text-align:right"><?=$podata_row['price'] - $podata_row['discount_amt']*$podata_row['qty']?></td>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <?php }else{ ?>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <?php }?>
                <td style="text-align:right"><?=$podata_row['item_total']?></td>
                <td> <?php 
			 if($proddet[4]=="UNIT" || $proddet[4]=="BOX" || $proddet[4]=="TAG"){
			 	if($podata_row['imei_attach']==""){
				 if($podata_row['type']=="PO"){ $grntype="PO";}else{ $grntype="";}
				 ?><a href="#" onClick="uploadIMEI('<?=$podata_row['id']?>');" title="Upload IMEI"><i class="fa fa-upload fa-lg faicon"></i></a>&nbsp;&nbsp;<!--<a href="invoice_imei.php?refid=<?=base64_encode($podata_row['challan_no'])?>&partcode=<?=base64_encode($podata_row['partcode'])?>&pqty=<?=base64_encode($podata_row['qty'])?>&partname=<?=base64_encode($make_partname)?>&model_id=<?=base64_encode($podata_row['model_id'])?>&grn_type=<?=$grntype?>&status=<?=$po_row['status']?>&doc=<?=$po_row['doc_type']?><?=$pagenav?>" title="Enter IMEI"><i class="fa fa-shower fa-lg faicon"></i></a>-->
				 <?php 
				}else{
					echo "Attached";
				}
			 }else{
				 echo "Not Applicable";
			 }
			?></td>
                 
              </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-default table-responsive">
      <div class="panel-heading">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo currencyFormat($po_row['basic_cost']);?></td>
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><?php echo currencyFormat($po_row['discount_amt']);?></td>
              </tr>
              <tr>                
                <td><label class="control-label">Round Off</label></td>
                <td><?php echo currencyFormat($po_row['round_off']);?></td>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['total_cost']);?></td>
              </tr>
               <tr>
                 <td><strong>Total Qty</strong></td>
                 <td><?=$total_qty?></td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
				<tr>
					<td><label class="control-label">Delivery Address</label></td>
					<td><?php echo $po_row['deliv_addrs'];?></td>
					<td><label class="control-label">Remark</label></td>
					<td><?php echo $po_row['billing_rmk'];?></td>
				</tr>
				
				<tr>
					<td><label class="control-label">Image Attachement</label></td>
					<td>
						<?php
						if($po_row['attach_a'] != "")
						{
						?>
						<div style="max-width:100%;margin:0px auto;">
							<img src="<?=$po_row['attach_a'];?>" alt="Attachement" style="display:block;max-width:inherit;margin:0px auto">
							<button type="button" class="btn btn-primary" name="" id="" onclick= "window.open('<?=$po_row['attach_a'];?>', '_blank');" title="View" style="width:100%;margin-top:5px;background:#33b767;border-color:#149b49;"><i class="fa fa-external-link" style="color:#fff;" aria-hidden="true"></i> View</button>
						</div>
						<?php
						}
						else
						{
							echo "-";
						}
						?>					
					</td>
					<td><label class="control-label">Video Attachement</label></td>
					<td>
						<?php
						if($po_row['attach_b'] != "")
						{
						?>
						<video width="720" height="240" controls style="max-width:100%;margin:0px auto;background:#000;">
							<source src="<?=$po_row['attach_b'];?>" type="video/mp4">
							Your browser does not support the video tag.
						</video>
						<button type="button" class="btn btn-primary" name="" id="" onclick= "window.open('<?=$po_row['attach_b'];?>', '_blank');" title="View" style="width:100%;background:#33b767;border-color:#149b49;"><i class="fa fa-external-link" style="color:#fff;" aria-hidden="true"></i> View</button>
						<?php
						}
						else
						{
							echo "-";
						}
						?>
					</td>
				</tr>
				
              <?php if($po_row['status']=="Cancelled"){ ?> 
              <tr>
				 <td><label class="control-label">Cancel By</label></td>
                 <td><?php echo getAdminDetails ($po_row['cancel_by'],"name",$link1);?></td>
				 <td><label class="control-label">Cancel Date</label></td>
                 <td ><?php echo dt_format ($po_row['cancel_date']);?></td>
				 </tr>
				<tr>                 
				 <td><label class="control-label">Cancel Remark</label></td>
                 <td colspan="3"><?php echo $po_row['cancel_rmk'];?></td>
                </tr>
			  <?php }?>
              
            <!--  <tr>  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
              <td>Imei Upload</td>
              <td><label >  
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                      
                         <input name="challan" type="hidden" class="form-control" id="challan" value="<?=$docid?>"/>	
                          <input name="location" type="hidden" class="form-control" id="challan" value="<?=$po_row['to_location']?>"/>					          
                    </span>
                    </label></td>
                    <td> <div style="display:inline-block;float:right"><a href="../templates/PO_DISPATCH_IMEI.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div></td>
                 <td colspan="4" align="center"><input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>></td>
                 </form>
                </tr>
              -->
              
              <tr>
              
              
                 <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='invoice_list.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>&doc_type=<?=$_REQUEST['doc_type']?><?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <?php /*?><div class="panel panel-default table-responsive">
      <div class="panel-heading">Logistic Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Logistic Name</label></td>
                <td width="30%"><?php echo getLogistic($po_row['diesel_code'],$link1);?></td>
                <td width="20%"><label class="control-label">Docket No.</label></td>
                <td width="30%"><?php echo $po_row['docket_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Logistic Person</label></td>
                <td><?php echo $po_row['logistic_person'];?></td>
                <td><label class="control-label">Contact No.</label></td>
                <td><?php echo $po_row['logistic_contact'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Carrier No.</label></td>
                 <td><?php echo $po_row['vehical_no'];?></td>
                 <td><label class="control-label">Dispatch Date</label></td>
                 <td><?php echo $po_row['dc_date'];?></td>
               </tr>
               <tr>
                <td><label class="control-label">Dispatch Remark</label></td>
                <td colspan="3"><?php echo $po_row['disp_rmk'];?></td>
                </tr>
               <tr>
                 <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='retailbillinglist.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel--><?php */?>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
////// function for open model to see the task history
function uploadIMEI(sno){
	$.get('invoiceupload_imei.php?refid=' + sno, function(html){
		 $('#updimei .modal-body').html(html);
		 $('#updimei').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<!-- Start upload imei Modal -->
          <div class="modal modalTH fade" id="updimei" role="dialog">
            <div class="modal-dialog modal-dialogTH modal-lg">
              <form id="frm1" name="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class="fa fa-upload fa-lg faicon"></i> Upload TAG /<?php echo SERIALNO ?>  </h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
              </form>
            </div>
          </div><!--close task history modal-->
</body>
</html>