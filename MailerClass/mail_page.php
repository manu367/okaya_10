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

require_once ("includes/class.phpmailer.php");

if ($_POST['mail_send']=='SEND'){	
	/////////////// Send Mail to ASC //////////////////
	$to_location_qr = mysqli_query($link1, "SELECT * FROM location_master_111 WHERE 1 ");
	
	while($to_location_info = mysqli_fetch_array($to_location_qr)){		
		if($to_location_info['emailid']!=""){
		
		
			$message="To $to_location_info[locationname],<br>Dear Sir/Mam,<br><br>You are now connected with our CRM.<br>We hereby inform you that your login credentials are given below.<br>URL : http://rv.cancrm.in/<br>USER ID : $to_location_info[location_code]<br>PASSWORD : $to_location_info[pwd]<br><br>With Best Regards,<br>RV Solutions Pvt Ltd";
			
				
			
			$mail = new PHPMailer;
			$mail->setFrom('doNotReply@candoursoft.com', 'RV Solutions Pvt Ltd');
			$mail->addAddress($to_location_info['emailid'], $to_location_info['locationname']);
			//$mail->addCC('ajit.kumar.singh@rvsolutions.in', 'Ajit Kumar Singh');
			//$mail->addCC('crmcare@candoursoft.com', 'Vikas Singh');
			//$mail->addCC('singhvikas270@gmail.com', 'Vikas Singh');
			$mail->addCC('jitendra@candoursoft.com', 'Jitendra');
			$mail->Subject = 'CRM login credentials';
			$mail->Body = $message;
			
			if (!$mail->send()) {
				$msg .= "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$msg .= "Message sent!";
			}
			
		}
	}///// end of loop
	///////////////////////////////////////////////////
	//header("location:http://localhost/MailerClass/mail_page.php?msg=".$msg);
	//exit;
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
	<?php
		if($_GET['msg']!=""){
			echo "<br><br>".$_GET['msg']."<br><br>";
		}
	?>
	<input type="submit" name="mail_send" id="mail_send" value="SEND" title="SEND">
</form>
</body>
</html>

			
	