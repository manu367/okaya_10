<?php
require_once("../includes/config.php");
///// get brand from url /////
$brand = base64_decode($_REQUEST['brand']);

///// if dispatch details is updated like courier or docket of any invoice
if($_POST['updpna']=="Yes"){
	$r_id = base64_decode($_POST['ref_no']);
	$sql_doc = "update auto_part_request set status='5', cancel_date = '".$today."',remark ='Cancelled' where id = '".$r_id."' ";
	$res_doc = mysqli_query($link1,$sql_doc);
	//// check if query is not executed
	if (!$res_doc) {
		$flag = false;
		$error_msg = "Error details1: " . mysqli_error($link1) . ".";
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	}else{
		$cflag = "success";
		$cmsg = "Success";
		$msg = "PNA  part is successfully cancelled ";
	}
	header("location:inventory_pna_bucket.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/jquery.js"></script>
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<!-- datatable plugin-->
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<!--  -->
<script>
	$(document).ready(function(){
        $("#frm").validate();
    });
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" >
 ////////////////////// function to check or uncheck ///////////////////////////////////////////
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function() {
	var dataTable = $('#pna-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/pnabucket-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "brandid": "<?=$brand?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".pna-grid-error").html("");
				$("#pna-grid").append('<tbody class="pna-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#pna-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to cancel row wise PNA part
function cancelPNAPart(partid,jobid,part_code){
	$.get('cancel_pna_part.php?refid=' + partid , function(html){
		 $('#cancel_pna .modal-body').html(html);
		 $('#cancel_pna').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<title>
<?=siteTitle?>
</title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> PNA Bucket</h2>
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <strong>
        <?=$_REQUEST['chkmsg']?>
        !</strong>&nbsp;&nbsp;
        <?=$_REQUEST['msg']?>
        . </div>
      <?php }?>
      <form class="form-horizontal" role="form" id="frm" name="frm" method="post" action="inventory_pnabucket_save.php">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
          <br/>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">To Location/WH<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                <select name="location_code" id="location_code" class="form-control required">
                 <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select wh_location  from map_wh_location where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y' and wh_location in (select location_code  from access_brand where brand_id ='".$brand."'  and  status = 'Y')"); 
                while($row_wh = mysqli_fetch_assoc($map_wh)){
				 //$location = mysqli_fetch_array(mysqli_query($link1, "select locationname, location_code from location_master where location_code = '".$row_wh['wh_location']."' "));				
				 $location = mysqli_fetch_array(mysqli_query($link1, "select locationname, location_code,cityid from location_master where location_code = '".$row_wh['wh_location']."' and  statusid = '1' and location_code in (select location_code from access_brand where brand_id in ($brand) and status = 'Y') "));	
				 if($location['location_code']!=""){			
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['location_code'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code']."),".getAnyDetails($location['cityid'],"city","cityid","city_master",$link1)?></option>
                <?php }} ?>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-buttons" style="float:right">
            <input type="button" class="btn btn<?=$btncolor?>"  onClick="checkAll(document.frm.checkBox)"  value="Select All"  />
            <input type="button"  class="btn btn<?=$btncolor?>" onClick="uncheckAll(document.frm.checkBox)"  value="Unselect All"/>
          </div>
          <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
          <table  width="100%" id="pna-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
            <thead>
              <tr>
                <th>S.No</th>
                <th>Partcode</th>
                <th>Stock Avilable</th>
                <th>Part Details</th>
                <th>Job No.</th>
                <th>Request Date</th>
                <th>Action</th>
                <th>Confirm</th>
              </tr>
            </thead>
          </table>
          <div align="center">
            <input type="submit" name="save" id="save"  class="btn btn<?=$btncolor?>" value="SAVE"/>
			<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_pna_bucket_brand.php?<?=$pagenav?>'">
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
          </div>
        </div>
        <!--</div>-->
      </form>
         <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="cancel_pna" role="dialog">
          <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class='fa fa-trash fa-lg faicon'></i> Delete PNA Part</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                	<input type="submit" class="btn<?=$btncolor?>" name="updpna" id="updpna" value="Yes" title="" <?php if($_POST['updpna']=='Yes'){?>disabled<?php }?>>
                    <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">No</button>
                </div>
              </div>
            </div>
            </form>
          </div><!--close Model Mapped modal-->
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>