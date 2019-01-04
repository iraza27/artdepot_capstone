<?php


	//Create  Album
	if(isset($_POST['create-album-submit'])){
		session_start();
		$fields = array('album_name', 'permission_level', 'album_color');
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
			$aName = $_POST['album_name'];
			$aPermission = $_POST['permission_level'];
			$aColor = $_POST['album_color'];
			$userName = $_SESSION['userName'];
			$userID = $_SESSION['userID'];

			$aNameNoSpace = str_replace(' ', '_', $aName);
			date_default_timezone_set('America/Toronto');
			$aDate = date('Y-m-d H:i:s');
			// get current directory and move up on level to access /user_profiles
			$curdir = getcwd()."/../user_profiles";

			$fullPath = $userName."/".$aNameNoSpace;
			//Create user directory
			mkdir($curdir."/".$userName."/".$aNameNoSpace, 0777);
			require('db-connection.php');

			$sql = ("INSERT INTO albums (albumName, albumDate, albumFolderLocation, userID, permissionLevel, albumCoverColor)
				 VALUES (?,?,?,?,?,?)");

			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sssiss", $aName, $aDate, $fullPath, $userID, $aPermission, $aColor);
			$stmt->execute();

			header("Location: ../artwork.php?user=".$userName);
			exit();
		}
		
	}

?>