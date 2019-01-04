<?php require('header.php'); ?>
<!-- Header -->

<!-- Specfic Images -->
<style>
	#reports-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<section class="body-content">
	<div class="user-list-container">
		<?php //Add condition so only admin can see this page
			if($_SESSION['isAdmin'] === 1){
		
				echo '<table id="report-list-table" class="table is-fullwidth is-bordered">
					<tr>
						<th>Report Issued By</th>
						<th>Artist Name</th>
						<th>Artwork Name</th>
						<th>Reason for report</th>
						<th>Action</th>
					</tr>';

					//Reports
					require("php/db-connection.php");
					$stmt = $conn->prepare(
						"SELECT r.reportID, r.reportCreatedUser, r.reportReason, u.userName, a.artName, r.reportedArtistID FROM reports r LEFT JOIN users u ON (r.reportedArtistID = u.userID) LEFT JOIN artwork a ON (r.reportedArtworkID = a.artID) WHERE r.reportResolved = 0 ORDER BY r.reportCreatedDate DESC"
					);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($reportID, $reportCreatedUser, $reportReason, $reportedUser, $reportedArtwork, $artistID);
					while($stmt->fetch()){
						echo '<tr>
							<td>'.$reportCreatedUser.'</td>
							<td>'.$reportedUser.'</td>
							<td>'.$reportedArtwork.'</td>
							<td>'.$reportReason.'</td>
							<td>
								<form method="POST" action="php/report-function.php">
									<input type="hidden" name="reportID" value="'.$reportID.'" />
									<input type="hidden" name="artistID" value="'.$artistID.'" />
									<button name="resolve" class="button is-info is-fullwidth">Resolved</button>
									<button name="deactivate" class="button is-danger is-fullwidth">Deactivate Account</button>
								</form>
							</td>
						</tr>';						
					}



				echo '</table>';
			}
		?>
	</div>
</section>

<!-- Footer -->
<?php require('footer.php'); ?>