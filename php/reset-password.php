<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	//Start Session
	session_start();
	// -- -- Create Session Variables for userid, username, email, secret answer, secret question. Make sure variables are unique and not already being used on the website.
	// Create session for specific phases of password test. pwd_reset_phase = 0, 1, 2

	// -- -- Reset password logic

	// IF username and email submit button was pressed.
	if(isset($_POST['pwd-rest-ue-btn'])){
		$uname = $_POST['username'];
		$uemail =  $_POST['email'];
		//Check if username and email field are filled out.
		if(isset($uname) && !empty($uname) && isset($uemail) && !empty($uemail)){
			//IF true -  Check database for email and username matches.
			require('db-connection.php');
			$stmtEmail = $conn->prepare("SELECT secretQuestion, secretAnswer, userID from users WHERE email = ? AND username = ?");
			$stmtEmail->bind_param("ss", $uemail, $uname);
			$stmtEmail->execute();
			$stmtEmail->store_result();
			$stmtEmail->bind_result($secretQuestion, $secretAnswer, $resetPass_userID);

			$row_count = $stmtEmail->num_rows;
			if($row_count > 0){
				// If true - return secret question and secret answer.
				if($stmtEmail->fetch()){
					$_SESSION['pwd_reset_phase'] = 1;
					$_SESSION['reset_pwd_userID'] = $resetPass_userID;
					$_SESSION['secretQuestion'] = $secretQuestion;
					$_SESSION['secretAnswer'] = $secretAnswer;
					//close DB connection
					$stmtEmail->close();
					$conn->close();

					//Return to sign up
					header("Location: ../sign-in.php");
					exit();
				}

			}else{
				$_SESSION['error_message_reset'] = 'Email or Username provided does not exist in our database. Please confirm username and email are correct. Create a new account using those credentials';
				header("Location: ../sign-in.php");
				exit();	
			}
		}else{
			// return error message
			$_SESSION['error_message_reset'] = 'Please fill out both username and email';
			header("Location: ../sign-in.php");
			exit();
		}
	}

	// If check secret answer button was set
	if(isset($_POST['pwd-reset-sa-btn'])){
		if(isset($_POST['secret_answer']) && !empty($_POST['secret_answer'])){
			// Compare submitted secret answer with secret answer provided in DB
			if($_POST['secret_answer'] == $_SESSION['secretAnswer']){
				//IF true - Ask user to input new password and confirmed password.
				$_SESSION['pwd_reset_phase'] = 2;
				header("Location: ../sign-in.php");
				exit();				
			}else{
				// return error message
				$_SESSION['error_message_reset'] = 'Your secret answer is not correct';
				header("Location: ../sign-in.php");
				exit();
			}
		}else{
			// return error message
			$_SESSION['error_message_reset'] = 'Please fill out your secret answer';
			header("Location: ../sign-in.php");
			exit();			
		}
	}

	// IF newpassword button is set
	if(isset($_POST['pwd-reset-np-btn'])){
		//Compare password and confirm password match
		if($_POST['password'] == $_POST['confirm_password']){
			// if true - hash new password and Update database table on userID
				$password1 = $_POST['password'];
				$uid = $_SESSION['reset_pwd_userID'];

				require('db-connection.php');
				$new_hashed_password = password_hash($password1, PASSWORD_DEFAULT);
				$stmt_password = $conn->prepare("UPDATE users SET passwordHashed = ? WHERE userID = ?");
				$stmt_password->bind_param("si", $new_hashed_password, $uid);
				$stmt_password->execute();

				// unset session variables and return session confirmation information
				unset($_SESSION['reset_pwd_userID']);
				unset($_SESSION['secretQuestion']);
				unset($_SESSION['secretAnswer']);
				
				// return success message
				$_SESSION['success_message_reset'] = 'Password has been changed! Please login with your new password';
				header("Location: ../sign-in.php");
				exit();	
		}else{
			// return error message
			$_SESSION['error_message_reset'] = 'Passwords do not match. Please make sure password and confirm password are the same';
			header("Location: ../sign-in.php");
			exit();			
		}
	}

?>