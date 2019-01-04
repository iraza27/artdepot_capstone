<?php

	//Gets a list of artwork from a given album
	
	session_start();


	//Call artwork table - userID and AlbumID
	if(isset($_POST['albumID']) && isset($_POST['img_sort'])){
		require("db-connection.php");

		$sortby = $_POST['img_sort'];
		$albumID = $_POST['albumID'];

		switch ($sortby) {
			case 'mv':
				# Most Viewed - Default
				$stmt = $conn->prepare("SELECT a.artID, a.artName, a.artFileLocation, a.permissionLevel, a.userID, a.albumID, u.userName, a.artViewCount FROM artwork a LEFT JOIN users u ON (a.userID = u.userID) WHERE a.albumID = ? ORDER BY a.artViewCount DESC");
				break;
			case 'lv':
				# Least Viewed
				$stmt = $conn->prepare("SELECT a.artID, a.artName, a.artFileLocation, a.permissionLevel, a.userID, a.albumID, u.userName, a.artViewCount FROM artwork a LEFT JOIN users u ON (a.userID = u.userID) WHERE a.albumID = ? ORDER BY a.artViewCount ASC");
				break;
			case 'az':
				# Alphabet A-Z
				$stmt = $conn->prepare("SELECT a.artID, a.artName, a.artFileLocation, a.permissionLevel, a.userID, a.albumID, u.userName, a.artViewCount FROM artwork a LEFT JOIN users u ON (a.userID = u.userID) WHERE a.albumID = ? ORDER BY a.artName ASC");
				break;
			case 'za':
				# Alphabet Z-A
				$stmt = $conn->prepare("SELECT a.artID, a.artName, a.artFileLocation, a.permissionLevel, a.userID, a.albumID, u.userName, a.artViewCount FROM artwork a LEFT JOIN users u ON (a.userID = u.userID) WHERE a.albumID = ? ORDER BY a.artName DESC");
				break;
		}

		$stmt->bind_param("i", $albumID);
		$stmt->execute();
		$stmt->bind_result($artID, $artName, $artFileLocation, $permissionLevel, $userID, $albumID, $userName, $viewcount);
		while($stmt->fetch()){
			echo 	'<a class="gallery-image-container" href="gallery-image.php?user='.$userName.'&albumID='.$albumID.'&artwork='.$artName.'&artworkID='.$artID.'">
					<h2>'.$artName.'</h2>
					<div class="gallery-img-bg" style="background-image:url(\'user_profiles/'.$artFileLocation.'\')">
					<span class="view_overlay">View</span></div>
					<p>Views: '.$viewcount.'</p></a>';
		}
	
	}

?>
