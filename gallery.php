<?php require('header.php'); ?>
<!-- Header -->
<style>
	#artwork-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<section class="body-content">
	<div class="gallery-container">
		<?php
			echo '<nav class="nav-breadcrumb breadcrumb is-centered" aria-label="breadcrumbs">
			  <ul>
			    <li><a href="artist.php?user='.$_GET['user'].'">'.$_GET['user'].'</a></li>
			    <li><a href="artwork.php?user='.$_GET['user'].'">Albums</a></li>
			    <li class="is-active"><a aria-current="page">'.$_GET['album'].'</a></li>
			  </ul>
			</nav>';

			// Check if Album exists
			require('php/db-connection.php');

			$stmt = $conn->prepare("SELECT albumID, albumName, permissionLevel FROM albums WHERE albumName = ? AND albumID = ?");

			$stmt->bind_param("si", $_GET['album'], $_GET['albumID']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($albumID, $albumName, $permissionLevel);

			$row_count = $stmt->num_rows;
			$access_granted = false;
			
			if($row_count > 0){
				//Album Name and ID exist in DB
				if($stmt->fetch()){
					switch ($permissionLevel) {
						case 'public':
							$access_granted = true;
							break;
						case 'users':
							if(isset($_SESSION['userName'])){
								$access_granted = true;
							}
							break;
						case 'private':
							if(isset($_SESSION['userName']) && $_SESSION['userName'] === $_GET['user']){
								$access_granted = true;
							}
							break;
					}

					if($access_granted){
						echo '<div class="gallery-header">';
						echo '<h1>'.$albumName.'</h1>';
						if(isset($_SESSION['userName']) && $_SESSION['userName'] === $_GET['user']){
							echo '<button class="button is-success" id="open-upload-modal-btn">Upload</button>';
							echo 	'<div class="modal" id="upload-img-modal">
									  <div class="modal-background"></div>
									  <div class="modal-card">
									    <section class="modal-card-body">
									    	<form method="POST" action="php/upload-artwork.php" enctype="multipart/form-data">
										    <div class="field is-horizontal">
											  <div class="field-label is-normal">
											    <label class="label">Artwork Name:</label>
											  </div>
											  <div class="field-body">
											    <div class="field">
											      <div class="control">
											        <input class="input" name="name" type="text" placeholder="Example..">
											      </div>
											    </div>
											  </div>
											</div>
											<div class="field is-horizontal">
											  <div class="field-label is-normal">
											    <label class="label">Artwork Description:</label>
											  </div>
											  <div class="field-body">
											    <div class="field">
											      <div class="control">
											        <textarea class="textarea" name="description" placeholder="What is this artwork about?"></textarea>
											      </div>
											    </div>
											  </div>
											</div>
											<div class="field is-horizontal">
											  <div class="field-label is-normal">
											    <label class="label">File:</label>
											  </div>
											  <div class="field-body">
											    <div class="field">
											      <div class="control">
											        <input type="file" name="myart" />
											      </div>
											    </div>
											  </div>
											</div>
											<div class="field is-horizontal">
											  <div class="field-label is-normal">
											    <label class="label">Type:</label>
											  </div>
											  <div class="field-body">
											    <div class="field">
											      <div class="control">
											            <div class="select">
													      <select name="type">
													        <option value="painting">Painting</option>
													        <option value="digital">Digital</option>
													      </select>
													    </div>
											      </div>
											    </div>
											  </div>
											</div>
											<input type="hidden" name="albumID" id="albumHiddenID" value="'.$albumID.'" />
											<button type="button" class="button is-danger is-outlined" id="close-modal">Cancel</button>
									       	<button type="submit" class="button is-success" name="upload-btn">Upload</button>
									       </form>
									    </section>
									  </div>
									</div>';
						}
						echo '<input type="hidden" name="albumID" id="albumHiddenIDOpen" value="'.$albumID.'" />';
						echo '</div>';

					}
				}			
			}

		?>
		<div class="sortby_selector">
			<label for="img_sort_select">Sort By:</label>
			<select class="select" id="img_sort_select">
				<option selected value="mv">Most Views</option>
				<option value="lv">Least Views</option>
				<option value="az">A-Z</option>
				<option value="za">Z-A</option>
			</select>
		</div>
		<div id="gallery-list"></div>
	</div>
</section>
<!-- Footer -->
<script>
	$(document).ready(function(e){

		$("#close-modal").click(function(){
			$('#upload-img-modal').removeClass('is-active');
		});
	
		$("#open-upload-modal-btn").click(function(){
			$('#upload-img-modal').addClass('is-active');
		});

		var hiddenID = $("#albumHiddenIDOpen").val();
		var sort = $("#img_sort_select").val();
		//On Page Load
		$.ajax({
			url: "php/get-artwork-list.php",
			method: "POST",
			data: ({img_sort:sort, albumID: hiddenID}),
			success: function(data){
				$("#gallery-list").html(data);
			}
		});

		//Dropdown Search

		$("#img_sort_select").change(function(){
			var sort = $(this).val();
			$.ajax({
				url: "php/get-artwork-list.php",
				method: "POST",
				data: ({img_sort:sort, albumID: hiddenID}),
				success: function(data){
					$("#gallery-list").html(data);
				}
			});
		});
	});
</script>			
<?php require('footer.php'); ?>