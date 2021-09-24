<?php

$config = include 'mailConfig.php';

extract($config);

$notifications = array();
$error = false;


if ( !isset($_POST['name']) || empty($_POST['name']) ) {
	$error = true;
	$notifications['nameMissed'] = 'You didn`t write "name"';
	$notifications['status'] = 'error';
}

if ( !isset($_POST['email']) || empty($_POST['email']) || ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || ! preg_match('/\w{3,30}@\w{1,20}\.\w{2,20}/i', $_POST['email'] )  ) {
	$error = true;
	$notifications['emailMissed'] = 'You didn`t write "email"';
	$notifications['status'] = 'error';
}

if ( !isset($_POST['comment']) || empty($_POST['comment']) ) {
	$error = true;
	$notifications['commentMissed'] = 'You didn`t write "comment"';
	$notifications['status'] = 'error';
}



if (!$error) {
	require_once 'class.phpmailer.php';
	require_once 'class.smtp.php';
	
	$mail = new PHPMailer;

	$message = '
	<html>
		<head>
			<title>Your Site Contact Form</title>
		</head>
		<body>
			<h3>Name: <span style="font-weight: normal;">' . $_POST['name'] . '</span></h3>
			<h3>Email: <span style="font-weight: normal;">' . $_POST['email'] . '</span></h3>
			<div>
				<h3 style="margin-bottom: 5px;">Comment:</h3>
				<div>' . $_POST['comment'] . '</div>
			</div>
		</body>
	</html>';

	$mail->isSMTP();
	$mail->Host = $SmtpHost;
	$mail->SMTPAuth = true;
	$mail->Username = $SmtpUser;
	$mail->Password = $SmtpPass;
	$mail->SMTPSecure = $SmtpSecure;
	$mail->Port = $SmtpPort;

	$mail->setFrom(filter_var( $_POST['email'] ) ) ;
	$mail->addAddress($to);
	$mail->isHTML(true);

	$mail->Subject = $subject;
	$mail->Body    = $message;


	if(!$mail->send()) {
		$notifications['errorSend'] = $mail->ErrorInfo;
		$notifications['status'] = 'error';
	} else {
		$notifications['successSend'] = 'Your email was send';
		$notifications['status'] = 'success';
	}
} 
	
echo json_encode($notifications);
