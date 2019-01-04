<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	//Sign in page logic

	if(isset($_POST['login'])){
		session_start();
		require_once('db-connection.php');
		//form fields
		$fields = array('username', 'userpassword');
		$is_errors = false;

		// check if fields are empty
		foreach($fields AS $field){
			if(!isset($_POST[$field]) || empty($_POST[$field])){
				$_SESSION['error_message'] = $field.' is empty';
				header("Location: ../sign-in.php");
				exit();
				$is_errors = true;
			}
		}

		// if there are no errors
		if(!$is_errors){
			$userName = $_POST['username'];
			$userPassword = $_POST['userpassword'];

			$stmt = $conn->prepare("SELECT userID, userName, passwordHashed, isAdmin, accountStatus FROM users WHERE userName = ?");
			$stmt->bind_param('s', $userName);

			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($uid, $uname, $hashedPassword, $isAdmin, $accountStatus);
			$row_count = $stmt->num_rows;

			if($row_count > 0){
				//Account name exists in database

				if($stmt->fetch()){
					//Compare password with hashpassword
					if(password_verify($userPassword, $hashedPassword) > 0){
						// Returns bool 1 being true, 0 being false
						if ($accountStatus == 'Active') {
							//Checks if account is active
							$_SESSION['userID'] = $uid;
							$_SESSION['userName'] = $uname;
							$_SESSION['isAdmin'] = $isAdmin;
							header("Location: ../index.php");
							exit();
						}else{
							$_SESSION['error_message'] = "Sorry your Account has been Deactivated";
							header("Location: ../sign-in.php");
							exit();							
						}
					}else{
						$_SESSION['error_message'] = "Password entered is incorrect";
						header("Location: ../sign-in.php");
						exit();
					}
				}
			}else{
				$_SESSION['error_message'] = "Sorry we can't find an account with that name. Please register a new account";
				header("Location: ../sign-in.php");
				exit();
			}
		}
	}
?>