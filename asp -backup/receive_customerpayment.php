<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$challan_no = base64_decode($_REQUEST['challan_no']);
////////////////////////////////////////// fetching datta from master table///////////////////////////////////////////////
$po_sql="select * from billing_master where challan_no='".$challan_no."' and from_location='".$_SESSION['asc_code']."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	//// Update status in  billing_master table
   $result=mysqli_query($link1,"update billing_master set status='4',receive_date='".$today."'  where challan_no ='".$challan_no."' and from_location='".$_SESSION['asc_code']."'");
	//// check if query is not executed
    if (!$result) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	
	//// Update status in  billing_master table
    $result1=mysqli_query($link1,"insert into payment_receive_loc set  location_code='".$_REQUEST['from_loc']."',inv_no='".$challan_no."'  ,amount = '".$_REQUEST['rcv_amt']."' ,customer_name = '".$_REQUEST['to_loc']."' , update_date ='".$today."',payment_mode ='".$_REQUEST['payment_mode']."', payment_remark ='".$_REQUEST['remark']."', remark = 'Payment Received From Customer'  ");
	//// check if query is not executed
    if (!$result1) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	/// insert  into  location_account_ledger table //////////////////////////
   $result1=mysqli_query($link1,"insert into location_account_ledger set transaction_type ='Payment Received From Customer', transaction_no='".$challan_no."',month_year ='".date("m-Y")."' , crdr = 'CR' , amount = '".$_REQUEST['rcv_amt']."' , entry_date = '".$today."' , remark = 'Payment Received From Customer' , location_code = '".$_SESSION['asc_code']."' ");
	//// check if query is not executed
    if (!$result1) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	
	////// insert in activity table////
	$flag=dailyActivity($_REQUEST['to_loc'],$challan_no,"Payment","Receive by Customer",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Received Payment for ".$challan_no;
		$cflag="success";
		$cmsg="Success";
    } else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
   header("location:payment_receipt_customer.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-rupee"></i> Receive Customer Payment</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From </label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["from_location"],"locationname","location_code","location_master",$link1)."(".$po_row['from_location'].")";?><input name="from_loc" id="from_loc" type="hidden" value="<?=$po_row['from_location']?>"/></td>
                <td width="20%"><label class="control-label">Customer</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["to_location"],"locationname","location_code","location_master",$link1)."(".$po_row['to_location'].")";?><input name="to_loc" id="to_loc" type="hidden" value="<?=$po_row['to_location']?>"/></td>
              </tr>
              <tr>
                <td><label class="control-label">From Address</label></td>
                <td><?php echo $po_row['from_addrs'];?></td>
                <td><label class="control-label">Customer Address</label></td>
                <td><?php echo $po_row['to_addrs'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Challan No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Sale Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr>  
			  <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php if($po_row['status']) {echo "Pending";}?></td>
                <td><label class="control-label"></label></td>
                <td></td>
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
              <th>S.No</th>
              <th>Item Description</th>
              <th>HSN Code</th>
              <th>Qty</th>
              <th>Unit</th>
              <th>Rate</th>
              <th>Total</th>
              <th>Discount</th>
              <th>Taxable Value</th>
              <th colspan="2">CGST</th>
			  <th colspan="2">SGST</th>
			  <th colspan="2">IGST</th>
            </tr>
			 <tr>
                 <th colspan="10"  align="right" style="text-align:right">Rate</th>
                            <th>Amt.</th>
                            <th>Rate</th>
                            <th>Amt.</th>
                            <th>Rate</th>
                            <th>Amt.</th>
                        </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_product_items where challan_no='".$challan_no."' and from_location='".$_SESSION['asc_code']."'";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
              <tr>
                <td><?=$i?></td>
               <td><?=$data_row['part_name'];?></td>
              <td><?=$data_row['hsn_code'];?></td>
              <td><?=$data_row['qty'];?></td>
              <td><?=$data_row['uom'];?></td>
              <td><?=$data_row['price'];?></td>
              <td><?=$data_row['value'];?></td>
              <td><?=$data_row['discount_amt'];?></td>
              <td><?=$data_row['basic_amt'];?></td>
              <td><?=$data_row['cgst_per'];?></td>
              <td><?=$data_row['cgst_amt'];?></td>
			  <td><?=$data_row['sgst_per'];?></td>
              <td><?=$data_row['sgst_amt'];?></td>
              <td><?=$data_row['igst_per'];?></td>
              <td><?=$data_row['igst_amt'];?></td>
                </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Receive</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>          
               <tr>
			   <td><label class="control-label">Total Amount<span style="color:#F00">*</span></label></td>
                 <td><input type="text" name="tot_amt" id="tot_amt" class="number form-control required"   value="<?=$po_row['total_cost'];?>"  readonly/></td>
                   <td><label class="control-label">Receive Amount <span style="color:#F00">*</span></label></td>
                 <td><input type="text" name="rcv_amt" id="" class="number form-control required"  required /></td>
                   
                 </tr>
                 <td><label class="control-label">Payment Mode<span style="color:#F00">*</span></label></td>
                 <td>
                <select name="payment_mode" id="payment_mode" class="form-control required"  required>
                  <option value="">Select Type--</option>
                  <option value="Cash">Cash</option>
                  <option value="Cheque">Cheque</option>
                  <option value="Credit Card">Credit Card</option>
                  <option value="Debit Card">Debit Card</option>
                   <option value="Paytm">Paytm</option>
                  <option value="Others">Others</option>
                  
                </select></td>
                   <td>Remark</td>
                 <td><input type="text" name="remark" id="remark" class=" form-control "   /></td>
                   
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='payment_receipt_customer.php?<?=$pagenav?>'">
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