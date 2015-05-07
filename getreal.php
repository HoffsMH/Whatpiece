
<?php 
	
	if (isset($_POST['login'])) {
		$email = trim(stripslashes($_POST['email']));
		$passmd5 = md5(stripslashes($_POST['password']));
	}
	@include_once('inc_db.php');

	if (preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $email) ==0) {
		echo "you need to enter a valid email address";
	}
	if ((isset($_POST['login'])) AND (!is_null($email))) {
		$SQLString = "SELECT userid, email, passmd5 FROM accounts WHERE email='" . $email . "' AND passmd5='" . $passmd5 ."'";
		$QueryResult = @mysql_query($SQLString, $DBServerConnect);
	
		if (@mysql_num_rows($QueryResult) == 0) {
			 $WrongInfo = TRUE;
			 echo "wrong info";
		} else {
			echo "correct info";

		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Whatpiece</title>
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
				<img src="resources/kaejaflipped.png" alt="Orc Monk" height="543" width="654">
			</div>

			<div id='formwrapper'>
				
					<p id='minoranswer'>So yeah Either you are purposely trying to break this site or dont realize that to GET data you first have to GIVE data.</p>
					<h4>I'm going to need you to just go ahead and get real.</h4>
					<a href='index.php'>Back</a>.
			
			</div>
			<div id='divider'>

			</div>
		</div>
			
	</body>

</html>
