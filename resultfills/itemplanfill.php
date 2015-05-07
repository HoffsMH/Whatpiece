<?php
	

	$FillString = "";
	$FillString .= "<h2 id='h2plan'>Your Item Plan for the following Weeks.</h2>";

	$FillString .= "<table id='itemplan'> $nl";
	$FillString .= "\t<tr id='itemplanhead'><th><div id='itemheading'>Item</div></th>";
	for ($i = 0; $i < $WeekCount; $i++) {
		if ($i == 0) {
			$FillString .= "<th>This Week";
			if ($i == ($WeekCount-1)) {
				$FillString .= "<br><span id='weekfooter'>Weapon!</span>";
			} else {
				$FillString .= "</th>";
			}
		}
		if ($i == 1) {
			$FillString .= "<th>Next Week";
			if ($i == ($WeekCount-1)) {
				$FillString .= "<br><span id='weekfooter'>Weapon!</span>";
			} else {
				$FillString .= "</th>";
			}
		}
		if ($i > 1) {
			$FillString .= "<th>Week " . ($i);
			if ($i == ($WeekCount-1)) {
				$FillString .= "<br><span id='weekfooter'>Weapon!</span>";
			} else {
				$FillString .= "</th>";
			}

		}
	}
	$FillString .= "</tr> $nl";

	#Outer loop (Number of rows)
	for ($i = 1; $i <= $ItemTiers; $i++) {
		#inner loop(Number of Columns)
		#Should always be $WeeksCount plus 1 for itemtype description
		for ($j = -1; $j < $WeekCount; $j++){
			if ($j == -1) {
				$FillString .= "\t<tr id='itemplanrow'><td id='itemplandesc'>";
				$FillString .= $Item[$i];
				$FillString .= " Piece</td>";
			} else {
				if (isset($ItemPlan[$j][$i])){
					$FillString .= "<td id='itemplandata'>" . $ItemPlan[$j][$i]."</td>";
				} else {
					$FillString .= "<td id='itemplandata'>" . 0 ."</td>";
				}
			}

		}
		$FillString .= "</tr>";
	}
	$FillString .= "</table>";

	echo $FillString;
	



?>