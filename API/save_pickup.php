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
	//if( $data[$i]->app_version == "1.0"){

	/// Save  Pickup
	
	
	
/*	[{"job_no":"UP2308220001","eid":"RVSASP0930U1","latitude":"28.6037158","longitude":"77.3535983",
	"date_time":"2023-08-30 15:55:20"
	,"location_address":"B- 32, near pink zone, Block B, Sector 57, Noida, Uttar Pradesh 201301, India",
	"warranty_status":"IN",
	"app_version":"1.0","dop":"2023-08-15",
	"serial_no":"76676787","model_id":"M04828","model":"AERO","remark":"testing ","status":"10","path_img":"30082023155458UP2308220001_GL_1.jpg","path_img1":"30082023155458UP2308220001_GL_2.jpg","path_img2":"30082023155458UP2308220001_GL_3.jpg","path_img_sign":"IMG_20230830_155512UP2308220001_signature.jpg"}]*/
		///////// check repair status is WIP (Work in progress / Pending) or Handover (Closed)
        if($data[$i]->status==17 ){
			$res = $db->savejob_pickup($data[$i]->job_no,$data[$i]->eid,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_sign);
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