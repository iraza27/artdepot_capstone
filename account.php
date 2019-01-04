<?php require('header.php'); ?>
<style>
	#account-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<!-- Header -->
<section class="body-content">
	<div id="sign-out">
		<h1>Account Options</h1>
		<form method="POST" action="php/logout.php">
			<div id="sign-in-buttons">
				<a href="change-password.php" class="sign-out-btns">Change Password</a>
				<a href="change-account-info.php" class="sign-out-btns">Change Account Information</a>
				<button type="submit" id="signout" name="signout" class="sign-out-btns">Sign Out</button>
			</div>
		</form>
	</div>
</section>
<!-- Footer -->
<?php require('footer.php'); ?>