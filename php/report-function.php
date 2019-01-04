<?php

	//report functions
	require("db-connection.php");
	if(isset($_POST['resolve'])){
		$reportID = $_POST['reportID'];

		//Resolve Report
		$stmt = $conn->prepare('UPDATE reports SET reportResolved = 1 WHERE reportID = ?');
		$stmt->bind_param("i", $reportID);
		$stmt->execute();
		$stmt->close();
	}elseif(isset($_POST['deactivate'])){
		$reportID = $_POST['reportID'];
		$artistID = $_POST['artistID'];

		//Resolve Report
		$stmt = $conn->prepare('UPDATE reports SET reportResolved = 1 WHERE reportID = ?');
		$stmt->bind_param("i", $reportID);
		$stmt->execute();
		$stmt->close();

		//Disable Account
		$stmt = $conn->prepare('UPDATE users SET accountStatus = "Deactivate" WHERE userID = ?');
		$stmt->bind_param("i", $artistID);
		$stmt->execute();
		$stmt->close();

	}

	header("Location: ../reports.php");
	exit();

?>