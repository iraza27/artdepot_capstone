<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// change password logic
	if(isset($_POST['change-password'])){
		require_once('db-connection.php');
		session_start();
		//form fields
		$fields = array('oldpassword', 'newpassword1', 'newpassword2');
		$is_errors = false;

		// check if fields are empty
		foreach($fields AS $field){
			if(!isset($_POST[$field]) || empty($_POST[$field])){
				$_SESSION['error_message'] = $field.' is empty';
				header("Location: ../change-password.php");
				exit();
				$is_errors = true;
			}
		}
		if(!$is_errors){
			$oldpassword = $_POST['oldpassword'];
			$password1 = $_POST['newpassword1'];
			$password2 = $_POST['newpassword2'];		
			$uid = $_SESSION['userID'];

			//Check password 1 and password 2 match
			if($password1 == $password2){
				//Check password in database matches password provided
				$stmt = $conn->prepare("SELECT passwordHashed FROM users WHERE userID = ?");
				$stmt->bind_param('i', $uid);

				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($hashedPassword);
				if($stmt->fetch()){
					//Compare password with hashpassword
					if(password_verify($oldpassword, $hashedPassword) > 0){
						$new_hashed_password = password_hash($password1, PASSWORD_DEFAULT);

						$stmt_password = $conn->prepare("UPDATE users SET passwordHashed = ? WHERE userID = ?");
						$stmt_password->bind_param("si", $new_hashed_password, $uid);
						$stmt_password->execute();

						$_SESSION['success_message'] = 'Password has been changed successfully!';
						$stmt->close();
						$stmt_password->close();
						$conn->close();
						header("Location: ../change-password.php");
						exit();
					}else{
						$_SESSION['error_message'] = "Password entered is incorrect";
						header("Location: ../change-password.php");
						exit();
					}
				}
			}else{
				// Passwords do not match provide error message
				$_SESSION['error_message'] = 'Passwords do not match. Make sure passwords for Password and Confirm Password are the same';
				header("Location: ../change-password.php");
				exit();
			}
	 	}
	}
	
?>