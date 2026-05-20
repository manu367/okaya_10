<?php
$page_type = "insecure";
require_once("security/backend.php");

if(!isset($_SESSION["userid"]))
{
	$user = $_POST['userid'];
	$pass = $_POST['pwd'];

	if($_SESSION["otp"]["otp"] == "verified")
	{
		$user = $_SESSION["otp"]["temp_user"];
		$pass = $_SESSION["otp"]["random"];
		unset($_SESSION["otp"]);
		$_SESSION["otp"] = "verified";
	}
	else
	{
		//$pass = hash("sha256", md5($pass));
	}
	
	$res = $acc->doLogin($link1, $user, $pass);
	if($res["status"] == "success")
	{
		if($res["type"] == "admin")
		{
			exit(header('Location:'.$root.'/admin/home2.php'));
			//exit(header('Location: ../admin/welcome.php'));
		}
		elseif($res["type"] == "location")
		{
			if($_SESSION['id_type']=='WH'){
				exit(header('Location:'.$root.'/wh/home2.php'));
			}
			else{
				exit(header('Location:'.$root.'/asp/home2.php'));
			}
		}
		elseif($res["type"] == "location_user")
		{
			if($_SESSION['id_type']=='WH'){
				exit(header('Location:'.$root.'/wh/home2.php'));
			}
			else{
				exit(header('Location:'.$root.'/asp/home2.php'));
			}
		}
		else
		{
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> "Unable to recognize user type!" ];
			exit(header('Location:'.$root.'/index.php'));
			//exit(header('Location:'.$root));
		}

	}
	else
	{
        $_SESSION["logres"] = [ "status"=>"failed", "msg"=> $res["msg"] ];
		exit(header('Location:'.$root.'/index.php'));
		//exit(header('Location:'.$root));
	}
}
else
{
	//exit(header('Location:'.$root.'/index.php'));
	exit(header('Location:'.$root.'/index.php'));
}
?>