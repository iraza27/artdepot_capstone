<nav class="side-nav">
	<h1 class="title-nav-art-depot"><span class="title-color-part1">art</span><span class="title-color-part2">depot</span></h1>
	<div class="bottom-nav">
		<!-- insert another a tag when user is signed in called my artwork -->
		<a id="home-nav-btn" href="index.php">Announcements</a>
		<?php 
			if(isset($_SESSION['userID']) && (!empty($_SESSION['userID'])) && $_SESSION['isAdmin'] == 0){
				echo '<a id="artist-nav-btn" href="artist.php?user='.$_SESSION['userName'].'">Home</a>';
				echo '<a id="artwork-nav-btn" href="artwork.php?user='.$_SESSION['userName'].'">My Artwork</a>';
			}
		?>
		<a id="artistlist-nav-btn" href="artist-list.php">Artists</a>
		<!-- once user has signed in change sign in with another a tag calling with the username of the person signed in -->
		<?php 
			if(isset($_SESSION['userID']) && (!empty($_SESSION['userID']))){
				if($_SESSION['isAdmin'] == 1){
					echo '<a id="reports-nav-btn" href="reports.php">Reports</a>';
					echo '<a id="userlist-nav-btn" href="user-list.php">User List</a>';
				}
				echo '<a id="commissions-nav-btn" href="commissions.php?user='.$_SESSION['userName'].'">Commissions</a>';
				echo '<a id="account-nav-btn" href="account.php">'.$_SESSION["userName"].'</a>';
			}else{
				echo '<a id="sign-in-nav-btn" href="sign-in.php">Sign-In</a>';
			}
		?>
		
	</div>
</nav>