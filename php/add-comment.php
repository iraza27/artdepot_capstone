<?php
	if(isset($_POST['c_artID']) && isset($_POST['c_albumID'])){
		session_start();
		$comment_artID = $_POST['c_artID'];
		$comment_albumID = $_POST['c_albumID'];

		require("db-connection.php");
		//Add a comment
		if(isset($_POST['c_text'])){
			$comment_text = $_POST['c_text'];
			$user_id = $_SESSION['userID'];
			date_default_timezone_set('America/Toronto');
			$comment_date = date('Y-m-d H:i:s');

			$stmt = $conn->prepare("INSERT INTO comments (commentDate, commentText, userID, albumID, artID) VALUES (?,?,?,?,?)");
			$stmt->bind_param("ssiii", $comment_date, $comment_text, $user_id, $comment_albumID, $comment_artID);
			$stmt->execute();

			$stmt->close();
		}
		
		if(isset($_POST['delete_comment'])){
			//Delete Comment
			$deleteCommentID = $_POST['delete_comment'];
			$stmtDelete = $conn->prepare("DELETE FROM comments WHERE commentID = ?");
			$stmtDelete->bind_param("i",$deleteCommentID);
			$stmtDelete->execute();

			$stmtDelete->close();
		}

		//Load Comments

		$stmt = $conn->prepare("SELECT c.commentID, date(c.commentDate), c.commentText, u.userName, u.isAdmin FROM comments c LEFT JOIN users u ON (c.userID = u.userID) WHERE albumID = ? AND artID = ?");
		$stmt->bind_param("ii", $comment_albumID, $comment_artID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($cID, $cDate, $cText, $uName, $isAdmin);

		//Get artwork Owner
		$stmtOwner = $conn->prepare("SELECT u.userName FROM artwork a LEFT JOIN users u ON a.userID = u.userID WHERE albumID = ? AND artID = ?");
		$stmtOwner->bind_param("ii", $comment_albumID, $comment_artID);
		$stmtOwner->execute();
		$stmtOwner->store_result();
		$stmtOwner->bind_result($ownerName);
		if($stmtOwner->fetch()){
			$artistName = $ownerName;
		}

		while($stmt->fetch()){
			echo '<div class="comment-block">
							<div class="comment-block-header">';
			if($isAdmin == 1){
				echo '<p><span class="admin-comment">'.$uName.'</span> - '.$cDate.'</p>';		
			}elseif ($uName === $artistName) {
				echo '<p><span class="owner-comment">'.$uName.'</span> - '.$cDate.'</p>';
			}elseif ($uName === $_SESSION['userName']){
				echo '<p><span class="my-comment">'.$uName.'</span> - '.$cDate.'</p>';
			}else{
				echo '<p>'.$uName.' - '.$cDate.'</p>';
			}

			if($_SESSION['userName'] === $artistName || $_SESSION['isAdmin'] == 1){
				echo 	'<a class="delete delete-comment">Delete</a>';
			}
			
				echo	'<input class="comment-id" type="hidden" value="'.$cID.'" />
					</div>
					<div class="comment-block-body">
						<p>'.$cText.'</p>
					</div>
				</div>';
		}

			if($_SESSION['userName'] === $artistName || $_SESSION['isAdmin'] == 1){
				echo 	'<div class="modal" id="comment-delete-confirmation-modal">
					  <div class="modal-background"></div>
					  <div class="modal-card">
					    <header class="modal-card-head">
					      <p class="modal-card-title">Delete Comment?</p>
					    </header>
					    <section class="modal-card-body">
					    	<input type="hidden" id="comment-delete-confirmation-id" />
					      	<div class="comment-delete-confirmation-btn-group">
					      		<button class="button is-danger is-medium" id="confirm_comment_delete">Yes</button><button id="close_comment_delete" class="button is-medium">No</button>
					      	</div>
					    </section>
					  </div>
					</div>';
			}
		$stmt->close();
		$stmtOwner->close();
	}
?>