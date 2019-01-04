<?php

	//Send a report
	if(isset($_POST['report'])){
		session_start();
		$reportUser = $_SESSION['userName'];
		date_default_timezone_set('America/Toronto');
		$reportDate = date('Y-m-d H:i:s');
		$reportArtworkID = $_POST['r_artID'];
		$reportText = $_POST['report'];
		$reportResolved = 0;

		require('db-connection.php');

		//Get ArtistID
		$stmt = $conn->prepare("SELECT userID FROM artwork WHERE artID = ?");
		$stmt->bind_param("i", $reportArtworkID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($userID);
		if($stmt->fetch()){
			$reportArtistID = $userID;
		}

		$stmt->close();

		$stmtInsert = $conn->prepare("INSERT INTO reports (reportCreatedUser, reportCreatedDate, reportedArtistID, reportedArtworkID, reportReason, reportResolved) VALUES (?,?,?,?,?,?)");
		$stmtInsert->bind_param("ssiisi", $reportUser, $reportDate, $reportArtistID, $reportArtworkID, $reportText, $reportResolved);
		$stmtInsert->execute();

		echo '<div id="report-sent">-- Report has been sent! --</div>';
	
		$stmtInsert->close();
		
	}

?>