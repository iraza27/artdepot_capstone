<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if(isset($_POST['save-aboutme'])){
		require('db-connection.php');
		session_start();
		$stmt = $conn->prepare("SELECT userID FROM aboutme WHERE userID = ?");
		$stmt->bind_param("i", $_SESSION['userID']);
		$stmt->execute();
		$stmt->store_result();
		$row_count = $stmt->num_rows;

		// assign variables

		$myIntro = $_POST['about-me-text'];
		$blogID = $_POST['blogID'];

		if($row_count > 0){
			//Update Record
			$stmtUpdate = $conn->prepare("UPDATE aboutme SET myintroduction = ? WHERE id = ?");
			$stmtUpdate->bind_param('i', $blogID);
			$stmtUpdate->execute();

			$stmtUpdate->close();
			$stmt->close();
			header("Location: ../artist.php?user=".$_SESSION['userName']);
			exit();

		}else{
			//Insert new record
			$stmtInsert = $conn->prepare("INSERT INTO aboutme (userID, myintroduction) VALUES (?,?)");
			$stmtInsert->bind_param('is', $_SESSION['userID'], $myIntro);
			$stmtInsert->execute();

			$stmtInsert->close();
			$stmt->close();
			$conn->close();
			header("Location: ../artist.php?user=".$_SESSION['userName']);
			exit();
		}
	}


?>