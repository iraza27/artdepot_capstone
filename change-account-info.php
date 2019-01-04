<?php require('header.php'); ?>
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
// call db connection
require('php/db-connection.php');

//bring up account information using session data
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
	if(isset($_SESSION['userID'])){
		$stmt = $conn->prepare("SELECT userName,email, firstName, lastName, city, province, country, secretQuestion, secretAnswer, userProfilePicture from users WHERE userid = ? ");
		$stmt->bind_param("i", $_SESSION['userID']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($uname, $uemail, $fname, $lname, $city, $province, $country, $secretQuestion, $secretAnswer, $profilepic);
		if($stmt->fetch()){
			$uname = $uname;
			$uemail = $uemail;
			$fname = $fname;
			$lname = $lname;
			$city = $city;
			$province = $province;
			$country = $country;
			$secretQuestion = $secretQuestion;
			$secretAnswer = $secretAnswer;
			$profile_picture_name = $profilepic;
		}
	}
	mysqli_close($conn);
?>
<!-- Header -->
<section class="body-content">
	<?php 
		if (!empty($error_message) || !empty($success_message)){
			echo '		<div id="register-error-messages">
							<span class="error_message">'.$error_message.'</span>
							<span class="success_message">'.$success_message.'</span>
						</div>';
		}
	?>
		<form id="register-main" method="POST" action="php/save-account-info.php" enctype="multipart/form-data">
			<div class="register-info-blocks" id="register-personal-info">
				<h2>Personal Information:</h2>
				<table>
					<tr>
						<td class="rib-lbls">First Name:</td>
						<td class="rib-input"><input type="text" id="rpi-firstname" name="firstname" value="<?php echo $fname; ?>" required /></td>
					</tr>
					<tr>
						<td class="rib-lbls">Last Name:</td>
						<td class="rib-input"><input type="text" id="rpi-lastname" name="lastname" value="<?php echo $lname; ?>" required /></td>
					</tr>
					<tr>
						<td class="rib-lbls">City:</td>
						<td class="rib-input"><input type="text" id="rpi-city" name="city" value="<?php echo $city; ?>" /></td>
					</tr>
					<tr>	
						<td class="rib-lbls">Province:</td>
						<td class="rib-input">
							<select id="rpi-province" name="province" required >
								<option value="ON">Ontario</option>
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
						<td class="rib-input"><input type="text" name="country" value="<?php echo $country; ?>" required /></td>
					</tr>
				</table>					
			</div>

			<div class="register-info-blocks" id="register-user-info">
				<h2>User Information:</h2>
				<table>
					<tr>
						<td class="rib-lbls">Email Address:</td>
						<td class="rib-input"><input type="text" name="emailaddress" value="<?php echo $uemail; ?>" required /></td>
					</tr>
					<tr>
						<td class="rib-lbls">Username:</td>
						<td class="rib-input"><input type="text" name="username" value="<?php echo $uname; ?>" readonly /></td>
					</tr>
					<tr>	
						<td class="rib-lbls">Current Profile Picture:</td>
						<td class="rib-input"><img src="<?php echo 'user_profiles/'.$uname.'/'.$profile_picture_name; ?>"  /></td>
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
						<td class="rib-input"><input type="text" name="secretquestion" value="<?php echo $secretQuestion; ?>" required /></td>
					</tr>
					<tr>
						<td class="rib-lbls">Answer:</td>
						<td class="rib-input"><input type="text" name="secretanswer" value="<?php echo $secretAnswer; ?>" required /></td>
					</tr>
				</table>	
			</div>
			<button id="save-account-btn" type="submit" name="save-account-info">Save</button>
		</form>
		<input type="text" id="province-hidden" value="<?php echo $province; ?>" hidden />
</section>
<script>
	$provinceShort = $('#province-hidden').val();
	$('#rpi-province').val($provinceShort);
</script>
<!-- Footer -->
<?php require('footer.php'); ?>