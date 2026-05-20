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
		$total_score=$_POST['lookScore1']+$_POST['lookScore2']+$_POST['lookScore3']+$_POST['lookScore4']+$_POST['lookScore5']+$_POST['lookScore6']+$_POST['lookScore7']+$_POST['lookScore8']+$_POST['lookScore9']+$_POST['lookScore10']+$_POST['lookScore11']+$_POST['lookScore12']+$_POST['lookScore13']+$_POST['lookScore14']+$_POST['lookScore15'];
		
		//////// update score in main table ///////////////////////////	
		$sql1=mysqli_query($link1,"Update audit_details set tot_audit_12='".$total_score."' , update_by='".$_SESSION['userid']."' ,update_date = '".$today."' where request_no='".$request_no."' " );
	//// check if query is not executed
		if (!$sql1) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
	
	////////////////////// ///////////////////// insert into  Audit12 Look & Feel  Table//////////////////////////////
		$sql2=mysqli_query($link1,"insert into  audit_12 set sc1='".$_POST['lookScore1']."',sc2='".$_POST['lookScore2']."',sc3='".$_POST['lookScore3']."',sc4='".$_POST['lookScore4']."',sc5='".$_POST['lookScore5']."',sc6='".$_POST['lookScore6']."',sc7='".$_POST['lookScore7']."',sc8='".$_POST['lookScore8']."',sc9='".$_POST['lookScore9']."',sc10='".$_POST['lookScore10']."',sc11='".$_POST['lookScore11']."',sc12='".$_POST['lookScore12']."',sc13='".$_POST['lookScore13']."',sc14='".$_POST['lookScore14']."',sc15='".$_POST['lookScore15']."',total_score='".$total_score."',update_by='".$_SESSION['userid']."',update_date='".$today."',status='update', request_no = '".$request_no."' , location_code = '".$location."' ,requestdate = '".$today."'   ");
			//// check if query is not executed
		if (!$sql2) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
		///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$request_no,"Look and Feel","Insert Score",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg="Successfully inserted Look and Feel Score of ".$request_no;
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
	 	for(var id=1;id<16;id++){
		 	var val=eval("document.auditForm.lookScore"+id+".options[document.auditForm.lookScore"+id+".selectedIndex].value");
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
      <h2 align="center"><i class="fa fa-balance-scale"></i>Look & Feel</h2>
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
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;Infrastructure >>Look & Feel</div>
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
				<td width="10%" align="center"><strong>Samples</strong></td>
            </tr>
			 <tr>
			<td width="5%">1</td>
              <td width="20%">Waiting area - Air Conditioner Availability</td>
              <td width="30%">Preferably Split AC</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="lookScore1" onChange="setMarks()" class="form-control"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score1></div></td>
				<td width="10%" rowspan=4>&nbsp;</td>
            </tr>
			<tr>
             <td width="5%">2</td>
              <td width="20%">Reception</td>
              <td width="30%">For customer guidance/education/information</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="lookScore2"  id="lookScore2"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score2></div></td>
            </tr>
			<tr>
              <td width="5%">3</td>
              <td width="20%">Token machine available with Display</td>
              <td width="30%">Token machine available with Display<B> (For ASC >200 Call Load)</B></td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore3"  id="lookScore3"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score3></div></td>
            </tr>
			<tr>
              <td width="5%">4</td>
              <td width="20%">Availability of Information System</td>
              <td width="30%">Information System TV preferably  Brand<B> (for ASC >200 Call)</B></td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore4"  id="lookScore4"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score4></div></td>
            </tr>
			<tr>
              <td width="5%">5</td>
              <td width="20%">Drinking Water facility</td>
              <td width="30%">Hygienic Water Dispenser with Glasses</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore5"  id="lookScore5"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score5></div></td>
			  <td width="10%">&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">6</td>
              <td width="20%">Hygiene / Cleanliness</td>
              <td width="30%">CCO desk, TRC, Customer waiting area must be clean and tidy, also customer waiting area should have flower pots</td>
              <td width="6%" align="center">3</td>
			   <td width="15%"><select name="lookScore6"  id="lookScore6"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="3">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score6></div></td>
			  <td width="10%" rowspan=2>&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">7</td>
              <td width="20%">Power backup Facility</td>
              <td width="30%">ASC must have Generators/Inverters for power backup</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore7"  id="lookScore7"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score7></div></td>
			  
            </tr>
			<tr>
              <td width="5%">8</td>
              <td width="20%">ASC  Glass doors and lights in customer waiting area</td>
              <td width="30%">Welcome door must be having Frosted Glass and door should have Push/Pull sticker</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="lookScore8"  id="lookScore8"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score8></div></td>
			  <td width="10%" >&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">9</td>
              <td width="20%">Glow Sign Board Available</td>
              <td width="30%">Glow Sign Board should be in working condition & Clean</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore9"  id="lookScore9"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score9></div></td>
			  <td width="10%" >&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">10</td>
              <td width="20%">Customer area sitting Chairs</td>
              <td width="30%">Airport style Silver Chair must be put in customer sitting area</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="lookScore10"  id="lookScore10"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score10></div></td>
			  <td width="10%" >&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">11</td>
              <td width="20%">News Papers & Magazines</td>
              <td width="30%">News Papers & Magazines in customer sitting area</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore11"  id="lookScore11"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score11></div></td>
			  <td width="10%"  rowspan=3>&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">12</td>
              <td width="20%">Notice board in waiting area</td>
              <td width="30%">Share By The Company</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore12"  id="lookScore12"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score12></div></td>			  
            </tr>
			<tr>
              <td width="5%">13</td>
              <td width="20%">CCTV availability</td>
              <td width="30%">For ASC, TRC, Customer waiting area <B>having call load > 200</B></td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore13"  id="lookScore13"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score13></div></td>		
            </tr>
			<tr>
              <td width="5%">14</td>
              <td width="20%">Magnifying Glass</td>
              <td width="30%">Magnifying Glass at CCO desk for ELS check & for customer convenience</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="lookScore14"  id="lookScore14"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score14></div></td>
			  <td width="10%" >&nbsp;</td>
            </tr>
			<tr>
              <td width="5%">15</td>
              <td width="20%">Counter number at CCO desk and cleanliness of desk</td>
              <td width="30%">Counter no's must be labelled at each CCO desk for customer convenience</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="lookScore15"  id="lookScore15"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score15></div></td>
			  <td width="10%" >&nbsp;</td>
            </tr>
			<tr>
              <td width="5%" colspan="3"></td>
              <td width="10%" align="center">22</td>
              <td width="30%"></td>
              <td width="10%" align="center"><div id=totalScore></div></td>
			   <td width="15%"></td>			 
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