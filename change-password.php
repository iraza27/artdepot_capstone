<?php require('header.php'); ?>
<style>
	#sign-in-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<?php
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
?>
<!-- Header -->
<section class="body-content">
	<div id="sign-in">
		<div id="register-error-messages">
			<span class="error_message"><?php echo $error_message; ?></span>
			<span class="success_message"><?php echo $success_message; ?></span>
		</div>
		<form method="POST" action="php/change-password-logic.php">
			<input type="password" name="oldpassword" class="change-password-text" placeholder="Old Password"  required />
			<input type="password" name="newpassword1" class="change-password-text" placeholder="Password" required />
			<input type="password" name="newpassword2" class="change-password-text" placeholder="Confirm Password" required />
				<div id="sign-in-buttons">
					<button type="submit" id="login" name="change-password" class="sign-in-btns" >Change Password</button>
				</div>
		</form>
	</div>
</section>
<!-- Footer -->
<?php require('footer.php'); ?>