<?php
if ($_POST['mail_send']=='SEND'){	
	
	/////////////// Send Mail to ASC //////////////////
	$to_location_email = getAnyDetails($row['to_location'],"locationname,emailid","location_code","location_master",$link1);
	$to_loc_info = explode("~",$to_location_email);
	
	$location_email = getAnyDetails($row['from_location'],"locationname,emailid","location_code","location_master",$link1);
	$loc_info = explode("~",$location_email);
	
	if($loc_info[1]!=""){
		$message="To ".$loc_info[0]."\n";
		$message.="Respected Sir/Mam\n";
		$message.="This courier( ".$po_no." ) is successfully recieved by L4.\n";
		$message.="\n";
		$message.="\n";
		################################ Design mail page ################################################################
		$message.="
					<div style='width:100%;'>
					
						<div>	
							<div style='text-align:center;font-size: 18px;'> <u> Challan's Detail </u> <div>
							<div class='panel-group'>
								<div class='panel-body'>
									<table class='table table-bordered' width='100%'>
										<tbody>
											<tr>
												<td width='20%'><label class='control-label'>Challan No: </label></td>
												<td width='30%'>".$row['challan_no']."</td>
												<td width='20%'><label class='control-label'>Entry Date:</label></td>
												<td width='30%'>".$row['challan_date']."</td>
											</tr>
											<tr>
												<td width='20%'><label class='control-label'>From Location: </label></td>
												<td width='30%'>".$loc_info[0]."</td>
												<td width='20%'><label class='control-label'>To Location:</label></td>
												<td width='30%'>".$to_loc_info[0]."</td>
											</tr>
											<tr>
												<td width='20%'><label class='control-label'>From Address: </label></td>
												<td width='30%'>".$row['from_address']."</td>
												<td width='20%'><label class='control-label'>To Address:</label></td>
												<td width='30%'>".$row['to_address']."</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					";
					
		$message.="\n";
		$message.="\n";
		
		$message.="
					<div style='width:100%;'>
		
						<div>	
							<div style='text-align:center;font-size: 18px;'> <u> Item's Information </u> <div>
							<div class='panel-group'>
								<div class='panel-body'>
									<table class='table table-bordered' width='100%'>
										<tbody>
											<tr>
												<td><label class='control-label'> S.No. </label></td>
												<td><label class='control-label'> Model </label></td>
												<td><label class='control-label'> Partcode | Description </label></td>
												<td><label class='control-label'> Rec. Qty </label></td>
												<td><label class='control-label'> Rec. Type </label></td>
											</tr>
					";	
					
					$sno=0;
					$res=mysqli_query($link1,"select model_id,part_id,qty,rec_type from sfr_transaction where challan_no='".$_REQUEST['challan_no']."' ");
					while($row_loc=mysqli_fetch_array($res)){ 
					$sno=$sno+1;  
									
					
		$message.="								
											<tr>
												<td>".$sno."</td>
												<td>".getAnyDetails($row_loc['model_id'],"model","model_id","model_master",$link1)."</td>
												<td>".getAnyDetails($row_loc['part_id'],"part_name","partcode","partcode_master",$link1)."</td>
												<td>".$row_loc['qty']."</td>
												<td>".$row_lic['rec_type']."</td>
											</tr>
					";	
					
					} /////////  end loop //////////////
					
		$message.="					
										</tbody>
									</table>
								</div>
							</div>
						</div>
						
					</div>
		
				";
		
		$message.="\n";
		
		#####################################################################################################
		$message.="With Best Regards\n";
		$message.="Vishwash Pvt Ltd";
	
		require_once ("../includes/PHPMailerAutoload.php");
		$mail = new PHPMailer;
		$mail->setFrom('doNotReply@candoursoft.com', 'Vishwash CRM');
		$mail->addAddress($loc_info[1], 'Location');
		$mail->Subject = 'Confirmation';
		$mail->Body = $message;
		
		if (!$mail->send()) {
			$msg .= "Mailer Error: " . $mail->ErrorInfo;
		} else {
			$msg .= "Message sent!";
		}
	}
	///////////////////////////////////////////////////
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Untitled Document</title>
</head>

<body>
<form id="frm1" name="frm1" action="" method="POST">
	<input type="submit" name="mail_send" id="mail_send" value="SEND" title="SEND">
</form>
</body>
</html>
