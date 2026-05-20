<?php
require_once("../includes/config.php");

$docid=base64_decode($_REQUEST['id']);
//// po to vendor details
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "' and po_type='CLAIM'";

$po_res = mysqli_query($link1, $po_sql);

$po_row = mysqli_fetch_assoc($po_res);

$location_info =  explode("~",getAnyDetails($po_row['from_location'],"locationname,stateid,cityid","location_code","location_master",$link1));
$cr_limit = getAnyDetails($po_row["from_location"],"total_credit_limit","location_code","current_cr_status",$link1);

///// after hitting receive button ///
 if ($_POST['save']=='Release'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	if($_POST['relese_amt'] >$_POST['amount']){
		$flag = false;
	   $error_msg = "Entered amount is greater than current amount";	
	}
	else
	{
			 $narreation= "Claim Release" ."-".$_POST['claim_month'];
			 
	 /// insert  into  location_account_ledger table //////////////////////////
   $result1=mysqli_query($link1,"insert into location_account_ledger set transaction_type ='".$narreation."',month_year ='".$_POST['claim_month'] ."' , crdr = 'DR' , amount = '".$_POST['relese_amt']."' , entry_date = '".$today."' , remark = '".$_POST['remark']."' , location_code = '".$_POST['location_code']."', transaction_no='".$_POST['inv_no']."' ");
	//// check if query is not executed
    if (!$result1) {
	   $flag = false;
	   $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
	/// insert  into  location_account_ledger table //////////////////////////
   $result4=mysqli_query($link1,"update billing_master set  disp_rmk='Claim Release' where  challan_no = '".$_POST['inv_no']."' ");
	//// check if query is not executed
    if (!$result4) {
	   $flag = false;
	   $error_msg = "Error details4: " . mysqli_error($link1) . ".";
    }

	/// insert  into  location_account_ledger table //////////////////////////
   $result3=mysqli_query($link1,"update current_cr_status set  credit_bal = credit_bal -'".$_POST['relese_amt']."'  , total_credit_limit = total_credit_limit -'".$_POST['relese_amt']."'    where  location_code = '".$_POST['location_code']."' ");
	//// check if query is not executed
    if (!$result3) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }	
	
	 		////// insert in activity table////
			$flag=dailyActivity($docid,"","Claim Release ","Claim Release",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	}	
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Claim Amount Released ";
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
   header("location:claim_generate_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-lock"></i> Claim Release </h2>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp; Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo  $location_info[0];?></td>
                <td width="20%"><label class="control-label">State</label></td>
                <td width="30%"><?php echo  getAnyDetails($location_info[1],"state","stateid","state_master",$link1);?></td>
              </tr>
			   <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo  getAnyDetails($location_info[2],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Claim Amount</label></td>
                <td><?php echo currencyFormat($po_row['total_cost']); ?></td>
              </tr>
              
              <tr>
                <td><label class="control-label">Claim Month</label></td>
                <td><?php echo $po_row['claim_month'];?></td>
                <td><label class="control-label">Invoice No</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
              </tr>
               <tr>
               
                <td><label class="control-label">Current Amount</label></td>
                <td><?php echo currencyFormat($cr_limit);?></td>
                <td><label class="control-label">&nbsp;</label></td>
                <td>&nbsp;</td>
              </tr>
             
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Release Amount</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
          
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Release Amount</label><span style="color:#F00">*</span></td>
                <td width="30%"><input type="hidden" id="claim_month" name="claim_month" class=" form-control " value=<?=$po_row['claim_month']?> >
                <input type="hidden" id="inv_no" name="inv_no" class=" form-control " value=<?=$po_row['challan_no']?> >
                 <input type="hidden" id="location_code" name="location_code" class=" form-control " value=<?=$po_row['from_location']?> >
                <input type="text" id="relese_amt" name="relese_amt" class=" form-control required" value=<?=$po_row['total_cost']?> required></td>
                <td width="20%"><label class="control-label">Remark</label><span style="color:#F00">*</span></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control required" required></textarea></td>
              </tr>
			 <tr>
                <td width="100%" align="center" colspan="8">
				<input type="hidden" name="amount" id="amount" value="<?=$cr_limit?>" >
				
				<input title="Release" type="submit" class="btn btn<?=$btncolor?>" id="save" name="save" value="Release" >&nbsp;&nbsp;
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='claim_generate_list.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
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