<?php require('header.php'); ?>
<style>
	#sign-in-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$error_message = "";
	$error_message_reset = "";
	$success_message_reset = "";

	if(isset($_SESSION['error_message'])){
		$error_message = $_SESSION['error_message'];
		session_unset();
		session_destroy();
	}
	if(isset($_SESSION['error_message_reset'])){
		$error_message_reset = $_SESSION['error_message_reset'];
		unset($_SESSION['error_message_reset']);
	}
	if(isset($_SESSION['success_message_reset'])){
		$success_message_reset = $_SESSION['success_message_reset'];
		unset($_SESSION['success_message_reset']);
	}
	$pwd_reset_phase = 0;
	if(isset($_SESSION['pwd_reset_phase'])){
		$pwd_reset_phase = $_SESSION['pwd_reset_phase'];
	}

?>
<!-- Header -->
<section class="body-content">
	<div id="sign-in">
		<div id="register-error-messages">
			<span class="error_message"><?php echo $error_message; ?></span>
			<span class="error_message"><?php echo $error_message_reset; ?></span>
			<span class="success_message"><?php echo $success_message_reset; ?></span>
		</div>
		<form method="POST" action="php/login_users.php">
			<input type="text" name="username" class="sign-in-text" placeholder="Username"  required />
			<input type="password" name="userpassword" class="sign-in-text" placeholder="Password" required />
			<div id="sign-in-buttons">
				<button type="submit" id="login" name="login" class="sign-in-btns" >Login</button>
				<button type="button" onclick="ResetPassword();" id="reset-password" class="sign-in-btns">Reset Password</button>
				<a href="register-account.php" id="register-user" class="sign-in-btns">Register</a>
			</div>
		</form>
		<!-- Bulma.io - CSS -->
		<?php 
			if($pwd_reset_phase == 1){
				// Display secret question and ask for answer
				echo '<div class="modal is-active" id="confirm-account-modal">
					  	<div class="modal-background"></div>
						<div class="modal-card">
							<header class="modal-card-head">
						    	<p class="modal-card-title">Password Reset Wizard</p>
						      	<button class="delete" onclick="CloseModal();" aria-label="close"></button>
						    </header>
						    <form method="POST" action="php/reset-password.php">
								<section class="modal-card-body">
									<div class="modal-form">
									    <h3>Please provide your answer to your secret question</h3>
									    <table>
									      	<tr>
									      		<td class="modal-form-lbl">Question:  </td>
									      		<td><p> '.$_SESSION["secretQuestion"].'</p></td>
									      	</tr>
									      	<tr>
									      		<td class="modal-form-lbl">Answer:  </td>
									      		<td><input class="input is-medium" type="text" name="secret_answer" required /></td>
									      	</tr>
									      </table>
								    </div>
								</section>
								<footer class="modal-card-foot">
								    <button type="submit" class="button is-success" id="modal-confirm-center" name="pwd-reset-sa-btn">Confirm</button>
								</footer>
							</form>
						</div>
					</div>';
			}elseif($pwd_reset_phase == 2){
				// Ask for new password and password confirmation
				echo '<div class="modal is-active" id="confirm-account-modal">
					  	<div class="modal-background"></div>
						<div class="modal-card">
							<header class="modal-card-head">
						    	<p class="modal-card-title">Password Reset Wizard</p>
						      	<button class="delete" onclick="CloseModal();" aria-label="close"></button>
						    </header>
						    <form method="POST" action="php/reset-password.php">
								<section class="modal-card-body">
									<div class="modal-form">
									    <h3>Enter New Password and Password Confirmation</h3>
									    <table>
									      	<tr>
									      		<td class="modal-form-lbl">New Password:  </td>
									      		<td><input class="input is-medium" type="password" name="password" required /></td>
									      	</tr>
									      	<tr>
									      		<td class="modal-form-lbl">Confirm New Password:  </td>
									      		<td><input class="input is-medium" type="password" name="confirm_password" required /></td>
									      	</tr>
									      </table>
								    </div>
								</section>
								<footer class="modal-card-foot">
								    <button type="submit" class="button is-success" id="modal-confirm-center" name="pwd-reset-np-btn">Reset Password</button>
								</footer>
							</form>
						</div>
					</div>';
			}else{
				// Default - Ask user for username and password
				echo '<div class="modal" id="confirm-account-modal">
					  	<div class="modal-background"></div>
						<div class="modal-card">
							<header class="modal-card-head">
						    	<p class="modal-card-title">Password Reset Wizard</p>
						      	<button class="delete" onclick="CloseModal();" aria-label="close"></button>
						    </header>
						    <form method="POST" action="php/reset-password.php">
								<section class="modal-card-body">
									<div class="modal-form">
									    <h3>Please provide username and email address associated with the account that you would like to reset the password for</h3>
									    <table>
									      	<tr>
									      		<td class="modal-form-lbl">Username:  </td>
									      		<td><input class="input is-medium" type="text" name="username" required /></td>
									      	</tr>
									      	<tr>
									      		<td class="modal-form-lbl">Email:  </td>
									      		<td><input class="input is-medium" type="text" name="email" required /></td>
									      	</tr>
									      </table>
								    </div>
								</section>
								<footer class="modal-card-foot">
								    <button type="submit" class="button is-success" id="modal-confirm-center" name="pwd-rest-ue-btn">Confirm</button>
								</footer>
							</form>
						</div>
					</div>';
				}
		?>
	</div>
</section>
<script>
	function CloseModalUnset(){
		$('#confirm-account-modal').removeClass('is-active');
		<?php unset($_SESSION['pwd_reset_phase']); ?>
	}
	function CloseModal(){
		$('#confirm-account-modal').removeClass('is-active');
	}
	function ResetPassword(){
		$('#confirm-account-modal').addClass('is-active');
	}
</script>
<!-- Footer -->
<?php require('footer.php'); ?>