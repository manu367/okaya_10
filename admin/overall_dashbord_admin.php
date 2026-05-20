<?php
include("../includes/config.php");

/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
/////// get Access brand////////////////////////
$arrbrand = getAccessBrand($_SESSION['userid'],$link1);
/////// get Access product category////////////////////////
$arrproduct = getAccessProduct($_SESSION['userid'],$link1);

////// filter value
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
	$daterange_close= "close_date >= '".$date_range[0]."' and close_date <= '".$date_range[1]."'";
}else{
	$daterange_open = "1";
	$daterange_close="1";
}
/////////
if($_REQUEST["state"]!=""){
	$state_condi = " stateid = '".$_REQUEST["state"]."'";
}else{
	$state_condi = " stateid in (".$arrstate.")";
}
/////////
if($_REQUEST["location_code"]!=""){
	$loc_condi = " (current_location='".$_REQUEST["location_code"]."' or location_code='".$_REQUEST["location_code"]."') ";
}else{
	$loc_condi = " (current_location in (select location_code from location_master where ".$state_condi.") or location_code in (select location_code from location_master where ".$state_condi.")) ";
}
//////// 
if($_REQUEST["brand"]!=""){
	$brand_condi = " and brand_id = '".$_REQUEST["brand"]."'";
}else{
	$brand_condi = " and brand_id in (".$arrbrand.")";
}
//////// 

//////// All call details function //////////////
function tcd_fun($call_typ, $st, $daterange_open, $loc_condi, $brand_condi, $brand, $link1){
	if($call_typ == "Installation"){
		$callTyp = " and (call_for = 'Installation' or call_for = 'Reinstallation') ";
	}else{
		$callTyp = " and (call_for != 'Installation' and call_for != 'Reinstallation') ";
	}
	
	if($st != ""){
		$status = " and status in (".$st.") ";
	}else{
		$status = " ";
	}
	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE  ".$loc_condi." ".$brand_condi." ".$status." ".$callTyp." and ".$daterange_open." and brand_id = '".$brand."' "));
	if($result['jc']!=""){
		$data = $result['jc'];
	}else{
		$data = 0;
	}
	return $data;
}

//////// Open Call Details functions ////////////
function ocd_fun($call_typ, $s_date, $e_date, $daterange_open, $loc_condi, $brand_condi, $st, $today, $link1){
	if($call_typ == "Installation"){
		$callTyp = " and (call_for = 'Installation' or call_for = 'Reinstallation') ";
	}else{
		$callTyp = " and (call_for != 'Installation' and call_for != 'Reinstallation') ";
	}
	if($s_date != "" && $e_date != ""){
		$dtRange = " and (datediff(".$today.",open_date)>=".$s_date." or datediff(".$today.",open_date) <= ".$e_date.") ";
	}else if($s_date != "" && $e_date == ""){
		$dtRange = " and datediff(".$today.",open_date)>".$s_date." ";
	}else{
		$dtRange = " ";
	}
	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$loc_condi." ".$brand_condi." and status in (".$st.") ".$callTyp." ".$dtRange." and ".$daterange_open." "));
	
	if($result['jc']!=""){
		$data = $result['jc'];
	}else{
		$data = 0;
	}
	return $data;
	
}

////// Local TAT Adhrence /////
function tat_adhrence_L1($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local') and status = '48' and (call_for = 'Installation' or call_for = 'Reinstallation') and  (datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 1 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}
function tat_adhrence_U1($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Upcountry' OR area_type = 'upcountry') and status = '48' and (call_for != 'Installation' and call_for != 'Reinstallation') and  (datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 3 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}
function tat_adhrence_T1($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local') OR (area_type = 'Upcountry' OR area_type = 'upcountry') and status in ('6','8','11','48') and  ( datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 2 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}

function tat_adhrence_L2($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local') and status in ('6','8','11') and (call_for != 'Installation' and call_for != 'Reinstallation') and  ( datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 2 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}
function tat_adhrence_U2($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Upcountry' OR area_type = 'upcountry') and status in ('6','8','11') and (call_for != 'Installation' and call_for != 'Reinstallation') and  (datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 3 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}
function tat_adhrence_T2($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local') OR (area_type = 'Upcountry' OR area_type = 'upcountry') and status in ('6','8','11','48') and  ( datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 3 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}

function tat_adhrence_G($tot_jobs, $daterange_open, $loc_condi, $brand_condi, $link1){	
	$result = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc FROM jobsheet_data WHERE  ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local') OR (area_type = 'Upcountry' OR area_type = 'upcountry') and status in ('6','8','11','48') and  ( datediff(close_date,open_date)>=0 or datediff(close_date,open_date) <= 2 )"));
	if($tot_jobs>0){
		$data = round($result['jc']/$tot_jobs);
	}else{
		$data = 0;
	}
	return $data."%";
}

function avg_tat_val($jobs, $job_tat){
	if($job_tat>0){
		$data = round(($jobs/$job_tat),2);
	}else{
		$data = 0;
	}
	return $data;
}

////// count Closed tat from jobsheet data
$interval1 = 0;
$interval2 = 0;
$interval3 = 0;
$interval4 = 0;
$interval5 = 0;
$interval5 = 0;

$res_jd = mysqli_query($link1,"select datediff(close_date,open_date) as ageing from jobsheet_data where ".$loc_condi." ".$brand_condi." and close_date!='0000-00-00' and ".$daterange_close."");
while($row_jd = mysqli_fetch_assoc($res_jd)){
	if($row_jd["ageing"] >= 0 && $row_jd["ageing"] <= 2){
		$interval1 ++;
	}else if($row_jd["ageing"] > 2 && $row_jd["ageing"] <= 5){
		$interval2 ++;
	}else if($row_jd["ageing"] > 5 && $row_jd["ageing"] <= 10){
		$interval3 ++;
	}else if($row_jd["ageing"] > 10 && $row_jd["ageing"] <= 15){
		$interval4 ++;
	}else if($row_jd["ageing"] > 15 && $row_jd["ageing"] <= 30){
		$interval5 ++;
	}else{
		$interval6 ++;
	}
}

$p_interval1 = 0;
$p_interval2 = 0;
$p_interval3 = 0;
$p_interval4 = 0;
$p_interval5 = 0;
$p_interval6 = 0;

$res_jd_p = mysqli_query($link1,"select datediff('".$today."',open_date) as ageing from jobsheet_data where  ".$loc_condi." ".$brand_condi." and close_date='0000-00-00' and ".$daterange_open." ");
while($row_jd_p = mysqli_fetch_assoc($res_jd_p)){
	if($row_jd_p["ageing"] >= 0 && $row_jd_p["ageing"] <= 2){
		$p_interval1 ++;
	}else if($row_jd_p["ageing"] > 2 && $row_jd_p["ageing"] <= 5){
		$p_interval2 ++;
	}else if($row_jd_p["ageing"] > 5 && $row_jd_p["ageing"] <= 10){
		$p_interval3 ++;
	}else if($row_jd_p["ageing"] > 10 && $row_jd_p["ageing"] <= 15){
		$p_interval4 ++;
	}else if($row_jd_p["ageing"] > 15 && $row_jd_p["ageing"] <= 30){
		$p_interval5 ++;
	}else{
		$p_interval6 ++;
	}
}

function po_details($state_condi,$loc,$status,$daterange,$link1){

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "po_date >= '".$date_range[0]."' and po_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";

}

if($loc!=""){
	$loc_condi = " from_code='".$loc."' ";
}else{
	$loc_condi = " from_code in (select location_code from location_master where ".$state_condi.") ";
}

$res_eng_p = mysqli_query($link1,"select count(id) as po_count from po_master where status='".$status."'  and ".$loc_condi."  and ".$daterange_open."");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['po_count']!=''){
$count_job=$row_count['po_count'];

}else{
$count_job=0;
}

return $count_job;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>
<?=siteTitle?>
</title>

  <meta charset="utf-8">
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">

<script language="javascript" type="text/javascript">

/////////// function to get location on the basis of state
$(document).ready(function(){
	$('#state').change(function(){
		var stateid=$('#state').val();
		if(stateid!=""){
	  	$.ajax({
	    	type:'post',
			url:'../includes/getAzaxFields.php',
			data:{getlocationdrop:stateid},
			success:function(data){
	    		$('#locdiv').html(data);
			}
	  	});
		}
    });
});

$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
	<?php if($_REQUEST['daterange']==""){ ?>startDate:'<?=date("Y-m-01");?>',<?php }?>
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
$(document).ready(function(){
Highcharts.chart('container', {
  chart: {
    styledMode: true
  },

  title: {
    text: 'Closed TAT'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
	plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.y} Jobs'
                }
            }
        },
	 tooltip: {
            headerFormat: '<span style="font-size:11px"><strong>Closed Call TAT</strong></span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y} Jobs</b> of total count<br/>'
        },		
  series: [{
     type: 'pie',
   allowPointSelect: true,
    keys: ['name', 'y', 'selected', 'sliced'],
    data: [
      ['0 - 2 days', <?=$interval1?>, false],
      ['3 - 5 days', <?=$interval2?>, false],
      ['6 - 10 days', <?=$interval3?>, false],
	  ['11 - 15 days', <?=$interval4?>, false],
	  ['16 - 30 days', <?=$interval5?>, false],
      ['> 31 days', <?=$interval6?>, false]
    ],
    showInLegend: true
  }]
});});
//// Ageing TAT/////////////////////////////
$(document).ready(function(){
Highcharts.chart('container_pending', {
  chart: {
    styledMode: true
  },

  title: {
    text: 'Ageing'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
	plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.y} Jobs'
                }
            }
        },
	 tooltip: {
            headerFormat: '<span style="font-size:11px"><strong>Ageing</strong></span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y} Jobs</b> of total count<br/>'
        },		
  series: [{
    type: 'pie',
    allowPointSelect: true,
    keys: ['name', 'y', 'selected', 'sliced'],
    data: [
      ['0 - 2 days', <?=$p_interval1?>, false],
      ['3 - 5 days', <?=$p_interval2?>, false],
      ['6 - 10 days', <?=$p_interval3?>, false],
	  ['11 - 15 days', <?=$p_interval4?>, false],
	  ['16 - 30 days', <?=$p_interval5?>, false],
      ['> 31 days', <?=$p_interval6?>, false]
    ],
    showInLegend: true
  }]
});});

</script>
<script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <script src="../high/highcharts_new.js"></script>
 <script src="../high/js/modules/exporting.js"></script>
 <link rel="stylesheet" href="../high/highcharts.css">
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bar-chart"></i>Call Details</h2>
      <br/>
      <form class="form-horizontal" role="form" name="form1" action="" method="post">
	   <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">State</label>
			<div class="col-md-6" align="left">
			   <select name="state" id="state" class="form-control">
              	<option value="">All</option>
              	<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in (".$arrstate.") order by state"); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             	<option value="<?=$stateinfo['stateid']?>" <?php if($_REQUEST['state'] == $stateinfo['stateid']) { echo 'selected'; }?>><?=$stateinfo['state']?></option>
                <?php }?>
              </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
			<div class="col-md-6" align="left" id="locdiv">
                  <select name="location_code" id="location_code" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_loc = mysqli_query($link1,"select location_code,locationname from location_master  where statusid='1' and stateid in (".$arrstate.") order by locationname"); 
					while($row_loc = mysqli_fetch_assoc($res_loc)){ 
					?>		
					<option value="<?=$row_loc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_loc['location_code']) { echo 'selected'; }?>><?=$row_loc['locationname']." ".$row_loc['location_code']?></option>
					<?php }?>
              	  </select>
            </div>
          </div>
	    </div><!--close form group-->
	   <div class="form-group">
	     <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		 <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>
            <div class="col-md-6" align="left">
              <select name="brand" id="brand" class="form-control custom-select" >
                <option value="">All</option>
                <?php 
				 $sql ="select brand_id,brand from brand_master where status='1' and brand_id in (".$arrbrand.") order by brand";
			  	 $qry = mysqli_query($link1,$sql) ;
			  	 while ($row=mysqli_fetch_array($qry)){?>
                <option value="<?php echo $row['brand_id'];?>"<?php if($_REQUEST['brand']==$row['brand_id']){echo "selected";}?>><?php echo $row['brand'];?></option>
                <?php } ?>
              </select>
            </div>
		  </div>
	    </div><!--close form group-->
				       
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-12" style="text-align:center;">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
          </div>
        </div><br>
        <!--close form group-->
      </form>
	  
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr>
          <td  id="container" width="50%" ></td>
          <td id="container_pending"  width="50%"></td>
        </tr>
      </table>
	  
	  <div class="panel panel-info table-responsive">
		  <div class="panel-heading" style="text-align:center;"> Total Calls Detail </div>
		  <div class="panel-body">
			  <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
				<tr class="<?=$tableheadcolor?>" >
				  <th style="text-align:center;"><label class="control-label">Brand</label></th>
				  <th style="text-align:center"><label class="control-label">Call Type</label></th>
				  <th style="text-align:center"><label class="control-label">All Calls</label></th>
				  <th style="text-align:center"><label class="control-label">Closed Calls</label></th>
				  <th style="text-align:center"><label class="control-label">Cancel Calls</label></th>
				  <th style="text-align:center"><label class="control-label">Open Calls</label></th>
				</tr>
				<?php
				$allCall_tot = 0;
				$closeCall_tot = 0;
				$cancelCall_tot = 0;
				$openCall_tot = 0;
				
				$brd_qr = mysqli_query($link1, "SELECT brand_id,brand FROM brand_master WHERE  brand_id in (".$arrbrand.") ");
				if(mysqli_num_rows($brd_qr)>0){
				while($br_row = mysqli_fetch_array($brd_qr)){
				?>
				<tr>
				  <td rowspan="2" style="text-align:center;vertical-align: middle;"><label class="control-label"><?php echo $br_row['brand']; ?></label></td>
				  <td style="text-align:center"><label class="control-label">Installation</label></td>
				  <td style="text-align:center"><?php $allCall = tcd_fun("Installation", "", $daterange_open, $loc_condi, $brand_condi, $br_row['brand_id'], $link1); echo $allCall; ?></td>
				  <td style="text-align:center"><?php $closeCall = tcd_fun("Installation", "'48'", $daterange_open, $loc_condi, $brand_condi, $br_row['brand_id'], $link1); echo $closeCall; ?></td>
				  <td style="text-align:center"><?php $cancelCall = tcd_fun("Installation", "'12'", $daterange_open, $loc_condi, $brand_condi, $br_row['brand_id'], $link1); echo $cancelCall; ?></td>
				  <td style="text-align:center"><?php $aaa = ($allCall - ($closeCall + $cancelCall)); echo $aaa; ?></td>
				</tr>
				<tr>
				  <td style="text-align:center"><label class="control-label">Repair</label></td>
				  <td style="text-align:center"><?php $allCall2 = tcd_fun("", "", $daterange_open, $loc_condi, $brand_condi, $br_row['brand_id'], $link1); echo $allCall2; ?></td>
				  <td style="text-align:center"><?php $closeCall2 = tcd_fun("", "'6','8','11'", $daterange_open, $loc_condi, $brand_condi, $br_row['brand_id'], $link1); echo $closeCall2; ?></td>
				  <td style="text-align:center"><?php $cancelCal2 = tcd_fun("", "'12'", $daterange_open, $loc_condi, $brand_condi, $br_row['brand_id'], $link1); echo $cancelCall; ?></td>
				  <td style="text-align:center"><?php $bbb = ($allCall2 - ($closeCall2 + $cancelCall2)); echo $bbb; ?></td>
				</tr>
				<?php
				$allCall_tot += ($allCall+$allCall2);
				$closeCall_tot += ($closeCall+$closeCall2);
				$cancelCall_tot += ($cancelCall+$cancelCal2);
				$openCall_tot += ($aaa + $bbb);
				}
				}
				?>
				<tr class="<?=$tableheadcolor?>">
				  <td colspan="2" style="text-align:right;"><label class="control-label">Total</label></td>
				  <td style="text-align:center"><?php echo $allCall_tot; ?></td>
				  <td style="text-align:center"><?php echo $closeCall_tot; ?></td>
				  <td style="text-align:center"><?php echo $cancelCall_tot; ?></td>
				  <td style="text-align:center"><?php echo $openCall_tot; ?></td>
				</tr>
			  </table>
		  </div>
	  </div>

	  
	  <div class="panel panel-info table-responsive">
		  <div class="panel-heading" style="text-align:center;"> Open Calls Detail </div>
		  <div class="panel-body">
			  <table width="100%" class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
				<tr class="<?=$tableheadcolor?>" >
				  <th style="text-align:center" ><label class="control-label">Call Type</label></th>
				  <th style="text-align:center" ><label class="control-label">Category</label></th>
				  <th style="text-align:center" ><label class="control-label">No. of Calls</label></th>
				  <th style="text-align:center" ><label class="control-label">(0-3)days</label></th>
				  <th style="text-align:center" ><label class="control-label">(4-7)days</label></th>
				  <th style="text-align:center" ><label class="control-label">(8-15)days</label></th>
				  <th style="text-align:center" ><label class="control-label">(16-21)days</label></th>
				  <th style="text-align:center" ><label class="control-label">(22-30)days</label></th>
				  <th style="text-align:center" ><label class="control-label">> 30 days</label></th>
				</tr>
				<tr>
				  <td rowspan="3" style="text-align:center;vertical-align: middle;"><label class="control-label">Installation</label></td>
				  <td style="text-align:center"><label class="control-label">Part Pending</label></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "", "", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "30", "", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				</tr>
				<tr>
				  <td style="text-align:center"><label class="control-label">Pending with ASP</label></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "", "", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("Installation", "30", "", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				</tr>
				<tr style="background-color:#d9edf7;">
				  <td style="text-align:center"><label class="control-label">Sub Total #1</label></td>
				  <td style="text-align:center"><?php $st1_a = ocd_fun("Installation", "", "", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_a; ?></td>
				  <td style="text-align:center"><?php $st1_b = ocd_fun("Installation", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_b; ?></td>
				  <td style="text-align:center"><?php $st1_c = ocd_fun("Installation", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_c; ?></td>
				  <td style="text-align:center"><?php $st1_d = ocd_fun("Installation", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_d; ?></td>
				  <td style="text-align:center"><?php $st1_e = ocd_fun("Installation", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_e; ?></td>
				  <td style="text-align:center"><?php $st1_f = ocd_fun("Installation", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_f; ?></td>
				  <td style="text-align:center"><?php $st1_g = ocd_fun("Installation", "30", "", $daterange_open, $loc_condi, $brand_condi, "'3','7'", $today, $link1); echo $st1_g; ?></td>
				</tr>
				
				<tr>
				  <td rowspan="5" style="text-align:center;vertical-align: middle;"><label class="control-label">Repair</label></td>
				  <td style="text-align:center"><label class="control-label">Part Pending</label></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "", "", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "30", "", $daterange_open, $loc_condi, $brand_condi, "'3'", $today, $link1); ?></td>
				</tr>
				<tr>
				  <td style="text-align:center"><label class="control-label">EP Pending</label></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "", "", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "30", "", $daterange_open, $loc_condi, $brand_condi, "'5'", $today, $link1); ?></td>
				</tr>
				<tr>
				  <td style="text-align:center"><label class="control-label">Pendig with ASP</label></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "", "", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "30", "", $daterange_open, $loc_condi, $brand_condi, "'7'", $today, $link1); ?></td>
				</tr>
				<tr>
				  <td style="text-align:center"><label class="control-label">Req. for Approval</label></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "", "", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				  <td style="text-align:center"><?php echo ocd_fun("", "30", "", $daterange_open, $loc_condi, $brand_condi, "'50'", $today, $link1); ?></td>
				</tr>
				<tr style="background-color:#d9edf7;">
				  <td style="text-align:center"><label class="control-label">Sub Total #2</label></td>
				  <td style="text-align:center"><?php $st2_a = ocd_fun("", "", "", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_a; ?></td>
				  <td style="text-align:center"><?php $st2_b = ocd_fun("", "0", "3", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_b; ?></td>
				  <td style="text-align:center"><?php $st2_c = ocd_fun("", "4", "7", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_c; ?></td>
				  <td style="text-align:center"><?php $st2_d = ocd_fun("", "8", "15", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_d; ?></td>
				  <td style="text-align:center"><?php $st2_e = ocd_fun("", "16", "21", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_e; ?></td>
				  <td style="text-align:center"><?php $st2_f = ocd_fun("", "22", "30", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_f; ?></td>
				  <td style="text-align:center"><?php $st2_g = ocd_fun("", "30", "", $daterange_open, $loc_condi, $brand_condi, "'3','5','7','50'", $today, $link1); echo $st2_g; ?></td>
				</tr>
				
				<tr class="<?=$tableheadcolor?>">
				  <td colspan="2" style="text-align:center"><label class="control-label">Grand Total </label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_a + $st2_a); ?></label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_b + $st2_b); ?></label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_c + $st2_c); ?></label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_d + $st2_d); ?></label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_e + $st2_e); ?></label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_f + $st2_f); ?></label></td>
				  <td style="text-align:center"><label class="control-label"><?php echo ($st1_g + $st2_g); ?></label></td>
				</tr>
			  </table>
		  </div>
	  </div>
	  
	  <?php
	  	///// for installation //////
		$tat_Grand = 2.0;
	  	$tat_L1 = 1.0;
		$tat_U1 = 3.0;
		$tat_T1 = 2.0;
		$job_tat_L1 = 0;
		$job_tat_U1 = 0;
		
		$count_L1 = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc1, sum(close_tat) AS ltat1 FROM jobsheet_data WHERE  ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local')  and status = '48' and (call_for = 'Installation' or call_for = 'Reinstallation') "));
		$count_U1 = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc1, sum(close_tat) AS utat1 FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Upcountry' OR area_type = 'upcountry')  and status = '48' and (call_for = 'Installation' or call_for = 'Reinstallation') "));
		$jobs_L1 = $count_L1['jc1'];
		$jobs_U1 = $count_U1['jc1'];
		$jobs_T1 = round(($jobs_L1 + $jobs_U1),2);
		
		if($count_L1['ltat1']=="" || $count_L1['ltat1']==0){
			$job_tat_L1 = 1;
		}else{
			$job_tat_L1 = $count_L1['ltat1'];
		}
		
		if($count_U1['utat1']=="" || $count_U1['utat1']==0){
			$job_tat_U1 = 1;
		}else{
			$job_tat_U1 = $count_U1['utat1'];
		}
		
		///// for repair //////
		$tat_L2 = 2.0;
		$tat_U2 = 3.0;
		$tat_T2 = 3.0;
		$job_tat_L2 = 0;
		$job_tat_U2 = 0;
		
		$count_L2 = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc2, sum(close_tat) AS ltat2 FROM jobsheet_data WHERE  ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Local' OR area_type = 'local') and status in ('6','8','11') and (call_for != 'Installation' and call_for != 'Reinstallation') "));
		
		$count_U2 = mysqli_fetch_array(mysqli_query($link1,"SELECT count(job_id) AS jc2, sum(close_tat) AS utat2 FROM jobsheet_data WHERE ".$daterange_open." and ".$loc_condi." ".$brand_condi." and (area_type = 'Upcountry' OR area_type = 'upcountry')  and status in ('6','8','11') and (call_for != 'Installation' and call_for != 'Reinstallation') "));
		$jobs_L2 = $count_L2['jc2'];
		$jobs_U2 = $count_U2['jc2'];
		$jobs_T2 = round(($jobs_L2 + $jobs_U2),2);
		
		if($count_L2['ltat2']=="" || $count_L2['ltat2']==0){
			$job_tat_L2 = 1;
		}else{
			$job_tat_L2 = $count_L2['ltat2'];
		}
		
		if($count_U2['utat2']=="" || $count_U2['utat2']==0){
			$job_tat_U2 = 1;
		}else{
			$job_tat_U2 = $count_U2['utat2'];
		}
		
		$g_tot_job = round(($jobs_T1 + $jobs_T2),2);
		
	  ?>
	  
	  <div class="panel panel-info table-responsive">
		  <div class="panel-heading" style="text-align:center;"> TAT Details </div>
		  <div class="panel-body">
			  <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
				<tr class="<?=$tableheadcolor?>">
				  <th style="text-align:center"><label class="control-label">Call Type</label></th>
				  <th style="text-align:center"><label class="control-label">Location</label></th>
				  <th style="text-align:center"><label class="control-label">TAT Benchmark</label></th>
				  <th style="text-align:center"><label class="control-label">No. of Calls Closed</label></th>
				  <th style="text-align:center"><label class="control-label">Avg. TAT (Days)</label></th>
				  <th style="text-align:center"><label class="control-label">TAT Adherence %</label></th>
				</tr>
				<tr >
				  <td rowspan="3" style="text-align:center;vertical-align: middle;"><label class="control-label">Installation</label></td>
				  <td style="text-align:center"><label class="control-label">Local</label></td>
				  <td style="text-align:center"><?php echo $tat_L1; ?></td>
				  <td style="text-align:center"><?php echo $jobs_L1; ?></td>
				  <td style="text-align:center"><?php echo avg_tat_val($jobs_L1, $job_tat_L1); ?></td>
				  <td style="text-align:center"><?php echo tat_adhrence_L1($jobs_L1, $daterange_open, $loc_condi, $brand_condi, $link1); ?></td>
				</tr>
				<tr >
				  <td style="text-align:center"><label class="control-label">Upcountry</label></td>
				  <td style="text-align:center"><?php echo $tat_U1; ?></td>
				  <td style="text-align:center"><?php echo $jobs_U1; ?></td>
				  <td style="text-align:center"><?php echo avg_tat_val($jobs_U1, $job_tat_U1); ?></td>
				  <td style="text-align:center"><?php echo tat_adhrence_U1($jobs_U1, $daterange_open, $loc_condi, $brand_condi, $link1); ?></td>
				</tr>
				<tr style="background-color:#d9edf7;" >
				  <td style="text-align:center"><label class="control-label">Total</label></td>
				  <td style="text-align:center"><?php echo $tat_T1; ?></td>
				  <td style="text-align:center"><?php echo $jobs_T1; ?></td>
				  <td style="text-align:center"><?php $jobs = ($jobs_L1 + $jobs_U1); $job_tat = ($job_tat_L1 + $job_tat_U1); echo avg_tat_val($jobs, $job_tat); ?></td>
				  <td style="text-align:center"><?php echo tat_adhrence_T1($jobs_T1, $daterange_open, $loc_condi, $brand_condi, $link1); ?></td>
				</tr>
				<tr >
				  <td rowspan="3" style="text-align:center;vertical-align: middle;"><label class="control-label">Repair</label></td>
				  <td style="text-align:center"><label class="control-label">Local</label></td>
				  <td style="text-align:center"><?php echo $tat_L2; ?></td>
				  <td style="text-align:center"><?php echo $jobs_L2; ?></td>
				  <td style="text-align:center"><?php echo avg_tat_val($jobs_L2, $job_tat_L2); ?></td>
				  <td style="text-align:center"><?php echo tat_adhrence_L2($jobs_L2, $daterange_open, $loc_condi, $brand_condi, $link1); ?></td>
				</tr>
				<tr >
				  <td style="text-align:center"><label class="control-label">Upcountry</label></td>
				  <td style="text-align:center"><?php echo $tat_U2; ?></td>
				  <td style="text-align:center"><?php echo $jobs_U2; ?></td>
				  <td style="text-align:center"><?php echo avg_tat_val($jobs_U2, $job_tat_U2); ?></td>
				  <td style="text-align:center"><?php echo tat_adhrence_U2($jobs_U2, $daterange_open, $loc_condi, $brand_condi, $link1); ?></td>
				</tr>
				<tr style="background-color:#d9edf7;" >
				  <td style="text-align:center"><label class="control-label">Total</label></td>
				  <td style="text-align:center"><?php echo $tat_T2; ?></td>
				  <td style="text-align:center"><?php echo $jobs_T2; ?></td>
				  <td style="text-align:center"><?php $jobs = ($jobs_L2 + $jobs_U2); $job_tat = ($job_tat_L2 + $job_tat_U2); echo avg_tat_val($jobs, $job_tat);  ?></td>
				  <td style="text-align:center"><?php echo tat_adhrence_T2($jobs_T2, $daterange_open, $loc_condi, $brand_condi, $link1); ?></td>
				</tr>
				<tr class="<?=$tableheadcolor?>" >
				  <th colspan="2" style="text-align:center;"><label class="control-label"> Grand Total </label></th>
				  <th style="text-align:center"><label class="control-label"><?php echo $tat_Grand; ?></label></th>
				  <th style="text-align:center"><label class="control-label"><?php echo $g_tot_job; ?></label></th>
				  <th style="text-align:center"><label class="control-label">
					  <?php 
						$a = 0;
						$b = 0;
						if(($job_tat_L1 + $job_tat_U1)>0){
							$a = (($jobs_L1 + $jobs_U1)/($job_tat_L1 + $job_tat_U1));
						}else{
							$a = 0;
						}
						if(($job_tat_L2 + $job_tat_U2)>0){
							$b = (($jobs_L2 + $jobs_U2)/($job_tat_L2 + $job_tat_U2));
						}else{
							$b = 0;
						}
						$x = ($a + $b);
						$val = round($x,2);
						echo $val;
						
					  ?>
				  </label></th>
				  <th style="text-align:center"><label class="control-label"><?php echo tat_adhrence_G($g_tot_job, $daterange_open, $loc_condi, $brand_condi, $link1); ?></label></th>
				</tr>
			  </table>
		  </div>
	  </div>
	  	  	  
	  <div class="panel panel-info table-responsive">
		  <div class="panel-heading" style="text-align:center;"> PO Details </div>
		  <div class="panel-body">
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr class="<?=$tableheadcolor?>" >
          <th style="text-align:center;"><label class="control-label">#</label></th>
          <th style="text-align:center;"><label class="control-label">Raised By ASP</label></th>
          <th style="text-align:center;"><label class="control-label">Processed By NSWH</label></th>
          <th style="text-align:center;"><label class="control-label">Dispatched By NSWH</label></th>
          <th style="text-align:center;"><label class="control-label">Received By ASP</label></th>
        </tr>
        <tr>
          <td style="text-align:center;"><label class="control-label">PO</label></td>
          <td style="text-align:center;"><?php echo $pen_po = po_details($state_condi,$_REQUEST["location_code"],1,$_REQUEST['daterange'],$link1);?></td>
          <td style="text-align:center;"><?php echo $pro_po = po_details($state_condi,$_REQUEST["location_code"],2,$_REQUEST['daterange'],$link1);?></td>
          <td style="text-align:center;">0</td>
          <td style="text-align:center;">0</td>
        </tr>
      </table>
	  </div>
	  </div>
	        
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
