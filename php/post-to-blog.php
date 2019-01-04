<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);	
	//Artist blogs - Post to the blog table
	if(isset($_POST['post-to-blog-btn'])){
		session_start();
		if(isset($_POST['blog-title']) && !empty($_POST['blog-title']) && isset($_POST['blog-text']) && !empty($_POST['blog-text'])){
			require('db-connection.php');
			$blogTitle = $_POST['blog-title'];
			$blogText = $_POST['blog-text'];
			$uID = $_SESSION['userID'];
			date_default_timezone_set('America/Toronto');
			$aDate = date('Y-m-d H:i:s');
			$stmt = $conn->prepare("INSERT INTO blogs (blogDate, blogText, userID, blogTitle) VALUES (?,?,?,?)");
			$stmt->bind_param('ssis', $aDate, $blogText, $uID, $blogTitle);
			$stmt->execute();

			header("Location: ../artist.php?user=".$_SESSION['userName']);
			exit();
		}else{
			header("Location: ../artist.php?user=".$_SESSION['userName']);
			exit();
		}
	}
	

?>