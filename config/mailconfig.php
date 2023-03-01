<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug = 0;
$mail->Host = ""; //SMTP server ip address or hostname
$mail->Port = 587; //SMTP port
$mail->SMTPSecure = "tls"; //Encryption type
$mail->isHTML(true);  
$mail->CharSet = "UTF-8";

$mail->Username = ''; //address - name@domain.tld
$mail->Password = ''; //SMTP account password
$mail->setFrom('', 'Support'); //Sender email address, sender name

?>