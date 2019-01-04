<?php require('header.php'); ?>
<style>
	#sign-in-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<?php
	$error_message = "";
	if(isset($_SESSION['error_message'])){
		$error_message = $_SESSION['error_message'];
		session_unset();
		session_destroy();
	}
?>
<!-- Header -->
<section class="body-content">
	<div id="register-error-messages">
		<span class="error_message"><?php echo $error_message; ?></span>
	</div>
	<form id="register-main" method="POST" action="php/register_user.php" enctype="multipart/form-data">
		<div class="register-info-blocks" id="register-personal-info">
			<h2>Personal Information:</h2>
			<table>
				<tr>
					<td class="rib-lbls">First Name:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="firstname" required /></td>
				</tr>
				<tr>
					<td class="rib-lbls">Last Name:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="lastname" required /></td>
				</tr>
				<tr>
					<td class="rib-lbls">City:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="city" required /></td>
				</tr>
				<tr>	
					<td class="rib-lbls">Province:</td>
					<td class="rib-input">
						<select id="rpi-province" name="province" required >
							<option value="ON" selected="true">Ontario</option>
							<option value="AB">Alberta</option>
							<option value="BC">British Columbia</option>
							<option value="MB">Manitoba</option>
							<option value="NB">New Brunswick</option>
							<option value="NL">Newfoundland and Labrador</option>
							<option value="NS">Nova Scotia</option>
							<option value="PEI">Prince Edward Island</option>
							<option value="QC">Quebec</option>
							<option value="SK">Saskatchewan</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="rib-lbls">Country:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="country" required /></td>
				</tr>
			</table>		
		</div>

		<div class="register-info-blocks" id="register-user-info">
			<h2>User Information:</h2>
			<table>
				<tr>
					<td class="rib-lbls">Email Address:</td>
					<td class="rib-input"><input type="text" class="input is-small"name="emailaddress" required /></td>
				</tr>
				<tr>
					<td class="rib-lbls">Username:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="username" required /></td>
				</tr>
				<tr>
					<td class="rib-lbls">Password:</td>
					<td class="rib-input"><input type="password" class="input is-small" name="password" required /></td>
				</tr>
				<tr>	
					<td class="rib-lbls">Confirm Password:</td>
					<td class="rib-input"><input type="password" class="input is-small" name="confirmpassword" required /></td>
				</tr>
				<tr>	
					<td class="rib-lbls">Profile Picture:</td>
					<td class="rib-input"><input type="file" name="user_profile_pic" /></td>
				</tr>
			</table>
		</div>
		<div class="register-info-blocks" id="register-recovery-info">
			<h2>Password Recovery:</h2>
			<table>
				<tr>
					<td class="rib-lbls">Secret Question:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="secretquestion" required /></td>
				</tr>
				<tr>
					<td class="rib-lbls">Answer:</td>
					<td class="rib-input"><input type="text" class="input is-small" name="secretanswer" required /></td>
				</tr>
			</table>	
		</div>
		<button id="register-create-account-btn" type="submit" name="register">Create Account</button>
	</form>
</section>
<!-- Footer -->
<?php require('footer.php'); ?>