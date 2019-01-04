<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	// Account Registering PHP Logic - Validates the information, checks if username already exists in database otherwise it will insert the data into the users table
	if(isset($_POST['register'])){
		session_start();
		require_once('db-connection.php');
		$formfields = array('firstname', 'lastname', 'city', 'province', 'username', 'emailaddress', 'password', 'confirmpassword', 'secretquestion', 'secretanswer');

		$is_errors = false;
		// check if fields are empty
		foreach($formfields AS $field){
			if(!isset($_POST[$field]) || empty($_POST[$field])){
				$_SESSION['error_message'] = $field.' is empty';
				header("Location: ../register-account.php");
				exit();
				$is_errors = true;
			}
		}		

		// when error is not true
		if(!$is_errors){
			// Assign form data to variables
			// personal info
			$firstName = ucwords($_POST['firstname']);
			$lastName = ucwords($_POST['lastname']);
			$city = ucwords($_POST['city']);
			$province = ucwords($_POST['province']);
			$country = ucwords($_POST['country']);

			// user info
			$userName = ucwords($_POST['username']);
			$userEmail = $_POST['emailaddress'];
			$password1 = $_POST['password'];
			$password2 = $_POST['confirmpassword'];

			//secret info
			$secretQuestion = $_POST['secretquestion'];
			$secretAnswer = $_POST['secretanswer'];

			// Check if email currently exists in database
			$stmtEmail = $conn->prepare("SELECT userID from users WHERE email = ?");
			$stmtEmail->bind_param("s", $userEmail);

			$stmtEmail->execute();
			$stmtEmail->store_result();
			$row_count = $stmtEmail->num_rows;

			if($row_count > 0){
				//Email exists in database
				$_SESSION['error_message'] = 'Email currently exists in database. Please select reset password, if you have forgotten your password';
				header("Location: ../register-account.php");
				exit();
			}else{
				//check if username currently exists in the database	
				$stmt = $conn->prepare("SELECT userID from users WHERE userName = ? ");
				$stmt->bind_param("s", $userName);
				$stmt->execute();
				$stmt->store_result();

				$row_count = $stmt->num_rows;

				if($row_count == 0){
					// check if password and confirm password match
					if($password1 != $password2){
						$_SESSION['error_message'] = 'Passwords do not match';
						header("Location: ../register-account.php");
						exit();
					}else{

						// get current directory and move up on level to access /user_profiles
						$curdir = getcwd()."/../user_profiles";

						//Create user directory
						mkdir($curdir."/".$userName, 0777);

						$profile_file_name = "";
						// Profile Picture Upload
						if(isset($_FILES['user_profile_pic'])){
							$tmpLocation = $_FILES['user_profile_pic']['tmp_name'];
							$profile_file_name = $_FILES['user_profile_pic']['name'];
							move_uploaded_file($tmpLocation, $curdir."/".$userName."/".$profile_file_name);
						}
						// Hash Password
						$hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

						//Insert data into database
						$stmt = $conn->prepare("INSERT INTO users (userName, email, passwordHashed, accountStatus, firstName, lastName, city, province, country, isAdmin, secretQuestion, secretAnswer, userProfilePicture) VALUES (?, ?, ?, 'Active', ?, ?, ?, ?, ?, 0, ?, ?, ?)");
						$stmt->bind_param("sssssssssss", $userName, $userEmail, $hashedPassword, $firstName, $lastName, $city, $province, $country, $secretQuestion, $secretAnswer, $profile_file_name);
						$stmt->execute();

						// Get id and username of new account and store them within a session. Transfer user to index.php
						$stmtUser = $conn->prepare("SELECT userID, userName,isAdmin FROM users WHERE userName = ?");
						$stmtUser->bind_param("s", $userName);
						$stmtUser->execute();
						$stmtUser->store_result();
						$stmtUser->bind_result($uid, $uname, $isAdmin);

						if($stmtUser->fetch()){
							$_SESSION['userID'] = $uid;
							$_SESSION['userName'] = $uname;
							$_SESSION['isAdmin'] = $isAdmin;

							//return
							header("Location: ../index.php");
							exit();
						}
					}						
				}else{
					// Account exists return to register form and add error message
					$_SESSION['error_message'] = 'Username currently exists in the database. Please select a new username';
					header("Location: ../register-account.php");
					exit();
				}
			}

			$stmt->close();
			$conn->close();
		}
	}
?>