<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);
$job_sql="SELECT * FROM jobsheet_data where job_no='".$_REQUEST['job_no']."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters
	$flag=true;
mysqli_autocommit($link1, false);
$error_msg="";
    if ($_POST['upd']=='Update'){
    /////////  checking  sfr Transaction////////////////////////////
		   


   //////////////////////////////// Insert call  history//////////////////////////////////////
    $flag2 = callHistory($_POST['job_no'],$_SESSION['asc_code'],"42","Assigned to Engineer","Assigned to Engineer",$_SESSION['userid'],$ws,$_REQUEST['els_status'],"","",$ip,$link1,$flag);

	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
	 
   $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='42',eng_id='".$eng_name."'  where job_no='".$_POST['job_no']."'" );

   if (!$up_job) {
    $flag = false;
   $msg = "Error details2.1: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_assign_job.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }

  else  if (!$flag2) {
    $flag = false;
   $msg = "Error details2.2: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_assign_job.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
  
  else {
  
  $cflag="success";
		$cmsg="Success";
		$msg="You have successfully Assign   job no ".$_REQUEST['job_no'];
		mysqli_commit($link1);
	header("location:sfr_assign_job.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
 


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
 <script>
$(document).ready(function(){
        $("#frm1").validate();
});
$(document).ready(function () {
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-list-alt"></i> Job Assign </h2>
      <h4 align="center">Job No.- <?=$_REQUEST['job_no']?></h4>
   <div class="panel-group">
     <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Location Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Details</label></td>
                <td width="30%"><?php echo $to_address=getLocationAddress($job_row["location_code"],$link1); ;?></td>
            
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
              <td><?=$$job_row['call_for']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=$job_row['dop']?></td>
              <td><label class="control-label">Activation Date</label></td>
              <td><?=$$job_row['activation']?></td>
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
              <td><?php echo $job_row['warranty_status'];?>   <input type="hidden" name="warranty_status"  id="warranty_status" value="<?=$job_row['warranty_status']?>" /></td>
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
              <td><label class="control-label">Remark By ASP </label></td>
              <td ><?=$job_row['remark']?></td>
			   <td><label class="control-label">Engineer Name </label></td>
              <td> <select name="eng_name" id="eng_name" class="form-control requried">
                <?php
				$qry_usr="Select * from locationuser_master where location_code = '".$_SESSION['asc_code']."' and statusid='1'";
$result_usr=mysqli_query($link1,$qry_usr);
		  while($arr_usr=mysqli_fetch_array($result_usr)){
		  ?>
        <option value="<?=$arr_usr['userloginid']?>" <?php if($_POST['eng_name']==$arr_usr['userloginid']) echo " selected"?>>
          <?=$arr_usr['locusername']?>
        </option>
        <?php
		  }
		  ?>
      </select>
                 </select></td>
            </tr>
            <tr>
                 <td width="100%" align="center" colspan="4"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='sfr_els_job.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="update els">  <input type="hidden" name="job_no"  id="job_no" value="<?=$job_row['job_no']?>" /></td>
               </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 </form>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>