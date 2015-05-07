<?php
	$nl = "<br>\n";
	$TestString = "";
	$errors = 0;
	if (isset($_POST['register'])) {
		

		#------------------
		if (empty($_POST['email'])) {
			$TestString .= "<p>You must enter a email address.</p> ";
			++ $errors;
			
		} else {
			$email = stripslashes($_POST['email']);
			
			if (preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $email) == 0) {
					++ $errors;
					
					$TestString .= "<p>You need to enter a valid email address.</p>";
					$email = "";
			}

		}

		#---------------------
		if (empty($_POST['nick'])) {
			++ $errors;
			$TestString .= "<p>You need to enter a NickName</p>\n";
			$nick = "";
		} else {
			$nick = stripslashes($_POST['nick']);
		} 

		#--------------
		if (empty($_POST['password'])) {
			++ $errors;
			$TestString .= "<p>You need to enter a password</p>\n";
			$password = "";
		} else {
			$password = stripslashes($_POST['password']);
		}
		
		#---------------
		if (empty($_POST['Cpassword'])) {
			++ $errors;
			$TestString .= "<p>You need to enter a confirmation password</p>\n";
			$Cpassword = "";
		} else {
			$Cpassword = stripslashes($_POST['Cpassword']);
		}

		#-----------
		if (  !(empty($password)) &&  !(empty($Cpassword))  ) {
				if (strlen($password) < 6) {
					++ $errors;
					$TestString .= "<p>The Password is too short</p>\n";
					$password = "";
					$Cpassword = "";


				}
				if ($password <> $Cpassword) {
					++ $errors;
					$TestString .= "<p>The Password do not match.</p>\n";
					$password = "";
					$Cpassword = "";

				}
		}

		#----------
		if ($errors == 0)  {

				@include_once("inc_db.php");

		}

		$TableName = "accounts";
		if ($errors == 0) {
			#$TestString .= "we made it here $nl";
			$SQLstring = "SELECT count(*)  FROM $TableName WHERE  email='$email'";
			$QueryResult = @mysql_query($SQLstring, $DBServerConnect);

			if ($QueryResult !== FALSE) {
				$Row = mysql_fetch_row($QueryResult);
				if ($Row[0]>0) {
					$TestString .= "<p>The email address entered (" . htmlentities($email) .") is already registered.</p>\n";
					++ $errors;
				} 
			} else {
				$TestString .= "<p>Could not select the database: " . mysql_error($DBServerConnect) . "</p>\n";
			}
		} 

		#----
		if ($errors == 0) {
				$Date = date("Y-m-d H:i:s");
				$SQLstring = "INSERT INTO $TableName (email, nick, passmd5, joindate) VALUES ('$email', '$nick', '" . md5($password) ."', '$Date')";

				$QueryResult = @mysql_query($SQLstring, $DBServerConnect);

				if ($QueryResult === FALSE) {
					$TestString .= "<p>Unable to save your registration information. Error Code: " . @mysql_errno($DBServerConnect) . ": " . @mysql_error($DBServerConnect);
					++ $errors;

				} else {
					$TestString .= "<p>Thank you for Registering $nick </p> $nl";
					$TestString .= "<a href='index.php'>Back</a>";

				}
				#setcookie("userid", );
				mysql_close($DBServerConnect);
		}
		if ($errors) {
			$TestString .= "Or go $nl";
			$TestString .= "<a href='index.php'>Back.</a>";
		}
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<title>Register</title>
		<link rel='stylesheet' type='text/css' href='style.css'>
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>

	</head>
	<body>
		<div id='wrapper'>
			<div id='headerwrapper'>
				<?php @include_once('miscfills/headerfill.php') ?>
			</div>
				<?php @include_once('miscfills/userplatefill.php') ?>
			<div id='loginwrapper'>
				<br>
				<br>
				<?php echo $TestString; ?>
			</div>
			<div id='formwrapper'>
				<h2>Register</h2>
				<form action='register.php' method='POST'>
					<p>Email: <input type='text' name='email'></p>
					<p>NickName: <input type='text' name='nick'></p>
					<p>Password: <input type='password' name='password'></p>
					<p>Confirm Password: <input type='password' name='Cpassword'></p>

					<input type='submit' name='register' value='Register.'>
					<br>
					<br>
					<input type='reset' name='clear' value='clear'>

				</form>
			</div>
			<div id='divider'>

			</div>

		</div>
	</body>
</html>