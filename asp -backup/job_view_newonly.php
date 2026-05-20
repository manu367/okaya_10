<?php
require_once("../includes/config.php");
$docid=$_REQUEST['refid'];
	$todayDate=date("Y-m-d");
	$todayTime=date("H:i:s");
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
////// final submit form ////
if($_POST){
@extract($_POST);
	if($_POST['saveticket']=='Save'){
		//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
			
		/////////////////////  entry in call history table ///////////////////////////
	 	$query="INSERT INTO call_history set job_no='".$_REQUEST['refid']."',location_code='".$_REQUEST['locationcode']."',status='Priority update',activity='Priority update',outcome='Priority update',updated_by='".$_SESSION['userid']."', warranty_status='".$_REQUEST['warranty']."', remark='".$remark."', ip='".$_SERVER['REMOTE_ADDR']."',priority='".$proiority."'";
	$result=mysqli_query($link1,$query);
	//// check if query is not executed
    if (!$result) {
	     $flag = false;
        $error_msg =  "Error detailsCH: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$_REQUEST['refid'],"Job","Priority",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	///// check query are successfully executed
	if ($flag) {
		$msg = "Sucessfully set Priority of job no.".$_REQUEST['refid'];
	    mysqli_commit($link1);
	} else {
	mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	
   ///// move to parent page
header("location:job_ticket_create.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
<div class="row content">
<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> Job Details</h2>
      <h4 align="center">Job No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $job_row['address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
                <td><?php echo $job_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $job_row['alternate_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $job_row['email'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $job_row['pincode'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Type</label></td>
                <td><?php echo $job_row['customer_type'];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Product</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td width="20%"><label class="control-label">Brand</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
            <tr>
              <td><label class="control-label">Model</label></td>
              <td><?=$job_row['model']?></td>
              <td><label class="control-label">Accessory Present</label></td>
              <td><?php echo $job_row['acc_rec'];?></td>
            </tr>
            <tr>
              <td><label class="control-label">IMEI 1/Serial No. 1</label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">IMEI 2/Serial No. 2</label></td>
              <td><?=$job_row['sec_imei']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Job Type</label></td>
              <td><?=$job_row['call_type']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=$job_row['dop']?></td>
              <td><label class="control-label">Activation Date</label></td>
              <td><?=$job_row['activation']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Initial Symptom</label></td>
                <td width="30%"><?php echo $job_row['symp_code'];?></td>
                <td width="20%"><label class="control-label">Physical Condition</label></td>
                <td width="30%"><?php echo $job_row['phy_cond'];?></td>
              </tr>
            <tr>
              <td><label class="control-label">ELS Status</label></td>
              <td><?=$job_row['els_status']?></td>
              <td><label class="control-label">Warranty Status</label></td>
              <td><?php echo $job_row['warranty_status'];?></td>
            </tr>
            <tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
            </tr>
            <tr>
              <td><label class="control-label">VOC</label></td>
              <td><?=$job_row['voc1']?></td>
              <td><?=$job_row['voc2']?></td>
              <td><?=$job_row['voc3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Remark </label></td>
              <td colspan="3"><?=$job_row['remark']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>Location</strong></td>
                    <td width="10%"><strong>Activity</strong></td>
                    <td width="15%" colspan="2"><strong>Outcome</strong></td>
                    <td width="10%"><strong>Warranty</strong></td>
                   
                    <td width="10%"><strong>Update By</strong></td>
                    <td width="10%"><strong>Remark</strong></td>
					<td width="10%"><strong>Priority</strong></td>
                    <td width="15%"><strong>Update on</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_jobhistory = mysqli_query($link1,"SELECT * FROM call_history where job_no='".$docid."'");
				while($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)){
				?>
                  <tr>
                    <td><?=$row_jobhistory['location_code']?></td>
                    <td><?=$row_jobhistory['activity']?></td>
                    <td colspan="2"><?=$row_jobhistory['outcome']?></td>
                    <td><?=$row_jobhistory['warranty_status']?></td>
                   
                    <td><?=$row_jobhistory['updated_by']?></td>
                    <td><?=$row_jobhistory['remark']?></td>
					 <td><?php if($row_jobhistory['priority'] == '1') {echo "Low";} elseif($row_jobhistory['priority'] == '2') {echo "Normal";} else {echo "High" ;}?></td>
                    <td><?=$row_jobhistory['update_date']?></td>
                  </tr>
                  <?php
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

  </div><!--close panel group-->
  <?php if($job_row['status']!='6' && $job_row['status']!='10' ){?>
  <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Priority</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><textarea name="remark" id="remark" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea> </td>
                <td width="20%"><label class="control-label">Priority</label></td>
                <td width="30%"><select name="proiority" id="proiority" class="form-control required"  required >
                          <option value=''>--Select Proiority--</option>
						   <option value='1'>Low</option>
						    <option value='2'>Normal</option>
							 <option value='3'>High</option>
                        </select>  </td>
              </tr>   
            </tbody>
          </table>
		   <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_ticket_create.php?<?=$pagenav?>'">&nbsp;
					  <input type="hidden" id="locationcode" name="locationcode" value ="<?=$job_row['location_code']?>" >
					  <input type="hidden" id="status" name="status" value ="<?=$job_row['status']?>" >
					   <input type="hidden" id="warranty" name="warranty" value ="<?=$job_row['warranty_status']?>" >
                       <input type="submit" class="btn<?=$btncolor?>" name="saveticket" id="saveticket" value="Save" title="Save Ticket Details" <?php if($_POST['saveticket']=='Save'){?>disabled<?php }?>>&nbsp;
                    </div>
                  </div> 
      </div><!--close panel body-->
    </div><!--close panel--><?php }?>
	</form>
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>