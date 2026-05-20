<?php
//require_once("../includes/config_mis.php");
require_once("../includes/config.php");
$engname=$_REQUEST['eng_id'];
$locstr=$_REQUEST['eng_id'];
$count=count($engname);
$arrstate = getAccessState($_SESSION['userid'],$link1);
//print_r($arrstate);
////get access product details
for($i=0; $i<count($engname); $i++){
				if($eng_str){
					$eng_str.="','".$engname[$i];
				}else{
					$eng_str.= $engname[$i];
				}				
			}

//print_r($eng_str);
/*if ($_POST['Submit']=='Track Distance'){

   $cuur_year = date('Y');
	if($_POST['daterange']=='15'){
	$fromdate = $cuur_year."-".$_POST['month']."-01";
	$todate =   $cuur_year."-".$_POST['month']."-".$_POST['daterange'];
	}else{
	$fromdate = $cuur_year."-".$_POST['month']."-16";
	$todate =   $cuur_year."-".$_POST['month']."-".$_POST['daterange'];
	}
	
	
	
	
	
	
function getGeoAddress($lat,$lng){
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyBkmAs_ApGHcXG8vTcxTF7K06RLa5gQnWo',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	$decode_resp = json_decode($response,true);
	return $decode_resp['results'][0]['formatted_address'];
}	
	
	
	
	
//function calculateLocCustDist1($eng_id,$link1){
	//echo "SELECT job_no,eng_id FROM jobsheet_data WHERE  eng_id in ('".$eng_str."') and status='10' and close_date='".$_REQUEST['daterange']."' group by eng_id";exit;
	$res_sms = mysqli_query($link1,"SELECT job_no,eng_id FROM jobsheet_data WHERE  eng_id in ('".$eng_str."') and status='10' and close_date='".$_REQUEST['daterange']."' group by eng_id");
while($row_job1 = mysqli_fetch_array($res_sms)){
	//echo "SELECT job_no,close_date,eng_id,current_location,open_date FROM jobsheet_data WHERE  eng_id='".$eng_id."' and status='10' and close_date='2025-03-25' order by job_id asc";exit;
//$job_qry = mysqli_query($link1,"SELECT job_no,close_date,eng_id,current_location,open_date FROM jobsheet_data WHERE  eng_id='43249' and status='10' and close_date='2025-03-24' order by job_id asc") or die(mysqli_error($link1));
//$job_qry = mysqli_query($link1,"SELECT job_no,close_date,eng_id,current_location,open_date FROM jobsheet_data WHERE  eng_id='44251' and status='10' and close_date='2025-03-25' order by job_id asc") or die(mysqli_error($link1));
$job_qry = mysqli_query($link1,"SELECT job_no,close_date,eng_id,current_location,open_date FROM jobsheet_data WHERE  eng_id='".$row_job1['eng_id']."' and status='10' and close_date='".$_REQUEST['daterange']."' group by job_no order by job_id asc") or die(mysqli_error($link1));
//$job_qry = mysqli_query($link1,"SELECT job_no,close_date,eng_id,current_location,open_date FROM jobsheet_data WHERE  eng_id in('41596','44251') and status='10' and close_date='2025-03-25' order by job_id asc") or die(mysqli_error($link1));
//$job_result = mysqli_fetch_array($job_qry);
//print_r(mysqli_num_rows($job_qry));exit;
	$numrows = mysqli_num_rows($job_qry);
//$newjob="";
$newjob=array();
$j=0;
//if(){}else{}
if($j<=$numrows){
	//print_r($j);
while($row_job = mysqli_fetch_array($job_qry)){
//echo "<pre>";print_r($row_job);
if($j==0){
	//echo "SELECT latitude_in, longitude_in FROM mic_attendence_data  where user_id='".$row_job['eng_id']."' and insert_date='".$row_job['close_date']."'";
	//$loc_qry = mysqli_query($link1,"SELECT punch_latitude, punch_longitude,job_no FROM job_punch_details  where job_no='".$row_job['job_no']."'") or die(mysqli_error($link1));
	$loc_qry = mysqli_query($link1,"SELECT latitude_in, longitude_in FROM mic_attendence_data  where user_id='".$row_job['eng_id']."' and insert_date='".$row_job['close_date']."' order by id desc") or die(mysqli_error($link1));
		$loc_result = mysqli_fetch_array($loc_qry);
		 //print_r($loc_result);exit;
	  if(mysqli_num_rows($loc_qry) > 0){
		//$loc_lat = $loc_result['latitude_in'];
		//$loc_long = $loc_result['longitude_in'];
		  $loc_lat = $loc_result['longitude_in'];
		$loc_long = $loc_result['latitude_in'];
		  //print_r($loc_lat);exit;
		 //print_r($loc_result['job_no']);exit;
      // $newjob[]=$row_job['job_no'];
	  }
}else if($j>0){
	//$job_qryq = mysqli_query($link1,"SELECT job_no,close_date,eng_id,current_location,open_date FROM jobsheet_data WHERE  jo='43249' and status='10' and close_date='2025-03-24' order by job_id asc") or die(mysqli_error($link1));
		//echo "SELECT latitude, longitude, ref_no FROM user_daily_track  where ref_no='".$row_job['job_no']."' and job_status='10' and latitude != '' and longitude != '' and entry_date='".$job_result['close_date']."' ORDER BY id DESC ";
	//while($row_job1 = mysqli_fetch_array($job_qryq)){
		$k=$j-1;
		//print_r($newjob[$k]);exit;
		echo "SELECT latitude, longitude, ref_no FROM user_daily_track  where ref_no='".$newjob[$k]."' and job_status='10' and latitude != '' and longitude != '' ORDER BY id DESC <br/>";
	$loc_qry = mysqli_query($link1,"SELECT latitude, longitude, ref_no FROM user_daily_track  where ref_no='".$newjob[$k]."' and job_status='10' and latitude != '' and longitude != '' ") or die(mysqli_error($link1));
		$loc_result = mysqli_fetch_array($loc_qry);
	  if(mysqli_num_rows($loc_qry) > 0){
		$loc_lat = $loc_result['latitude'];
		$loc_long = $loc_result['longitude'];
		
	  }

}
	
	
if($loc_lat != "" && $loc_long != ""){
//print_r('dddddd');exit;
		//$hist_qry = mysqli_query($link1,"SELECT latitude, longitude FROM call_history  where job_no='".$job_no."' and latitude != '' and longitude != '' ORDER BY id DESC ") or die(mysqli_error($link1));
		//echo "SELECT latitude, longitude FROM user_daily_track  where ref_no='".$job_result['job_no']."' and job_status='10' and latitude != '' and longitude != '' and entry_date='".$job_result['close_date']."' ORDER BY id DESC ";exit;
		$hist_qry = mysqli_query($link1,"SELECT latitude, longitude FROM user_daily_track  where ref_no='".$row_job['job_no']."' and job_status='10' and latitude != '' and longitude != '' and entry_date='".$row_job['close_date']."' ORDER BY id DESC ") or die(mysqli_error($link1));
		$hist_result = mysqli_fetch_array($hist_qry);
//print_r($hist_result);exit;
		$cust_lat = $hist_result['latitude'];
		$cust_long = $hist_result['longitude'];

		////get way point array
		$intermediate_str = "";
		
		//foreach($arr_waypoints as $key => $arr_wp){
			foreach($hist_result as $key => $arr_wp){
			if($intermediate_str){
				$intermediate_str .= ',{
								"location":{
									"latLng":{
									"latitude": '.$arr_wp['lat'].',
									"longitude": '.$arr_wp['lng'].'
									}
								}
								}';
			}else{
				$intermediate_str .= '{
								"location":{
									"latLng":{
									"latitude": '.$arr_wp['lat'].',
									"longitude": '.$arr_wp['lng'].'
									}
								}
								}';
			}
		}
		$post_json = '{
							"origin":{
								"location":{
								"latLng":{
									"latitude": '.$loc_lat.',
									"longitude": '.$loc_long.'
								}
								},
								"sideOfRoad": true
							},
							"destination":{
								"location":{
								"latLng":{
									"latitude": '.$cust_lat.',
									"longitude": '.$cust_long.',
								}
								}
							},
							"intermediates": ['.$intermediate_str.'],
							"travelMode": "DRIVE",
							"routingPreference": "TRAFFIC_AWARE",
							"computeAlternativeRoutes": false,
							"routeModifiers": {
								"avoidTolls": false,
								"avoidHighways": false,
								"avoidFerries": false
							},
							"languageCode": "en-US",
							"units": "IMPERIAL"
							}';
		
		$url = "https://routes.googleapis.com/directions/v2:computeRoutes";

		/////execute curl
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $post_json,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'X-Goog-Api-Key: AIzaSyA3Zh6BY9Ypc7O7nXKd9sddeH76ChNVDPI',
				'X-Goog-FieldMask: routes.duration,routes.distanceMeters,routes.legs.distanceMeters,routes.legs.startLocation,routes.legs.endLocation'
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$decode_resp = json_decode($response,true);

		$dist_in_mtr = $decode_resp['routes'][0]['distanceMeters'];
		if($dist_in_mtr!="" && $dist_in_mtr!="0"){
			$dist = ($dist_in_mtr/1000);
		}else{
			$dist = 0;
		}
		

		
		
		$from_addrs = getGeoAddress($loc_lat,$loc_long);
		$to_addrs = getGeoAddress($cust_lat,$cust_long);

		echo "<br><br>";
		//echo $status." - status ";
		echo "<br><br>";
		echo $from_addrs;
		echo "<br><br>";
		echo $to_addrs;
		echo "<br><br>";
		echo $dist;
		echo "<br><br>";
		echo $dist_in_mtr;
		echo "<br><br>";

		$response1 = str_replace("'"," ",$response);
        date_default_timezone_set('Asia/Calcutta');
		//echo "INSERT INTO google_api_req_response SET job_no = '".$job_no."', eng_id = '', entry_date = '".$today."', entry_time = '".$today_time."', loc_lat = '".$loc_lat."', loc_long = '".$loc_long."', cust_lat = '".$cust_lat."', cust_long = '".$cust_long."', request_data = '".$url."', distence = '".$dist."', distence_in_mtr = '".$dist_in_mtr."', status = '".$status."', from_address = '".$from_addrs."', to_address = '".$to_addrs."', response_data = '".$response1."' "."<br><br>";
        $today = date("Y-m-d");
	    $today_time = date('H:i:s');
	    $datetime = date("Y-m-d H:i:s");
		//$resp_qry = mysqli_query($link1,"INSERT INTO google_api_req_response SET job_no = '".$job_no."', eng_id = '', entry_date = '".$today."', entry_time = '".$today_time."', loc_lat = '".$loc_lat."', loc_long = '".$loc_long."', cust_lat = '".$cust_lat."', cust_long = '".$cust_long."', request_data = '".$url."', distence = '".$dist."', distence_in_mtr = '".$dist_in_mtr."', status = '".$status."', from_address = '".$from_addrs."', to_address = '".$to_addrs."', response_data = '".$response1."' ") or die(mysqli_error($link1));
		//$resp_qry = mysqli_query($link1,"INSERT INTO eng_travel_details SET job_no = '".$row_job['job_no']."', eng_id = '".$row_job['eng_id']."', entry_date = '".$today."', entry_time = '".$today_time."', current_loc = '".$row_job['current_location']."', job_open_date = '".$row_job['open_date']."', job_close_date = '".$row_job['close_date']."', distence_in_km = '".$dist."', distence_in_mtr = '".$dist_in_mtr."',from_address='".$from_addrs."',to_address='".$to_addrs."', update_date_time='".$datetime."', api_response = '".$response1."' ") or die(mysqli_error($link1));

		//return "1^".$dist_in_mtr."^".$dist;
	}
	//else{
		//return "0^NA^NA";
	//}	
	
	
	
	
	
	
	
	$newjob[]=$row_job['job_no'];
$j++;	
}
}
}


mysqli_close($link1);
header("location:eng_distance_tracking.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;

	
	
	
	
	
}	*/
//print_r($_REQUEST[state]);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script type="text/javascript" language="javascript" >
/////////// function to get model on the basis of brand
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		dateLimit: { days: 30 },
		//minDate: new Date(2023, 04 - 1, 15),
		minDate: new Date(2025, 02 - 0, 1),
		maxDate: new Date(),
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
</script>

<link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="../js/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>

<script language="javascript" type="text/javascript">
	function makeDropdown(){
		$('.selectpicker').selectpicker();
	}
	$(document).ready(function() {
	$('#eng_id').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200",
		    enableFiltering: true
	});
});
	//function hideField(val){
	//alert();
	$(document).ready(function() {
		var value = $('#allst').val();
		//var all = 
	//alert(value);
		if(value=="all"){
			//$('#loc1').addClass('hide');
			//$('#eng1').addClass('hide');
		document.getElementById("loc1").style.display = "none";
		document.getElementById("location_code").style.display = "none";	
		document.getElementById("eng1").style.display = "none";
		document.getElementById("eng_id").style.display = "none";
		document.getElementById("location_code").removeAttribute("required");
		document.getElementById("eng_id").removeAttribute("required");	
		}else{
		document.getElementById("loc1").style.display = "block";
		document.getElementById("eng1").style.display = "block";
		document.getElementById("eng_id").style.display = "block";
		document.getElementById("location_code").style.display = "block";
			
		}
	//}
		});
</script>
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i> Eng Distance Tracking </h2>
		
		<?php if ($_REQUEST['Submit']=='Track Distance'){
		//print_r($_REQUEST);
		 $engstr = "";
			$arr_eng = $_REQUEST['eng_id'];
			for($i=0; $i<count($arr_eng); $i++){
				if($engstr){
					$engstr.="','".$arr_eng[$i];
				}else{
					$engstr.= $arr_eng[$i];
				}
			}	 	
		?>

		        <div class="form-group">
		  <div class="col-md-10">  
			<div class="col-md-6" align="left" style="float: right;margin-right: -349px;">
			
               <!--<a href="../excelReports/pna_aging_excel.php?rname=<?=base64_encode("serial_history_excel")?>&rheader=<?=base64_encode("Serial History Report")?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&eng_code=<?=base64_encode($_REQUEST['eng_code'])?>&daterange=<?=$_REQUEST['daterange']?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a>-->
				
				Eng Distance Tracking <a href="excelexport.php?rname=<?=base64_encode("eng_distance_tracking")?>&rheader=<?=base64_encode("eng_distance_tracking")?>&daterange=<?=$_REQUEST['daterange']?>&eng=<?=base64_encode($engstr);?>" title="Export employees details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export employees details in excel"></i></a>
			
            </div>
          </div>
	    </div><!--close form group-->
		
        <?php }?>
		
		
	  <form class="form-horizontal" role="form" name="form1" action="" method="post" >
		<!--<div class="form-group">
         <div class="col-md-6" style="margin-left: 175px;margin-top: 0px;"><label class="col-md-6 control-label"> Month <span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
				
				<select name="month" id="month" class="form-control selectpicker required" required onChange="document.form1.submit();" data-live-search="true">
					
				 <option value="" <?php if($_REQUEST['month'] == "") { echo 'selected'; }?> >Please Select Month</option>
					 <option value="01" <?php if($_REQUEST['month'] == "01") { echo 'selected'; }?> >January</option>
					 <option value="02" <?php if($_REQUEST['month'] == "02") { echo 'selected'; }?> >February</option>
					 <option value="03" <?php if($_REQUEST['month'] == "03") { echo 'selected'; }?> >March</option>
					 <option value="04" <?php if($_REQUEST['month'] == "04") { echo 'selected'; }?> >April</option>
					 <option value="05" <?php if($_REQUEST['month'] == "05") { echo 'selected'; }?> >May</option>
					 <option value="06" <?php if($_REQUEST['month'] == "06") { echo 'selected'; }?> >June</option>
					 <option value="07" <?php if($_REQUEST['month'] == "07") { echo 'selected'; }?> >July</option>
					 <option value="08" <?php if($_REQUEST['month'] == "08") { echo 'selected'; }?> >August</option>
					 <option value="09" <?php if($_REQUEST['month'] == "09") { echo 'selected'; }?> >September</option>
					 <option value="10" <?php if($_REQUEST['month'] == "10") { echo 'selected'; }?> >October</option>
					 <option value="11" <?php if($_REQUEST['month'] == "11") { echo 'selected'; }?> >November</option>
					 <option value="12" <?php if($_REQUEST['month'] == "12") { echo 'selected'; }?> >December</option>
				 </select>
            </div>
          </div>
		  	
	    </div>-->
		  <!--close form group-->
		  <div class="form-group">
        
				  
			<div id= "dt_range" class="col-md-6" style="margin-left: 216px;"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
			<!-- <div class="col-md-6 input-append date" align="left">
				<select name="daterange" id="daterange" class="form-control selectpicker required" required onChange="document.form1.submit();" data-live-search="true">
					<?php 
	$cuur_month = date('m');
    $cuur_year = date('Y');
	if($_REQUEST['month']!='' && $cuur_month!=$_REQUEST['month']){
	$a_date = $cuur_year."-".$_REQUEST['month']."-01";
	$last_day = date("t", strtotime($a_date));
	}else{
	//$cuur_month = date('m');
    $a_date = $cuur_year."-".$cuur_month."-01";
	$last_day=date("t", strtotime($a_date));
	}
					 
   //echo date("Y");
					?>
				 <option value="" <?php if($_REQUEST['daterange'] == "") { echo 'selected'; }?> >Please Select Month <span style="color:#F00">*</span></option>
					<?php if($_REQUEST['month']!='' && $_REQUEST['month'] <= $cuur_month){ ?>
					
									
					
					<?php for($i=1;$i<=$last_day;$i++){
	                $month = $_REQUEST['month'];
					$days = $cuur_year."-".$month."-".$i;
					?>
					
					<option value="2025-<?=$_REQUEST['month']?>-<?=$i?>" <?php if($_REQUEST['daterange'] == $days) { echo 'selected'; }?> >2025-<?=$_REQUEST['month']?>-<?=$i?></option>
					
					<?php }} ?>
				 </select>
			</div>-->
          
			
	    </div><!--close form group-->
		 <!-- <div class="form-group">
         <div class="col-md-6" style="margin-left: 175px;"><label class="col-md-6 control-label"> State <span style="color:#F00">*</span></label>	  
			 <input type="hidden" name="allst" id="allst" value="<?=$_REQUEST['state']?>">
			<div class="col-md-6" >
				
				<select name="state" id="state" class="form-control selectpicker required" required onChange="document.form1.submit();hideField(this.value)" data-live-search="true" >
					<option value="" <?php if($_REQUEST['state'] == "") { echo 'selected'; }?> >Please Select State</option>
					<option value="all" <?php if($_REQUEST['state']=="all"){echo 'selected';}?>>ALL</option>
					<?php
					$state_sql = mysqli_query($link1,"SELECT stateid,state FROM state_master  where countryid='1' and stateid!='41' ") or die(mysqli_error($link1));
				while($state = mysqli_fetch_assoc($state_sql)){
					?>
				 
					
					 <option value="<?php echo $state['stateid'];?>" <?php if($_REQUEST['state'] == $state['stateid']) { echo 'selected'; }?> ><?php echo $state['state'];?></option>
					<?php } ?>
				 </select>
            </div>
          </div>
		  	
	    </div>-->
		  <div class="form-group" id="loc1">
         <div class="col-md-6" style="margin-left: 175px;"><label class="col-md-6 control-label"> location <span style="color:#F00">*</span> </label>	  
			<div class="col-md-6" >
				<!--<input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />-->
				<select name="location_code" id="location_code" class="form-control selectpicker " <?php if($_REQUEST['state']!="all"){echo 'required';}?> onChange="document.form1.submit();" data-live-search="true">
					<option value="" <?php if($_REQUEST['location_code'] == "") { echo 'selected'; }?> >Please Select Location</option>
					<?php 
					//$loc_sql = mysqli_query($link1,"SELECT location_code,locationname FROM location_master  where statusid='1' and stateid='".$_REQUEST['state']."' and location_code in(SELECT location_code FROM locationuser_master  where statusid='1' and stateid='".$_REQUEST['state']."') ") or die(mysqli_error($link1));
					$loc_sql = mysqli_query($link1,"SELECT location_code,locationname FROM location_master  where statusid='1' and location_code in(SELECT location_code FROM locationuser_master  where statusid='1' and stateid in($arrstate)) ") or die(mysqli_error($link1));
				while($location = mysqli_fetch_assoc($loc_sql)){
					?>
				 
					
					 <option value="<?php echo $location['location_code'];?>" <?php if($_REQUEST['location_code'] == $location['location_code']) { echo 'selected'; }?> ><?php echo $location['locationname'];?>(<?php echo $location['location_code'];?>)</option>
					<?php } ?>
				 </select>
            </div>
          </div>
		  	
	    </div>
		  <div class="form-group" id="eng1">
         <div class="col-md-6" style="margin-left: 175px;"><label class="col-md-6 control-label"> Engneer <span style="color:#F00">*</span> </label>	  
			<div class="col-md-6" >
				<!--<input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />-->
				<select name="eng_id[]" id="eng_id" multiple="multiple" class="form-control " <?php if($_REQUEST['state']!="all"){echo 'required';}?> onChange="document.form1.submit();" >
					
					<?php
					//$eng_sql = mysqli_query($link1,"SELECT eng_id FROM jobsheet_data  where status='10' and close_date!='0000-00-00' and close_date='2025-03-25' and eng_id!='' group by eng_id order by job_id desc  ") or die(mysqli_error($link1));
					$eng_sql = mysqli_query($link1,"SELECT userloginid,locusername FROM locationuser_master  where statusid='1' and location_code='".$_REQUEST['location_code']."'") or die(mysqli_error($link1));
				while($engneer = mysqli_fetch_assoc($eng_sql)){
					?>
				 
					
					 <option value="<?php echo $engneer['userloginid'];?>" <?php if(!empty($engname)){for($i=0; $i<count($engname); $i++){if($engname[$i] == $engneer['userloginid']) { echo 'selected'; }}}?> ><?php echo $engneer['locusername'];?>(<?php echo $engneer['userloginid'];?>)</option>
					<?php } ?>
				 </select>
            </div>
          </div>
		  	
	    </div><!--close form group-->
		
        <div class="form-group">
         <div class="col-md-6" style="margin-left: 140px;"><label class="col-md-5 control-label" style="margin-right: 28px;"><!---Engineer Name----></label>	  
			<div class="col-md-6">
        <div style="display:inline-block;float:right">
                	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               		<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               		<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="Track Distance"  title="Track Distance!">
                </div>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6">
                
            </div>
          </div>
	    </div>
        <!--close form group-->
	  </form>
		
       

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>