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
  $data = json_decode($json); 
 
/*
//Decode JSON into an Array 

#### Check APP JSON
$app_json="insert into api_json_data set doc_no='".$data[0]->amcid."',data='".$json."',activity='AMC API',ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json=mysqli_query($conn,$app_json);
################*/
//Util arrays to create response JSON 
$a=array();
$b=array(); 



for($i=0; $i<count($data) ; $i++) {
	
$res = $db->postTrainingdata($data[$i]->training_subject,$data[$i]->details,$data[$i]->eng_id);
//Based on inserttion, create JSON response     
if($res==1){                 
$b["msg"] ='Traiing Request Details Successfully created.';         
array_push($a,$b); 
}else{    
$b["msg"] ='Please try Again.'; 
 array_push($a,$b);  

 } 
 } //Post JSON response back to Android Application 
 echo json_encode($a); 
 ?>