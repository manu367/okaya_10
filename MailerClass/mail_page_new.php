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

//require_once ("includes/class.phpmailer.php");

if ($_POST['mail_send']=='SEND'){	
	/////////////// Send Mail to ASC //////////////////
	$to_location_qr = mysqli_query($link1, "SELECT * FROM location_master_111 WHERE 1 ");
	
	while($to_location_info = mysqli_fetch_array($to_location_qr)){		
		if($to_location_info['emailid']!=""){
		
		/*
			$message="To ".$to_location_info['locationname'].",<br>";
			$message.="Respected Sir/Mam,<br><br>";
			$message.="Location created successfully in RV Solutions.<br>";
			$message.="<br>";
			################################ Design mail page ################################################################
			$message.="
						<div style='width:100%;'>
							<div style='font-size: 18px;'> <u> <span  style ='font-weight:800;' >Location Details </u> <div><br>
							<table>
								<tbody>
								    <tr style='text-align:left;font-weight:800;'>
										<td style='width:20%;'><label> CRM Url : </label></td>
										<td style='width:20%;'><a href='http://rv.cancrm.in/'>http://rv.cancrm.in/</a></td>
									</tr>
									<tr style='text-align:left;font-weight:800;'>
										<td style='width:20%;'><label> User ID : </label></td>
										<td style='width:20%;'>".$to_location_info['location_code']."</td>
									</tr>
									<tr style='text-align:left;font-weight:800;'>
										<td style='width:20%;'><label> Password : </label></td>
										<td style='width:20%;'>".$to_location_info['pwd']."</td>
									</tr>
								</tbody>
							</table>
						</div>
						";
						
			$message.="<br>";
									
			#####################################################################################################
			$message.="<div style='text-align:left;' >With Best Regards,<br></div>";
			$message.="<div style='text-align:left;' >RV Solutions Pvt Ltd</div>";
			
			*/
			
			$message="<table>";
			$message.="<tr><td>To ".$to_location_info['locationname'].",</td></tr>";
			$message.="<tr><td>Dear Sir/Mam,</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>You are now connected with our CRM.</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>We hereby inform you that your login credentials are given below.</td></tr>";
			$message.="<tr><td> URL : http://rv.cancrm.in/ </td></tr>";
			$message.="<tr><td> USER ID : ".$to_location_info['location_code']." </td></tr>";
			$message.="<tr><td> PASSWORD : ".$to_location_info['pwd']." </td></tr><tr><td> </td></tr>";
			$message.="<tr><td>With Best Regards,</td></tr>";
			$message.="<tr><td>RV Solutions Pvt Ltd</td></tr>";
			$message.="</table>";
			//$message="To $to_location_info['locationname'],<br>Dear Sir/Mam,<br><br>You are now connected with our CRM.<br>We hereby inform you that your login credentials are given below.<br>URL : http://rv.cancrm.in/<br>USER ID : $to_location_info['location_code']<br>PASSWORD : $to_location_info['pwd']<br><br>With Best Regards,<br>RV Solutions Pvt Ltd";
			
			//echo $message;
			
			
			$to = $to_location_info['emailid'];
			$subject = "CRM login credentials";
			$txt = "Hello world!";
			$headers = "From: doNotReply@candoursoft.com" . "\r\n" .
			"CC: crmcare@candoursoft.com".
			"CC: singhvikas270@gmail.com";
			
			mail($to,$subject,$message,$headers);
		
			/*
			$mail = new PHPMailer;
			$mail->setFrom('doNotReply@candoursoft.com', 'RV Solutions Pvt Ltd');
			$mail->addAddress($to_location_info['emailid'], $to_location_info['locationname']);
			//$mail->addCC('ajit.kumar.singh@rvsolutions.in', 'Ajit Kumar Singh');
			$mail->addCC('crmcare@candoursoft.com', 'Vikas Singh');
			$mail->addCC('singhvikas270@gmail.com', 'Vikas Singh');
			//$mail->addCC('jitendra@candoursoft.com', 'Jitendra');
			$mail->Subject = 'CRM login credentials';
			$mail->Body = $message;
			
			if (!$mail->send()) {
				$msg .= "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$msg .= "Message sent!";
			}*/
			
		}
	}///// end of loop
	///////////////////////////////////////////////////
	header("location:http://localhost/MailerClass/mail_page.php?msg=".$msg);
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
	<?php
		if($_GET['msg']!=""){
			echo "<br><br>".$_GET['msg']."<br><br>";
		}
	?>
	<input type="submit" name="mail_send" id="mail_send" value="SEND" title="SEND">
</form>
</body>
</html>
