<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
$applicationemail='cccapp@cccbeautysupplies.com';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'Mailer/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer();

    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;           // لتفاصيل الأخطاء (يمكن تغييره لـ 0 بعد التأكد)
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';       // خادم SMTP الخاص بـ Hostinger
    $mail->SMTPAuth   = true;                        // تفعيل المصادقة
    $mail->Username   = 'cccapp@cccbeautysupplies.com';     // بريدك كامل
    $mail->Password   = 'Maher@141168';        // استبدلها بـ App Password إذا كان الحساب محمي
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS على المنفذ 587
    $mail->Port       = 587;   
// Content
$mail->isHTML(true);  
$mail->CharSet = "UTF-8";
?>