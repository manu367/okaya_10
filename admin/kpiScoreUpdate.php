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
	$total_score=$_POST['kpiScore1']+$_POST['kpiScore2']+$_POST['kpiScore3'];
		
		//////// update score in main table ///////////////////////////	
		$sql1=mysqli_query($link1,"Update audit_details set tot_audit_10='".$total_score."' , update_by='".$_SESSION['userid']."' ,update_date = '".$today."' where request_no='".$request_no."' " );
	//// check if query is not executed
		if (!$sql1) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
	
	////////////////////// ///////////////////// insert into  Audit10 KPI Table//////////////////////////////
		$sql2=mysqli_query($link1,"insert into  audit_10 set sc1='".$_POST['kpiScore1']."',sc2='".$_POST['kpiScore2']."',sc3='".$_POST['kpiScore3']."',total_score='".$total_score."',update_by='".$_SESSION['userid']."',update_date='".$today."',status='update', request_no = '".$request_no."' , location_code = '".$location."' ,requestdate = '".$today."'   ");
			//// check if query is not executed
		if (!$sql2) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
		///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$request_no,"KPI","Insert Score",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg="Successfully inserted KPI Score of ".$request_no;
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
   function setMarks(){
	 	var tot=0;
	 	for(var id=1;id<4;id++){
		 	var val=eval("document.auditForm.kpiScore"+id+".options[document.auditForm.kpiScore"+id+".selectedIndex].value");
	 		if(val!=''){
	 			tot=parseInt(tot)+parseInt(val);
	 		}
	 		document.getElementById("score"+id).innerHTML = "<B>"+val+"</B>";
	 	}
	 	document.getElementById("totalScore").innerHTML = "<B>"+tot+"</B>";
	}
   </script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
  include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-balance-scale"></i>KPI </h2>
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
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;Infrastructure >>KPI </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>              
            <tr>
			<td width="5%"  align="center">SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Actual Status</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>
            </tr>
			 <tr>
			<td width="5%">1</td>
              <td width="20%">Customer Bounce %</td>
              <td width="30%">Refer SCMP guidelines</td>
              <td width="6%" align="center">10</td>
			   <td width="15%"><select name="kpiScore1"  id="kpiScore1" onChange="setMarks()" class="form-control"><option value="">Select</option><option value="10">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score1></div></td>
            </tr>
			<tr>
             <td width="5%">2</td>
              <td width="20%">TAT Handset</td>
              <td width="30%">Refer SCMP guidelines.</td>
              <td width="6%" align="center">5</td>
			   <td width="15%"><select name="kpiScore2"  id="kpiScore2"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="5">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score2></div></td>
            </tr>
			<tr>
             <td width="5%">3</td>
              <td width="20%">TAT Accessories</td>
              <td width="30%">Refer SCMP guidelines.</td>
              <td width="6%" align="center">5</td>
			   <td width="15%"><select name="kpiScore3"  id="kpiScore3"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="5">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score3></div></td>
            </tr>
			<tr>
              <td width="5%" colspan="3"></td>
              <td width="10%" align="center">20</td>
              <td width="30%"></td>
              <td width="10%" align="center"><div id=totalScore></div></td>		 
            </tr>
			<tr>
                 <td width="100%" align="center" colspan="7"><input type="submit" class="btn<?=$btncolor?>" name="save" id="save" value="Save" title="Save Score">&nbsp;&nbsp;<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_feedback.php?id=<?=$_REQUEST['sno'];?>&request_no=<?=$request_no;?><?=$pagenav?>'"></td>
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