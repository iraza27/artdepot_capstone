<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if(isset($_POST['list_function'])){
		session_start();
		require('db-connection.php');
		$functionType = $_POST['list_function']; // Stores the type of function that needs to be called
		$uID = $_POST['uID'];
		switch ($functionType) {
			case 'activate':
				//Activate Account
				$stmt = $conn->prepare("UPDATE users SET accountStatus = 'Active' WHERE userID = ?");
				$stmt->bind_param("i", $uID);
				$stmt->execute();
				$stmt->close();	
				break;
			
			case 'deactivate':
				//Deactivate Account
				$stmt = $conn->prepare("UPDATE users SET accountStatus = 'Deactive' WHERE userID = ?");
				$stmt->bind_param("i", $uID);
				$stmt->execute();
				$stmt->close();	
				break;
			case 'emailpass':
				// Reset user password and email using php mailer

				//Random password string
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ{}|[]';
				$randstring = '';
				for ($i = 0; $i < 10; $i++) {
				    $randstring .= $characters[rand(0, strlen($characters))];
				}

				// Hash Password note: insert this instead of randstring after email is working
				$hashedPassword = password_hash($randstring, PASSWORD_DEFAULT);

				$stmt = $conn->prepare("UPDATE users SET passwordHashed = ? WHERE userID = ? ");
				$stmt->bind_param("si", $hashedPassword, $uID);
				$stmt->execute();
				$stmt->close();

				$stmtEmail = $conn->prepare("SELECT email, firstName FROM users WHERE userID = ?");
				$stmtEmail->bind_param("i", $uID);
				$stmtEmail->execute();

				$stmtEmail->store_result();
				$stmtEmail->bind_result($email, $fName);

				if($stmtEmail->fetch()){
					$uEmail = $email;
					$firstName = $fName;
				}

				//PHP Mailer 

				require_once("PHPMailer/src/Exception.php");
				require_once("PHPMailer/src/PHPMailer.php");
				require ('PHPMailer/src/SMTP.php');

				$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
				try {
				    //Server settings
				    $mail->SMTPDebug = 2;
				    $mail->SMTPOptions = array(
					    'ssl' => array(
					    'verify_peer' => false,
					    'verify_peer_name' => false,
					    'allow_self_signed' => true
					    )
					);                                 // Enable verbose debug output
				    $mail->isSMTP();                                      // Set mailer to use SMTP
				    $mail->Host = 'smtp.gmail.com';  					  // Specify main and backup SMTP servers
				    $mail->SMTPAuth = true;                               // Enable SMTP authentication
				    $mail->Username = 'test27iraza@gmail.com';            // SMTP username
				    $mail->Password = 'irazaDummyPass';                   // SMTP password
				    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
				    $mail->Port = 587;                                    // TCP port to connect to

				    //Recipients
				    $mail->setFrom('admin@fakeemail.com', 'Admin');
				    $mail->addAddress($uEmail, $firstName);     // Add a recipient

				    //Content
				    $mail->isHTML(true);                                  // Set email format to HTML
				    $mail->Subject = 'Temp Password for ArtDepot Account';
				    $mail->Body    = 'Hello, '.$firstName.'<br>Your temporary password is <b>'.$randstring.'</b>';

				    $mail->send();
				    $_SESSION['success_message'] = "Email with temporary password has been sent!";
				} catch (Exception $e) {
				    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
				}
				break;
		}
			header("Location: ../user-list.php");
			exit();	
	}



?>