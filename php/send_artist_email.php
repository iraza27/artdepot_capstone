<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	//Send artist an email

	if(isset($_POST['sendemailbtn'])){
		session_start();
		require("db-connection.php");

		$myID = $_SESSION['userID'];
		$artistID = $_POST['email_artist_id'];
		$email_msg = $_POST['email_message'];

		//Get basic account information from sender
		$stmt = $conn->prepare("SELECT userName, email FROM users WHERE userID = ?");
		$stmt->bind_param("i", $myID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($myUserName, $myEmail);
		if($stmt->fetch()){	
			$myUserName = $myUserName;
			$myEmail = $myEmail;
		}
		$stmt->close();

		//Get info from Artist
		$stmt = $conn->prepare("SELECT userName, email FROM users WHERE userID = ?");
		$stmt->bind_param("i", $artistID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($artistUserName, $artistEmail);
		if($stmt->fetch()){	
			$artistUserName = $artistUserName;
			$artistEmail = $artistEmail;
		}

		$stmt->close();


		//PHP Mailer 

		require_once("PHPMailer/src/Exception.php");
		require_once("PHPMailer/src/PHPMailer.php");
		require ('PHPMailer/src/SMTP.php');

		$mail = new PHPMailer(true);                            
		try {
			//Server settings
			$mail->SMTPDebug = 2;
			$mail->SMTPOptions = array(
				'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
				)
			);             
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  					  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'test27iraza@gmail.com';            // SMTP username
			$mail->Password = 'irazaDummyPass';                   // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 587;                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('admin@fakeemail.com', 'Admin');
			$mail->addAddress($artistEmail, $artistUserName);     // Add a recipient

			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Email sent by'.$myUserName.' from ARTDEPOT';
			$mail->Body    = 'This email was sent by ARTDEPOTs automated email service.<br>Username: '.$myUserName.'<br>User Email:'.$myEmail.'<br>Message: '.$email_msg;

			$mail->send();
			$_SESSION['success_message'] = "Email with temporary password has been sent!";
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}

		header("Location: ../artist.php?user=".$artistUserName);
		exit();	
	}

?>