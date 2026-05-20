<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
/////url
$url = $db->getAttachmentUrl();
$makeurl = trim($url,"/API")."/";
$app_version=$_REQUEST['app_version']; 
$a = array();  
$b = array();
$c = array();
$d= array();
//if($app_version == "1.1" || $app_version == "1.2" || $app_version == "2.0" || $app_version == "2.1"){
if($app_version == "1.0"){
$users = $db->getUserDetails();
if(mysqli_num_rows($users)>0){
   $row = mysqli_fetch_array($users);
   ///// insert in app log table
   $applog = $db->appLogin($_REQUEST['eid'],$_REQUEST['deviceid'],$_REQUEST['token']);
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
		$branch_name= $db->getAnyDetails($row['location_code'],"locationname","location_code","location_master");
	//$map_rep_loc="Maploc :". $mk;
   $username = preg_replace('/[^A-Za-z0-9]/', "", $row["locusername"]); 
   $usercode = preg_replace('/[^A-Za-z0-9]/', "", $row["userloginid"]); 
	$b["username"] = $username;
	$b["usercode"]=$row["userloginid"];
	$b["contact_no"] = $row["contactmo"];
    $b["password"] = $row["pwd"]; 
	$b["branch_code"] = $row["location_code"];
	$b["branch_name"] = $branch_name;	
	$b["maplist"] =$d; 
	$b["state"] = $state_name;
	$b["city"] =$city_name;
	$b["status"]=1;
	$b["nav_list"] = array(
						array("tab_name" => "Home","tab_link" => "ServicesFragment","tab_icon" => $makeurl."icon/home.png"),
						array("tab_name" => "Service","tab_link" => "ServicesFragment","tab_icon" => $makeurl."icon/consult.png"),
						array("tab_name" => "Stock","tab_link" => "StockFragment","tab_icon" => $makeurl."icon/shelf.png"),
						array("tab_name" => "Performance","tab_link" => "PerformanceFragment","tab_icon" => $makeurl."icon/rate.png"),
						array("tab_name" => "Notice Board","tab_link" => "NoticeBoardFragment","tab_icon" => $makeurl."icon/noticeboard.png"),
						array("tab_name" => "Trainings","tab_link" => "TrainingActivity","tab_icon" => $makeurl."icon/elearning.png"),
						array("tab_name" => "Attendance","tab_link" => "AttendanceFragment","tab_icon" => $makeurl."icon/biometric-identification.png"),
						array("tab_name" => "Apply Leave","tab_link" => "LeaveApply","tab_icon" => $makeurl."icon/departure.png"),
						array("tab_name" => "Expense & Claim","tab_link" => "TaDaActivity","tab_icon" => $makeurl."icon/expenses.png"),
						array("tab_name" => "Profile","tab_link" => "Profile","tab_icon" => $makeurl."icon/personal-information.png"),
						array("tab_name" => "Change Password","tab_link" => "","tab_icon" => $makeurl."icon/secure.png"),
						array("tab_name" => "Engineer Support","tab_link" => "CustomerSupport","tab_icon" => $makeurl."icon/technical-support.png"));
}else{
	$b["status"]=0;
}
array_push($a,$b);
}else{
$b["status"]="App Version Error";
array_push($a,$b);
}
echo json_encode($a);    
?>