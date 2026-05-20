<?php 

include_once 'db_functions.php'; 

$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
 
 $json = $_REQUEST["usersJSON"];

if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
	

//Decode JSON into an Array 
$data = json_decode($json); 
#### Check APP JSON
$app_json="insert into api_json_data set doc_no='".$data[0]->amcid."',data='".$json."',activity='AMC API',ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json=mysqli_query($conn,$app_json);
################
//Util arrays to create response JSON 
$a=array();
$b=array(); 



for($i=0; $i<count($data) ; $i++) {

 
  
  $res = $db->saveAMCAutoInvoice($data[$i]->amcStatus,$data[$i]->remark,$data[$i]->amcid,$data[$i]->eid,$data[$i]->latitude,$data[$i]->longitude,$data[$i]->location_address,$data[$i]->app_version,$data[$i]->path_img,$data[$i]->path_img1,$data[$i]->path_img2,$data[$i]->path_img_serial,$data[$i]->paymentDetail,$data[$i]->location_code,$data[$i]->name);

//Based on inserttion, create JSON response     
if($res==1){        
$b["amcid"] = $data[$i]->amcid;         
$b["status"] =$res;
$b["msg"] ='AMC Details Successfully Updated.';         
array_push($a,$b); 
}else{    
	//$res_expl = explode("~",$res);
$b["amcid"] = $data[$i]->amcid;
	//if($res_expl[0]=="0"){
		//$b["status"] = $res_expl[0];
		//$b["msg"] = $res_expl[1]; 
	//}else{
		$b["status"] = $res;
		$b["msg"] = 'Please wait and try after sometime.'; 

	//}
 array_push($a,$b);  

 } 
 } //Post JSON response back to Android Application 
 echo json_encode($a); 
 ?>