<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from grn_master where grn_no='".$po_no."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
//////////////// upload excel file for IMEI upload
if($_POST['Submit']=="Upload"){
	////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	if ($_FILES["fileupload"]["error"] > 0){
		$code=$_FILES["fileupload"]["error"];
	}
	else{
		move_uploaded_file($_FILES["fileupload"]["tmp_name"],"../ExcelExportAPI/upload_grn/".$now.$_FILES["fileupload"]["name"]);
		$file="../ExcelExportAPI/upload_grn/".$now.$_FILES["fileupload"]["name"];
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
	$grn_part = mysqli_fetch_assoc(mysqli_query($link1,"select * from grn_data where sno='".base64_decode($_POST['refsno'])."'"));
	//// check grn qty vs uploading qty
	if($grn_part['shipped_qty'] == ($highest-1)){
		//importing files to the database
		for($row =2 ;$row <= $highest;$row++){
			$imei1 = $sheet->getCellByColumnAndRow(0,$row)->getValue();
			$imei2 = $sheet->getCellByColumnAndRow(1,$row)->getValue();
			///// check serial no. in stock
			$res = mysqli_query($link1,"select id,status from imei_details where imei1='".$imei1."' and location_code='".$_SESSION['asc_code']."'");
			$row_checkstock=mysqli_fetch_assoc($res);
			if($row_checkstock['id']!='' && $row_checkstock['status']==1) {
				$flag = false;
				$error_msg = "Error details1: IMEI no. is already exits into database.";
			}else{
				//inserting query into data base			
				$result = mysqli_query($link1,"INSERT INTO imei_details set imei1='".$imei1."',imei2='".$imei2."',grn_no='".$grn_part['grn_no']."',grn_date='".$po_row['receive_date']."',partcode='".$grn_part['partcode']."',model_id='".$grn_part['model_id']."', location_code='".$_SESSION['asc_code']."',status='1',entry_date='".$today."' ,stock_type ='okqty'");
				//// check if query is not executed
				if (!$result) {
					$flag = false;
					$error_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
				///// update serial no. inventroy
				$result2 = mysqli_query($link1,"INSERT INTO imei_history set imei1='".$imei1."',imei2='".$imei2."',partcode='".$grn_part['partcode']."',transaction_no='".$grn_part['grn_no']."',remark='GRN Receive' ,location_code='".$_SESSION['asc_code']."' "); 
				//// check if query is not executed
				if (!$result2) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
			}
		}///// end of for loop
		/////// update attachment flag
		$checkboxqty=mysqli_fetch_assoc(mysqli_query($link1,"select COUNT(id) as dispqty from imei_details where grn_no='".$grn_part['grn_no']."' and partcode='".$grn_part['partcode']."'"));
		if($checkboxqty['dispqty'] == $grn_part['shipped_qty']){
			$result3 = mysqli_query($link1,"update grn_data set attach_file='".$fsave_name."',imei_attach='Y' where sno='".$grn_part['sno']."'");
			//// check if query is not executed
			if (!$result3) {
				$flag = false;
				$error_msg = "Error details4: " . mysqli_error($link1) . ".";
			}
		}
	}else{
		$flag = false;
        $error_msg = "Error details:1 Uploading qty does not match with grn qty.";
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
  	header("location:statusgrn_view.php?refid=".base64_encode($grn_part['grn_no'])."&msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
   <div class="<?=$screenwidth?> tab-pane fade in active">
      <h2 align="center"><i class="fa fa-ship"></i> GRN View</h2>
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
        <div class="panel-heading">GRN Entry Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["party_code"],"name","id","vendor_master",$link1)."(".$po_row["party_code"].")";?></td>
                <td width="20%"><label class="control-label">Location</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["location_code"],"locationname","location_code","location_master",$link1)."(".$po_row['location_code'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">Gate Entry No.</label></td>
                <td><?php echo $po_row['gate_entry_no'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['remark'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">GRN Date</label></td>
                <td><?php echo dt_format($po_row['receive_date']);?></td>
              </tr>  
			  <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php echo getdispatchstatus($po_row["status"]);?></td>
                <td><label class="control-label">GRN No.</label></td>
                <td><?php echo $po_row['grn_no'];?></td>
              </tr>       
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%" style="font-size:12px">
           <thead>
            <tr class="<?=$tableheadcolor?>">
              <td rowspan="2">S.No</td>
              <td rowspan="2">Brand</td>
              <td rowspan="2">Model</td>
              <td rowspan="2">Partcode</td>
              <td rowspan="2">Qty</td>
			  <td colspan="3" align="center">Received Qty</td>
			  <td rowspan="2">Price</td>
              <td rowspan="2">Subtotal</td>
              <td rowspan="2">Tax</td>
			  <?php if($po_row['total_igst_amt'] == '0.00') {?>
              <td rowspan="2">CGST %</td>
			  <td rowspan="2">CGST Amt</td>
			  <td rowspan="2">SGST %</td>
			  <td rowspan="2">SGST Amt</td>
			  <?php } else {?>
			  <td rowspan="2">IGST %</td>
			  <td rowspan="2">IGST Amt</td>
			  <?php }?>
              <td rowspan="2">Total Amt</td>
			   <td rowspan="2">TAG /<?php echo SERIALNO ?> No.</td>
            </tr>
            <tr class="<?=$tableheadcolor?>">
              <td>OK</td>
              <td>DAMAGE</td>
              <td>MISSING</td>
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from grn_data where grn_no='".$po_no."' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
				$brand_name=getAnyDetails($data_row['brand_id'],"brand","brand_id" ,"brand_master",$link1);
				$model_name=getAnyDetails($data_row['model_id'],"model","model_id" ,"model_master",$link1);
				$part_name=explode("~",getAnyDetails($data_row['partcode'],"part_name,part_category","partcode" ,"partcode_master",$link1));
				$make_partname = $part_name[0]."&nbsp;&nbsp;|&nbsp;&nbsp;".$model_name."&nbsp;&nbsp;|&nbsp;&nbsp;".$brand_name;
			?>
              <tr>
                <td><?=$i?></td>
               <td><?=$brand_name?></td>
              <td><?=$model_name?></td>
              <td><?=$part_name[0]?></td>
              <td align="right"><?=$data_row['shipped_qty'];?></td>
               <td align="right"><?=$data_row['okqty'];?></td>
                <td align="right"><?=$data_row['damage'];?></td>
                <td align="right"><?=$data_row['missing'];?></td>  
                <td align="right"><?=$data_row['price'];?></td>  
             	<td align="right"><?=$data_row['sub_total'];?></td>
             <td><?=$data_row['tax_name']?></td>
			 	  <?php if($po_row['total_igst_amt'] == '0.00') {?>
             <td align="right"><?=$data_row['cgst_per'];?></td>
			  <td align="right"><?=$data_row['cgst_amt'];?></td>
			   <td align="right"><?=$data_row['sgst_per'];?></td>
			    <td align="right"><?=$data_row['sgst_amt'];?></td>
				<?php } else {?>
				 <td align="right"><?=$data_row['igst_per'];?></td>
				  <td align="right"><?=$data_row['igst_amt'];?></td>
				  <?php }?>
             <td align="right"><?=$data_row['amount'];?></td>
             <td align="left">
			 <?php 
			 if($part_name[1]=="UNIT" || $part_name[1]=="BOX" || $part_name[1]=="TAG"){
			 	if($data_row['imei_attach']==""){
				 if($data_row['type']=="PO"){ $grntype="VGRN";}else{ $grntype="LGRN";}
				 ?><a href="#" onClick="uploadIMEI('<?=$data_row['sno']?>');" title="Upload IMEI"><i class="fa fa-upload fa-lg faicon"></i></a>&nbsp;&nbsp;<a href="enter_imei.php?refid=<?=base64_encode($po_row['grn_no'])?>&partcode=<?=base64_encode($data_row['partcode'])?>&pqty=<?=base64_encode($data_row['shipped_qty'])?>&partname=<?=base64_encode($make_partname)?>&model_id=<?=base64_encode($data_row['model_id'])?>&grn_type=<?=$grntype?><?=$pagenav?>" title="Enter IMEI"><i class="fa fa-shower fa-lg faicon"></i></a>
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
			$total_qty+= $data_row['shipped_qty'];
			$total_okqty+= $data_row['okqty'];
			$total_dmgqty+= $data_row['damage'];
			$total_misqty+= $data_row['missing'];
			$total_sub+= $data_row['sub_total'];
			$total_igstamt+= $data_row['igst_amt'];
			$total_cgstamt+= $data_row['cgst_amt'];
			$total_sgstamt+= $data_row['sgst_amt'];
			$total_amt+= $data_row['amount'];
			$i++;
			}
			?>
            <tr align="right">
                <td colspan="4" align="right"><strong>Total</strong></td>
                <td><strong>
                  <?=$total_qty?>
                </strong></td>
                <td><strong>
                  <?=$total_okqty?>
                </strong></td>
                <td><strong>
                  <?=$total_dmgqty?>
                </strong></td>                
                <td>&nbsp;</td>
				<td>&nbsp;</td>
                <td><strong>
                  <?=currencyFormat($total_sub)?>
                </strong></td>
                <td>&nbsp;</td>
				 <?php if($po_row['total_igst_amt'] == '0.00') {?>
				<td>&nbsp;</td>
                <td><strong>
                  <?=currencyFormat($total_cgstamt)?>
                </strong></td>
				<td>&nbsp;</td>
				<td><strong>
                  <?=currencyFormat($total_sgstamt)?>
                </strong></td>
				<?php } else { ?>
		<td>&nbsp;</td>
		<td><strong>
                  <?=currencyFormat($total_igstamt)?>
                </strong></td>
				<?php }?>
                <td><strong>
                  <?=currencyFormat($total_amt)?>
                </strong></td>
             <td><strong>
                 &nbsp;
                </strong></td>
              </tr>
             <tr>
                 <td colspan="16" align="center">
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='grn_status.php?<?=$pagenav?>'">
                 </td>
             </tr>
            </tbody>
          </table>

          
      </div><!--close panel body-->
    </div><!--close panel-->
 <!--close panel-->
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
	$.get('upload_imei.php?refid=' + sno, function(html){
		 $('#updimei .modal-body').html(html);
		 $('#updimei').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
/*(document).ready(function () {
	$('#test').bootstrapValidator({
		live: 'enabled',
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			fileupload: {
				validators: {
					file: {
						extension: 'xlsx',
						//type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/rtf,application/zip',
						type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel',
						maxSize: 3 * 1024 * 1024, // 5 MB
						message: 'The selected file is not valid, it should be (xlsx) and 3 MB at maximum.'
					}
				}
			}
		}
	});
});*/
</script>
<!-- Start upload imei Modal -->
          <div class="modal modalTH fade" id="updimei" role="dialog">
            <div class="modal-dialog modal-dialogTH modal-lg">
              <form id="frm1" name="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class="fa fa-upload fa-lg faicon"></i>Upload TAG /<?php echo SERIALNO ?></h4>
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

