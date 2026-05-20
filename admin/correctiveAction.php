<?php
require_once("../includes/config.php");
//// details
$request_no  = base64_decode($_REQUEST['request_no']);
$job_sql="SELECT * FROM audit_details where request_no='".$request_no."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
@extract($_POST);
if ($_POST['save']=='Save'){
	//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
		
		//////// update correction  in main table ///////////////////////////	
		$sql1=mysqli_query($link1,"Update audit_details set remak1='".$_POST['corActionPlan1']."',remak2='".$_POST['corActionPlan2']."' ,remak3='".$_POST['corActionPlan3']."' ,remak4='".$_POST['corActionPlan4']."' ,Respos1='".$_POST['corActionPlanResp1']."' ,Respos2='".$_POST['corActionPlanResp2']."' ,Respos3='".$_POST['corActionPlanResp3']."' ,Respos4='".$_POST['corActionPlanResp4']."' ,t1_date='".$_POST['corActionPlanDate1']."',t2_date='".$_POST['corActionPlanDate2']."' ,t3_date='".$_POST['corActionPlanDate3']."',t4_date='".$_POST['corActionPlanDate4']."' , update_by='".$_SESSION['userid']."' ,update_date = '".$today."' where request_no='".$request_no."' " );
	//// check if query is not executed
		if (!$sql1) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
	
		///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$request_no,"Corrective Action","Insert Score",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg="Successfully  Corrective Action taken of ".$request_no;
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
		header("location:location_feedback.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&id=".$_REQUEST['sno']."&request_no=".$request_no."".$pagenav);
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
  <script type="text/javascript" src="../js/moment.js"></script>
   <script type="text/javascript" language="javascript" >
   $(document).ready(function () {
	$('#corActionPlanDate1').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});

$(document).ready(function () {
	$('#corActionPlanDate2').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});

$(document).ready(function () {
	$('#corActionPlanDate3').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});

$(document).ready(function () {
	$('#corActionPlanDate4').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});
 
   </script>
   <!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
  include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-balance-scale"></i>Corrective Action & Improvements Points</h2>
       <h4 align="center"><?=$request_no?></h4>
	   <form name="auditForm"  id= "auditForm" method="post" >
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Location Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo $job_row['name'];?></td>
                <td width="20%"><label class="control-label">Location Code</label></td>
                <td width="30%"><?php echo $job_row['location_code'];?><input type="hidden" id="location" name="location" value="<?=$job_row['location_code'];?>"></td>
              </tr>
              <tr>
                <td><label class="control-label">Address</label></td>
                <td><?php echo getAnyDetails($job_row['location_code'],"locationaddress","location_code","location_master",$link1);?></td>
                <td><label class="control-label">Audit Id</label></td>
                <td><?php echo $job_row['request_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Audit Date</label></td>
                <td><?php echo dt_format($job_row['audit_date']);?></td>
                <td><label class="control-label">Audit by</label></td>
                <td><?php echo $job_row['update_by'];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;Corrective Action & Improvements Points Agreed In This Audit</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>              
            <tr>
			<td width="5%"  align="center">SNo</strong></td>
              <td width="10%"><strong>Action Points Planned & Agreed</strong></td>
              <td width="30%" align="center"><strong>Responsibility</strong></td>
              <td width="7%" align="center"><strong>Target Date</strong></td>
            </tr>
			 <tr>
			<td width="5%">1</td>
             <td width="30%"><input type="text"  name="corActionPlan1" id= "corActionPlan1" size="75" value="" class="form-control"></td>
              <td width="6%" align="center"><input type="text" name="corActionPlanResp1"  id="corActionPlanResp1" maxlength="100" size="10" value="" class="form-control"></td>
			  <td width="10%"><input type="text" class="form-control span2 required" name="corActionPlanDate1"  id="corActionPlanDate1" style="width:150px;" required value="<?=$today?>"><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
            </tr>
			<tr>
             <td width="5%">2</td>
             <td width="30%"><input type="text" name="corActionPlan2" id= "corActionPlan2" size="75" value="" class="form-control"></td>
              <td width="6%" align="center"><input type="text" name="corActionPlanResp2"  id="corActionPlanResp2" maxlength="100" size="10" value="" class="form-control"></td>
			  <td width="10%"><input type="text" class="form-control span2 required" name="corActionPlanDate2"  id="corActionPlanDate2" style="width:150px;" required value="<?=$today?>"><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
            </tr>
			<tr>
             <td width="5%">3</td>
              <td width="20%"><input type="text" name="corActionPlan3" id= "corActionPlan3" size="75" value="" class="form-control"></td>
              <td width="30%"><input type="text" name="corActionPlanResp3"  id="corActionPlanResp3" maxlength="100" size="10" value="" class="form-control"></td>
			  <td width="10%"><input type="text" class="form-control span2 required" name="corActionPlanDate3"  id="corActionPlanDate3" style="width:150px;" required value="<?=$today?>"><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
            </tr>
			<tr>
             <td width="5%">4</td>
              <td width="20%"><input type="text" name="corActionPlan4" id= "corActionPlan4" size="75" value="" class="form-control"></td>
              <td width="30%"><input type="text" name="corActionPlanResp4"  id="corActionPlanResp4" maxlength="100" size="10" value="" class="form-control"></td>
			  <td width="10%"><input type="text" class="form-control span2 required" name="corActionPlanDate4"  id="corActionPlanDate4" style="width:150px;" required value="<?=$today?>"><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div></td>
            </tr>
			<tr>
                 <td width="100%" align="center" colspan="7"><input type="submit" class=" btn<?=$btncolor?>" name="save" id="save" value="Save" title="Save Score">&nbsp;&nbsp;<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_feedback.php?id=<?=$_REQUEST['sno'];?>&request_no=<?=$request_no;?><?=$pagenav?>'"></td>
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