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
	$to_loc_name = "Vikas Singh";
	$to_loc_email = "crmcare@candoursoft.com";
	
	//while($to_location_info = mysqli_fetch_array($to_location_qr)){		
		if($to_loc_email!=""){
			
			
			
			$message="<html><table>";
			$message.="<tr><td>To ".$to_loc_name.",</td></tr>";
			$message.="<tr><td>Dear Sir/Mam,</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>You are now connected with our CRM.</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>We hereby inform you that your login credentials are given below.</td></tr>";
			$message.="<tr><td> URL : http://rv.cancrm.in/ </td></tr>";
			$message.="<tr><td> USER ID : VIKAS </td></tr>";
			$message.="<tr><td> PASSWORD : 12345 </td></tr><tr><td> </td></tr>";
			$message.="<tr><td>With Best Regards,</td></tr>";
			$message.="<tr><td>RV Solutions Pvt Ltd</td></tr>";
			$message.="</table></html>";
						
		//echo $message;
		
			//require_once ("includes/PHPMailerAutoload.php");
			require_once ("includes/class.phpmailer.php");
			$mail = new PHPMailer;
			$mail->setFrom('doNotReply@candoursoft.com', 'RV Solutions Pvt Ltd');
			$mail->addAddress($to_loc_email, $to_loc_name);
			$mail->addCC('chandra000shikhar@gmail.com', 'Vikas Singh');
			$mail->Subject = 'Login credentials';
			$mail->Body = $message;
			
			if (!$mail->send()) {
				$msg .= "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$msg .= "Message sent!";
			}
		}
	//}///// end of loop
	///////////////////////////////////////////////////
	header("location:http://rv.cancrm.in/MailerClass/mail_page_test.php?msg=".$msg);
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
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
