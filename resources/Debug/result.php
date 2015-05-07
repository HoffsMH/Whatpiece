<?php
	$Current = $_POST['Current'];
	echo "Current: " . $_POST['Current'] . "<br>\n";
	$WeeklyEarned = $_POST['WeeklyEarned'];
	echo "WeeklyEarned: " . $_POST['WeeklyEarned'] . "<br>\n";
	$WeeklyCap = $_POST['WeeklyCap'];
	echo "WeeklyCap: " . $_POST['WeeklyCap'] . "<br>\n";
	$Season = $_POST['Season'];
	echo "Season: " . $_POST['Season'] . "<br>\n";
	$Cap = $_POST['Cap'];
	echo "Cap: " . $_POST['Cap'] . "<br>\n";

	$nl = "<br>\n";

	$WeekCount = 0;
	$STweek[0] = 0;
	$RemainingTotal = 7250 - $Season;
	$Remaining[0] = $RemainingTotal;
	#current item tiers and their cost on live
	$Item[0] = 0;
	$Item[1] = 1250;
	$Item[2] = 1750;
	$Item[3] = 2250;
	$ItemTiers = 3;
	#Starting counter for item tiers may want to start on tier 2 or 3 in future
	$CI = 1;
	#array of items in whatever order 
	$ItemType = array();

 	

	if ($WeeklyEarned < $WeeklyCap) {
		$ThisWeek = TRUE;
	} else {
		$ThisWeek = FALSE;
	}

	#this while loop is a sort of simulation of the coming weeks
	while ($RemainingTotal > 0) {
		echo "We have just entered the loop.$nl";
		echo "----------------------------------- $nl";

		if ($ThisWeek == TRUE) {
			echo "turns out we haven't capped this week $nl";
			$Remaining[0] -= ($WeeklyCap - $WeeklyEarned);
			echo "Remaining is now {$Remaining[0]} $nl";
			$RemainingTotal -=  ($WeeklyCap - $WeeklyEarned);
			echo "Remaningtotal is now $RemainingTotal $nl";
			$STweek[0] += ($Season + ($WeeklyCap - $WeeklyEarned));
			echo "STweek[0] is now {$STweek[0]} $nl";
			$ThisWeek = FALSE;
		} else {
			echo "turns out we have capped this week $nl";
			echo "we are in week $WeekCount $nl";
			if ($WeekCount > 0) {
				echo "WeekCount is > 0 : " . $WeekCount . $nl;
				$Remaining[$WeekCount] = ($Remaining[($WeekCount-1)] - $Cap);
				echo "Remaining is now {$Remaining[$WeekCount]} $nl";
				$RemainingTotal -= $Cap;
				echo "RemainingTotal is now $RemainingTotal $nl";
				$STweek[$WeekCount] = ($STweek[($WeekCount -1)] + $Cap);
				echo "STweek[$WeekCount] is now {$STweek[$WeekCount]} $nl";
			} else {
				echo "WeekCount is !> 0 : " . $WeekCount .  $nl;
				echo "Remaining is now {$Remaining[$WeekCount]} $nl";
				echo "RemainingTotal is now $RemainingTotal $nl";
				$STweek[$WeekCount] += $Season;
				echo "STweek[$WeekCount] is now {$STweek[$WeekCount]} $nl";

			}
		}
		echo "This has been week $WeekCount $nl";
		++ $WeekCount;
		echo "WeekCount is now $WeekCount $nl";
		echo "Loop iteration complete $nl $nl";
	}

	echo "we got our weapon in week " . ($WeekCount -1) . " $nl";
	echo "initial $nl";
	echo $Remaining[($WeekCount-2)] . " $nl";

	#Now we have to figure out how much the user has already spent
	$Spent = ($Season - $Current);
	#Now we have 

	$Needed = 3500 - $Cap;
	$Room = (($STweek[$WeekCount -2] - $Needed) - $Spent);

	echo "Spent $Spent $nl Needed $Needed $nl Room $Room $nl";
	$STRoom = $Room;

	echo "So it will take" . ($WeekCount) ." to get out weapon and at our projected conquest cap $nl";
	echo " we have to save $Needed by the week of before weapon week $nl";
	echo "so $Needed/" . ($WeekCount -1) . " is " . ($Needed/($WeekCount -1)) ."$nl";
	$AverageSavingsWanted = ($Needed/($WeekCount -1));

	$WeeklyRemainder = 0;
	$BudgetVariance = 0;
	$ItemPlan = array(array());
	$WeeksTotal = count($STweek);
	echo "real quick weekstotal: "  . $WeeksTotal . $nl;
	$STCurrent = 0;
	$NextWeek = TRUE;

	while ($STCurrent < $WeeksTotal) {
		$STCurrentV = $STweek[$STCurrent];
	
		echo "$nl======================================$nl";
		echo "we are in weekly purchase simulation loop $nl";
		echo "Our BudgetVariance is: ". $BudgetVariance . $nl;

		echo "STcurrent = " .  $STCurrent . "$nl";
		echo "STcurrentV = "  . $STCurrentV . "$nl";




		#time to calculate what is available to spend on current week
		#if we are in our first week
		if ($STCurrent == 0) {
			echo "we are in our first week $nl";
			echo "we determine how much conquest is available to spend on our first week $nl";
			if ($NextWeek == TRUE) {
				$Available = (($WeeklyCap - $WeeklyEarned) + $Current);
			}
				
			echo "Available: " . $Available . $nl;


		} else { #we are not in our first week 
			echo "We are in week $STCurrent $nl";
			echo "We  determine how much conquest is available to spend on week $STCurrent $nl";
			if ($NextWeek == TRUE) {
				$Available = ($Cap + $WeeklyRemainder);
			}
			echo "Available: " . $Available . $nl;
		}

		if ($STCurrent == ($WeekCount-2)) {
			echo "we are in the last prep week $nl";
			$Available = $Available - $Needed;
			echo "The new Available is " . $Available . "$nl";
			$j = $ItemTiers;
			echo "j: " . $j;
			#while ($j >= 1) {
			#	while ($Available >= $Item[$j]){
			#		$ItemPlan[$STCurrent][$j] += 1;
			#		$Available -= $Item[$j];
			#	}
			#$j--;
			#}
		}
		
		
		#Data gathering loop
		$i = $ItemTiers;
		$SupposedWeeklyRemainder = array();
		$Afford = array();
		$SupposedVariance = array();

		while (($i >= 1) AND !($STCurrent == ($WeekCount-2))) {
			echo "$nl---------------------------------------$nl";
			echo "we are now in the Data gathering loop $nl";
			echo "right now we are on item $i which is a {$Item[$i]} piece $nl";
			echo "we are considering this item for week $STCurrent $nl";
			#we figure out if we can afford it
			if ($Available >= $Item[$i]){
				$Afford[$i] = TRUE;
			} else {
				$Afford[$i] =FALSE;
			}
			echo "Afford: {$Afford[$i]} $nl";
			#We figure out how much we would have left this week after buying this item
			$SupposedWeeklyRemainder[$i] = ($Available - $Item[$i]);
			echo "Supposedweeklyremainder {$SupposedWeeklyRemainder[$i]} $nl";
			$SupposedVariance[$i] = ($AverageSavingsWanted - $SupposedWeeklyRemainder[$i]);
			echo "SupposedVariance $i:  {$SupposedVariance[$i]} $nl";
			$i--;
			echo "i is now $i endloop $nl";

		}

		#Create Variance array to be sorted
		for ($i = 1; $i <= $ItemTiers; $i++) {
			$Fit[$i] = $i;
		}
		echo "The unSorted Rating array $nl";
		echo "Fit 1 {$Fit[1]} $nl";
		echo "Fit 2 {$Fit[2]} $nl";
		echo "Fit 3 {$Fit[3]} $nl";

		#Variance rating Group

		if (!($STCurrent == ($WeekCount-2))) {
			do {
				echo "$nl---------------------------------------$nl";
				echo "we are now in the Variance Rating loop (bubble sort) $nl";
				$Swap = FALSE;
				$Set = 0;
				for ($i = 2; $i <= ($ItemTiers+1); $i ++) {
					echo "is the supposed variance of fit " . ($i-1) . " Greater than than the variance of item $i ? $nl";
					echo "item " . $Fit[$i-1] . ": " . $SupposedVariance[$Fit[$i-1]] . "$nl";
					echo "item " . $Fit[$i] . ": " . $SupposedVariance[$Fit[$i]] . "$nl";
					if (!($SupposedVariance[$i] == "")){
						if (abs($SupposedVariance[$Fit[$i-1]]) > abs($SupposedVariance[$Fit[$i]])) {
							echo "Apparently so: switch them $nl";
							$temp = $Fit[$i];
							echo "temp $temp $nl";
							$Fit[$i] = $Fit[$i-1];
							echo "Fit $i" . $Fit[$i] . $nl;
							$Fit[$i-1] = $temp;
							echo "Fit".  ($i-1) . " " . $Fit[$i-1] . $nl;

							$Swap = TRUE;
						} else {
							echo "Apparently not: dont switch them $nl";
							$Set += 1;
						}
					} else {
						echo "Doesn't matter one of em's blank $nl";
						$Swap = TRUE;
					}
				}
				if ($Set == ($ItemTiers-1)) $Swap = FALSE;
			} while (($Swap == TRUE));
			echo "The Sorted Rating array $nl";
		echo "Fit 1 {$Fit[1]} which is" . $Item[$Fit[1]].  " Which has " . abs($SupposedVariance[$Fit[1]]) . " Variance $nl";
		echo "Fit 2 {$Fit[2]} which is" . $Item[$Fit[2]]. "  Which has " . abs($SupposedVariance[$Fit[2]]) . " Variance $nl";
		echo "Fit 3 {$Fit[3]} which is" . $Item[$Fit[3]]. "  Which has " . abs($SupposedVariance[$Fit[3]]) . " Variance  $nl";
		}


		

		#Buy Decision Loop
		$i = 1;
		$ItemsBoughtThisweek = 0;
		while(($i <= $ItemTiers) AND (!($STCurrent == ($WeekCount-2)))) {
			echo "Entered Buy decision loop in week $STCurrent $nl";
			$RecentlyBought = FALSE;
			if ($Item[$Fit[$i]] <= $Available) {
				if ($BudgetVariance <> 0){
					#Boolean values to make following decision easier to see
					$UnderBudget = ($BudgetVariance > 0); #If BudgetVariance is a positive number then we need to over save this week
					$OverBudget = ($BudgetVariance < 0); #If BudgetVariance is a negative number then we need to under save this week

					$OverSave =  ($SupposedVariance[$Fit[$i]] > $AverageSavingsWanted);
					$UnderSave = ($SupposedVariance[$Fit[$i]] < $AverageSavingsWanted);

					if (($UnderBudget AND $OverSave) OR ($OverBudget AND $UnderSave)) {
						echo 'if (($UnderBudget AND $OverSave) OR ($OverBudget AND $UnderSave))';
						echo $nl;
						echo "is true $nl";
						if ($ItemPlan[$STCurrent][$Fit[$i]] == "") {
							$ItemPlan[$STCurrent][$Fit[$i]] = 1;
						} else {
							$ItemPlan[$STCurrent][$Fit[$i]] += 1;
						}
						echo "ItemPlan for item {$Fit[$i]}:  " . $ItemPlan[$STCurrent][$Fit[$i]] . "$nl";
						$BudgetVariance += $SupposedVariance[$Fit[$i]];
						$Available -= $Item[$Fit[$i]];
						echo "Available is " .  $Available . $nl;
						$RecentlyBought = TRUE;
						$ItemsBoughtThisweek += 1;
						echo '$BudgetVariance += $SupposedVariance[$Fit[$i]];';
						echo $nl;
						echo  $BudgetVariance ;
						echo  " " . $SupposedVariance[$Fit[$i]] . $nl;

					}


				} else {
					echo 'if ($BudgetVariance <> 0)';
					echo $nl;
					echo "is not true $nl";
					if ($ItemPlan[$STCurrent][$Fit[$i]] == "") {
						$ItemPlan[$STCurrent][$Fit[$i]] = 1;
					} else {
						$ItemPlan[$STCurrent][$Fit[$i]] += 1;
					}
					echo $Fit[$i] . $nl;
					echo "ItemPlan for item {$Fit[$i]}:  " . $ItemPlan[$STCurrent][$Fit[$i]] . "$nl";
					
					$BudgetVariance += $SupposedVariance[$Fit[$i]];
					$Available -= $Item[$Fit[$i]];
					echo "Available is " .  $Available . $nl;
					$RecentlyBought = TRUE;
					$ItemsBoughtThisweek += 1;
					echo '$BudgetVariance += $SupposedVariance[$Fit[$i]];';
					echo $nl;
					echo  $BudgetVariance ;
					echo  " " . $SupposedVariance[$Fit[$i]] . $nl;
				}

			} else {
				echo "Cant afford item " . $i . $Item[$Fit[$i]] . $nl;
			}
			if ($RecentlyBought) {
				$i = $ItemTiers +1;

			} else {
				$i++;
			}
			

		}

		#if its not possible to buy the cheapest item with the remaining available
		if (($Available >= $Item[1]) AND ($ItemsBoughtThisweek > 0)) {
			#Dont increment STCurrent
			$NextWeek = FALSE;
			echo "Next week is FALSE $nl";
		} else {
			$STCurrent += 1;
			$NextWeek = TRUE;
			$WeeklyRemainder = $Available;
			echo "Next week is TRUE $nl";
		}
	}
	echo "ItemPlan[" . 0 . "][" . 1 . "]: " . $ItemPlan[0][1] . "$nl";
	echo "ItemPlan[" . 0 . "][" . 2 . "]: " . $ItemPlan[0][2] . "$nl";
	echo "ItemPlan[" . 0 . "][" . 3 . "]: " . $ItemPlan[0][3] . "$nl";

	echo "ItemPlan[" . 1 . "][" . 1 . "]: " . $ItemPlan[1][1] . "$nl";
	echo "ItemPlan[" . 1 . "][" . 2 . "]: " . $ItemPlan[1][2] . "$nl";
	echo "ItemPlan[" . 1 . "][" . 3 . "]: " . $ItemPlan[1][3] . "$nl";

	echo "ItemPlan[" . 2 . "][" . 1 . "]: " . $ItemPlan[2][1] . "$nl";
	echo "ItemPlan[" . 2 . "][" . 2 . "]: " . $ItemPlan[2][2] . "$nl";
	echo "ItemPlan[" . 2 . "][" . 3 . "]: " . $ItemPlan[2][3] . "$nl";

	$Sentence = " ";

	$ItemCounter = 0;
	$Itemor = FALSE;

	for ($i = 1; $i <= $ItemTiers; $i++) {
		 if (!(is_null($ItemPlan[0][$i]))) {
		 	$Itemor = TRUE;
		 	echo "Itemor is now TRUE $nl";
		 }	
	}
	$FirstTime = TRUE;
	
	if ($Itemor == TRUE) {
		$Sentence .= "You should get ";
		
		
		for ($i = $ItemTiers; $i >= 1; $i--) {
			if (!(is_null($ItemPlan[0][$i]))) {
				
				$Sentence .= " (" . strval($ItemPlan[0][$i]) . ")";
				$Sentence .= " ";
				$Sentence = $Sentence . (string)$Item[$i];
				$Sentence .= " piece(s)";
			}
			if (!(is_null($ItemPlan[0][$i-1]))) {
					$Sentence .= " and ";

				}
			
		}

	}
	
	$Sentence .= " this week";
	$Sentence .= ". With your Current project cap of $Cap, you will get your weapon in " . ($WeekCount -1) . " week(s) from now. "; 
	
	echo $Sentence;





?>