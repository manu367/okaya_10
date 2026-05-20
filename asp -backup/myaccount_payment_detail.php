<?php
require_once("../includes/config.php");
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from payment_details where id='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new payment details
if($_POST){
   if ($_POST['add']=='ADD'){
   //// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
		//// pick max count of job
		$res_jobcount = mysqli_query($link1,"SELECT * from invoice_counter where location_code='".$_SESSION['asc_code']."'");
		$row_jobcount = mysqli_fetch_assoc($res_jobcount);
		///// make job sequence
		$nextjobno = $row_jobcount['pay_counter'] + 1;
		//$jobno = $_SESSION['asc_code']."P".str_pad($nextjobno,4,0,STR_PAD_LEFT);
	    $jobno = $_SESSION['asc_code']."P".$row_jobcount['fy'].str_pad($nextjobno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$upd = mysqli_query($link1,"UPDATE invoice_counter set pay_counter='".$nextjobno."' where location_code='".$_SESSION['asc_code']."'");
		//// check if query is not executed
		if (!$upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		
		/////////////////////////////// insert data into payment details table///////////////////////////////////////////////
 		$payment="INSERT INTO payment_details set amount ='".$amount."',  bankname  ='".$bank."', pay_mode  ='".$pay_mode."', account_no ='".$acc_no."', dd_chequeno='".$dd_no."', dd_date='".$dd_date."', couriername='".$courier_name."' , docketno ='".$docket_no."', courierdate='".$courier_date."' , attachment = '".$file."' ,status = '1' , to_location= '".$to_location."', from_location = '".$_SESSION['asc_code']."' ,challan_no = '".$jobno. "'  ";
    	$result=mysqli_query($link1,$payment);
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		////// return message
		$msg="You have successfully entered Payment Details";
		$cflag="success";
		$cmsg="Success";
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$acc_no,"Payment","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
   }
   
   else if ($_POST['upd']=='Update'){
   //// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
		
   		 $pay_upd="update payment_details set amount ='".$amount."',  bankname  ='".$bank."', pay_mode  ='".$pay_mode."', account_no ='".$acc_no."', dd_chequeno='".$dd_no."', dd_date='".$dd_date."', couriername='".$courier_name."' , docketno ='".$docket_no."', courierdate='".$courier_date."',entry_date='".$today."'  where id = '".$refid."'";
    $res_upd=mysqli_query($link1,$pay_upd);
	//// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
	
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$refid,"Payment","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully Updated Payment Details ";
	$cflag="success";
	$cmsg="Success";
   }
   ///// check both master and data query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
	} else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
   ///// move to parent page
    header("location:myaccount_add_payment.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}
?>

<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>
 <?=siteTitle?>
 </title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });	
	$(document).ready(function () {
		$('#courier_date').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	
	$(document).ready(function () {
		$('#dd_date').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	
 </script>
 <script language="javascript" type="text/javascript">
  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
 <style type="text/css">
.custom_label {
	text-align:left;
	vertical-align:middle
}
</style>
 <body>
 <div class="container-fluid">
   <div class="row content">
     <?php 
 		include("../includes/leftnavemp2.php");
    ?>
     <div class="<?=$screenwidth?>">
     <h2 align="center"><i class="fa fa-rupee"></i> Payment  Details</h2>
     <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
       <div class="panel-group">
         <div class="panel panel-info">
           <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Payment Details</div>
           <div class="panel-body">
           <div class="form-group">
             <div class="col-md-6">
               <label class="col-md-6 custom_label"> Amount<span class="red_small">*</span></label>
               <div class="col-md-6">
                 <input name="amount" id="amount"  type="text"  class="number form-control required" value="<?=$sel_result['amount']?>" required/>
               </div>
             </div>
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Payment Mode <span class="red_small">*</span></label>
               <div class="col-md-6">
                 <select id="pay_mode"  name="pay_mode" class="form-control required">
                   <option value=''>--Please Select-</option>
                   <option value="Cash" <?php if($sel_result['pay_mode'] == "Cash") { echo 'selected'; }?>>Cash</option>
                   <option value="DD" <?php if($sel_result['pay_mode'] == "DD"){ echo 'selected'; }?>>DD</option>
                   <option value="Cheque" <?php if($sel_result['pay_mode'] == "Cheque"){ echo 'selected'; }?>>Cheque</option>
                   <option value="NEFT" <?php if($sel_result['pay_mode'] == "NEFT"){ echo 'selected'; }?>>NEFT</option>
                   <option value="RTGS" <?php if($sel_result['pay_mode'] == "RTGS"){ echo 'selected'; }?>>RTGS</option>
                 </select>
               </div>
             </div>
           </div>
           <div class="form-group">
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Bank Name</label>
               <div class="col-md-6">
                 <select name="bank" id="bank" class="form-control"  >
                   <option value="">Please Select</option>
                   <?php
                $map_wh = mysqli_query($link1,"select *  from bank_master where  status = '1'"); 
                while($row = mysqli_fetch_assoc($map_wh)){			
				?>
                   <option value="<?=$row['bank_id']?>" <?php if($sel_result['bankname'] == $row['bank_id']) { echo 'selected'; }?>>
                   <?=$row['name']?>
                   </option>
                   <?php } ?>
                 </select>
               </div>
             </div>
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Bank Account No.</label>
               <div class="col-md-6">
                 <input name="acc_no" id="acc_no" type="text" value="<?=$sel_result['account_no']?>" class="form-control"/>
               </div>
             </div>
           </div>
           <div class="form-group">
             <div class="col-md-6">
               <label class="col-md-6 custom_label">DD/Cheque No.</label>
               <div class="col-md-6">
                 <input name="dd_no" id="dd_no" type="text" value="<?=$sel_result['dd_chequeno']?>" class="form-control"/>
               </div>
             </div>
             <div class="col-md-6">
               <label class="col-md-6 custom_label">DD Date</label>
               <div class="col-md-6">
                 <div style="display:inline-block;float:left;">
                   <input type="text" class="form-control span2" name="dd_date"  id="dd_date" style="width:150px;"  value="<?php if($sel_result['dd_date']=="0000-00-00"){}else{  echo $sel_result['dd_date'];}?>">
                 </div>
                 <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i> </div>
               </div>
             </div>
           </div>
           <div class="form-group">
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Courier Name</label>
               <div class="col-md-6">
                 <input name="courier_name" id="courier_name" type="text" value="<?=$sel_result['couriername']?>" class="form-control"/>
               </div>
             </div>
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Docket No.</label>
               <div class="col-md-6">
                 <input name="docket_no" id="docket_no" type="text" value="<?=$sel_result['docketno']?>" class="form-control"/>
               </div>
             </div>
           </div>
           <div class="form-group">
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Courier Date</label>
               <div class="col-md-6">
                 <div style="display:inline-block;float:left;">
                   <input type="text" class="form-control span2" name="courier_date"  id="courier_date" style="width:150px;"  value="<?=$sel_result['courierdate']?>">
                 </div>
                 <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i> </div>
               </div>
             </div>
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Attachment</label>
               <div class="col-md-6">
                 <input type="file"  name="file"  id = "file"  class="form-control" value="<?=$sel_result['attachment']?>"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/  >
               </div>
             </div>
           </div>
           <div class="form-group">
             <div class="col-md-6">
               <label class="col-md-6 custom_label">Send To</label>
               <div class="col-md-6">
                 <select name="to_location" id="to_location" class="form-control required"  >
                   <option value="">Please Select</option>
                   <?php
                $res_pro = mysqli_query($link1,"select wh_location from map_wh_location where location_code ='".$_SESSION['asc_code']."' and status = 'Y' "); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){
				$res_wh = mysqli_fetch_array(mysqli_query($link1 ,"select location_code , locationname from  location_master where location_code ='".$row_pro['wh_location']."' "));
				?>
                   <option value="<?=$res_wh['location_code']?>" <?php if($sel_result['to_location'] == $res_wh['location_code']) { echo 'selected'; }?>>
                   <?=$res_wh['locationname']." (".$res_wh['location_code'].")"?>
                   </option>
                   <?php } ?>
                 </select>
               </div>
             </div>
             <div class="col-md-6">
               <label class="col-md-6 custom_label"></label>
               <div class="col-md-6"> </div>
             </div>
           </div>
           <div class="form-group">
             <div class="col-md-12" align="center">
               <?php if($_REQUEST['op']=='Add'){ ?>
               <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add Payment   Details">
               <?php }else{
				   if($sel_result['status']==1){?>
               <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Payment Details">
               <?php  }}?>
               <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
               <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='myaccount_add_payment.php?<?=$pagenav?>'">
             </div>
           </div>
         </div>
       </div>
       <!-- end panal-->
       </div>
       <!-- end panal group-->
     </form>
   </div>
   <!--End col-sm-9--> 
 </div>
 <!--End row content-->
 </div>
 <!--End container fluid-->
 <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>