<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();
	//Uploading Artwork into DB

	if(isset($_POST['upload-btn'])){
		//if submitted

		$name = $_POST['name'];
		$description = $_POST['description'];
		$type = $_POST['type'];
		$aID = $_POST['albumID'];

		//Appends a random character string infront of the image name. This is to avoid duplicate images from overwritting each other in the directory.
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 10; $i++) {
		    $randstring .= $characters[rand(0, strlen($characters))];
		}

		//Get album folder location
		require('db-connection.php');

		$stmt = $conn->prepare('SELECT albumName, albumFolderLocation, permissionLevel FROM albums WHERE albumID = ?');
		$stmt->bind_param("i", $aID);
		$stmt->execute();

		$stmt->store_result();
		$stmt->bind_result($aName, $location, $permissionLevel);

		if($stmt->fetch()){
			if(isset($_FILES['myart'])){

				//--Save image to directory--
				$tmpLocation = $_FILES['myart']['tmp_name'];
				$img_name = $_FILES['myart']['name'];
				$img_name = $randstring.'_'.$img_name;

				//Image location in the directory 
				$fullPath = "../user_profiles/".$location."/".$img_name;  //Save to Directory
				$savedPath = $location."/".$img_name; //Inserted into DB

				//Move image to directory location from temp
				move_uploaded_file($tmpLocation, $fullPath);

				list($imgWidth, $imgHeight) = getimagesize($fullPath);
				
				date_default_timezone_set('America/Toronto');
				$artUploadDate = date('Y-m-d H:i:s');

				//Starting view count
				$vc = 0;

				//Insert into the artwork table
				$stmtInsert = $conn->prepare('INSERT INTO artwork (artDate, artName, artFileLocation, userID, albumID, artViewCount, artDescription, permissionLevel, artWidth, artHeight, artType) VALUES (?,?,?,?,?,?,?,?,?,?,?)');

				$stmtInsert->bind_param('sssiiissiis', $artUploadDate, $name, $savedPath, $_SESSION['userID'], $aID, $vc, $description, $permissionLevel, $imgWidth, $imgHeight, $type);
				$stmtInsert->execute();
				$stmtInsert->close();
				header("Location: ../gallery.php?user=".$_SESSION['userName'].'&album='.$aName.'&albumID='.$aID);
				exit();
			}
		}

	}
?>