<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// OLD autoload hata diya
require 'PHPMailer/class.phpmailer.php';
require 'PHPMailer/class.smtp.php';

$mail = new PHPMailer(true);

// ================= SMTP CONFIG =================
$mail->isSMTP();
$mail->SMTPDebug  = 2; // testing ke baad 0 kar dena
$mail->Host       = 'starplus.cancrm.in';
$mail->SMTPAuth   = true;
$mail->Username   = 'info@starplus.cancrm.in';
$mail->Password   = '#h2z6k2G2';

$mail->SMTPSecure = 'ssl';
$mail->Port       = 465;

// Self-signed SSL ke liye (Plesk note ke hisaab se)
$mail->SMTPOptions = [
    'ssl' => [
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true,
    ],
];

// ================= MAIL CONTENT =================
$mail->setFrom('info@starplus.cancrm.in', 'CAN CRM');
$mail->addAddress($_GET['email']?:'pathakmanu174@gmail.com');

$mail->isHTML(true);
$mail->Subject = 'SMTP Test Mail – Plesk Server';
$mail->Body = '
    <h2>Hello Manu 👋</h2>
    <p>This mail is sent using <b>Plesk SMTP (SSL 465)</b>.</p>
    <p>No Gmail. No hacks. Pure server power.</p>
';

// ================= SEND =================
if (!$mail->send()) {
    echo "❌ Mail Error: " . $mail->ErrorInfo;
} else {
    echo "✅ Mail Sent Successfully!";
}
