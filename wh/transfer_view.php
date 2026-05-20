<?php
require_once("../includes/config.php");
$dt = date('m-Y');
$docid=base64_decode($_REQUEST['refid']);
//// po to vendor details
$job_sql="SELECT * FROM current_cr_status where location_code='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$location_info =  explode("~",getAnyDetails($job_row['location_code'],"locationname,stateid,cityid","location_code","location_master",$link1));

///// after hitting receive button ///
 if ($_POST['save']=='Receive'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	if($_POST['security_amt'] >$_POST['amount']){
		$flag = false;
	   $error_msg = "Enter Security Amount is more than Main Amount";	
	}
	else
	{
	 /// insert  into  location_account_ledger table //////////////////////////
	 echo "insert into location_account_ledger set transaction_type ='Amount Transfer to Security A/C',month_year ='".$dt ."' , crdr = 'DR' , amount = '".$_POST['security_amt']."' , entry_date = '".$today."' , remark = '".$_POST['remark']."' , location_code = '".$docid."'";
   $result1=mysqli_query($link1,"insert into location_account_ledger set transaction_type ='Amount Transfer to Security A/C',month_year ='".$dt ."' , crdr = 'DR' , amount = '".$_POST['security_amt']."' , entry_date = '".$today."' , remark = '".$_POST['remark']."' , location_code = '".$docid."' ");
	//// check if query is not executed
    if (!$result1) {
	   $flag = false;
	   $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
	/// insert  into  location_account_ledgersecurity  table //////////////////////////
   $result2=mysqli_query($link1,"insert into location_account_ledger_security set transaction_type ='Security Amount',month_year ='".$dt ."' , crdr = 'CR' , amount = '".$_POST['security_amt']."' , entry_date = '".$today."' , remark = '".$_POST['remark']."', location_code = '".$docid."' ");
	//// check if query is not executed
    if (!$result2) {
	   $flag = false;
	   $error_msg = "Error details2: " . mysqli_error($link1) . ".";
    }

	/// insert  into  location_account_ledger table //////////////////////////
   $result3=mysqli_query($link1,"update current_cr_status set  credit_bal = credit_bal -'".$_POST['security_amt']."'  , total_credit_limit = total_credit_limit -'".$_POST['security_amt']."'  ,security_amt = '".$_POST['security_amt']."'  where  location_code = '".$docid."' ");
	//// check if query is not executed
    if (!$result3) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }	
	
	 		////// insert in activity table////
			$flag=dailyActivity($docid,"","Security Transfer","Security Transfer",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	}	
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Security Amount Received ";
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
   header("location:transfer_security.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-lock"></i> Security Transfer </h2>
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
                <td><label class="control-label">Amount</label></td>
                <td><?php echo $job_row['total_credit_limit'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Last Updated Date</label></td>
                <td><?php echo dt_format($job_row['last_updated']);?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
             
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Receive Security</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
          
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Security Amount</label><span style="color:#F00">*</span></td>
                <td width="30%"><input type="text" id="security_amt" name="security_amt" class="digits form-control required" required></td>
                <td width="20%"><label class="control-label">Remark</label><span style="color:#F00">*</span></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control required" required></textarea></td>
              </tr>
			 <tr>
                <td width="100%" align="center" colspan="8">
				<input type="hidden" name="amount" id="amount" value="<?=$job_row['total_credit_limit']?>" >
				
				<input title="Receive" type="submit" class="btn btn<?=$btncolor?>" id="save" name="save" value="Receive" >&nbsp;&nbsp;
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='transfer_security.php?<?=$pagenav?>'">
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