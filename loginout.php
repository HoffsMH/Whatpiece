<?php

	function gen_sess() {
		$seed = '';
		$a = @fopen('/dev/urandom','rb');
		$seed .= @fread($a,23);
		$b = @fclose($a);
		$seed = md5(stripslashes($seed));

		return $seed;
	}

	$errors = 0;
	$login = FALSE;
	$logout = FALSE;
	$Debug = TRUE;
	$DebugString  = "";

	$OutString = "";

	if (isset($_POST['login'])) {
		$login = TRUE;
		$logout = FALSE;
	}
	if (isset($_POST['logout'])) {
		$logout = TRUE;
		$login = FALSE;
	}
	if (!($login) AND !($logout)) {
		$OutString .= "<p>Please visit Main page to login or logout.</p><br>\n";
		$OutString .= "<a href='index.php'>Back</a>. \n";
	}
	if (isset($_COOKIE['_uid'])) {
		
		$LoggedIn = TRUE;
		session_name('_uid');
		session_start();
		
	}


	if ($login AND !($LoggedIn)) {
		@include_once('inc_db.php');
		$TableName = 'accounts';

		$SQLString = "SELECT userid, nick FROM $TableName" .
						" WHERE BINARY passmd5='" . md5(stripslashes($_POST['password'])) .
						"' AND email='" . stripslashes($_POST['email']) . "'";

		$QueryResult = mysql_query($SQLString, $DBServerConnect);
		$DebugString .= "mysql error: " . mysql_error($DBServerConnect) ." <br>\n";

		if (mysql_num_rows($QueryResult) == 0) {
			$OutString .= "<p>The email address/password " .
				"combination is not valid</p>\n";
			$OutString .= "<a href='index.php'>Back</a>. \n";
			++ $errors;
		} else {
			$Row = mysql_fetch_assoc($QueryResult);
			$DebugString .=  "Found user and pass and id matched in db. <br>\n";
			$DebugString .= "Fetched an array. <br>\n";

			$SessId = gen_sess();
			$SessName = '_uid';
			$DebugString .= "Generated a new random Sessionid: $SessId <br>\n";

			session_id($SessId);
			session_name($SessName);

			#if (!(@setcookie($SessName, $SessId, time()+60*60*3, "/", "whatpiece.atspace.cc"))){
			#	$DebugString .= "Could not set cookie <br>\n";
			#} else {
			#	$DebugString .= "Sucessfully Set Cookie <br>\n";
			#}
			

			

			$DebugString .= "Attemping to initiate Session <br>\n";

			session_start();
			$DebugString .= "Now lets see if our id and name are those found in the cookie. <br>\n";
			$DebugString .=  "Session Name: " . session_name() . "<br>\n";
			$DebugString .=  "Session id: " . session_id() . "<br>\n";
			$_SESSION['nick'] = $Row['nick'];
			$_SESSION['userid'] = $Row['userid'];
			$DebugString .= "just for now we need to make sure we captured userid. <br>\n";
			$DebugString  .= "userid: " . $_SESSION['userid'];

			
			$DebugString .= "Your nick should be: " . $_SESSION['nick'] . "<br>\n";

			header('location: index.php');
			exit();



			
		}
		@mysql_close($DBServerConnect);
		$DebugString .= "Closed the Connection <br>\n";

	} else {
		if ($login) {
			$OutString .= "<p>You are already logged in on another account please logout first.</p>\n";
		}
	}

	if ($logout) {
		if ($LoggedIn) {
			$SessName = '_uid';
			setcookie ($SessName, "", 1);
			setcookie ($SessName, false);
			unset($_COOKIE[$SessName]);
			session_unset();
			session_destroy();
			$LoggedIn = FALSE;
			$OutString .= "Thank you. You have been logged out.<br>\n";
			$OutString .= "<a href='index.php'>Back</a>. \n";
		} else {
			$OutString .= "You were not logged in in any known account. <br>\n";
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
				<?php echo $OutString; ?>
				<?php if ($Debug) {echo $DebugString;}?>
			</div>
			<div id='formwrapper'>
				<img src="resources/kaeja.png" alt="Orc Monk" height="389" width="252">
			</div>
			<div id='divider'>

			</div>

		</div>
	</body>
</html>