
<?php 
	$CurrFile = "index.php";
	@require_once('includes/verify.php');


	
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
				<?php @include_once('indexfills/loginfill.php') ?>
			</div>

			<div id='formwrapper'>
				<form id='conq' action='result.php' method='POST'>
					<p>I have <input type='number' name='Current' max='99999' tabindex='2'> Conquest Points.</p>
					<p>I have earned <input type='number' name='WeeklyEarned' max='99999' tabindex='3'> of my <input type='number' name='WeeklyCap' max='99999' tabindex='4'> weekly cap 
						and <input type='number' name='Season' max='99999' tabindex='5'> season total.</p>
					<p>My Projected Conquest cap for next week is <input type='number' name='Cap' max='99999' tabindex='6'>.</p>
					<input type='submit' name='submit' value='Go.' tabindex='7'>
					<br>
					<br>
					<input type='reset' name='clear' value='clear' tabindex='8'>
				</form>
			</div>
			<div id='divider'>

			</div>
		</div>
			
	</body>

</html>
