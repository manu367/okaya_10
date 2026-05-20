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
  if($_POST['Submit']=='Cancel'){
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
	
	$inv_tot_cost	= explode("~",  getAnyDetails($docid,"total_cost,from_location,po_type,claim_month" , "challan_no" ,"billing_master" ,$link1));
	

		
	  ///// update credit limit of receiver (reverse entry)///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	  
	//  echo "UPDATE current_cr_status set claim_amt = claim_amt - '".$inv_tot_cost[0]."'  where location_code='".$inv_tot_cost[1]."'";			
	$res_cr = mysqli_query($link1,"UPDATE current_cr_status set claim_amt = claim_amt - '".$inv_tot_cost[0]."'  where location_code='".$inv_tot_cost[1]."'");
	if(!$res_cr){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}		
	
	$trns_type ="Invoice cancel From Claim -".$inv_tot_cost[3];
		////////// insert into location acount ledger  for credit entry //////////////////////////////////////////////////////////////////////////////////////
	$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$inv_tot_cost[1]."',entry_date='".$today."',remark='".$remark."', transaction_type = '".$trns_type."',month_year='".date("m-Y")."',crdr='DR',amount='".$inv_tot_cost[0]."' , transaction_no= '".$docid."' ");
	if(!$res_ac_ledger){
		$flag = false;
		$error_msg = "Error details8: " . mysqli_error($link1) . ".";
	}
		
		
			$res_job_claim = mysqli_query($link1,"UPDATE job_claim_appr set claim_no=''  where action_by = '".$inv_tot_cost[1]."'  and  app_status='Y' and hand_date like '%".$inv_tot_cost[3]."%'");
	if(!$res_job_claim){
		$flag = false;
		$error_msg = "Error details14: " . mysqli_error($link1) . ".";
	}
	
	$res_inv= mysqli_query($link1,"UPDATE claim_invoice set status='5'  where claim_no = '".$docid."'");
	if(!$res_inv){
		$flag = false;
		$error_msg = "Error details12: " . mysqli_error($link1) . ".";
	}
	
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

              <td width="3%">#</td>

              <td width="17%"><strong>level Discription</strong></td>

            

              <td width="10%"><strong> Count</strong></td>

              <td width="10%"><strong>Rate</strong></td>

        

			  <td width="10%"><strong>Cost</strong></td>

              <?php if($job_row['to_stateid']==$job_row['from_stateid']){ ?>

			  <td width="5%"><strong>SGST %</strong></td>

			  <td width="5%"><strong>SGST Amount</strong></td>

			  <td width="5%"><strong>CGST %</strong></td>

			  <td width="5%"><strong>CGST Amount</strong></td>

              <?php }else{?>

			  <td width="10%"><strong>IGST %</strong></td>

			  <td width="10%"><strong>IGST Amount</strong></td>

              <?php }?>

			  <td width="10%"><strong>Amount</strong></td>

              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
	 $podata_sql="SELECT * FROM claim_invoice where claim_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($claim_row=mysqli_fetch_assoc($podata_res)){
			?>
               <tr>

                <td><?=$i?></td>

                <td>Repair Level <?=$claim_row['level']."/".$claim_row['cat']?></td>

             

                <td><?=$claim_row['tot_lvl']?></td>

                <td><?=currencyFormat($claim_row['price'])?></td>

              

				<td><?=currencyFormat($claim_row['value'])?></td>

                <?php if($job_row['to_stateid']==$job_row['from_stateid']){ ?>

				<td><?=$claim_row['sgst_per']?></td>

				<td><?=currencyFormat($claim_row['sgst_amt'])?></td>

				<td><?=$claim_row['cgst_per']?></td>

				<td><?=currencyFormat($claim_row['cgst_amt'])?></td>

                <?php }else{?>

				<td><?=$claim_row['igst_per']?></td>

				<td><?=currencyFormat($claim_row['igst_amt'])?></td>

                <?php }?>

				<td><?=currencyFormat($claim_row['total_cost'])?></td>       

                </tr>
            <?php
			$total+=$claim_row['qty'];

			$price+=$claim_row['price'];

			$value+=$claim_row['total_cost'];    
			$i++;
			}
				if($po_row['to_stateid']==$po_row['from_stateid']){ $colspn=7; }else{ $colspn=9;}

			?>
			
			<tr>

                	<td colspan="<?=$colspn?>" align="right"><strong>Sub Total</strong></td>

                    <td><?php echo currencyFormat($value); ?></td>

                </tr>

                <tr>

                	<td colspan="<?=$colspn?>" align="right"><strong>Round Off</strong></td>

                    <td><?php echo currencyFormat($job_row['round_off']); ?></td>

                </tr>

                <tr>

                	<td colspan="<?=$colspn?>" align="right"><strong>Total Amount</strong></td>

                    <td><?php echo currencyFormat($job_row['total_cost']); ?></td>

                </tr>

                <tr>

                  <td colspan="<?=$colspn+1?>"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($job_row['total_cost']) . " Only"; ?></td>

                </tr>

                <tr>

                  <td colspan="<?=$colspn+1?>"><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$job_row['billing_rmk']."-".$job_row['claim_month']  ?></td>

                </tr>
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