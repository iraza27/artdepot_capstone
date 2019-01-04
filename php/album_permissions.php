<?php
	
	//This script does two things. Based off which button is set.
	// 1. Get current permission level
	// 2. Set new permission level


	if(isset($_POST['check_permissions'])){

		require('db-connection.php');
		$albumID = $_POST['albumID'];

		$sql = ("SELECT permissionLevel FROM albums WHERE albumID = ?");
		$stmt = $conn->prepare($sql);

		$stmt->bind_param("i", $albumID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($permission);

		if($stmt->fetch()){
			switch ($permission) {
				case 'public':
					echo '<input type="hidden" name="hiddenAlbumID" value="'.$albumID.'" />
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="public" checked>Public</label>
					</div>
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="users">Users</label>
					</div>
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="private">Only Me</label>
					</div>';
					break;			
				case 'users':
					echo '<input type="hidden" name="hiddenAlbumID" value="'.$albumID.'" />
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="public">Public</label>
					</div>
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="users" checked>Users</label>
					</div>
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="private">Only Me</label>
					</div>';
					break;
				case 'private':
					echo '<input type="hidden" name="hiddenAlbumID" value="'.$albumID.'" />
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="public">Public</label>
					</div>
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="users">Users</label>
					</div>
					<div class="control">
					  <label class="radio"><input type="radio" name="permission_level" value="private" checked>Only Me</label>
					</div>';
					break;
			}
		}

		$stmt->close();
		$conn->close();
	}elseif(isset($_POST['update_permissions'])){
		session_start();

		$pl = $_POST['permission_level'];
		$id = $_POST['hiddenAlbumID'];
		$userName = $_SESSION['userName'];
		require('db-connection.php');

		$stmt = $conn->prepare("UPDATE albums SET permissionLevel = ? WHERE albumID = ?");
		$stmt->bind_param('si', $pl, $id);
		$stmt->execute();
		$stmt->close();
		$conn->close();		
		header("Location: ../artwork.php?user=".$userName);
		exit();
	}

?>