<?php

	$FillString = "";

	

	$FillString .= "<div id='userplate'>\n";
		if ($LoggedIn) {
			$Nick  = stripslashes($_SESSION['nick']);
			
			if ($CurrFile == "index.php") {
				$FillString .= "<div id='userplateleft'><input form='conq' type='text' name='charname' placeholder='Character Name' tabindex='1'></div> ";
			}
			if ($CurrFile == "result.php") {
				$FillString .= "<div id='userplateleft'><h2>$CharName</h2></div> ";
			}


			$FillString .= "<div id='userplateright'><ul><li>" . $Nick . " :</li><li> <form action='loginout.php' method='POST'>";
			$FillString .= "<input id='logoutbutton' type='submit' name='logout' Value='logout'></form></li></ul></div><br>\n";

		}
		
	$FillString .= "</div>\n";
	echo $FillString;
?>