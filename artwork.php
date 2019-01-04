<?php require('header.php'); ?>
<!-- Albums -->
<style>
	#artwork-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<!-- Header -->
<section class="body-content">
	<div class="artwork-container">
	<?php
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		require("php/db-connection.php");
		$error_message = "";

		if(isset($_SESSION['error_message'])){
			$error_message = $_SESSION['error_message'];
			unset($_SESSION['error_message']);
		}


		//Check if username exists in DB
		$sql = ("SELECT userID, userName FROM users WHERE userName = ?");
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $_GET['user']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($uid, $uname);
		$row_count = $stmt->num_rows;

		if($row_count > 0){
			if($stmt->fetch()){
				echo '<h1>'.$uname.'</h1>';
			}
			echo '<div class="album-overview">';

			//Check Album database

			$sql = ("SELECT albumID, albumName, permissionLevel, albumCoverColor FROM albums WHERE userID = ?");
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $uid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($albumID, $albumName, $permissionLevel, $albumCoverColor);
			while($stmt->fetch()){
				switch ($permissionLevel) {
					case 'public':
						echo '<div class="album-object"><a class="album-object-inner" style="background-color:'.$albumCoverColor.';" href="gallery.php?user='.$uname.'&album='.$albumName.'&albumID='.$albumID.'"><h4>'.$albumName.'</h4></a>';

						if(isset($_SESSION['userName']) && ($_SESSION['userName']) === $_GET['user'] || isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin']) === 1){
										echo '<input type="hidden" class="hiddenAlbumID" value="'.$albumID.'" /><button class="check_permissions_btn">Set Permissions</button>';
									}

						echo '</div>';
						break;
					case 'users':
						if(isset($_SESSION['userName'])){
							echo '<div class="album-object"><a style="background-color:'.$albumCoverColor.';" class="album-object-inner" href="gallery.php?user='.$uname.'&album='.$albumName.'&albumID='.$albumID.'"><h4>'.$albumName.'</h4></a> ';

							if(isset($_SESSION['userName']) && ($_SESSION['userName']) === $_GET['user'] || isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin']) === 1){
										echo '<input type="hidden" class="hiddenAlbumID" value="'.$albumID.'" /><button class="check_permissions_btn">Set Permissions</button>';
									}

							echo '</div>';							
						}
						break;
					case 'private':
						if(isset($_SESSION['userName']) && ($_SESSION['userName']) === $_GET['user'] || isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin']) === 1){
							echo '<div class="album-object"><a style="background-color:'.$albumCoverColor.';" class="album-object-inner" href="gallery.php?user='.$uname.'&album='.$albumName.'&albumID='.$albumID.'"><h4>'.$albumName.'</h4></a>';

							if(isset($_SESSION['userName']) && ($_SESSION['userName']) === $_GET['user'] || isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin']) === 1){
									echo 	'<input type="hidden" class="hiddenAlbumID" value="'.$albumID.'" /><button class="check_permissions_btn">Set Permissions</button>';
									}
							echo '</div>';								
						}
						break;
				}
			}
			// Check if the user is the owner of the page
				if(isset($_SESSION['userName']) && ($_SESSION['userName']) === $_GET['user'] || isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin']) === 1){

					echo '<a id="album-create"><h4>Create New Album</h4></a>';
					//Modals
					//Create New Album
					echo '<div class="modal" id="create-album-modal">
					  <div class="modal-background"></div>
					  <div class="modal-card">
					    <header class="modal-card-head">
					      <p class="modal-card-title">Create Album</p>
					      <button class="delete" aria-label="close"></button>
					    </header>
					    <form method="POST" action="php/create-album.php">
						    <section class="modal-card-body">
						    	<div class="create-album-modal-fields">';
						    		if (!empty($error_message)){
										echo 	'<div id="register-error-messages">
													<span class="error_message">'.$error_message.'</span>
												</div>';
									}
										echo '<ol>
											<li>
												<label for="frm-field-album-name">Album Name:</label>
												<input id="frm-field-album-name" name="album_name" class="input" type="text" placeholder="Give your album a name..." required />
											</li>
											<li>
												<label for="frm-field-album-name">Permission Level:</label>
												<select name="permission_level" class="select is-normal" id="camf-30h-select">
													<option value="public" selected>Public</option>
													<option value="users">Users</option>
													<option value="private">Only Me</option>
												</select>
											</li>
											<li>
												<label for="frm-field-album-name">Album Colour:</label>
												<input name="album_color" value="#ff00ff" type="color"/>
											</li>
										</ol>				
								</div>
								<button type="submit" name="create-album-submit" class="button is-success">Create</button>
						    </section>
						</form>
					  </div>
					</div>';


					//Set Permissions
				}
			echo '</div>';		
		}
			//User exists
		
	?>
	</div>

	<div class="modal" id="permission_modal">
	  <div class="modal-background"></div>
	  <div class="modal-card" id="permission_modal_card">
	    <header class="modal-card-head">
	      <p class="modal-card-title">Set Permission</p>
	      <button class="delete" aria-label="close"></button>
	    </header>
	    <section class="modal-card-body">
	    	<form method="POST" action="php/album_permissions.php">
	    		<div id="permission_control_group">
				</div>
	      		<button type="submit" name="update_permissions" class="button is-success">Set Permission</button>
	      	</form>
	    </section>
	  </div>
</div>
</section>
<script>
	$(document).ready(function(){
		$("#album-create").click(function(){
			$('#create-album-modal').addClass('is-active');
		});

		$(".delete").click(function(){
			$(this).parent().parent().parent().removeClass('is-active');
		});


		//AJAX

		$(".check_permissions_btn").click(function(){
			//AJAX to get permission  levels
			var albumID = $(this).siblings(".hiddenAlbumID").val();

			$.ajax({
				url: "php/album_permissions.php",
				method: "POST",
				data: ({albumID:albumID, check_permissions:'true'}),
				success: function(data){
					$("#permission_control_group").html(data);
				}
			});

			$('#permission_modal').addClass('is-active');
		});

	});
</script>
<!-- Footer -->
<?php require('footer.php'); ?>