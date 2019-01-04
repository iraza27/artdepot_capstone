<?php require('header.php'); ?>
<style>
	#artist-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<!-- Header -->
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	require('php/db-connection.php');
	// Check who the artist of the page is
	$pageArtist = $_GET['user'];
	// search the database for this artist
	$stmt = $conn->prepare("SELECT u.userName,  u.userProfilePicture, am.myintroduction,am.id, u.userID FROM users u LEFT JOIN aboutme am  ON (u.userID = am.userID) WHERE u.userName  = ?");
	$stmt->bind_param('s', $pageArtist);

	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($uname, $userProfilePicture, $myIntroduction, $amID ,$userID);
	$row_count = $stmt->num_rows;

?>
<section class="body-content">
	<?php 

		if($row_count > 0){
			// fetch user profile and about me 
			if($stmt->fetch()){
				//if found - assign variables for all the data
				$username = $uname;
				$profile_pic_name = $userProfilePicture;
				$myIntro = $myIntroduction;
				$uID = $userID;
				$aboutmeID = $amID;
				$profile_pic_path =  "user_profiles/".$uname."/".$userProfilePicture;
				$stmt->close();

				echo 	'<div class="user-page-header">
							<img class="user-profile-picture" src="'.$profile_pic_path.'"/>
						</div>
						<div class="about-me-container">
							<h1>'.ucwords($uname).'</h1>
							<form method="POST" action="php/update-about-me.php">
								<textarea class="about-me-info" name="about-me-text"';

						//Add readonly if user is not the owner of the page
						if(!isset($_SESSION['userName']) || $_SESSION['userName'] != $pageArtist){
								echo 'readonly';
						}

				echo '>'.$myIntro.'</textarea><input type="text" name="blogID" hidden value="'.$aboutmeID.'" />';
				
				if(isset($_SESSION['userName']) && $_SESSION['userName'] == $pageArtist){
					echo 	'<div>
								<button type="submit" name="save-aboutme" class="button is-success">
									<span>Save Changes</span>
									<span class="icon is-small"><i class="fas fa-check"></i></span>
								</button>
							</div>';
				}else{
					echo '<div>';

					if(isset($_SESSION['userName'])){
						echo	'<a class="button is-success" id="request-comm-btn"><span class="icon"><i class="far fa-bell"></i></span><span>Request Art Commission</span></a>
							<a href="artwork.php?user='.$_GET['user'].'" class="button is-info"><span>View Artwork</span></a>
							<a class="button is-primary" id="send-email-modal-btn"><span class="icon"><i class="far fa-envelope"></i></span><span>Email Artist</span></a>	';
					}else{
						echo '<a href="artwork.php?user='.$_GET['user'].'" class="button is-info"><span>View Artwork</span></a>';
					}
					echo '</div>';
				}

				echo '</form></div><div class="blog-posts-container">';
				// Check if user is Signed in
				if(isset($_SESSION['userName']) && $_SESSION['userName'] == $pageArtist){
					echo 	'<button class="button is-large is-link create-post-btn" id="create-blog-btn">
			  					<span>Create Post</span>
			  					<span class="icon is-small">
				      				<i class="fas fa-pen"></i>
				    			</span>
			  				</button>';
				}

				if($result = $conn->query("SELECT blogID, blogDate, blogTitle, blogText FROM blogs WHERE userID = $uID ORDER BY blogDate DESC")){
					while($row = $result->fetch_assoc()){
						// fetch blog posts
						echo 	'<div class="blog-post">
									<form method="POST" action="php/blog-control.php">
										<input type="text" name="blogTitle" class="readonly blog-post-title input is-large is-fullwidth" value="'.$row["blogTitle"].'"/>
										<span class="blog-post-date">'.$row["blogDate"].'</span>
										<textarea class="textarea readonly blog-post-body" name="blogText">'.$row["blogText"].'</textarea>
										<input type="text" name="blogID" value="'.$row["blogID"].'" hidden />';

						if(isset($_SESSION['userName']) && $_SESSION['userName'] == $pageArtist){
									echo 	'<button type="submit" class="button is-danger post-delete is-outlined" name="post_delete">
											<span class="icon is-small">
											    <i class="fas fa-times"></i>
											</span>
											<span>Delete</span>
										</button>
										<button type="button" class="button post-edit">Edit</button>
										<button type="submit" class="button is-success post-save" name="post_save">Save</button>';
						}
									echo '</form></div>';
					}
				}
				// bug that caused the buttons not to display correctly if this block was not positioned below blog-post
				if(isset($_SESSION['userName']) && $_SESSION['userName'] == $pageArtist){

						echo 	'</div>
								<div class="modal" id="postblog-modal">
								  <div class="modal-background"></div>
								  <div class="modal-card">
								    <header class="modal-card-head">
								      <p class="modal-card-title">Whats on your mind?</p>
								      <button class="delete" id="modal-close" aria-label="close"></button>
								    </header>
								    <form method="POST" id="post-to-blog-form" action="php/post-to-blog.php">
									    <section class="modal-card-body">
									    	<input type="text" class="input is-large" name="blog-title" placeholder="Blog Title" require />
									     	<textarea class="textarea create-post-text-body" name="blog-text" require placeholder="Type Something....."></textarea>
									    </section>
									    <footer class="modal-card-foot">
									      <button type="submit" name="post-to-blog-btn" class="button is-success is-large is-fullwidth">Post</button>
									    </footer>
								    </form>
								  </div>
								</div>';
					}
					if(isset($_SESSION['userName'])){
								echo '<div class="modal" id="request-art-comm-modal">
							  <div class="modal-background"></div>
							  <div class="modal-card">
							    <header class="modal-card-head">
							      <p class="modal-card-title">Request Art Commission</p>
							      <button class="delete close-request-art-comm-modal" aria-label="close"></button>
							    </header>
							    <section class="modal-card-body">
							      <form method="POST" action="php/request-commission-function.php" id="racform">
							      		<label>Suggested Price <i class="fas fa-dollar-sign"></i></label><input type="text" class="input" placeholder="0.00" name="racprice" required />
							      		<h2>Art Description</h2>
							      		<textarea class="textarea" placeholder="Enter Description" rows="10" maxlength="1000" name="racdescription" required></textarea>
							      		<input type="hidden" name="racuserID" value="'.$uID.'" />
									    </section>
									    <footer class="modal-card-foot">
									      <button type="submit" class="button is-success btn-center" name="racbtn">Request Art Commission</button>
									    </footer>
							    	</form>
							  </div>
							</div>';
							echo '<div class="modal" id="email-artist-modal">
							  <div class="modal-background"></div>
							  <div class="modal-card">
							    <header class="modal-card-head">
							      <p class="modal-card-title">Send Email</p>
							      <button class="delete close-email-artist-modal" aria-label="close"></button>
							    </header>
							    <section class="modal-card-body">
							      <form method="POST" action="php/send_artist_email.php" id="emailform">
							      		<textarea class="textarea" placeholder="Enter your message" rows="10" maxlength="1000" name="email_message" required></textarea>
							      		<input type="hidden" name="email_artist_id" value="'.$uID.'" />
									    </section>
									    <footer class="modal-card-foot">
									      <button type="submit" class="button is-success btn-center" name="sendemailbtn">Send Email</button>
									    </footer>
							    	</form>
							  </div>
							</div>';
					}
			}
		}else{
			echo 'User does not exist';
		}
		$conn->close();
	?>
</section>		
<!-- Footer -->
<script>
	$(document).ready(function(){
		$(".blog-post-title").prop('readonly', true);
		$(".post-save").hide();
		$(".blog-post-body").prop('readonly', true);

		//edit button click
		$(".post-edit").click(function(){
			$(this).siblings(".post-save").show();
			$(this).parent().parent().find(".blog-post-title").prop('readonly', false);
			$(this).parent().parent().find(".blog-post-body").prop('readonly', false);
			$(this).parent().parent().find(".blog-post-title").removeClass('readonly');
			$(this).parent().parent().find(".blog-post-body").removeClass('readonly');
		});

		$("#modal-close").click(function(){
			$('#postblog-modal').removeClass('is-active');
			$('#post-to-blog-form')[0].reset();
		});
	
		$("#create-blog-btn").click(function(){
			$('#postblog-modal').addClass('is-active');
		});
			
		//Request Art Commission button

		$("#request-comm-btn").click(function(){
			$("#request-art-comm-modal").addClass("is-active");
		});

		$(".close-request-art-comm-modal").click(function(){
			$("#request-art-comm-modal").removeClass("is-active");
			$('#racform')[0].reset();
		});
		$("#send-email-modal-btn").click(function(){
			$("#email-artist-modal").addClass("is-active");
		});
		$(".close-email-artist-modal").click(function(){
			$("#email-artist-modal").removeClass("is-active");
			$('#emailform')[0].reset();
		});
		$("#emailform").validate();
		$("#racform").validate({
			rules:{
				racprice: {
					required: true,
					number: true
				},
				racdescription:{
					required: true,
					maxlength: 1000
				}
			}
		});

	
	});
</script>
<?php 
	// $stmt->close();
	// $conn->close();
	require('footer.php');
?>