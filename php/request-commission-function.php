<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	// Art Request Function

	if(isset($_POST['racbtn'])){
		//Form sent via submit button
		session_start();
		require("db-connection.php");
		$racPrice = $_POST['racprice'];
		$requestDescription = $_POST['racdescription'];
		$requestArtistID = $_POST['racuserID'];
		$reqUserID = $_SESSION['userID'];
		$commStatus = 'negotiations';
		$commISPaid = 0;
		date_default_timezone_set('America/Toronto');
		$reqCommDate = date('Y-m-d H:i:s');
		$stmt = $conn->prepare("INSERT INTO commissions (commRequestDate, commRequestUserID, commSuggPrice, commDescription, commStatus, commISPaid, commArtistID) VALUES (?,?,?,?,?,?,?)");
		$stmt->bind_param("siissii", $reqCommDate, $reqUserID, $racPrice, $requestDescription, $commStatus, $commISPaid, $requestArtistID);
		$stmt->execute();
		$stmt->close();

		//Get Artist Page user name for return param
		$stmt = $conn->prepare("SELECT userName FROM users WHERE userID = ?");
		$stmt->bind_param("i", $requestArtistID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($artistUserName);
		if($stmt->fetch()){	
			$artistUserName = $artistUserName;
		}
			$stmt->close();
			header("Location: ../artist.php?user=".$artistUserName);
			exit();	
	}

?>