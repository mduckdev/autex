<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require(dirname(__FILE__) . "/" . "./dotenv.php");

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = $_ENV["SMTP_HOST"];
$mail->SMTPAuth = true;
$mail->Username = $_ENV["SMTP_USERNAME"];
$mail->Password = $_ENV["SMTP_PASSWORD"];
$mail->SMTPSecure = true;
$mail->Port = $_ENV["SMTP_PORT"];;
$mail->CharSet = 'UTF-8';
?>