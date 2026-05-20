<?php


//include "pdf_page/invoice_pdf.php";

$message="To".$locationname."<br/>";
$message.="Respected Sir<br/>";
$message.="Please find PO attached. You are requested to process the same<br/>";
$message.="<br/>";
$message.="<br/>";
$message.="<br/>";
$message.="<br/>";
$message.="With Best Regards<br/>";
$message.="For VELO Services Pvt Ltd";




        require '../PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->setFrom('doNotReply@candoursoft.com', 'Velo CRM');
        $mail->addAddress('chandra000shikhar@gmail.com', 'SHIKHAR');
        $mail->Subject = 'PHPMailer file sender';
        $mail->Body = "hell0";
        // Attach the uploaded file
        $mail->addAttachment($name, 'My uploaded file');
        if (!$mail->send()) {
            $msg .= "Mailer Error: " . $mail->ErrorInfo;
        } else {
            $msg .= "Message sent!";
        }
   
?>
