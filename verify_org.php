<?php
$today = date("Y-m-j");
$user_id = $_POST['userid'];
$password1 = $_POST['pwd'];
require_once("includes/dbconnect.php");
///// if admin user is going to login
$query_aut="Select * from admin_users where (username = '".$user_id."' or sapid = '".$user_id."') and status='1'";
$result_aut=mysqli_query($link1,$query_aut) or die("er1".mysqli_error($link1));
if(mysqli_num_rows($result_aut)>0){
	$arr_res_aut=mysqli_fetch_assoc($result_aut);
	if($arr_res_aut['password']==$password1){
		session_start();
		$_SESSION['userid']=$arr_res_aut['username'];
		$_SESSION['sapid']=$arr_res_aut['sapid'];
		$_SESSION['uname']=$arr_res_aut['name'];
		$_SESSION['utype']=$arr_res_aut['utype'];
		$_SESSION['id_type']=$arr_res_aut['utype'];
		$_SESSION['master']=$arr_res_aut['master'];
		///// capture login details
		$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		$expld = explode(",",$details -> loc);
		$lati = $expld[0];
		$longi = $expld[1];
		//$location = file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']);
		//print_r($location);
		mysqli_query($link1,"INSERT INTO login_master set userid='".$_SESSION['userid']."', sapid='".$_SESSION['sapid']."', lastlogin='".date("Y-m-d H:i:s")."', latitude='".$lati."', longitude='".$longi."', ipaddress='".$_SERVER['REMOTE_ADDR']."'");
		header("Location:admin/home2.php");
		exit;
	}else{
		$msg='1';
		header("Location:index.php?msg=".$msg);
		exit;
	}
}
///// if location is going to login
$query_aut="Select * from location_master where location_code='".$user_id."' and statusid='1'";
$result_aut=mysqli_query($link1,$query_aut) or die(mysqli_error($link1));
if(mysqli_num_rows($result_aut)>0){
	$arr_res_aut=mysqli_fetch_assoc($result_aut);
	if($arr_res_aut['pwd']==$password1){
		////////////////////////////
		session_start();
		$_SESSION['userid']=$arr_res_aut['location_code'];
		$_SESSION['uname']=$arr_res_aut['locationname'];
		$_SESSION['id_type']=$arr_res_aut['locationtype'];	
		$_SESSION['asc_code']=$arr_res_aut['location_code'];
		$_SESSION['othid']=$arr_res_aut['othid'];
		$_SESSION['email']=$arr_res_aut['emailid'];
		$_SESSION['countryid']=$arr_res_aut['countryid'];
		$_SESSION['stateid']=$arr_res_aut['stateid'];
		$_SESSION['cityid']=$arr_res_aut['cityid'];
		$_SESSION['districtid']=$arr_res_aut['districtid'];
		$_SESSION['zipcode']=$arr_res_aut['zipcode'];
		$_SESSION['gstno']=$arr_res_aut['gstno'];
		$_SESSION['locusertype']="LOCATION";
		///// capture login details
		$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		$expld = explode(",",$details -> loc);
		$lati = $expld[0];
		$longi = $expld[1];
		mysqli_query($link1,"INSERT INTO login_master set userid='".$_SESSION['userid']."', sapid='".$_SESSION['sapid']."', lastlogin='".date("Y-m-d H:i:s")."', latitude='".$lati."', longitude='".$longi."', ipaddress='".$_SERVER['REMOTE_ADDR']."'");
		if($_SESSION['id_type']=='WH'){
			header("Location:wh/home2.php");	
		}
		else{
			header("Location:asp/home2.php");
		}
		exit;
	}else{
		$msg='1';
		header("Location:index.php?msg=".$msg);
		exit;
	}
}
///// if location user is going to login
$query_aut="Select * from locationuser_master where userloginid='".$user_id."' and statusid='1'";
$result_aut=mysqli_query($link1,$query_aut) or die(mysqli_error($link1));
if(mysqli_num_rows($result_aut)>0){
	$arr_res_aut=mysqli_fetch_assoc($result_aut);
	if($arr_res_aut['pwd']==$password1){
		///// get location details
		$loc_det = mysqli_fetch_assoc(mysqli_query($link1,"select locationtype,countryid,stateid,cityid,districtid,zipcode,othid from location_master where location_code='".$arr_res_aut['location_code']."'"));
		////////////////////////////
		session_start();
		$_SESSION['userid']=$arr_res_aut['userloginid'];
		$_SESSION['uname']=$arr_res_aut['locusername'];
		$_SESSION['id_type']=$loc_det['locationtype'];	
		$_SESSION['asc_code']=$arr_res_aut['location_code'];
		$_SESSION['email']=$arr_res_aut['emailid'];
			$_SESSION['othid']=$loc_det['othid'];
		$_SESSION['countryid']=$loc_det['countryid'];
		$_SESSION['stateid']=$loc_det['stateid'];
		$_SESSION['cityid']=$loc_det['cityid'];
		$_SESSION['districtid']=$loc_det['districtid'];
		$_SESSION['zipcode']=$loc_det['zipcode'];
		$_SESSION['locusertype']="LOCATION USER";
		///// capture login details
		$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		$expld = explode(",",$details -> loc);
		$lati = $expld[0];
		$longi = $expld[1];
		mysqli_query($link1,"INSERT INTO login_master set userid='".$_SESSION['userid']."', sapid='".$_SESSION['sapid']."', lastlogin='".date("Y-m-d H:i:s")."', latitude='".$lati."', longitude='".$longi."', ipaddress='".$_SERVER['REMOTE_ADDR']."'");
	if($_SESSION['id_type']=='WH'){
		header("Location:wh/home2.php");	
	}
	else{
		header("Location:asp/home2.php");
	}
	exit;
}else{
	$msg='1';
	header("Location:index.php?msg=".$msg);
	exit;
}
}
else{
	$msg='4';
	header("Location:index.php?msg=".$msg);
	exit;
}
?>