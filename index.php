<?php require('header.php'); ?>
<style>
	#home-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<!-- Header -->
<section class="body-content">
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1){
		echo 	'<div class="post-announcement">
					<form id="blog_post_form" method="POST" action="php/post-blog.php">
						<input class="announcement-text-title" type="text" name="announcement-title" placeholder="Title of this post" required />
						<textarea class="announcement-text-body" name="announcement-body" placeholder="Type something here...." required></textarea>
						<button class="announcement-post-btn button is-success" name="post" type="submit">Post</button>
					</form>
				</div>';
	}

	require_once('php/db-connection.php');

	if($result = $conn->query("SELECT a.a_date AS date, a.a_text AS text, a.a_title AS title, u.userName AS user FROM announcements a LEFT JOIN users u ON (u.userID = a.userID) ORDER BY date DESC")){
		while($row = $result->fetch_assoc()){
			echo 	'<div class="announcements">
						<h2 class="announcement-title">'.$row["title"].'</h2>
						<div class="announcement-info">
							<span>By: <span style="color:red";>'.$row["user"].'</span> on '.$row["date"].'</span>
						</div>
						<div class="announcement-text">'.$row["text"].'</div>
					</div>';
		}
	}
	


?>

	<!-- Test format for announcements -->

</section>

<!-- Footer -->
<?php require('footer.php'); ?>
