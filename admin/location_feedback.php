<?php
require_once("../includes/config.php");
//// details
$request_no  = $_REQUEST['request_no'];
$job_sql="SELECT * FROM audit_details where request_no='".$request_no."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

/////////////perivious detail of location
$perv_code="select * from audit_details where location_code='".$job_row['location_code']."' and sno < '".$job_row['sno']."' ORDER BY sno DESC";
$result_perv=mysqli_query($link1,$perv_code);
$perv_result=mysqli_fetch_array($result_perv);
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
  include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-balance-scale"></i> Location Audit</h2>
       <h4 align="center"><?=$request_no?></h4>
	   <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
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
                <td width="30%"><?php echo $job_row['location_code'];?></td>
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
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;Summary of Location Performance</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>              
            <tr>
			<td width="20%" colspan="2" align="center"><strong>Audit Point</strong></td>
              <td width="10%"><strong>Max Score</strong></td>
              <td width="10%" align="center"><strong>Previous Audit Score</strong></td>
              <td width="10%" align="center"><strong>Current Audit Score</strong></td>
            </tr>
			 <tr>
			 <td colspan="5">
			 <a href="statusCheck.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?>" Title='Status Check on Action Point of Previous Audit'>Status on Action Points of Previous Audit</a></td>
			 </tr>
			 <tr>
			<td width="20%"><strong>Infrastructure</strong></td>
              <td width="20%"></td>
              <td width="10%"></td>
              <td width="10%"></td>
			   <td width="10%"></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php if($job_row['tot_audit_1']> 0){echo "Customer Convenience" ; }  else {?> <a href="customerConveninceScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Customer Convenince'>Customer Convenience</a>  <?php } ?></td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_1']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_1']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_12'] >0){echo "Look & Feel" ; }  else {?> <a href="lookFeelScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Look & Feel'>Look & Feel</a><?php } ?></td>
              <td width="10%" align="center">22</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_12']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_12']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_2']>0){echo "Manpower" ; }  else {?> <a href="manpowerScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Manpower'>Manpower</a><?php } ?></td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_2']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_2']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_3'] >0){echo "TRC / ESD Compliance" ; }  else {?><a href="trcComplianceScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='TRC / ESD Compliance'>TRC / ESD Compliance</a><?php } ?></td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_3']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_3']; ?></td>
            </tr>
			<tr>
			<td width="20%"><strong>Process Adherence</strong></td>
              <td width="20%"></td>
              <td width="10%"></td>
              <td width="10%"></td>
			   <td width="10%"></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_4']> 0){echo "Financial Health" ; }  else {?><a href="financialHealthScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Financial Health'>Financial Health</a><?php } ?></td>
              <td width="10%" align="center">3</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_4']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_4']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_11']>0){echo "Store Management" ; }  else {?><a href="storeManagementScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Store Management'>Store Management</a><?php } ?></td>
              <td width="10%" align="center">13</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_11']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_11']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_5'] >0){echo "DSRO" ; }  else {?><a href="dsroScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='DSRO'>DSRO</a><?php } ?></td>
              <td width="10%" align="center">4</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_5']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_5']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_6']>0){echo "Staff competencies" ; }  else {?><a href="staffCompetenciesScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Staff competencies'>Staff competencies</a><?php } ?></td>
              <td width="10%" align="center">4</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_6']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_6']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_7']>0){echo "General" ; }  else {?><a href="generalScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='General'>General</a><?php } ?></td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_7']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_7']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_8']>0){echo "DOA" ; }  else {?><a href="doaScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='DOA'>DOA</a><?php } ?></td>
              <td width="10%" align="center">2</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_8']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_8']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_9'] >0){echo "Satisfaction" ; }  else {?><a href="satisfactionScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Satisfaction'>Satisfaction</a><?php } ?></td>
              <td width="10%" align="center">8</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_9']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_9']; ?></td>
            </tr>
			<tr>
			<td width="20%"><strong>KPI</strong></td>
              <td width="20%"></td>
              <td width="10%"></td>
              <td width="10%"></td>
			   <td width="10%"></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%"><?php  if($job_row['tot_audit_10']>0){echo "KPI" ; }  else {?><a href="kpiScoreUpdate.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='KPI'>KPI</a><?php } ?></td>
              <td width="10%" align="center">20</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_10']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_10']; ?></td>
            </tr>
			 <tr>
			 <td colspan="5">
			 <a href="correctiveAction.php?sno=<?=$_REQUEST['id'];?>&request_no=<?=base64_encode($_REQUEST['request_no']);?><?=$pagenav?>" Title='Corrective Action & Improvement points agreed Check on Action Point of Previous Audit'>Corrective Action & Improvement points agreed</a>
			 </td>
			 </tr>
			<tr>
			<td width="20%" colspan="2" align="right"><strong>Total</strong></td>
              <td width="10%" align="center">100</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_1']+$perv_result['tot_audit_2']+$perv_result['tot_audit_3']+$perv_result['tot_audit_4']+$perv_result['tot_audit_5']+$perv_result['tot_audit_6']+$perv_result['tot_audit_7']+$perv_result['tot_audit_8']+$perv_result['tot_audit_9']+$perv_result['tot_audit_10']+$perv_result['tot_audit_11']+$perv_result['tot_audit_12']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_1']+$job_row['tot_audit_2']+$job_row['tot_audit_3']+$job_row['tot_audit_4']+$job_row['tot_audit_5']+$job_row['tot_audit_6']+$job_row['tot_audit_7']+$job_row['tot_audit_8']+$job_row['tot_audit_9']+$job_row['tot_audit_10']+$job_row['tot_audit_11']+$job_row['tot_audit_12']; ?></td>
            </tr>
			<tr>
                 <td width="100%" align="center" colspan="5"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_audit.php?<?=$pagenav?>'"></td>
               </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>