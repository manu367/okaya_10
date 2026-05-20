<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// invoice  details
$job_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$fromlocation =getAnyDetails($job_row["from_location"],"locationname" ,"location_code","location_master",$link1);
////////// if from_location exist  in vendor master table///////////////////////////////////
 if($fromlocation == '')
 {
  $fromlocation = getAnyDetails($job_row["from_location"],"name" ,"id","vendor_master",$link1);
}
////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST[Submit]=='Cancel'){
	  mysqli_autocommit($link1, false);
	  $flag = true;	  
	  ///// cancel po in billing_master ///////////
	   $query1=("UPDATE billing_master set status='5',cancel_by='".$_SESSION['userid']."',cancel_date='".$today."',cancel_rmk='".$remark."' where challan_no='".$docid."'");	
	  $result = mysqli_query($link1,$query1);	  
	  //// check if query is not executed
	  if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	
	$inv_tot_cost	= explode("~",  getAnyDetails($docid,"total_cost,to_location,po_type,from_location" , "challan_no" ,"billing_master" ,$link1));
	
		/////////////////////   condition for po, pna, localpurchase /////////////////////////////////////////////////////////////
	if($inv_tot_cost[2] == 'PO' || $inv_tot_cost[2] == 'PNA') {
		
	  ///// update credit limit of receiver (reverse entry)///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
	$res_cr = mysqli_query($link1,"UPDATE current_cr_status set credit_bal = credit_bal +  '".$inv_tot_cost[0]."', total_credit_limit = total_credit_limit + '".$inv_tot_cost[0]."' where location_code='".$inv_tot_cost[1]."'");
	if(!$res_cr){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}		
		////////// insert into location acount ledger  for credit entry //////////////////////////////////////////////////////////////////////////////////////
	$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$inv_tot_cost[1]."',entry_date='".$today."',remark='".$remark."', transaction_type = '".$inv_tot_cost[2]."',month_year='".date("m-Y")."',crdr='CR',amount='".$inv_tot_cost[0]."' , transaction_no= '".$docid."' ");
	if(!$res_ac_ledger){
		$flag = false;
		$error_msg = "Error details8: " . mysqli_error($link1) . ".";
	}
		$podata_sql="SELECT partcode,qty,from_location,to_location,price FROM billing_product_items where challan_no='".$docid."'";
	$podata_res=mysqli_query($link1,$podata_sql);
	while($podata_row=mysqli_fetch_assoc($podata_res)){
	///////// deduct stock from client inventory
  $upd = mysqli_query($link1 , "update client_inventory set okqty= okqty+'".$podata_row['qty']."'  where   location_code = '".$podata_row['from_location']."'  and  partcode = '".$podata_row['partcode']."' " );
   //// check if query is not executed
	  if (!$upd) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	/////////// insert entry into stock ledger//////////////////////////////////////
	$flag=stockLedger($docid,$today,$podata_row['partcode'],$_SESSION['userid'],$podata_row['from_location'],"IN","OK","Stock IN","Invoice Cancel",$podata_row['qty'],$podata_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
	
		}//////////////// CLOSE OF WHILE LOOP
	
	////// insert in activity table////
	 $flag = dailyActivity($_SESSION['userid'],$docid,"Invoice ","Cancel",$ip,$link1,$flag);
		//// entry in stock ledger ///////////////////////////////////////////////////////////////
	$flag=stockLedger($docid,$today,"",$inv_tot_cost[3],$inv_tot_cost[3],"",$inv_tot_cost[2],$inv_tot_cost[2],"Invoice Cancel","",$inv_tot_cost[0],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
	}
	else if ($inv_tot_cost[2] == 'LOCAL PURCHASE' ) {
	  /////////////// update inventory if cancel for local purchase  ///////////////////////////////////////////////////////////////////////////////////////////
	$podata_sql="SELECT * FROM billing_product_items where challan_no='".$docid."'";
	$podata_res=mysqli_query($link1,$podata_sql);
	while($podata_row=mysqli_fetch_assoc($podata_res)){
	///////// deduct stock from client inventory
  $upd = mysqli_query($link1 , "update client_inventory set okqty= okqty-'".$podata_row['qty']."'  where   location_code = '".$podata_row['to_location']."'  and  partcode = '".$podata_row['partcode']."' " );
   //// check if query is not executed
	  if (!$upd) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	/////////// insert entry into stock ledger//////////////////////////////////////
	$flag=stockLedger($docid,$today,$podata_row['partcode'],$podata_row['from_location'],$podata_row['to_location'],"OUT","OK","Stock OUT","Invoice Cancel",$podata_row['qty'],$podata_row['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
	
		}//////////////// CLOSE OF WHILE LOOP
	}
  else {}
	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$docid,"Invoice ","Cancel",$ip,$link1,$flag);
		
	  ///// check  master  query are successfully executed
	 if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Invoice  is Cancelled successfully with Invoice no." .$docid ;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		$cflag = "danger";
		$cmsg = "Failed";
	} 
    mysqli_close($link1);
	  
  }/// 
  ///// move to parent page
header("Location:invoice_cancellation.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-desktop"></i>&nbsp;&nbsp;Invoice Cancel</h2>
      <h4 align="center">Invoice No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">  
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Invoice Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location Name</label></td>
                <td width="30%"><?php echo $fromlocation;?><input type="hidden" id="" name="from_location" value="<?=$job_row["from_location"]?>"></td>
                <td width="20%"><label class="control-label">To Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["to_location"],"locationname" ,"location_code","location_master",$link1);?><input type="hidden" id="to_location" name="to_location" value="<?=$job_row["to_location"]?>"></td>
              </tr>
              <tr>
                <td><label class="control-label">GST No.</label></td>
                <td><?php echo $job_row['from_gst_no'];?></td>
                <td><label class="control-label">GST No.</label></td>
                <td><?php echo $job_row['to_gst_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Type</label></td>
                <td><?php echo $job_row['po_type'];?><input type="hidden" id="po_type" name="po_type" value="<?=$job_row['po_type']?>" ></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Invoice Items Details</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="5%" style="text-align:center">#</th>
                <th width="20%" style="text-align:center">Item Description</th>
                <th width="15%" style="text-align:center">Partcode</th>
                <th width="8%" style="text-align:center">Qty</th>
                <th width="10%" style="text-align:center">Price</th>
                <th width="9%" style="text-align:center">Basic Amt</th>
				<th width="6%" style="text-align:center">Cgst Per</th>
				<th width="10%" style="text-align:center">Cgst Amt</th>
				<th width="5%" style="text-align:center">Sgst Per</th>
				<th width="10%" style="text-align:center">Sgst Amt</th>
				<th width="10%" style="text-align:center">Igst Per</th>
				<th width="12%" style="text-align:center">Igst Amt</th>
				<th width="12%" style="text-align:center">Total Amt</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_product_items where challan_no='".$job_row['challan_no']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
            	 <td><?=getAnyDetails($podata_row['partcode'],"part_name","partcode","partcode_master",$link1);?></td>    
				<td><?=$podata_row['partcode'];?></td> 
                <td><?=$podata_row['qty'];?></td>
                <td><?=$podata_row['price'];?></td>
                <td><?=$podata_row['qty']*$podata_row['price'];?></td>    
				<td><?=$podata_row['cgst_per'];?></td> 
				<td><?=$podata_row['cgst_amt'];?></td> 
				<td><?=$podata_row['sgst_per'];?></td> 
				<td><?=$podata_row['sgst_amt'];?></td>    
				<td><?=$podata_row['igst_per'];?></td> 
				<td><?=$podata_row['igst_amt'];?></td> 
				<td><?=$podata_row['item_total'];?></td>         
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
       <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Cancel Reason</div>
      <div class="panel-body">
       
        <table class="table table-bordered" width="100%">
            <tbody>
              
              <tr>
                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
         
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='invoice_cancellation.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
         
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
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