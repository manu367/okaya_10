<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
/////// make db function object
$db = new DB_Functions();
////// make clone of db connection
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db); 
//////requested JSON data
$json = $_REQUEST["BTR_JSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
//Decode JSON into an Array 
$data = json_decode($json); 
#### Check APP JSON
$app_json = "INSERT INTO api_json_data SET doc_no='".$data->job_no."',data='".$json."',activity='Final BTR Submit',ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json = mysqli_query($conn,$app_json);
################
//print_r($data);
//echo $data;
//Util arrays to create response JSON   
$users = $db->Complaint_FinalBTR($data->c1,$data->c2,$data->c3,$data->c4,$data->c5,$data->c6,$data->toc,$data->charging_hour,$data->backup_load,$data->backup_time,$data->eng_id,$data->job_no,$data->voc,$data->use_load);   
$a = array();
if ($users['error_code']!=''){         

array_push($a,$users);         
}       
####        
echo json_encode($a);     

?>