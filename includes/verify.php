<?php
	#we use a cookie to start a session at the beginning of the file and we make sure that the value of 
	#that cookie cooresponds to a real session file on the server
	

	$LoggedIn = FALSE;
	$VerifyOutput = "";


	if (isset($_COOKIE['_uid'])) {
		session_name('_uid');
		session_start();
		if (($_COOKIE['_uid'] == session_id()) AND isset($_SESSION['nick']) AND isset($_SESSION['userid'])) {
			$LoggedIn = TRUE;
		} else {
			$VerifyOutput .= "There was a problem with your session or it expired.";

		}

		
		
	}


?>