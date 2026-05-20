<?php 
include_once 'db_functions.php'; 
/////// make db function object
$db = new DB_Functions();
////// make clone of db connection
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db); 
//////requested JSON data
$json = $_REQUEST["usersJSON"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 
$data = json_decode($json); 
//print_r($data);
//exit;
#### Check APP JSON
$app_json = "INSERT INTO api_json_data SET doc_no='".$data[0]->job_no."',data='".$json."',activity='Complaint API',ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json = mysqli_query($conn,$app_json);
################
//Util arrays to create response JSON 
$a=array();
$b=array();
for($i=0; $i<count($data) ; $i++) {
	////// check APP version
//	if( $data[$i]->app_version == "1.0"){

   if($data[$i]->status==10 ){
			$res = $db->savejob_drop($data[$i]->job_no,$data[$i]->eid,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign);
  		}
//	}
	//Based on inserttion, create JSON response     
	if($res){        
		$b["id"] = $data[$i]->job_no;         
		$b["status"] =$res;         
		array_push($a,$b); 
	}else{    
		$b["id"] = $data[$i]->job_no;         
		$b["status"] = $res; 
		array_push($a,$b);
 	} 
}//Post JSON response back to Android Application 
echo json_encode($a); 
?>