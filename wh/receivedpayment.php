<?php
require_once("../includes/config.php");
$dt = date('m-Y');
////////////////////////////////////////// fetching datta from payment details table///////////////////////////////////////////////
$po_sql="select * from payment_details where challan_no='".$_REQUEST['challan_no']."' ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){
	$ref_no=base64_decode($_POST['refno']);
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	//// Update  in  payment_details table
   $result=mysqli_query($link1,"update payment_details set status='4', receiveddate='".$today."',remark ='".$_POST['rcv_rmk']."' , receive_amt = '".$_POST['rcv_amt']."'  where challan_no ='".$ref_no."' ");
	//// check if query is not executed
    if (!$result) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }

   /// insert  into  location_account_ledger table //////////////////////////
   $result1=mysqli_query($link1,"insert into location_account_ledger set transaction_type ='Payment Received', transaction_no='".$ref_no."',month_year ='".$dt ."' , crdr = 'CR' , amount = '".$_POST['rcv_amt']."' , entry_date = '".$today."' , remark = 'Payment Received' , location_code = '".$_POST['from_location']."' ");
	//// check if query is not executed
    if (!$result1) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	

	/// insert  into  location_account_ledger table //////////////////////////
   $result2=mysqli_query($link1,"update current_cr_status set  credit_bal = credit_bal +'".$_POST['rcv_amt']."'  , total_credit_limit = total_credit_limit +'".$_POST['rcv_amt']."'   where  location_code = '".$_POST['from_location']."' ");
	//// check if query is not executed
    if (!$result2) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	
	
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['asc_code'],$ref_no,"Payment","RECEIVE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Received for ".$_REQUEST['challan_no'];
		$cflag="success";
		$cmsg="Success";
    } else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
   header("location:wh_receive_payment.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
<script type="text/javascript">
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
   <div class="<?=$screenwidth?> tab-pane fade in active">
      <h2 align="center"><i class="fa  fa-money"></i> Receive Payment</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Payment Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["from_location"],"locationname","location_code","location_master",$link1)."(".$po_row['from_location'].")";?></td>
                <td width="20%"><label class="control-label">To Location</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["to_location"],"locationname","location_code","location_master",$link1)."(".$po_row['to_location'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Document Date</label></td>
                <td><?php echo dt_format($po_row['entry_date']);?></td>
              </tr>  
              <tr>
                <td><label class="control-label">Bank Name</label></td>
                <td><?php echo $po_row['bankname'];?></td>
                <td><label class="control-label">Payment Mode</label></td>
                <td><?php echo $po_row['pay_mode'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">DD/Cheque No.</label></td>
                <td><?php echo $po_row['dd_chequeno'];?></td>
                <td><label class="control-label">DD/Cheque Date</label></td>
                <td><?php echo dt_format($po_row['dd_date']);?></td>
              </tr>
             <tr>
                <td><label class="control-label">Courier Name</label></td>
                <td><?php echo $po_row['couriername'];?></td>
                <td><label class="control-label">Docket No.</label></td>
                <td><?php echo ($po_row['docketno']);?></td>
              </tr>
             <tr>
                <td class="btn-success"><label class="control-label">Amount</label></td>
                <td class="btn-success"><?php echo currencyFormat($po_row['amount']);?></td>
                <td><label class="control-label">Courier Date</label></td>
                <td><?php echo dt_format($po_row['courierdate']);?></td>
              </tr>
              
			  <tr>
                <td><label class="control-label">Account No.</label></td>
                <td><?php echo $po_row['account_no'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php if($po_row['status'] == '1') { echo "Pending"; } ?></td>
              </tr>       
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Receive Status</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>          
               <tr>
                   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea name="rcv_rmk" id="rcv_rmk" class="form-control required" style="resize:none;width:200px;" required></textarea></td>
				 <td><label class="control-label">Receive Amount <span style="color:#F00">*</span></label></td>
                 <td><input type= "text" name="rcv_amt" id="rcv_amt" class="number form-control required" required></input></td>                  
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
                    <input name="from_location" id="from_location" type="hidden" value="<?=$po_row['from_location']?>"/>
                    <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['challan_no'])?>"/>
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='wh_receive_payment.php?<?=$pagenav?>'">
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
</html>