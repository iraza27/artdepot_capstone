<?php

	if(isset($_POST['signout'])){
		session_start();
		session_destroy();
		header("Location: ../index.php");
		exit();
	}


?>