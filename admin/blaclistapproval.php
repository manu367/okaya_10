<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po to vendor details
$job_sql="SELECT * FROM customer_master where customer_id ='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
@extract($_POST);
if($_POST){
if($_POST['save']=='Save'){
	
////// initialize parameter ////////////////////////////////
	mysqli_autocommit($link1, false);
	$flag = true;
	/////////////// update status in master table////////////////////////////////////////
	 $upd_status="update  customer_master set b_cust_id ='".$status."'  where customer_id ='".$docid."' ";
    $result=mysqli_query($link1,$upd_status);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
		////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"Black List Action",$status,$_SERVER['REMOTE_ADDR'],$link1,$flag);	
	$msg = "Successfully done";
	if ($flag) {
        mysqli_commit($link1);  
		$cflag = "success";
		$cmsg = "Success";  
    } else {
		mysqli_rollback($link1);
			$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	   ///// move to parent page
    header("location:customer_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript">
  
 $(document).ready(function(){
        $("#frm1").validate();
    });

 </script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
       include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-ship"></i> BlackList Action</h2>
      <h4 align="center">Customer ID- <?=$docid?></h4>
	  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Customer ID</label></td>
                <td width="30%"><?php echo $job_row['customer_id'];?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $job_row['address1'];?></td>
                <td><label class="control-label">State</label></td>
                <td><?php  echo getAnyDetails($job_row["stateid"],"state","stateid","state_master",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $job_row['email'];?></td>
                <td><label class="control-label">Mobile No.</label></td>
                <td><?php echo $job_row["mobile"];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>Action</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            
            <tbody>
			  	<?php if($job_row['b_custid'] == ''){?>
			<tr>
            <td colspan="4">Status</td><td><select class="form-control required" required name="status" id="status" colspan="2" />
                      <option value="">Please Select</option>
                      <option value="Y">Y</option>
                      <option value="N">N</option>
                   </select></td>
            </tr><?php }?>
			 <tr>
                <td width="100%" align="center" colspan="12">
			      <?php if($job_row['b_custid'] == ''){?>
				    <input title="save" type="submit" class="btn btn<?=$btncolor?>" id = "save"  name= "save" value="Save" >
                    <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='customer_list.php?<?=$pagenav?>'">
                 <input type="hidden" name="docid" id="docid" value="<?=$job_row['customer_id']?>" />
                 <?php } else {?>
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='customer_list.php?<?=$pagenav?>'">
                   <?php }?>
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