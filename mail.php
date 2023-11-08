<?php
/* Needs to be done */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'path_to/PHPMailer/Exception.php';
require 'path_to/PHPMailer/PHPMailer.php';
require 'path_to/PHPMailer/SMTP.php';

require dirname(__FILE__)."/../vendor/autoload.php";

$mail = new PHPMailer(true);

try {
	// Server settings
	$mail->isSMTP();
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPAuth = true;
	$mail->Username = "timtester74@gmail.com";
	$mail->Password = "kwpf vhea voyu gxto";
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;

	// Recipients
	$mail->setFrom("noreply@readerswriters.com","Readers and Writers");
	$mail->addAddress("","");
	$mail->addReplyTo("","");
	
	// Content
	$mail->isHTML(true);
	$mail->Subject = "";
	$mail->Body = "";
	$mail->AltBody = "";

	$mail->send();
	echo 'Message has been sent.';
} catch (Exception $e) {
	echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
}

/*function send_mail_multiples($mail_list,  $body,  $subject = ""){
	$mail = new PHPMailer();		
	$mail->IsSMTP();
	$mail->Host       = "smtp.gmail.com";
	$mail->SMTPAuth   = true;
	$mail->Username   = "timtester74@gmail.com";
	$mail->Password   = "kwpf vhea voyu gxto";
	$mail->SMTPSecure = "tls";
	$mail->Port       = 587;
	$mail->SetFrom('noreply@readerswriters.com', 'Registration complete');
	$mail->AddAddress();
	$mail->Subject    = $subject;
	$mail->MsgHTML($body);
	$mails = explode(",", $mail_list);
	foreach($mails as $mailaddress){
		$mail->AddAddress($mailaddress, $mailaddress);
	}
	if(!$mail->Send()) {
	  return $mail->ErrorInfo;
	} else {
	  return TRUE;
	}
}*/

