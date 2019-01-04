<?php require('header.php'); ?>
<!-- Header -->

<!-- Specfic Images -->
<style>
	#artwork-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<section class="body-content">
	<div class="gallery-container">
		<?php

			$albumID = $_GET['albumID'];
			// Get Album Name
			require("php/db-connection.php");
			$stmt = $conn->prepare("SELECT albumName FROM albums WHERE albumID = ?");
			$stmt->bind_param("i", $albumID);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($albumName);
			$row_count = $stmt->num_rows;

			if($row_count > 0){
				if($stmt->fetch()){
					echo '<nav class="nav-breadcrumb breadcrumb is-centered" aria-label="breadcrumbs">
					  <ul>
					    <li><a href="artist.php?user='.$_GET['user'].'">'.$_GET['user'].'</a></li>
					    <li><a href="artwork.php?user='.$_GET['user'].'">Albums</a></li>
					    <li><a href="gallery.php?user='.$_GET['user'].'&album='.$albumName.'&albumID='.$albumID.'">'.$albumName.'</a></li>
					    <li class="is-active"><a aria-current="page">'.$_GET['artwork'].'</a></li>
					  </ul>
					</nav>';
					$artworkID = $_GET['artworkID'];
					if(isset($_GET['artworkID']) && is_numeric($_GET['artworkID'])){
						$stmtArtwork = $conn->prepare("SELECT DATE(artDate), artName, artFileLocation, artDescription, artWidth, artHeight, artType, userID FROM artwork WHERE artID = ?");
						$stmtArtwork->bind_param("i", $artworkID);
						$stmtArtwork->execute();
						$stmtArtwork->store_result();
						$stmtArtwork->bind_result($artDate, $artName, $AFL, $artDescription, $artWidth, $artHeight, $artType, $userID);
						$row_count_art = $stmtArtwork->num_rows;

						if($row_count_art > 0){
							if($stmtArtwork->fetch()){

								//Hidden IDs for the page
								echo '<input type="hidden" id="art-id-hidden-comment" value="'.$_GET['artworkID'].'"/>
										<input type="hidden" id="album-id-hidden-comment" value="'.$_GET['albumID'].'"/>';

								// Main HTML
								echo '<div class="artwork-theater-wrapper">
										<div id="theater-image">
											<img src="user_profiles/'.$AFL.'" />
										</div>
										<div id="theater-artwork-info">
											<div id="theater-artwork-info-header">
												<h4 class="title is-4">'.$artName.'</h4>
												<p>Upload Date: '.$artDate.'</p>
											</div>
											<div id="theater-artwork-info-body">
												<p>'.$artDescription.'</p>
											</div>
											<div id="theater-artwork-info-footer">
												<p>Type: '.$artType.'</p>
												<p>Dimensions: '.$artWidth.'x'.$artHeight.'</p>
											</div>
										</div>
										<div id="report-sent-location"></div>
										<div id="comment-section">
											<div id="comment-section-header">';

												if (isset($_SESSION["userName"])){
													echo '<button class="button is-danger" id="report-artwork">Report Artwork</button>
														 <h3 class="title is-3">Comments</h3>
														 <button class="button" id="add-comment">Add Comment</button>';
												}else{
													echo '<h3 class="title is-3">Comments</h3>';
												}
											
											echo '</div>
											<div id="comment-section-body"></div>
										</div>
									</div>
								</div>';

								//Update View Count if not viewed by owner of the page
								if($_SESSION['userID'] != $userID){
									$stmtUPDATE = $conn->prepare("UPDATE artwork SET artViewCount = artViewCount + 1 WHERE artID = ?");
									$stmtUPDATE->bind_param("i", $artworkID);
									$stmtUPDATE->execute();
								}

							}
						}else{
							echo 'Cannot Find an Artwork with that ID';							
						}											
					}else{
						echo 'Artwork ID is not valid';
					}

				}
			}else{
				echo 'No Album Found with that ID';
			}

			if(isset($_SESSION["userName"])){
				echo '	<!-- Add Comment Modal -->
						<div class="modal" id="add-comment-modal">
						  <div class="modal-background"></div>
						  <div class="modal-card">
						    <header class="modal-card-head">
						      <p class="modal-card-title">Add Comment</p>
						      <button class="delete" id="close-comment-modal" aria-label="close"></button>
						    </header>
						    <section class="modal-card-body">
								<textarea class="textarea" id="comment-text" placeholder="500 Character Limit" rows="10" maxlength="500"></textarea>
						    </section>
						    <footer class="modal-card-foot">
						      <button class="button is-success btn-center" id="add-comment-btn">Add Comment</button>
						    </footer>
						  </div>
						</div>
						<!-- Report Artwork Modal -->
					<div class="modal" id="report-artwork-modal">
					  <div class="modal-background"></div>
					  <div class="modal-card">
					    <header class="modal-card-head">
					      <p class="modal-card-title">Report Artwork</p>
					      <button class="delete" id="close-report-modal" aria-label="close"></button>
					    </header>
					    <section class="modal-card-body">
					      <textarea id="report-text" class="textarea" placeholder="Enter a reason for the report - 200 Character Limit" rows="5" maxlength="200"></textarea>
					    </section>
					    <footer class="modal-card-foot">
					      <button class="button is-danger btn-center" id="send-report-btn">Send Report</button>
					    </footer>
					  </div>
					</div>';


			}
		?>
</section>
<script>
	$(document).ready(function(e){
		var commentArtID = $("#art-id-hidden-comment").val();
		var commentAlbumID = $("#album-id-hidden-comment").val();
		//Load Comments
		$.ajax({
			url: "php/add-comment.php",
			method: "POST",
			data:({c_artID:commentArtID, c_albumID: commentAlbumID }) ,
			success: function(data){
				$("#comment-section-body").html(data);
			}
		});
		//Add Comment - button ajax
		$("#add-comment-btn").click(function(){
			var commentText = $("#comment-text").val();
			$("#add-comment-modal").removeClass("is-active");

			$.ajax({
				url: "php/add-comment.php",
				method: "POST",
				data: ({c_text:commentText, c_artID:commentArtID, c_albumID: commentAlbumID }),
				success: function(data){
					$("#comment-section-body").html(data);
					$("#comment-text").val("");
				}
			});
		});

		//Send Report - button ajax

		//Add Comment Modal Controls
		$("#add-comment").click(function(){
			$("#add-comment-modal").addClass("is-active");
		});
		$("#close-comment-modal").click(function(){
			$("#add-comment-modal").removeClass("is-active");
			$("#comment-text").val("");
		});
		//Report Artwork Modal Controls
		$("#report-artwork").click(function(){
			$("#report-artwork-modal").addClass("is-active");
		});
		$("#close-report-modal").click(function(){
			$("#report-artwork-modal").removeClass("is-active");
			$("#report-text").val("");
		});

		//Delete Comment Controls
		$('#comment-section-body').on('click', '.delete-comment', function() { 
			var commentID = $(this).siblings(".comment-id").val();
			$("#comment-delete-confirmation-modal").addClass("is-active");
			$("#comment-delete-confirmation-id").val(commentID);
		});
		//Confirm comment deletion
		$('#comment-section-body').on('click', '#confirm_comment_delete', function() { 
			var commentDeleteID = $("#comment-delete-confirmation-id").val();
			$.ajax({
				url: "php/add-comment.php",
				method: "POST",
				data: ({delete_comment: commentDeleteID, c_artID:commentArtID, c_albumID: commentAlbumID}),
				success: function(data){
					$("#comment-section-body").html(data);
				}
			});
			$("#comment-delete-confirmation-modal").removeClass("is-active");
		});

		//Close comment deletion modal
		$('#comment-section-body').on('click', '#close_comment_delete', function() { 
			$("#comment-delete-confirmation-modal").removeClass("is-active");	
		});

		//Send Report Button

		$("#send-report-btn").click(function(){
			var reportText = $("#report-text").val();
			$.ajax({
				url: "php/send_report.php",
				method: "POST",
				data: ({report: reportText, r_artID:commentArtID}),
				success: function(data){
					$("#report-sent-location").html(data);
					$("#report-text").val("");
					DelayHideReport();
				}
			});
			$("#report-artwork-modal").removeClass("is-active");			
		});

		//Hide report confirmation after 2s
		function DelayHideReport(){
			setTimeout(function() {
				$('#report-sent').fadeOut('slow');}, 2000);
		}
	});
</script>
<?php require('footer.php'); ?>
