<?php
	$FillString = "";
		

	if (!($LoggedIn)){
		
		$FillString .= "<p><h2>Log in</h2>/ or <a href='register.php'>Register</a></p> \n
					<form action='loginout.php' method='POST'> \n
						<p>Email: <input type='text' name='email'></p> \n
						<p>Password: <input type='password' name='password'></p> \n
						<input type='submit' name='login' value='login'> \n

					</form> \n";
		
	} else {
		$FillString .= "<p>Under Construction. Character list coming soon.</p> \n";
	}
	echo $FillString;
 ?>