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
	
	///////////////////////// Fetch Jobsheet Data /////////////////////////////
		$job_details = mysqli_fetch_assoc(mysqli_query($link1,"select * from jobsheet_data where job_no='".$job_no."'"));
    /////////  Insert QC list////////////////////////////

		   
$up_qc=mysqli_query($link1,"insert into qc_check_list set job_no='".$job_no."',q1='".$q1."',q2='".$q2."',q3='".$q3."',q4='".$q4."',q5='".$q5."',q6='".$q6."',q7='".$q7."',q8='".$q8."',q9='".$q9."',q10='".$q10."',q11='".$q11."',q12='".$q12."',q13='".$q13."',q14='".$q14."',q15='".$q15."',q16='".$q16."',q23='".$q23."',qc_fail_remark='".$qc_remark."',location_code='".$_SESSION['asc_code']."',qc_date='".$today."',qc_eng='".$_SESSION['userid']."'");

   //////////////////////////////// Insert call  history//////////////////////////////////////
    $flag2 = callHistory($_POST['job_no'],$_SESSION['asc_code'],$q23,"Pending For Dispatch Back","Pending For Dispatch Back",$_SESSION['userid'],"","","",$qc_remark,$ip,$link1,$flag);

	  	//// inser SFR Repair Bin details in sfr bin table
		$rep_sfrbin = mysqli_query($link1,"INSERT INTO sfr_repaired_bin set location_code='".$_SESSION['asc_code']."', to_location='".$job_details['location_code']."', job_no='".$job_no."', imei='".$job_details['imei']."', model_id='".$job_details['model_id']."',  	part_id='".$job_details['partcode']."', qty='1', entry_date='".$today."', status='417'");
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
	 
   $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='".$q23."',eng_id='".$eng_name."'  where job_no='".$_POST['job_no']."'" );

   if (!$up_job) {
    $flag = false;
   $msg = "Error details2.1: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_job_qc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }

  else  if (!$flag2) {
    $flag = false;
   $msg = "Error details2.2: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_job_qc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
    else  if (!$up_qc) {
    $flag = false;
   $msg = "Error details2.3: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_job_qc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
     else  if (!$rep_sfrbin) {
    $flag = false;
   $msg = "Error details2.4: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_job_qc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
  
  else {
  
  $cflag="success";
		$cmsg="Success";
		$msg="You have successfully Update QC   job no ".$_REQUEST['job_no'];
		mysqli_commit($link1);
	header("location:sfr_job_qc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-list-alt"></i> Job QC </h2>
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
                <td width="20%"><label class="control-label">QC Status </label></td>
                <td width="30%"><select name="q4" class="form-control requried" id="q4" >
                    <option value="Ok" selected="selected">Repair</option>
                    <option value="RWRL">Liquid damaged (RWR)</option>
                    <option value="RWRT">Tempered (RWR)</option>
                    <option value="RWRM">Mismatched (RWR)</option>
                  </select></td>
                <td width="20%"><label class="control-label">Incoming/Outgoing Audio</label></td>
                <td width="30%"><select name="q5" class="form-control requried" id="q5" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
              </tr>
            <tr>
              <td><label class="control-label">Jobsheet </label></td>
              <td> <select name="q1"  id="q1"   class="form-control requried"  >
                      <option value="Ok">OK</option>
                      <option value="Mis">Mismatched</option>
                    </select></td>
              <td><label class="control-label">Model</label></td>
              <td><select name="q2" class="form-control requried" id="q2"  >
                    <option value="Ok">OK</option>
                    <option value="Mis">Mismatched</option>
                  </select></td>
            </tr>
            <tr>
              <td><label class="control-label">IMEI</label></td>
              <td><select name="q3" class="form-control requried" id="q3" >
                    <option value="Ok">OK</option>
                    <option value="Mis">Mismatched</option>
                  </select></td>
              <td><label class="control-label">Latest S/W Version</label></td>
              <td><select name="q6" class="form-control requried" id="q6" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
            </tr>
            <tr>
              <td><label class="control-label">Keypad Ok</label></td>
              <td><select name="q7" class="form-control requried" id="q7" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
              <td><label class="control-label">Touch Pad</label></td>
              <td><select name="q8" class="form-control requried"  id="q8" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
            </tr>
			  <tr>
              <td><label class="control-label">Touch Light</label></td>
              <td><select name="q9" class="form-control requried" id="q9" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
              <td><label class="control-label">Hang/Slow Processing</label></td>
              <td><select name="q10" class="form-control requried"  id="q10" >
                     <option value="NO">NO</option>
                    <option value="YES">YES</option>
                  </select></td>
            </tr>
			  <tr>
              <td><label class="control-label">Display / Backlight / LCD Lens</label></td>
              <td><select name="q11" class="form-control requried" id="q11" >
                    <option value="Ok">OK</option>
                    <option value="BRK">Broken</option>
                    <option value="SCR">Scratches</option>
                  </select></td>
              <td><label class="control-label">Keypad / Backlight</label></td>
              <td><select name="q12" class="form-control requried"  id="q12" >
                    <option value="Ok">OK</option>
                    <option value="BRK">Broken</option>
                  </select></td>
            </tr>
			  <tr>
              <td><label class="control-label">MIC</label></td>
              <td><select name="q13" class="form-control requried"  id="q13" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
              <td><label class="control-label">Earpiece</label></td>
              <td><select name="q14" class="form-control requried"  id="q14" >
                      <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
            </tr>
			  <tr>
              <td><label class="control-label">Ringer/Ringtone</label></td>
              <td><select name="q15" class="form-control requried" id="q15" >
                     <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
              <td><label class="control-label">Charging</label></td>
              <td><select name="q16" class="form-control requried"  id="q16" >
                    <option value="Ok">OK</option>
                    <option value="Fail">Fail</option>
                  </select></td>
            </tr>
			  <tr>
              <td><label class="control-label">QC (PASS / FAIL MARKERS)</label></td>
              <td><select name="q23" class="form-control requried" id="q23" >
                     <option value="417">Pass</option>
                    <option value="418">Fail</option>
                  </select></td>
              <td><label class="control-label">Remark</label></td>
              <td>  <textarea name="qc_remark" id="qc_remark" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea></td>
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