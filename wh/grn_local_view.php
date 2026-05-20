<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po to vendor details
$job_sql="SELECT * FROM grn_master where grn_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$bill_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM billing_master where challan_no='".$docid."'"));
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
				$result = mysqli_query($link1,"INSERT INTO imei_details set imei1='".$imei1."',imei2='".$imei2."',grn_no='".$grn_part['grn_no']."',grn_date='".$po_row['receive_date']."',partcode='".$grn_part['partcode']."',model_id='".$grn_part['model_id']."', location_code='".$_SESSION['asc_code']."',status='1',entry_date='".$today."', stock_type ='okqty' ");
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
  	header("location:grn_local_view.php?refid=".base64_encode($grn_part['grn_no'])."&msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
   	exit;
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
<body >
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-car"></i> Local GRN Details</h2>
      <h4 align="center">GRN No.- <?=$docid?></h4>
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
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Party Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['party_code'],"name","id","vendor_master",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php  echo getdispatchstatus($job_row["status"]);?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Bill To</label></td>
                <td><?php echo getAnyDetails($job_row['location_code'],"locationname","location_code","location_master",$link1);?></td>
                 <td width="20%"><label class="control-label">Ship To</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['comp_code'],"locationname","location_code","location_master",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">GRN No.</label></td>
                <td><?php echo $job_row['grn_no'];?></td>
                <td><label class="control-label">GRN Date</label></td>
                <td><?php echo dt_format($job_row['receive_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Supplier Address</label></td>
                <td><?=$bill_det['from_addrs']?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?=$job_row['remark']?></td>
              </tr>
              <tr>
                <td><label class="control-label">Billing Address</label></td>
                <td><?=$bill_det['to_addrs']?></td>
                <td><label class="control-label">Shipping Address</label></td>
                <td><?=$bill_det['deliv_addrs']?></td>
              </tr>
             
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;GRN Items Details</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%" style="font-size:12px">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="3%" style="text-align:center">#</th>
                <th width="10%" style="text-align:center">Product</th>
                <th width="10%" style="text-align:center">Brand</th>
                <th width="10%" style="text-align:center">Model</th>
                <th width="15%" style="text-align:center">Part Name</th>
                <th width="8%" style="text-align:center">Qty</th>
                <th width="8%" style="text-align:center">Price</th>
                <th width="10%" style="text-align:center">SubTotal</th>
                <th width="8%" style="text-align:center">Tax</th>
                <th width="8%" style="text-align:center">Tax Amt</th>
                <th width="10%" style="text-align:center">Amount</th>
				 <td width="10%" style="text-align:center">Attach IMEI</td>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$totqty = 0;
			$totamt = 0.00;
			$podata_sql="SELECT * FROM grn_data where grn_no='".$job_row['grn_no']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part_name=explode("~",getAnyDetails($podata_row['partcode'],"part_name,part_category","partcode" ,"partcode_master",$link1));
				$make_partname = $part_name[0]."&nbsp;&nbsp;|&nbsp;&nbsp;".$model[0]."&nbsp;&nbsp;|&nbsp;&nbsp;".$brand[0];
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0];?></td>
                <td><?=$brand[0];?></td>
                <td><?=$model[0];?></td>
                <td><?=$podata_row['part_name']." (".$podata_row['partcode'].")";?></td>
                <td align="right"><?=$podata_row['okqty']?></td>
                <td align="right"><?=$podata_row['price']?></td>
                <td align="right"><?=$podata_row['sub_total']?></td>
                <td align="left"><?=$podata_row['tax_name']?></td>
                <td align="right"><?=$podata_row['tax_amt']?></td>
                <td align="right"><?=$podata_row['amount']?></td>
				<td align="left">
			 <?php 
			 if($part_name[1]=="UNIT" || $part_name[1]=="BOX"){
			 	if($podata_row['imei_attach']==""){
				 if($podata_row['type']=="PO"){ $grntype="VGRN";}else{ $grntype="LGRN";}
				 ?><a href="#" onClick="uploadIMEI('<?=$podata_row['sno']?>');" title="Upload IMEI"><i class="fa fa-upload fa-lg faicon"></i></a>&nbsp;&nbsp;<a href="local_enter_imei.php?refid=<?=base64_encode($job_row['grn_no'])?>&partcode=<?=base64_encode($podata_row['partcode'])?>&pqty=<?=base64_encode($podata_row['shipped_qty'])?>&partname=<?=base64_encode($make_partname)?>&model_id=<?=base64_encode($podata_row['model_id'])?>&grn_type=<?=$grntype?><?=$pagenav?>" title="Enter IMEI"><i class="fa fa-shower fa-lg faicon"></i></a>
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
			$totqty+=$podata_row['okqty'];
			$subtot+=$podata_row['sub_total'];
			$taxtot+=$podata_row['tax_amt'];
			$totamt+=$podata_row['amount'];
			$i++;
			}
			?>
            <tr>
                <td colspan="5" align="right"><strong>Total</strong></td>
                <td align="right"><strong><?=$totqty?></strong></td>
                <td align="right">&nbsp;</td>
                <td align="right"><strong><?=currencyFormat($subtot)?></strong></td>
                <td align="right">&nbsp;</td>
                <td align="right"><strong><?=currencyFormat($taxtot)?></strong></td>
                <td align="right"><strong><?=currencyFormat($totamt)?></strong></td>
              </tr>
			 <tr>
                <td align="center" colspan="17">
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='grn_local.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
////// function for open model to see the task history
function uploadIMEI(sno){
	$.get('localupload_imei.php?refid=' + sno, function(html){
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
                  <h4 class="modal-title" align="center"><i class="fa fa-upload fa-lg faicon"></i> Upload IMEI</h4>
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