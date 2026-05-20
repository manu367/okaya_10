<?php 
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$json = $_POST["btrJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
//Decode JSON into an Array 
$data = json_decode($json); 
#### Check APP JSON
$app_json="insert into discharger_api_json_data set doc_no='".$data->complaint_no."',data='".str_replace("'"," replace ",$json)."',activity='BTR_SAVE',ip='".$_SERVER['REMOTE_ADDR']."' , serial_no = '".$data->serial_no."' ";
$sql_json=mysqli_query($conn,$app_json);
################
//Util arrays to create response JSON 
$a=array();
if(empty($data)){
	$b["job_no"] = $job_no;
	$b["status"] = 'POST Data Blank';
	$b["status_code"]='1004';
array_push($a,$b); 
}else if(!empty($data)){
$test_dt = $data->testing_date;
$job_no = $data->complaint_no;
$test_loc = $data->tested_location;
$test_by = $data->tested_by;
$device_name = $data->device_name;
$capacity = $data->battery_capacity;
$test_load = $data->test_load;
$start_voltage = $data->start_voltage;
$cut_off_voltage = $data->cut_off_voltage;
$test_time = $data->test_time;
$test_result = $data->test_result;
$special_gr = $data->special_gr;
$backup = $data->backup;
$doc_name = $data->result_document_name;
$end_voltage = $data->end_voltage;
$app_version = $data->app_version;
$test_decision = $data->final_decision;
$phy_condition = $data->phy_condition;
$test_interrupt = $data->test_interrupt;	
$serial_no = $data->serial_no;	
//Store Complaints into MySQL DB 
$res = $db->save_btr($test_dt,$job_no,$test_loc,$test_by,$device_name,$capacity,$test_load,$start_voltage,$cut_off_voltage,$test_time,$test_result,$special_gr,$backup,$doc_name,$end_voltage,$app_version,$test_decision,$phy_condition,$test_interrupt,$serial_no);
//Based on inserttion, create JSON response             
array_push($a,$res); 
	
  }
 //Post JSON response back to Android Application 
 echo json_encode($a); 
 mysqli_close($conn);
 
 
 ?>