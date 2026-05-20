<?php
include("MPDF/mpdf.php");
include('MPDF/classes/barcode.php');

include "invoice_pdf.php";


$message="To".$locationname."\n";
$message.="Respected Sir\n";
$message.="Please find PO attached. You are requested to process the same\n";
$message.="\n";

$message.="With Best Regards\n";
$message.="For VELO Services Pvt Ltd";




        require 'PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->setFrom('doNotReply@candoursoft.com', 'Velo CRM');
        $mail->addAddress('chandra000shikhar@gmail.com', 'Shikhar');
        $mail->Subject = 'PHPMailer file sender';
        $mail->Body = $message;
        // Attach the uploaded file
        $mail->addAttachment($name, 'Invoice');
        if (!$mail->send()) {
            $msg .= "Mailer Error: " . $mail->ErrorInfo;
            echo "send";
        } else {
            $msg .= "Message sent!";
            echo $msg;
        }
   
?>
