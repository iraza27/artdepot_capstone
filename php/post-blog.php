<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	//Admin Blog posts

	if(isset($_POST['post'])){
		if(!empty($_POST['announcement-title']) && !empty($_POST['announcement-body'])){
			session_start();
			require_once('db-connection.php');

			$aTitle = $_POST['announcement-title'];
			$aBody = $_POST['announcement-body'];
			$uID = $_SESSION['userID'];
			date_default_timezone_set('America/Toronto');
			$aDate = date('Y-m-d H:i:s');
			$stmt = $conn->prepare("INSERT INTO announcements (a_date, a_text, userID, a_title) VALUES (?,?,?,?)");
			$stmt->bind_param('ssis', $aDate, $aBody, $uID, $aTitle);
			$stmt->execute();

			header("Location: ../index.php");
			exit();
		}
	}



?>