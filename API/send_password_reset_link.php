<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a=array();

date_default_timezone_set('Asia/Kolkata');
$today=date("Y-m-d");
$currtime=date("H:i:s");

$userid=$_REQUEST['id'];
$usertype=$_REQUEST['typ'];

if($userid != "" && $usertype != ""){
	$uid = $userid;
	$mob_no = "";
	$msg1 = "";
	$tr=0;
	if($usertype=="A"){
		$chkquery="SELECT username, id, phone  FROM admin_users where username='" . $uid . "' and status = '1' ";
		$check=mysqli_query($conn,$chkquery) or die(mysqli_error($conn));
		$check_dt=mysqli_fetch_array($check);
		
		$mob_no = $check_dt['phone'];
		$tr=1;
		$msg1 = "Password changing link sended on your mobile - ".$mob_no;
	}else if($usertype=="L"){
		$chkquery="SELECT location_code, locationid, contactno1 FROM location_master where location_code='" . $uid . "' and statusid = '1' ";
		$check=mysqli_query($conn,$chkquery) or die(mysqli_error($conn));
		$check_dt=mysqli_fetch_array($check);
		
		$mob_no = $check_dt['contactno1'];
		$tr=1;
		$msg1 = "Password changing link sended on your mobile - ".$mob_no;
	}else if($usertype=="U"){
		$chkquery="SELECT userloginid, id, contactmo FROM locationuser_master where userloginid='" . $uid . "' and statusid = '1' ";
		$check=mysqli_query($conn,$chkquery) or die(mysqli_error($conn));
		$check_dt=mysqli_fetch_array($check);
		
		$mob_no = $check_dt['contactmo'];
		$tr=1;
		$msg1 = "Password changing link sended on your mobile - ".$mob_no;
	}else{
		$msg1 = "Some error occured, Please try again.";
		$tr=0;
	}
}else{
	$msg1 = "Some error occured, Please try again.";
	$tr=0;
	$mob_no = "";
}

if($tr == 1){
	/*if($mob_no != ""){
		$link_str = "https://microtek.cancrm.in/fp.php?id=".base64_encode($uid)."&typ=".base64_encode($usertype);
		$msg = "Hi Microtek User,
There was a request to change your password!
If you did not make this request then please ignore this SMS.
Otherwise, please click this link to change your password: ".$link_str;
		
		$mobile_no = $mob_no;
		//$mobile_no = "9694608882";
					
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  //CURLOPT_URL =>'http://www.smsjust.com/sms/user/urlsms.php?username=microtek&pass=saloni19&senderid=MtekIn&dest_mobileno='.$mobile_no.'&message='.urlencode($msg).'&response=Y',
		  CURLOPT_URL =>'http://114.143.190.131/sms/user/urlsms.php?username=microtek&pass=saloni19&senderid=MtekIn&dest_mobileno='.$mobile_no.'&message='.urlencode($msg).'&response=Y',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  //CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		));
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_TIMEOUT_MS, 1000); 
		
		$response = curl_exec($curl);
		curl_close($curl);
	}*/
}

$a["msg"]=$msg1;
$a["status"]=$tr; 

echo json_encode($a);    
?>