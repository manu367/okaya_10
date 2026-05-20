<?php
require_once("../includes/config.php");
	//// details
	$request_no  = base64_decode($_REQUEST['request_no']);
	$job_sql="SELECT * FROM audit_details where request_no='".$request_no."'";
	$job_res=mysqli_query($link1,$job_sql);
	$job_row=mysqli_fetch_assoc($job_res);

		/////////////////////  Audit1 Customer Convenience Table///////////////
		$Customer=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_1 where request_no='".$job_row['request_no']."'"));

		///////////////////// Audit2 Manpower Table///////////////
		$Manpower=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_2 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit3 TRC / ESD Compliance Table//////////////
		$TRC=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_3 where request_no='".$job_row['request_no']."'"));

		///////////////////// Audit4 Financial Health Table///////////////
		$Financial=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_4 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit5 DSRO Table///////////////
		$DSRO=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_5 where request_no='".$job_row['request_no']."'"));

		///////////////////// Audit6 Staff competencies Table///////////////
		$Staff=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_6 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit7 General Table///////////////
		$General=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_7 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit8 DOA Table///////////////
		$DOA=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_8 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit9 Satisfaction Table///////////////
		$Satisfaction=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_9 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit10 KPI Table///////////////
		$KPI=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_10 where request_no='".$job_row['request_no']."'"));

		///////////////////// Audit11 Store Management Table///////////////
		$Store=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_11 where request_no='".$job_row['request_no']."'"));
		///////////////////// Audit12 Look & Feel  Table///////////////
		$Look=mysqli_fetch_array(mysqli_query($link1,"SELECT * FROM audit_12 where request_no='".$job_row['request_no']."'"));

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
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $(window).scroll(function(){
        if($(this).scrollTop() > 100){
            $('#scroll').fadeIn();
        }else{
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});
</script>
<style type="text/css">
/* BackToTop button css */
#scroll {
    position:fixed;
    right:10px;
    bottom:10px;
    cursor:pointer;
    width:50px;
    height:50px;
    background-color:#3498db;
    text-indent:-9999px;
    display:none;
    -webkit-border-radius:5px;
    -moz-border-radius:5px;
    border-radius:5px;
}
#scroll span {
    position:absolute;
    top:50%;
    left:50%;
    margin-left:-8px;
    margin-top:-12px;
    height:0;
    width:0;
    border:8px solid transparent;
    border-bottom-color:#ffffff
}
#scroll:hover {
    background-color:#e74c3c;
    opacity:1;
    filter:"alpha(opacity=100)";
    -ms-filter:"alpha(opacity=100)";
}
</style>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
  include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-balance-scale"></i> Previous Audit Details</h2>
       <h4 align="center"><?=$request_no?></h4>
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
			<td width="20%"><strong>Infrastructure</strong></td>
              <td width="20%"></td>
              <td width="10%"></td>
              <td width="10%"></td>
			   <td width="10%"></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">Customer Convenience</td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_1']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_1']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">Look & Feel</td>
              <td width="10%" align="center">22</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_12']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_12']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">Manpower</td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_2']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_2']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">TRC / ESD Compliance</td>
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
              <td width="20%">Financial Health</td>
              <td width="10%" align="center">3</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_4']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_4']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">Store Management</td>
              <td width="10%" align="center">13</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_11']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_11']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">	DSRO</td>
              <td width="10%" align="center">4</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_5']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_5']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">Staff competencies</td>
              <td width="10%" align="center">4</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_6']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_6']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">General</td>
              <td width="10%" align="center">6</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_7']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_7']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">DOA</td>
              <td width="10%" align="center">2</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_8']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_8']; ?></td>
            </tr>
			<tr>
			<td width="20%"></td>
              <td width="20%">Satisfaction</td>
              <td width="10%" align="center">8</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_6']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_6']; ?></td>
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
              <td width="20%">KPI</td>
              <td width="10%" align="center">20</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_10']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_10']; ?></td>
            </tr>
			<tr>
			<td width="20%" colspan="2" align="right"><strong>Total</strong></td>
              <td width="10%" align="center">100</td>
              <td width="10%" align="center"><?php echo $perv_result['tot_audit_1']+$perv_result['tot_audit_2']+$perv_result['tot_audit_3']+$perv_result['tot_audit_4']+$perv_result['tot_audit_5']+$perv_result['tot_audit_6']+$perv_result['tot_audit_7']+$perv_result['tot_audit_8']+$perv_result['tot_audit_9']+$perv_result['tot_audit_10']+$perv_result['tot_audit_11']+$perv_result['tot_audit_12']; ?></td>
			   <td width="10%" align="center"><?php echo $job_row['tot_audit_1']+$job_row['tot_audit_2']+$job_row['tot_audit_3']+$job_row['tot_audit_4']+$job_row['tot_audit_5']+$job_row['tot_audit_6']+$job_row['tot_audit_7']+$job_row['tot_audit_8']+$job_row['tot_audit_9']+$job_row['tot_audit_10']+$job_row['tot_audit_11']+$job_row['tot_audit_12']; ?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;Status on Action Points of Previous Audit</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="30%"><strong>Action Points Planned & Agreed in Previous Audit</strong></td>
					<td width="25%"><strong>Responsibility</strong></td>
                    <td width="10%"  align="center"><strong>Target Date</strong></td>				
                    <td width="20%"><strong>Current Status</strong></td>
                  </tr>
				  <tr>
                    <td width="20%"><?php echo $perv_result['remak1']; ?></td>
					<td width="20%"><?php echo $perv_result['Respos1']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t1_date']); ?></td>				
                    <td width="15%"><?php echo $perv_result['action_1']; ?></td>
                  </tr>
				  <tr>
                    <td width="20%"><?php echo $perv_result['remak2']; ?></td>
					<td width="20%"><?php echo $perv_result['Respos2']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t2_date']); ?></td>				
                    <td width="15%"><?php echo $perv_result['action_2']; ?></td>
                  </tr>
				    <tr>
                    <td width="20%"><?php echo $perv_result['remak3']; ?></td>
					<td width="20%"><?php echo $perv_result['Respos3']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t3_date']); ?></td>				
                    <td width="15%"><?php echo $perv_result['action_3']; ?></td>
                  </tr>
				   <tr>
                    <td width="20%"><?php echo $perv_result['remak4']; ?></td>
					<td width="20%"><?php echo $perv_result['Respos4']; ?></td>
                    <td width="25%"  align="center"><?php echo dt_format($perv_result['t4_date']); ?></td>				
                    <td width="15%"><?php echo $perv_result['action_4']; ?></td>
                  </tr>           
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div>
	<a href="javascript:void(0);" id="scroll" title="Scroll to Top" style="display: none;">Top<span></span></a>
	</div>
	<br>
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;Corrective Action & Improvement points agreed in this Audit</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="5%"><strong>S No</strong></td>
					<td width="20%"><strong>Action Points Planned & Agreed</strong></td>
                    <td width="25%"  align="center"><strong>Responsibility</strong></td>				
                    <td width="15%"><strong>Target Date</strong></td>
                  </tr>
				  <tr>
				  <td width="5%">1</td>
                    <td width="15%"><?php echo $perv_result['remak1']; ?></td>
					<td width="10%"><?php echo $perv_result['Respos1']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t1_date']); ?></td>				                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
                    <td width="15%"><?php echo $perv_result['remak2']; ?></td>
					<td width="10%"><?php echo $perv_result['Respos2']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t2_date']); ?></td>	
                  </tr>
				    <tr>
                    <td width="5%">3</td>
                    <td width="15%"><?php echo $perv_result['remak3']; ?></td>
					<td width="10%"><?php echo $perv_result['Respos3']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t3_date']); ?></td>	
                  </tr>
				   <tr>
				   <td width="5%">4</td>
                    <td width="15%"><?php echo $perv_result['remak4']; ?></td>
					<td width="10%"><?php echo $perv_result['Respos4']; ?></td>
                    <td width="10%"  align="center"><?php echo dt_format($perv_result['t4_date']); ?></td>				
                  </tr>           
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;Infrastructure&nbsp;>>&nbsp;Customer Convenience</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="20%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
                   <td width="20%">ASC Location, Parking Facility</td>
              		<td width="30%">ASC Location Convenient for customer to reach & parking facility must for Metro & Tier1</td>
              		<td width="6%" align="center">1</td>	
			  		<td width="10%"><?php echo $Customer['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">ASC Address & Contact Details</td>
              		<td width="30%">Correct Address and Contact Details of ASC must be updated, in Company Systems, Website & SMS tool</td>
             	 	<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $Customer['sc2']?></td>		   
                  </tr>
				    <tr>
                     <td width="5%">3</td>
              		<td width="20%">Landline Number Working Properly</td>
              		<td width="30%">Landline number must be in working condition( Dedicated 1 Landline number for Incoming calls). CSM must check himself by calling on this number.</td>
             		 <td width="6%" align="center">4</td>
					 <td width="10%"><?php echo $Customer['sc3']?></td>
                  </tr>
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">6</td>
              		<td width="10%"><?php echo $job_row['tot_audit_1']?></td>
 			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;Infrastructure&nbsp;>>&nbsp;Look & Feel of Checkpoint  </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
					 <td width="20%">Waiting area - Air Conditioner Availability</td>
             	 	<td width="30%">Preferably Split AC</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $Look['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
              		<td width="20%">Reception</td>
             		 <td width="30%">For customer guidance/education/information</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $Look['sc2']?></td>		   
                  </tr>
				    <tr>
                    <td width="5%">3</td>
             	 	<td width="20%">Token machine available with Display</td>
              		<td width="30%">Token machine available with Display<B> (For ASC >200 Call Load)</B></td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc3']?></td>
                  </tr>
				   <tr>
                    <td width="5%">4</td>
             	 	<td width="20%">Availability of Information System</td>
             		 <td width="30%">Information System TV preferably  Brand<B> (for ASC >200 Call)</B></td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc4']?></td>
                  </tr>
				   <tr>
                    <td width="5%">5</td>
             	 	 <td width="20%">Drinking Water facility</td>
              		<td width="30%">Hygienic Water Dispenser with Glasses</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc5']?></td>
                  </tr>
				   <tr>
                    <td width="5%">6</td>
             	 	<td width="20%">Hygiene / Cleanliness</td>
             		 <td width="30%">CCO desk, TRC, Customer waiting area must be clean and tidy, also customer waiting area should have flower pots</td>
              		<td width="6%" align="center">3</td>
					 <td width="10%"><?php echo $Look['sc6']?></td>
                  </tr>
				  <tr>
                    <td width="5%">7</td>
             	 	<td width="20%">Power backup Facility</td>
              		<td width="30%">ASC must have Generators/Inverters for power backup</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc7']?></td>
                  </tr>
				  <tr>
                    <td width="5%">8</td>
             	 	  <td width="20%">ASC  Glass doors and lights in customer waiting area</td>
              		<td width="30%">Welcome door must be having Frosted Glass and door should have Push/Pull sticker</td>
              		<td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Look['sc8']?></td>
                  </tr>
				  <tr>
                    <td width="5%">9</td>
             	 	 <td width="20%">Glow Sign Board Available</td>
              		<td width="30%">Glow Sign Board should be in working condition & Clean</td>
             		 <td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc9']?></td>
                  </tr>
				  <tr>
                    <td width="5%">10</td>
             	 	 <td width="20%">Customer area sitting Chairs</td>
              		<td width="30%">Airport style Silver Chair must be put in customer sitting area</td>
             		 <td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Look['sc10']?></td>
                  </tr>
				   <tr>
                    <td width="5%">11</td>
             	 	 <td width="20%">News Papers & Magazines</td>
              		<td width="30%">News Papers & Magazines in customer sitting area</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc11']?></td>
                  </tr>
				   <tr>
                    <td width="5%">12</td>
             	 	 <td width="20%">Notice board in waiting area</td>
              		<td width="30%">Share By The Company</td>
             		 <td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc12']?></td>
                  </tr>
				  <tr>
                    <td width="5%">13</td>
             	 	 <td width="20%">CCTV availability</td>
             		 <td width="30%">For ASC, TRC, Customer waiting area <B>having call load > 200</B></td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc13']?></td>
                  </tr>
				  <tr>
                    <td width="5%">14</td>
             	 	<td width="20%">Magnifying Glass</td>
              		<td width="30%">Magnifying Glass at CCO desk for ELS check & for customer convenience</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Look['sc14']?></td>
                  </tr>
				  <tr>
                    <td width="5%">15</td>
             	 	<td width="20%">Counter number at CCO desk and cleanliness of desk</td>
              		<td width="30%">Counter no's must be labelled at each CCO desk for customer convenience</td>
              		<td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Look['sc15']?></td>
                  </tr>
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">22</td>
              		<td width="10%"><?php echo $job_row['tot_audit_12']?></td>
 			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->	
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;Infrastructure&nbsp;>>&nbsp;Manpower</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
                   <td width="20%">Availability of CCO & Engineers</td>
              	<td width="30%">1 CCO & 1 Engineer per 200 call load</td>
             	 <td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $Manpower['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">CCO uniform, as per the guidelines</td>
              		<td width="30%">As per the latest SCMP</td>
             		 <td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $Manpower['sc2']?></td>		   
                  </tr>
				    <tr>
                     <td width="5%">3</td>
              		<td width="20%">CCO/Engineers-Qualified & Experienced</td>
              		<td width="30%">CCO-Min. Qualification Graduate<br>
					    Engineer-Min. Qualification Diploma /B.tech</td>
             		 <td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Manpower['sc3']?></td>
                  </tr>
				  <tr>
                     <td width="5%">4</td>
              		<td width="20%">ASC Owner/Manager engagement in business </td>
              		<td width="30%">Owner/Manager-Daily engagement with staff<br>
					     Eg:-Morning meetings,updating latest updates to their staffs regarding</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Manpower['sc4']?></td>
                  </tr>
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">6</td>
              		<td width="10%"><?php echo $job_row['tot_audit_2']?></td>
 			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;Infrastructure&nbsp;>>&nbsp; TRC/ESD Compliance</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
                   <td width="20%">ESD mat available on working tables & TRC Floor,as per ESD guidelines</td>
              		<td width="30%">As per the latest process bulletin</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $TRC['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">Specified tool kit/jigs available or not</td>
             		 <td width="30%">AS per the latest process bulletin</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $TRC['sc2']?></td>		   
                  </tr>
				    <tr>
                     <td width="5%">3</td>
              		<td width="20%">Engineers are wearing an ESD coat,wrist band & wrist band should be connected to mat</td>
             		 <td width="30%">As per the latest process bulletin</td>
             		 <td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $TRC['sc3']?></td>
                  </tr>				
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">6</td>
              		<td width="10%"><?php echo $job_row['tot_audit_3']?></td>
 			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp; Financial Health </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
                  <td width="20%">Credit utilisation/Credit block</td>
              <td width="30%">ASC should be under credit limit</td>
              <td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $Financial['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					 <td width="20%">Invoices clearance</td>
              		<td width="30%">All Invoices sent to HO within 15 days(Labour claim/Incentive)and record maintained in file</td>
              		<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $Financial['sc2']?></td>		   
                  </tr>			
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">3</td>
              		<td width="10%"><?php echo $job_row['tot_audit_4']?></td>
 			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp;Store Management</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
					 <td width="20%">Product Handling</td>
              		<td width="30%">Handsets are stored as per legend in Proper bins with  supplied secure pouches</td>
             	 	<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $Store['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
              		<td width="20%">Handset Audit</td>
             		 <td width="30%">Customer Handsets Availability(System vs. Physical)along with QC checklist</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $Store['sc2']?></td>		   
                  </tr>
				    <tr>
                    <td width="5%">3</td>
             	 	<td width="20%">Spare Management</td>
              		<td width="30%">Proper bins for spare with proper part code labelling bins</td>
            	  <td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Store['sc3']?></td>
                  </tr>
				   <tr>
                    <td width="5%">4</td>
             	 	 <td width="20%">Spares Audit</td>
             	 	<td width="30%">Spares Inventory Audit(System vs.Physical)</td>
              		<td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Store['sc4']?></td>
                  </tr>
				   <tr>
                    <td width="5%">5</td>
             	 	<td width="20%">Spares Demand Check</td>
              		<td width="30%">Spares demanded in PNA calls as per symptom (Check 10 sample job sheets)</td>
              		<td width="6%" align="center">2</td>
					 <td width="10%"><?php echo $Store['sc5']?></td>
                  </tr>
				   <tr>
                    <td width="5%">6</td>
             	 	<td width="20%">PNA to WFR Tracking</td>
             		 <td width="30%">All Job Sheets closed as per parts received within 1 day(PNA to WFR closure %)</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Store['sc6']?></td>
                  </tr>
				  <tr>
                    <td width="5%">7</td>
             	 	 <td width="20%">Defective Spares Management</td>
              			<td width="30%">Defective spares need to be stored in proper labelled bin ,if any defective part is found unaccountable/without tagged with jobSheets,it will be considered as waste & CSM should scrap it then & there</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Store['sc7']?></td>
                  </tr>
				  <tr>
             	 	  <td width="5%">8</td>
              		<td width="20%">Customer Replaced/Swap Handset check</td>
              		<td width="30%">Replaced handset must be handed over to the customer with same IMEI handset issued/dispatched by Company</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Store['sc8']?></td>
                  </tr>
				  <tr>
                    <td width="5%">9</td>
             	 	 <td width="20%">Transfer Call Management</td>
              		<td width="30%">Follow SCMP Guideline</td>
              		<td width="6%" align="center">1</td>
					 <td width="10%"><?php echo $Store['sc9']?></td>
                  </tr>			  
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">13</td>
              		<td width="10%"><?php echo $job_row['tot_audit_12']?></td> 			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->	
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp; DSRO </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>

                  </tr>
				  <tr>
				  <td width="5%">1</td>
                  <td width="20%">Defective Spare Handling -Part 1</td>
              		<td width="30%">Defective spares should be tagged with closed JobSheets</td>
             		 <td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $DSRO['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">Defective Spare Handling -Part 2</td>
              		<td width="30%">Defective parts  Challan/Invoice created & dispatched in system for closed jobsheets,(as per matrix defined)and record maintained for audit</td>
              <td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $DSRO['sc2']?></td>		   
                  </tr>			
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">4</td>
              		<td width="10%"><?php echo $job_row['tot_audit_5']?></td>			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp; Staff Competencies </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>
                  </tr>
				  <tr>
				  <td width="5%">1</td>
                  <td width="20%">Greeting to customer</td>
              		<td width="30%">CCO greeting to customer( Good Morning/Good Afternoon/ Good Evening)or as per the customer convenient language<BR>CCO conveying message for inconvenience to customer & Thanking him for vising at service center</td>
              	<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $Staff['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">Technicians aware about latest Technical Bulletin</td>
              		<td width="30%">CSM should check  any previous three latest technical Bulletin with engineers</td>
             		 <td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $Staff['sc2']?></td>		   
                  </tr>
				  <tr>
                    <td width="5%">3</td>
					<td width="20%">Test compliance adherence for CCO & Engineers</td>
              		<td width="30%">As per the guidelines by technical team</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $Staff['sc3']?></td>		   
                  </tr>			
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">4</td>
              		<td width="10%"><?php echo $job_row['tot_audit_6']?></td>			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp; General </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>
                  </tr>
				  <tr>
				  <td width="5%">1</td>
                 	<td width="20%">KPI chart displayed on manager dashboard</td>
              		<td width="30%">KPI chart displayed on manager/TRC dashboard</td>
              		<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $General['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">Manual jobsheets issued by ASC</td>
              		<td width="30%">No manual jobsheets should be issued by ASC</td>
              		<td width="6%" align="center">2</td>
			  		<td width="10%"><?php echo $General['sc2']?></td>		   
                  </tr>
				  <tr>
                    <td width="5%">3</td>
					<td width="20%">Insurance Premises</td>
              		<td width="30%">ASC should have a valid Insurance for ASC premises</td>
             		 <td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $General['sc3']?></td>		   
                  </tr>	
				   <tr>
                    <td width="5%">4</td>
					<td width="20%">Technical Bulletin Management </td>
             		 <td width="30%">Proper technical Bulletin's file should be maintained at ASC</td>
              		<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $General['sc4']?></td>		   
                  </tr>
				  <tr>
                    <td width="5%">5</td>
					<td width="20%">Proper invoice records for O/W calls</td>
              		<td width="30%">All O/W payments are recorded with proper receipt books, are available in ASC for inspection<BR>
							~ Jobsheets no. should be mentioned in all O/W invoices~ All O/W payments are recorded with proper receipt books, are available in ASC for inspection</td>
              		<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $General['sc5']?></td>		   
                  </tr>			
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">6</td>
              		<td width="10%"><?php echo $job_row['tot_audit_7']?></td>			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
		<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp;DOA</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>
                  </tr>
				  <tr>
				  <td width="5%">1</td>
                 	<td width="20%">DOA Check at ASC</td>
              		<td width="30%">DOA made by ASC after proper ELS check. No rejection by  in last quarter</td>
              		<td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $DOA['sc1']?></td>		                   
                  </tr>
				  <tr>
                    <td width="5%">2</td>
					<td width="20%">Zero DOA Pendency</td>
              		<td width="30%">No DOA Pendency at ASC, if any DOA handset found at ASC, ASC should have proper justification for pending DOA certificate. Pending cases not more then 2 days at all.</td>
              <td width="6%" align="center">1</td>
			  		<td width="10%"><?php echo $DOA['sc2']?></td>		   
                  </tr>			  
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">2</td>
              		<td width="10%"><?php echo $job_row['tot_audit_8']?></td>			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp; Process Adherence&nbsp;>>&nbsp;Satisfaction</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>
                  </tr>
			 <tr>
			<td width="5%">1</td>
             <td width="20%">CSI Score</td>
             <td width="30%">ASC should have achievement as follows<BR>
							- For Metro+Tier1----90%<BR>
							- For Non Metro------85%</td>
             <td width="6%" align="center">4</td>
			 <td width="10%"><?php echo $Satisfaction['sc1']?></td>		                   
                  </tr>
			 <tr>
			 <td width="5%">2</td>
              <td width="20%">DSI Score</td>
              <td width="30%">ASC should have achievement as follows<BR>
						- For Metro+Tier1----90%<BR>
						- For Non Metro------85%</td>
              <td width="6%" align="center">4</td>
			  		<td width="10%"><?php echo $Satisfaction['sc2']?></td>		   
                  </tr>			  
				   <tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">8</td>
              		<td width="10%"><?php echo $job_row['tot_audit_9']?></td>			 
            </tr>   
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
		<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;KPI &nbsp;>>&nbsp;KPI</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
               <tr>
               <td width="5%"  align="center"><strong>SNo</strong></td>
              <td width="10%"><strong>Parameter</strong></td>
              <td width="40%" align="center"><strong>Calibration Guidelines</strong></td>
              <td width="7%" align="center"><strong>Marks</strong></td>
			   <td width="10%" align="center"><strong>Score</strong></td>
                  </tr>
			 <tr>
			<td width="5%">1</td>
             <td width="20%">Customer Bounce %</td>
              <td width="30%">Refer SCMP guidelines</td>
              <td width="6%" align="center">10</td>
			 <td width="10%"><?php echo $KPI['sc1']?></td>		                   
                  </tr>
			 <tr>
			 <td width="5%">2</td>
              <td width="20%">TAT Handset</td>
              <td width="30%">Refer SCMP guidelines.</td>
              <td width="6%" align="center">5</td>
			  <td width="10%"><?php echo $KPI['sc2']?></td>		   
                </tr>	
				<tr>
			 <td width="5%">3</td>
             <td width="20%">TAT Accessories</td>
              <td width="30%">Refer SCMP guidelines.</td>
              <td width="6%" align="center">5</td>
			  <td width="10%"><?php echo $KPI['sc3']?></td>		   
                </tr>			  
				<tr>
              		<td width="10%" colspan="3" align="right"><strong>Total</strong></td>
              		<td width="10%" align="center">20</td>
              		<td width="10%"><?php echo $job_row['tot_audit_10']?></td>			 
            </tr> 
			<tr>
                 <td width="100%" align="center" colspan="5"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_audit.php?<?=$pagenav?>'"></td>
               </tr>  
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