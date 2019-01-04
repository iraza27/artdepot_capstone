<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	session_start();


		$blogID = $_POST['blogID'];
		$blogTitle = $_POST['blogTitle'];
		$blogText = $_POST['blogText'];
		date_default_timezone_set('America/Toronto');
		$blogDate = date('Y-m-d H:i:s');

	if(isset($_POST['post_save'])){  //Update Blog Post
		//Artist Blog Controls -  Delete and Save

		require('db-connection.php');
		$stmt = $conn->prepare("UPDATE blogs SET blogTitle = ?, blogText = ?, blogDate = ? WHERE blogID = ?");
		$stmt->bind_param("sssi", $blogTitle, $blogText, $blogDate, $blogID);
		$stmt->execute();
		$stmt->close();
	}elseif(isset($_POST['post_delete'])){  //Delete Blog Post
		//Artist Blog Controls -  Delete and Save
		require('db-connection.php');
		$stmt = $conn->prepare("DELETE FROM blogs WHERE blogID = ?");
		$stmt->bind_param("i", $blogID);
		$stmt->execute();
		$stmt->close();
	}
	header("Location: ../artist.php?user=".$_SESSION['userName']);
	exit();
?>