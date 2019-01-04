<?php

	// Save changes from Change Account Information page
	
	if(isset($_POST['save-account-info'])){
		require_once('db-connection.php');
		session_start();
		$formfields = array('firstname', 'lastname', 'city', 'province', 'emailaddress', 'secretquestion', 'secretanswer');
		$is_errors = false;
		// check if fields are empty
		foreach($formfields AS $field){
			if(!isset($_POST[$field]) || empty($_POST[$field])){
				$_SESSION['error_message'] = $field.' is empty';
				header("Location: ../change-account-info.php");
				exit();
				$is_errors = true;
			}
		}

		if(!$is_errors){
			$firstName = $_POST['firstname'];
			$lastName = $_POST['lastname'];
			$city = $_POST['city'];
			$province = $_POST['province'];
			$country = $_POST['country'];
			// user info
			$userEmail = $_POST['emailaddress'];
			//secret info
			$secretQuestion = $_POST['secretquestion'];
			$secretAnswer = $_POST['secretanswer'];
			$userid = $_SESSION['userID'];

			// Profile Picture Upload
			if(isset($_FILES['user_profile_pic']['name']) && !empty($_FILES['user_profile_pic']['name'])){
				$userName = $_SESSION['userName'];
				$curdir = getcwd()."/../user_profiles";
				$tmpLocation = $_FILES['user_profile_pic']['tmp_name'];
				$profile_file_name = $_FILES['user_profile_pic']['name'];
				move_uploaded_file($tmpLocation, $curdir."/".$userName."/".$profile_file_name);

				//Update User Data
				$stmt = $conn->prepare("UPDATE users SET email = ?, firstName = ?, lastName = ?, city = ?, province = ?, country = ?, secretQuestion = ?, secretAnswer = ?, userProfilePicture = ? WHERE userID = ?");
				$stmt->bind_param("sssssssssi", $userEmail, $firstName, $lastName, $city, $province, $country, $secretQuestion, $secretAnswer, $profile_file_name, $userid);
				$stmt->execute();

			}else{
				//Update User Data
				$stmt = $conn->prepare("UPDATE users SET email = ?, firstName = ?, lastName = ?, city = ?, province = ?, country = ?, secretQuestion = ?, secretAnswer = ? WHERE userID = ?");
				$stmt->bind_param("ssssssssi", $userEmail, $firstName, $lastName, $city, $province, $country, $secretQuestion, $secretAnswer, $userid);
				$stmt->execute();
			}

			sleep(1);
			$_SESSION['success_message'] = 'Account Information has been saved!';
			$stmt->close();
			$conn->close();
			header("Location: ../change-account-info.php");
			exit();
		}
	}
?>