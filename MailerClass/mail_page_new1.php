<?php
$db_user = 'rvsolution_user';
$db_pass = 'zkL4d1!4';
$db_host = 'localhost';
$db = "rvsolution_crm";
$link1 = mysqli_connect($db_host, $db_user, $db_pass,$db);
if (!$link1) {
    echo "Error in connecting DB: " . mysqli_connect_error();
	exit();
}

/*			##############################	TIME Diffrence US to INDIA		####################*/
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
/*			##############################	TIME Diffrence US to INDIA		####################*/

if ($_POST['mail_send']=='SEND'){	
	/////////////// Send Mail to ASC //////////////////
	$to_location_qr = mysqli_query($link1, "SELECT * FROM location_master WHERE 1 ");
	
	while($to_location_info = mysqli_fetch_array($to_location_qr)){		
		if($to_location_info['emailid']!=""){
		
			$toemail =$to_location_info['emailid'].",".'jasoriya.manish@gmail.com'.",".'jitugupta20121989@gmail.com'."";
			$urll = "http://rv.cancrm.in/";
			
			$message="<table>";
			$message.="<tr><td>To ".$to_location_info['locationname'].",</td></tr>";
			$message.="<tr><td>Dear Sir/Mam,</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>You are now connected with our CRM.</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>We hereby inform you that your login credentials are given below.</td></tr>";
			$message.="<tr><td> URL : ".$urll." </td></tr>";
			$message.="<tr><td> USER ID : ".$to_location_info['location_code']." </td></tr>";
			$message.="<tr><td> PASSWORD : ".$to_location_info['pwd']." </td></tr><tr><td> </td></tr>";
			$message.="<tr><td>With Best Regards,</td></tr>";
			$message.="<tr><td>RV Solutions Pvt Ltd</td></tr>";
			$message.="</table>";
				
			
			// Always set content-type when sending HTML email
			$headers1 = "MIME-Version: 1.0\r\n";
			$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers1 .= "From:doNotReply@digicare.com". "\r\n";
			$subject = "CRM login credentials";
			$data = mail($toemail,$subject,$message ,$headers1);
			if($data){	
				mysqli_query($link1, "update location_master set mailsend = 'Y' where location_code = '".$to_location_info['location_code']."' ");
			} 
			
		}
	}///// end of loop
	///////////////////////////////////////////////////
	header("location:mail_page_new1.php?msg=".$msg);
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=WINDOWS-1252" />
<title>Mail Sender</title>
</head>

<body>
<form id="frm1" name="frm1" action="" method="POST">
	
	<input type="submit" name="mail_send" id="mail_send" value="SEND" title="SEND">
</form>
</body>
</html>
