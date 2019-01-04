<?php

	//Search and Load Artists for the Artist List page

	require("db-connection.php");
	if(isset($_POST['searched']) && !empty($_POST['searched'])){
		//When something is typed in the search box
		$search = preg_replace('/\s+/', '', $_POST['searched']);
		$searchlike = "%".$search."%";
		$sql = "SELECT userName, userProfilePicture FROM users WHERE accountStatus = 'Active' AND isAdmin = 0 AND userName LIKE ? ORDER BY userName DESC";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $searchlike);
		$stmt->execute();
		$stmt->bind_result($username, $img);
		echo '<h2>Search Result for: <span>'.$search.'</span></h2>';
		while($stmt->fetch()){
			echo	'<a href="artist.php?user='.$username.'" class="artist-list-container">
						<h4>'.$username.'</h4>
						<img src="user_profiles/'.$username.'/'.$img.'" />
					</a>';
		}
	}elseif(isset($_POST['searchbyletter'])){
		// When a letter is selected
		$letter = $_POST['searchbyletter'];
		$search = $letter."%";
		$sql = "SELECT userName, userProfilePicture FROM users WHERE accountStatus = 'Active' AND isAdmin = 0 AND userName LIKE ? ORDER BY userName DESC";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $search);
		$stmt->execute();
		$stmt->bind_result($username, $img);
		echo '<h2>Search Result for: <span>'.$letter.'</span></h2>';
		while($stmt->fetch()){
			echo	'<a href="artist.php?user='.$username.'" class="artist-list-container">
						<h4>'.$username.'</h4>
						<img src="user_profiles/'.$username.'/'.$img.'" />
					</a>';
		}
	}else{
		//Default behaviour
		$sql = "SELECT userName, userProfilePicture FROM users WHERE accountStatus = 'Active' AND isAdmin = 0 ORDER BY userName DESC";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($username, $img);
		while($stmt->fetch()){
			echo	'<a href="artist.php?user='.$username.'" class="artist-list-container">
						<h4>'.$username.'</h4>
						<img src="user_profiles/'.$username.'/'.$img.'" />
					</a>';
		}
	}
	mysqli_close($conn);

?>