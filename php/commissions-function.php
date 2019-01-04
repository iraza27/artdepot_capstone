<?php

	//functions for the commissions page
	session_start();
	require("db-connection.php");
	//If type is set - Did not come to this page directly.
	if(isset($_POST['type'])){
		$type = $_POST['type'];
		$myID = $_SESSION['userID'];

		switch ($type) {
			case 'request':
				//Default and Request Tab
				$stmt = $conn->prepare("SELECT c.commID, date(c.commRequestDate), c.commSuggPrice, c.commDescription, c.commRequestUserID, u.userName, c.commStatus FROM commissions c LEFT JOIN users u ON (c.commRequestUserID = u.userID) WHERE c.commRequestUserID != ? AND c.commStatus = 'negotiations' ");

				$stmt->bind_param("i", $myID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($commID, $commDate, $commSPrice, $commDescription, $commRequestUserID, $commRequestUserName, $commStatus);
				while($stmt->fetch()){

				echo '<div class="commission_report_container box">
				        <label>Commission Date:</label><span class="crcb_content">'.$commDate.'</span>
				        <label>User:</label><span class="crcb_content">'.$commRequestUserName.'</span>
				        <label>Suggested Price:</label><span class="crcb_content">$'.$commSPrice.'</span>
				        <label>Status:</label><span class="crcb_content">'.$commStatus.'</span>
				        <label>Description:</label><span class="crcb_content">'.$commDescription.'</span>
				        <input type="hidden" class="requestUserID" value="'.$commRequestUserID.'" />
					    <input type="hidden" class="commID" value="'.$commID.'" />
				        <label>Notes:</label><span class="crcb_content"><textarea row="10" maxlength="600" class="textarea artistNotes" placeholder="Enter any additional information for client"></textarea></span>
				        <label>Final Price:</label><span class="crcb_content"><input type="text" required class="input finalprice" placeholder="0.00" /></span>
				        <button class="button is-primary approve-btn"><span>Approve</span><span class="icon is-small"><i class="far fa-thumbs-up" aria-hidden="true"></i></span></button>
				        <button class="button is-danger reject-btn"><span>Reject</span><span class="icon is-small"><i class="far fa-thumbs-down" aria-hidden="true"></i></span></button>
				      </div>';

				}
				$stmt->close();
				break;
			
			case 'commissions':
				//My Commissions Tab - Commissions I Requested
				$stmt = $conn->prepare("SELECT c.commID, date(c.commRequestDate), c.commSuggPrice, c.commDescription, c.commArtistID, u.userName, c.commStatus, c.commArtistNotes, c.commFinalPrice, c.commISPaid, c.commUploadedArtwork  FROM commissions c LEFT JOIN users u ON (c.commArtistID = u.userID) WHERE c.commRequestUserID = ?");

				$stmt->bind_param("i", $myID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($commID, $commDate, $commSPrice, $commDescription, $commArtistID, $commArtistUserName, $commStatus, $commArtistNote, $commFinalPrice, $isPaid, $artwork_file);
				while($stmt->fetch()){

				echo '<div class="commission_report_container box">
				        <label>Commission Date:</label><span class="crcb_content">'.$commDate.'</span>
				        <label>Artist:</label><span class="crcb_content">'.$commArtistUserName.'</span>
				        <label>Suggested Price:</label><span class="crcb_content">$'.$commSPrice.'</span>
				        <label>Status:</label><span class="crcb_content">'.$commStatus.'</span>
				        <label>Description:</label><span class="crcb_content">'.$commDescription.'</span>
				        <input type="hidden" class="artistUserID" value="'.$commArtistID.'" />
					    <input type="hidden" class="commID" value="'.$commID.'" />
				        <label>Notes:</label><span class="crcb_content">'.$commArtistNote.'</span>
				        <label>Final Price:</label><span class="crcb_content">$'.$commFinalPrice.'</span>';
				        if($commStatus === 'Complete' && $isPaid === 1){
				        	echo '<a href="user_profiles/'.$commArtistUserName.'/'.$artwork_file.'" download class="button">Download Artwork</a>';
				        }
				        if($commStatus === 'Approved' && $isPaid === 0){ 
				        	echo '<div id="paypal-button" class="btn-center"></div>
									<script>
									  paypal.Button.render({
									    // Configure environment
									    env: "sandbox",
									    client: {
									      sandbox: "demo_sandbox_client_id",
									      production: "demo_production_client_id"
									    },
									    // Customize button (optional)
									    locale: "en_US",
									    style: {
									      size: "medium",
									      color: "gold",
									      shape: "rect",
									    },

									    // Enable Pay Now checkout flow (optional)
									    commit: true,

									    // Set up a payment
									    payment: function(data, actions) {
									      return actions.payment.create({
									        transactions: [{
									          amount: {
									            total: "'.$commFinalPrice.'",
									            currency: "CAD"
									          }
									        }]
									      });
									    },
									    // Execute the payment
									    onAuthorize: function(data, actions) {
									      return actions.payment.execute().then(function() {
									        // Show a confirmation message to the buyer
									        window.alert("Thank you for your purchase!");
									        ArtworkPaid('.$commID.');
									      });
									    }
									  }, "#paypal-button");
									    function ArtworkPaid(commID){
									      var commID = commID;
									      var payment = true;
									      $.ajax({
									        url: "php/commissions-function.php",
									        method: "POST",
									        data: ({payment:payment, commID:commID}),
									        success: function(data){
									          location.reload();
									        }
									      });    
									    }
									</script>';
				        }
				      echo '</div>';

				}
				$stmt->close();
				break;
			case 'approved':
				//Approved Commissions Tab - Starting work
				$stmt = $conn->prepare("SELECT c.commID, date(c.commRequestDate), c.commSuggPrice, c.commDescription, c.commArtistID, u.userName, c.commStatus, c.commArtistNotes, c.commFinalPrice  FROM commissions c LEFT JOIN users u ON (c.commRequestUserID = u.userID) WHERE c.commRequestUserID != ? AND c.commISPaid = 0 AND c.commStatus = 'Approved'");

				$stmt->bind_param("i", $myID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($commID, $commDate, $commSPrice, $commDescription, $commArtistID, $commArtistUserName, $commStatus, $commArtistNote, $commFinalPrice);
				while($stmt->fetch()){

				echo '<div class="commission_report_container box">
				        <label>Commission Date:</label><span class="crcb_content">'.$commDate.'</span>
				        <label>Artist:</label><span class="crcb_content">'.$commArtistUserName.'</span>
				        <label>Suggested Price:</label><span class="crcb_content">$'.$commSPrice.'</span>
				        <label>Status:</label><span class="crcb_content">'.$commStatus.'</span>
				        <label>Description:</label><span class="crcb_content">'.$commDescription.'</span>
				        <input type="hidden" class="artistUserID" value="'.$commArtistID.'" />
					    <input type="hidden" class="commID" value="'.$commID.'" />
				        <label>Notes:</label><span class="crcb_content">'.$commArtistNote.'</span>
				        <label>Final Price:</label><span class="crcb_content">$'.$commFinalPrice.'</span>
				    </div>';

				}
				$stmt->close();
				break;

			case 'pending':
				//Pending Commissions Tab - Pending Payment
				$stmt = $conn->prepare("SELECT c.commID, date(c.commRequestDate), c.commSuggPrice, c.commDescription, c.commArtistID, u.userName, c.commStatus, c.commArtistNotes, c.commFinalPrice  FROM commissions c LEFT JOIN users u ON (c.commRequestUserID = u.userID) WHERE c.commRequestUserID != ? AND c.commISPaid = 1 AND c.commStatus = 'Pending'");

				$stmt->bind_param("i", $myID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($commID, $commDate, $commSPrice, $commDescription, $commArtistID, $commArtistUserName, $commStatus, $commArtistNote, $commFinalPrice);
				while($stmt->fetch()){

				echo '<div class="commission_report_container box">
					    <label>Commission Date:</label><span class="crcb_content">'.$commDate.'</span>
					    <label>Artist:</label><span class="crcb_content">'.$commArtistUserName.'</span>
					    <label>Suggested Price:</label><span class="crcb_content">$'.$commSPrice.'</span>
					    <label>Status:</label><span class="crcb_content">'.$commStatus.'</span>
					    <label>Description:</label><span class="crcb_content">'.$commDescription.'</span>
					    <input type="hidden" class="artistUserID" value="'.$commArtistID.'" />
					    <label>Notes:</label><span class="crcb_content">'.$commArtistNote.'</span>
					    <label>Final Price:</label><span class="crcb_content">$'.$commFinalPrice.'</span>
					    <form method="POST" action="php/commissions-function.php" enctype="multipart/form-data">
					    	<input type="hidden" class="commID" name="commID" value="'.$commID.'" />
					        <label>Upload Artwork:</label><span class="crcb_content"><input type="file" name="comm_file_upload" /></span>
					        <button class="button is-primary" name="upload_file_btn" type="submit">Upload</button>
					    </form>
				    </div>';

				}
				$stmt->close();
				break;
			case 'completed':
				//Comepleted Commissions Tab - Transaction complete
				$stmt = $conn->prepare("SELECT c.commID, date(c.commRequestDate), c.commSuggPrice, c.commDescription, c.commArtistID, u.userName, c.commStatus, c.commArtistNotes, c.commFinalPrice, date(c.commCompleteDate)  FROM commissions c LEFT JOIN users u ON (c.commRequestUserID = u.userID) WHERE c.commRequestUserID != ? AND c.commISPaid = 1 AND c.commStatus = 'Complete'");

				$stmt->bind_param("i", $myID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($commID, $commDate, $commSPrice, $commDescription, $commArtistID, $commArtistUserName, $commStatus, $commArtistNote, $commFinalPrice, $commCompleteDate);
				while($stmt->fetch()){

				echo '<div class="commission_report_container box">
				        <label>Commission Date:</label><span class="crcb_content">'.$commDate.'</span>
				        <label>Complete Date:</label><span class="crcb_content">'.$commCompleteDate.'</span>
				        <label>Artist:</label><span class="crcb_content">'.$commArtistUserName.'</span>
				        <label>Suggested Price:</label><span class="crcb_content">$'.$commSPrice.'</span>
				        <label>Status:</label><span class="crcb_content">'.$commStatus.'</span>
				        <label>Description:</label><span class="crcb_content">'.$commDescription.'</span>
				        <input type="hidden" class="artistUserID" value="'.$commArtistID.'" />
					    <input type="hidden" class="commID" value="'.$commID.'" />
				        <label>Notes:</label><span class="crcb_content">'.$commArtistNote.'</span>
				        <label>Final Price:</label><span class="crcb_content">$'.$commFinalPrice.'</span>
				    </div>';

				}
				$stmt->close();
				break;
		}
	}

	//Reject and Approve Button
	if(isset($_POST['approve'])){
		//Final price and notes must be set
		if(isset($_POST['finalPrice']) && isset($_POST['additionalNotes']) && isset($_POST['commID'])){
			$commID = $_POST['commID'];
			$finalPrice = $_POST['finalPrice'];
			$additionalNotes = $_POST['additionalNotes'];

			$stmt = $conn->prepare("UPDATE commissions SET commStatus = 'Approved', commArtistNotes = ?, commFinalPrice = ? WHERE commID = ?");
			$stmt->bind_param("sii", $additionalNotes, $finalPrice, $commID);
			$stmt->execute();

		}

	}elseif(isset($_POST['reject'])){
		if(isset($_POST['commID'])){
			$commID = $_POST['commID'];

			$stmt = $conn->prepare("UPDATE commissions SET commStatus = 'Rejected' WHERE commID = ?");
			$stmt->bind_param("i",$commID);
			$stmt->execute();

		}
	}

	//When payment is made
	if(isset($_POST['payment'])){
		$commID = $_POST['commID'];
		$stmt = $conn->prepare("UPDATE commissions SET commISPaid = 1, commStatus = 'Pending' WHERE commID = ?");
		$stmt->bind_param("i",$commID);
		$stmt->execute();		
	}

	//When file is uploaded
	if(isset($_POST['upload_file_btn'])){
		$me = $_SESSION['userName'];
		$commID = $_POST['commID'];
		// Profile Picture Upload
		$curdir = getcwd()."/../user_profiles";
		$profile_file_name = "";
		if(isset($_FILES['comm_file_upload'])){
			$tmpLocation = $_FILES['comm_file_upload']['tmp_name'];
			$profile_file_name = $_FILES['comm_file_upload']['name'];
			$profile_file_name = 'commission_'.$profile_file_name;
			move_uploaded_file($tmpLocation, $curdir."/".$me."/".$profile_file_name);
		}
		date_default_timezone_set('America/Toronto');
		$commCompleteDate = date('Y-m-d H:i:s');
		$stmt = $conn->prepare("UPDATE commissions SET commStatus = 'Complete', commCompleteDate = ?, commUploadedArtwork = ? WHERE commID = ?");
		$stmt->bind_param("ssi", $commCompleteDate, $profile_file_name, $commID);
		$stmt->execute();
		$stmt->close();
		header("Location: ../commissions.php?user=".$me);
		exit();
	}
?>