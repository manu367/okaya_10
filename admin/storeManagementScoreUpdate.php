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
		$total_score=$_POST['storeManagementScore1']+$_POST['storeManagementScore2']+$_POST['storeManagementScore3']+$_POST['storeManagementScore4']+$_POST['storeManagementScore5']+$_POST['storeManagementScore6']+$_POST['storeManagementScore7']+$_POST['storeManagementScore8']+$_POST['storeManagementScore9'];
		
		//////// update score in main table ///////////////////////////	
		$sql1=mysqli_query($link1,"Update audit_details set tot_audit_11='".$total_score."' , update_by='".$_SESSION['userid']."' ,update_date = '".$today."' where request_no='".$request_no."' " );
	//// check if query is not executed
		if (!$sql1) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
	
	////////////////////// ///////////////////// insert into  Store Management Table//////////////////////////////
		$sql2=mysqli_query($link1,"insert into  audit_11 set sc1='".$_POST['storeManagementScore1']."',sc2='".$_POST['storeManagementScore2']."',sc3='".$_POST['storeManagementScore3']."',sc4='".$_POST['storeManagementScore4']."',sc5='".$_POST['storeManagementScore5']."',sc6='".$_POST['storeManagementScore6']."',sc7='".$_POST['storeManagementScore7']."',sc8='".$_POST['storeManagementScore8']."',sc9='".$_POST['storeManagementScore9']."',total_score='".$total_score."',update_by='".$_SESSION['userid']."',update_date='".$today."',status='update', request_no = '".$request_no."' , location_code = '".$location."' ,requestdate = '".$today."'   ");
			//// check if query is not executed
		if (!$sql2) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
		///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$request_no,"Store Management","Insert Score",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg="Successfully inserted Store Management Score of ".$request_no;
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
	 	for(var id=1;id<10;id++){
		 	var val=eval("document.auditForm.storeManagementScore"+id+".options[document.auditForm.storeManagementScore"+id+".selectedIndex].value");
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
      <h2 align="center"><i class="fa fa-balance-scale"></i>Store Management</h2>
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
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;Infrastructure >>Store Management</div>
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
              <td width="20%">Product Handling</td>
              <td width="30%">Handsets are stored as per legend in Proper bins with  supplied secure pouches</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="storeManagementScore1"  id="storeManagementScore1" onChange="setMarks()" class="form-control"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score1></div></td>
				<td width="10%" rowspan=2>&nbsp;</td>
            </tr>
			<tr>
             <td width="5%">2</td>
              <td width="20%">Handset Audit</td>
              <td width="30%">Customer Handsets Availability(System vs. Physical)along with QC checklist</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="storeManagementScore2"  id="storeManagementScore2"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score2></div></td>
            </tr>
			<tr>
              <td width="5%">3</td>
              <td width="20%">Spare Management</td>
              <td width="30%">Proper bins for spare with proper part code labelling bins</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="storeManagementScore3"  id="storeManagementScore3"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score3></div></td>
			  <td width="10%"></td>
            </tr>
			<tr>
              <td width="5%">4</td>
              <td width="20%">Spares Audit</td>
              <td width="30%">Spares Inventory Audit(System vs.Physical)</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="storeManagementScore4"  id="storeManagementScore4"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score4></div></td>
            </tr>
			<tr>
              <td width="5%">5</td>
              <td width="20%">Spares Demand Check</td>
              <td width="30%">Spares demanded in PNA calls as per symptom (Check 10 sample job sheets)</td>
              <td width="6%" align="center">2</td>
			   <td width="15%"><select name="storeManagementScore5"  id="storeManagementScore5"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="2">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score5></div></td>
            </tr>
			<tr>
              <td width="5%">6</td>
              <td width="20%">PNA to WFR Tracking</td>
              <td width="30%">All Job Sheets closed as per parts received within 1 day(PNA to WFR closure %)</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="storeManagementScore6"  id="storeManagementScore6"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score6></div></td>
			
            </tr>
			<tr>
              <td width="5%">7</td>
              <td width="20%">Defective Spares Management</td>
              <td width="30%">Defective spares need to be stored in proper labelled bin ,if any defective part is found unaccountable/without tagged with jobSheets,it will be considered as waste & CSM should scrap it then & there</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="storeManagementScore7"  id="storeManagementScore7"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score7></div></td>
			  
            </tr>
			<tr>
              <td width="5%">8</td>
              <td width="20%">Customer Replaced/Swap Handset check</td>
              <td width="30%">Replaced handset must be handed over to the customer with same IMEI handset issued/dispatched by Company</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="storeManagementScore8"  id="storeManagementScore8"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score8></div></td>
            </tr>
			<tr>
              <td width="5%">9</td>
              <td width="20%">Transfer Call Management</td>
              <td width="30%">Follow SCMP Guideline</td>
              <td width="6%" align="center">1</td>
			   <td width="15%"><select name="storeManagementScore9"  id="storeManagementScore9"  class="form-control" onChange="setMarks()"><option value="">Select</option><option value="1">TRUE</option><option value="0">FALSE</option></select></td>
			  <td width="10%"><div id=score9></div></td>

            </tr>
			<tr>
              <td width="5%" colspan="3"></td>
              <td width="10%" align="center">13</td>
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