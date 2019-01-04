<?php require('header.php'); ?>
<!-- Header -->

<!-- Specfic Images -->
<style>
	#userlist-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<section class="body-content">
	<div class="user-list-container">
		<?php //Add condition so only admin can see this page
			if($_SESSION['isAdmin'] === 1){
				$error_message = "";
				$success_message = "";
				if(isset($_SESSION['error_message'])){
					$error_message = $_SESSION['error_message'];
					unset($_SESSION['error_message']);
				}
				if(isset($_SESSION['success_message'])){
					$success_message = $_SESSION['success_message'];
					unset($_SESSION['success_message']);
				}
				require("php/db-connection.php");

				$stmt = $conn->prepare("SELECT userID, userName, accountStatus FROM users WHERE isAdmin = 0");
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($userID, $userName, $accountStatus);
				if (!empty($error_message) || !empty($success_message)){
					echo '		<div id="register-error-messages">
									<span class="error_message">'.$error_message.'</span>
									<span class="success_message">'.$success_message.'</span>
								</div>';
				}

				echo '<table id="user-list-table" class="table is-fullwidth">
						<tr>
							<th>Status</th>
							<th>Account Name</th>
							<th></th>
						</tr>';

				while($stmt->fetch()){
					if($accountStatus === 'Active'){
						echo '<tr><td><button class="button is-danger btn-deactivate">Deactivate</button></td>';
					}else{
						echo '<tr class="user-deactive"><td><button class="button is-primary btn-activate">Activate</button></td>';
					}
								
					echo 	'<td>'.$userName.'<input class="hiddenUserIDSet" type="hidden" value="'.$userID.'" /></td>
							<td><button class="button btn-change-pass">Reset Password</button></td>
						</tr>';
				}
				echo '</table>';
				//Deactivate Modal
				echo '<div class="modal" id="change-status-deactivate-modal">
					<div class="modal-background"></div>
					<div class="modal-card">
						<header class="modal-card-head">
							<p class="modal-card-title">Are you sure you want to Deactivate this account?</p>
						</header>
						<section class="modal-card-body">
							<form method="POST" action="php/user-list-functions.php">
								<input type="hidden" name="list_function" value="deactivate" />
								<input type="hidden" id="hiddenUserIDGetDeactive" name="uID" />
								<div class="comment-delete-confirmation-btn-group">
									<button type="submit" class="button is-danger is-medium" id="confirm-deactivate-btn">Yes</button><button id="close-deactivate-btn" class="button is-medium">No</button>
								</div>
							</form>
						</section>
					</div>
				</div>';

				// Activate Modal
				echo '<div class="modal" id="change-status-activate-modal">
					<div class="modal-background"></div>
					<div class="modal-card">
						<header class="modal-card-head">
							<p class="modal-card-title">Are you sure you want to Activate this account?</p>
						</header>
						<section class="modal-card-body">
							<form method="POST" action="php/user-list-functions.php">
								<input type="hidden" name="list_function" value="activate" />
								<input type="hidden" id="hiddenUserIDGetActive" name="uID" />
								<div class="comment-delete-confirmation-btn-group">
									<button type="submit" class="button is-primary is-medium" id="confirm-activate-btn">Yes</button><button id="close-activate-btn" class="button is-medium">No</button>
								</div>
							</form>
						</section>
					</div>
				</div>';

				//Change Email Modal
				echo '<div class="modal" id="change-password-modal">
					<div class="modal-background"></div>
					<div class="modal-card">
						<header class="modal-card-head">
							<p class="modal-card-title">Would you like to email the user a temporary password?</p>
						</header>
						<section class="modal-card-body">
							<form method="POST" action="php/user-list-functions.php">
								<input type="hidden" name="list_function" value="emailpass" />
								<input type="hidden" id="hiddenUserIDGetChangePass" name="uID" />
								<div class="comment-delete-confirmation-btn-group">
									<button type="submit" class="button is-info is-medium" id="confirm-change-pass-btn">Yes</button><button id="close-change-pass-btn" class="button is-medium">No</button>
								</div>
							</form>
						</section>
					</div>
				</div>';
			}
		?>
	</div>
</section>
<script>
	$(document).ready(function(e){
		//Activate Modal Controls
		$(".btn-activate").on("click", function(){
			$("#change-status-activate-modal").addClass("is-active");
			var userID = $(this).parent().siblings().find(".hiddenUserIDSet").val();
			$("#hiddenUserIDGetActive").val(userID);
		});
		$("#close-activate-btn").on("click", function(){
			$("#change-status-activate-modal").removeClass("is-active");
			$("#hiddenUserIDGetActive").val("");
		});

		//Deactivate Modal Controls
		$(".btn-deactivate").on("click", function(){
			$("#change-status-deactivate-modal").addClass("is-active");
			var userID = $(this).parent().siblings().find(".hiddenUserIDSet").val();
			$("#hiddenUserIDGetDeactive").val(userID);
		});
		$("#close-deactivate-btn").on("click", function(){
			$("#change-status-deactivate-modal").removeClass("is-active");
			$("#hiddenUserIDGetDeactive").val("");
		});

		//Email Modal Controls
		$(".btn-change-pass").on("click", function(){
			$("#change-password-modal").addClass("is-active");
			var userID = $(this).parent().siblings().find(".hiddenUserIDSet").val();
			$("#hiddenUserIDGetChangePass").val(userID);
		});
		$("#close-change-pass-btn").on("click", function(){
			$("#change-password-modal").removeClass("is-active");
			$("#hiddenUserIDGetChangePass").val("");
		});
	});
</script>
<!-- Footer -->
<?php require('footer.php'); ?>