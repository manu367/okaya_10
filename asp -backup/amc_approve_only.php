<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$amc_sql = "SELECT * FROM amc where amcid='".$docid."'";
$amc_res = mysqli_query($link1,$amc_sql);
$amc_row = mysqli_fetch_assoc($amc_res);

@extract($_POST);
////// if we hit process button
if ($_POST) {
    if ($_POST['update'] == 'Update') {
		mysqli_autocommit($link1, false);
            $flag = true;
            $err_msg = "";
			/////update estimate master table

					
				/////update jobsheet data  table
				if($status=='approved'){
		$sql_inst = "update amc set status='1', app_status='".$status."',quotetype='A',app_remark='".$remark."',app_by='".$_SESSION['asc_code']."',app_date='".$today."' where amcid='".$amcid."'";

	$res_inst = mysqli_query($link1,$sql_inst);
	
	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error amc : " . mysqli_error($link1) . ".";

	}
		$res_import = mysqli_query($link1,"UPDATE imei_data_import set amc='Y'  where (imei1 = '".$serail_no."'  or  imei2 = '".$serail_no."' )");

	//// check if query is not executed

	if (!$res_import) {

		 $flag = false;

		 $error_msg = "Error details import: " . mysqli_error($link1) . ".";

	}
	
	$flag = dailyActivity($_SESSION['userid'],$amcid,"AMC","AMC Approved",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	
				}else{
							$sql_inst = "update amc set status='2',quotetype='R', app_status='".$status."',app_remark='".$remark."',app_by='".$_SESSION['asc_code']."',app_date='".$today."' where amcid='".$amcid."'";

	$res_inst = mysqli_query($link1,$sql_inst);
	
	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error amc : " . mysqli_error($link1) . ".";

	}
	
		$flag = dailyActivity($_SESSION['userid'],$amcid,"AMC","AMC Rejected",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	
					}
			/// check if query is execute or not//
				
	
 if ($flag) {
                        mysqli_commit($link1);
                        $msg = "Successfully done with ref. no. " . $docid;
						$cflag = "success";
						$cmsg = "Success";
                    } else {
                        mysqli_rollback($link1);
                        $msg = "Request could not be processed " . $error_msg . ". Please try again.";
						$cflag = "danger";
						$cmsg = "Failed";
                    }
                    mysqli_close($link1);
					 ///// move to parent page
        header("location:amc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-list-alt"></i> AMC View</h2>
      <h4 align="center">AMC No.- <?=$docid?></h4>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $amc_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $amc_row['addrs'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No. </label></td>
                <td><?php echo $amc_row['contract_no'];?></td>
                <td><label class="control-label">Country</label></td>
                <td><?php echo getAnyDetails($amc_row["country_id"],"countryname","countryid","country_master",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($amc_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $amc_row['email'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($amc_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Landmark</label></td>
                <td><?php echo $amc_row['landmark'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Type</label></td>
                <td><?php echo $amc_row['customer_type'];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Product</label></td>
                <td width="30%"><?php echo getAnyDetails($amc_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td width="20%"><label class="control-label">Brand</label></td>
                <td width="30%"><?php echo getAnyDetails($amc_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
            <tr>
                <td><strong>Model</strong></td>
                <td><?php echo getAnyDetails($amc_row["model_id"],"model","model_id","model_master",$link1);?></td>
                <td><strong>IMEI/Serial No</strong></td>
                <td><?php echo $amc_row['serial_no'];?></td>
              </tr>
          
          
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;AMC Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">AMC Date</label></td>
                <td width="30%"><?=dt_format($amc_row['purchase_date'])." ".$amc_row['open_time']?></td>
                <td width="20%"><label class="control-label">AMC Start Date</label></td>
                <td width="30%"><?=dt_format($amc_row['amc_start_date']);?></td>
              </tr>
            <tr>
              <td><label class="control-label">AMC End Date</label></td>
             <td width="30%"><?=dt_format($amc_row['amc_end_date']);?></td>
              <td><label class="control-label">AMC Amount</label></td>
              <td><?php echo $amc_row['amc_amount'];?></td>
            </tr>
            <tr>
              <td><label class="control-label">AMC Type</label></td>
              <td><?=$amc_row['amc_type']?></td>
              <td><label class="control-label">AMC Duration(in month)</label></td>
              <td><?=$amc_row['amc_duration']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Payment Mode</label></td>
              <td><?=$amc_row['mode_of_payment']?></td>
              <td><label class="control-label">Bank name</label></td>
              <td><?=$amc_row['bank']?></td>
            </tr>
			 <tr>
              <td><label class="control-label">Cheque number</label></td>
              <td><?=$amc_row['cheque_no']?></td>
              <td><label class="control-label">Cheque Date</label></td>
              <td><?=dt_format($amc_row['cheque_date'])?></td>
            </tr>
            <tr>
              <td><label class="control-label">Remark </label></td>
              <td colspan="3"><?=$amc_row['remarks']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

	

		<!--approval for EP-->
	<?php  if($amc_row['quotetype'] != '') { ?>
	<form   id="frm1" name="frm1" method="post" >
<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Estimate Approval</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Status</label></td>
                <td width="30%"><select   id="status" name="status" class="form-control" required >
				<option value="">Please Select</option><option value="approved" <?php if($_REQUEST['status'] == "approved") { echo 'selected'; }?>>Approve by Customer</option><option value="rejected" <?php if($_REQUEST['status'] == "rejected") { echo 'selected'; }?>>Reject  by Customer</option></select></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control" required></textarea>
				<input type="hidden" name="serail_no" class=" form-control" id="serail_no"  value="<?=$amc_row['serial_no']?>" readonly/> </td>
              </tr> 
			   <tr>
                 <td width="100%" align="center" colspan="8">   <input name="amcid" type="hidden" class="required form-control" required id="amcid" value="<?=$docid?>"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='amc_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
               </tr>     
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</form>
<?php  }?>

 
 
  
  
 
  
    <div class="panel panel-info table-responsive">
     
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
    <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='amc_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"></td>
               </tr>
           
          </table>
     
    </div><!--close panel-->
      </div><!--close panel-->
	
	
  </div><!--close panel group-->
	

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