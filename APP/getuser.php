<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getUserDetails();  
  
 $a = array();  
 
$b = array();

if(mysqli_num_rows($users)>0){
   $row = mysqli_fetch_array($users);
  $c = array();
$d= array();
  
   $map_loc = $db->getmaploc($row["location_code"]);  

	while($row_loc=mysqli_fetch_array($map_loc)){
	
	// $maprepair=$row_loc["repair_location"];
	  $p_name=$db->getAnyDetails($row_loc["repair_location"],"locationname","location_code","location_master");
	$c["mapcode"]=$row_loc["repair_location"];
	$c["location_name"]= $p_name;
	array_push($d,$c);
	 
	}
        $mk = json_encode($d); 
	   $map_state = $db->getlocationstate($row["location_code"]);  
	   $row_state=mysqli_fetch_array($map_state);
	    $state_name=$db->getAnyDetails($row_state["stateid"],"state","stateid","state_master");
		$city_name= $db->getAnyDetails($row_state['cityid'],"city","cityid","city_master");
	//$map_rep_loc="Maploc :". $mk;
   $username = preg_replace('/[^A-Za-z0-9]/', "", $row["locusername"]); 
   $usercode = preg_replace('/[^A-Za-z0-9]/', "", $row["userloginid"]); 
	$b["return_status"] = $username;
	$b["usercode"]=$usercode;
	$b["contact_no"] = $row["contactmo"];
    $b["password"] = $row["pwd"]; 
	$b["branch_code"] = $row["location_code"]; 
	$b["maplist"] =$d; 
	$b["state"] = $state_name;
	$b["city"] =$city_name;
	

	$b["status"]=1;
}else{
	$b["status"]=0;
}
array_push($a,$b);


echo json_encode($a);    
?>