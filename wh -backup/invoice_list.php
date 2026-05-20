<?php
require_once("../includes/config.php");
$_SESSION['messageIdent_whdispatch']="";
///// if dispatch details is updated like courier or docket of any invoice
if($_POST['upddckt']=="Update"){
	$sql_doc = "UPDATE billing_master set courier = '".$_POST['courier_name']."', docket_no='".$_POST['docket_no']."',dc_date='".$today."', dc_time='".$currtime."', status='3',disp_rmk='".$_POST['disprmk']."',doc_price='".$_POST['courier_amt']."' where challan_no='".base64_decode($_POST['ref_no'])."' ";
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
		$msg = "Dispatch details is successfully updated against ".base64_decode($_POST['ref_no']);
	}
	//$sms_msg="Dear Partner. your consignment has been dispatched with courier ".$_POST['courier_name']." and docket no ".$_POST['docket_no']."";
	header("location:invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&to=".$_POST['asc_phone']."");
	exit;
}

$invice_no=$_REQUEST[challanno];
$location=$_REQUEST[tolocation];
if($location!='' && $invice_no!=''){
$name=getAnyDetails($location,"locationname","location_code","location_master",$link1);
$email=getAnyDetails($location,"emailid","location_code","location_master",$link1);

include "send_mail.php";
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
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
			
		}
	});
});
$(document).ready(function() {
	var dataTable = $('#po-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/invoicelist-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "status": "<?=$_REQUEST['status']?>", "location_code": "<?=$_REQUEST['location_code']?>", "doc_type": "<?=$_REQUEST['doc_type']?>"},
			type: "post",  // method  , by default get
			
			error: function(){  // error handling
				$(".po-grid-error").html("");
				$("#po-grid").append('<tbody class="po-grid-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
				$("#po-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to update courier details
function openCourierModel(docid,couriername,docketno,drmk){
	$.get('update_document_courier.php?doc_id=' + docid + '&couriernam=' + couriername + '&docktno=' + docketno + '&disprmk=' + drmk, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-book"></i> Invoices/Challans</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php unset($_POST);
		
		 }?>
                 
     
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">Document Status</label>	  
			<div class="col-md-6" align="left">
			  <select id="status"  name="status" class="form-control">
			  <option value=''>--Please Select-</option>
               <option value="2" <?php if($_REQUEST['status'] == "2") { echo 'selected'; }?>>Processed</option>
               <option value="3" <?php if($_REQUEST['status'] == "3") { echo 'selected'; }?>>Dispatched</option>
               <option value="4" <?php if($_REQUEST['status'] == "4") { echo 'selected'; }?>>Received</option>
			   <option value="5" <?php if($_REQUEST['status'] == "5"){ echo 'selected'; }?>>Cancelled</option>				
			</select>
            </div>
          </div>
	    </div><!--close form group-->
        <!--close form group-->
         <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location Name</label>
            <div class="col-md-6">
              <select name="location_code" id="location_code" class="form-control">
              <option value=''>--Please Select-</option>
                <?php
                $res_maploc = mysqli_query($link1,"select location_code from map_wh_location where wh_location='".$_SESSION['asc_code']."'"); 
                while($row_maploc = mysqli_fetch_assoc($res_maploc)){
					$locname = getAnyDetails($row_maploc['location_code'],"locationname","location_code","location_master",$link1);
					?>
                <option value="<?=$row_maploc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_maploc['location_code']) { echo 'selected'; }?>><?=$locname." (".$row_maploc['location_code'].")"?></option>
                <?php } ?>
              </select>
            </div>
	      </div>
          <div class="col-md-6"><label class="col-md-4 control-label">Document Type</label>
            <div class="col-md-6">
            <div style="display:inline-block;float:left">
            <select name="doc_type" id="doc_type" class="form-control" >
              <option value=''>All</option>
              <option value='INV'<?php if($_REQUEST['doc_type']=="INV"){ echo "selected";}?>>Invoice</option>
              <option value='DC'<?php if($_REQUEST['doc_type']=="DC"){ echo "selected";}?>>Challan</option>
            </select>
            </div>
            <div style="display:inline-block;float:right">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			   <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="GO"></div>
            </div>
	      </div>
         </div>
		  </form>
		   <?php if ($_REQUEST['Submit']){
		   ?>
         <div class="form-group">
				    
			<div class="col-md-12" align="right">
               <?php
			    //// get excel section id ////
				//$sectionid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$sectionid,$link1)==1){
			   ?> <div class="float-right">
              <!-- <a href="po_report.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>" title="Export PO details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export PO details in excel"></i></a>-->
               <?php
				//}
				?>
				<?php 
			$location_code = $_REQUEST['location_code'];
			$statusval =  $_REQUEST['status']  ;
			$doc_type = $_REQUEST['doc_type']  ;


			if($_REQUEST['daterange'] != ""){
				$date_range = explode(" - ",$_REQUEST['daterange']);
				$daterange = "sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."'";
			}else{
				$daterange = "1";
			}
			## selected  status 
			if($statusval != ""){
				$status = "status = '".$statusval."'";
			}else{
				$status = " status in ('2','3','4')";
			}
			## selected  location
			if($location_code != ""){
				$locationcode = "to_location = '".$location_code."'";
			}else{
				$locationcode = "1";
			}
			## selected  document type
			if($doc_type != ""){
				$documenttype = "document_type = '".$doc_type."'";
			}else{
				$documenttype = "1";
			}	 
		  $date_range = explode(" - ",$_REQUEST['daterange']);
		  $daysdiff=daysDifference($date_range[1],$date_range[0]);
		    $po_sql = "SELECT * FROM billing_master where ".$status." and ".$daterange." and ".$locationcode." and ".$documenttype." and from_location='".$_SESSION['asc_code']."'";
			$po_res = mysqli_query($link1, $po_sql);
	if(mysqli_num_rows($po_res) > 0){
		  if($_REQUEST['daterange']!='' && $_REQUEST['location_code']!='' && $daysdiff <= 10){ 
		  
		  ?>
		 <a href="bulkinvoice_challan_print.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code']);?>&status=<?=base64_encode($_REQUEST['status']);?>&doc_type=<?=base64_encode($_REQUEST['doc_type']);?>" class="btn<?=$btncolor?>"   target="_blank" title="Bulk print Invoice">Bulk printing</a></div>
		
      <!-- <button title="Location-wise PO" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='bulkinvoice_challan_print.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code']);?>&status=<?=base64_encode($_REQUEST['status']);?>&doc_type=<?=base64_encode($_REQUEST['doc_type']);?>"><span>Bul Printer</span></button>&nbsp;&nbsp;-->
		  <?php } else {?>
		  <div class="float-right" style="float: right;"> <span class="text-info">For bulk printing, limit the date range to 10 days</span><br>
		   <span  class="text-primary">Select a location for bulk printing</span></div>
		  
		  <?php } } else {?>
			<span  class="text-primary">No records found</span></div>	
				<?php } ?>
            </div>
           
			 
	    </div><!--close form group-->
		 <?php }?>
	  
      <form class="form-horizontal" role="form" id="frm1" name="frm1">
		 
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="po-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Document No.</th>
			  <th>Document Date</th>
			  <th>Document Type</th>
              <th>Location Code</th>
              <th>Location Name</th>
              <th>Ref. No.</th>
			  <th>Status</th>
              <th>View</th>
			  <th>Print</th>
			  <th>Update Courier</th>
            </tr>
          </thead>
       </table>
      </div>
      <!--</div>-->
      </form>
      <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="courierModel" role="dialog">
          <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class='fa fa-truck fa-lg faicon'></i> Update Dispatch Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <input type="submit" class="btn<?=$btncolor?>" name="upddckt" id="upddckt" value="Update" title="" <?php if($_POST['upddckt']=='Update'){?>disabled<?php }?>>
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
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