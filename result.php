<?php
	$CurrFile = "result.php";
	@require_once('includes/verify.php');
	if (isset($_POST['submit'])) {
		$CharName = $_POST['charname'];
		$Current = $_POST['Current'];
		$WeeklyEarned = $_POST['WeeklyEarned'];
		$WeeklyCap = $_POST['WeeklyCap'];
		$Season = $_POST['Season'];
		$Cap = $_POST['Cap'];
	}

	
	

	#here we make sure that there are some reasonable values and if not we send the user
	#to a page where we ask them politely to get real
	$GetReal = FALSE;
	if (($Current > 15000) OR ($Current < 0) OR (empty($Current))) {
		$GetReal = TRUE;
	}
	if (($WeeklyEarned > $WeeklyCap) OR ($WeeklyEarned < 0) OR (empty($WeeklyEarned))) {
		$GetReal = TRUE;
	}
	if (($WeeklyCap > 30000) OR ($WeeklyCap < 0) OR (empty($WeeklyCap))) {
		$GetReal = TRUE;

	}
	if (($Season > 50000) OR ($Season < 0) OR (empty($Season))) {
		$GetReal = TRUE;
	}
	if (($Cap > 10000) OR ($Cap < 0) OR (empty($Cap))) {
		$GetReal = TRUE;
	}
	if ($GetReal) {
		header('Location: getreal.php');
		exit();
	}

	$nl = "<br>\n";
	$DebugString = "";
	$Debug = FALSE;

		
	#current item tiers and their cost on live
	$Item[0] = 0;
	$Item[1] = 1250;
	$Item[2] = 1750;
	$Item[3] = 2250;
	$ItemTiers = 3;
	$WeapReq = 7250;
	$WeapCost = 3500;
	
	
	

 	

	

	#this while loop is a sort of simulation of the coming weeks and 
	#basicly calculates what week our season total will reach what is required for weapon 
	#Usually (7250)
	if ($WeeklyEarned < $WeeklyCap) {
		$NotCapped = TRUE;
	} else {
		$NotCapped = FALSE;
	}

	$RemainingTotal = $WeapReq - $Season;
	$Remaining[0] = $RemainingTotal;
	$WeekCount = 0;
	$STweek[0] = 0;
	#--------------------------------------------------------------------------------
	while ($RemainingTotal > 0) {
		
		if ($NotCapped == TRUE) {
			$Remaining[0] -= ($WeeklyCap - $WeeklyEarned);
			$RemainingTotal -=  ($WeeklyCap - $WeeklyEarned);
			$STweek[0] += ($Season + ($WeeklyCap - $WeeklyEarned));
			$NotCapped = FALSE;
		} else {
			if ($WeekCount > 0) {
				$Remaining[$WeekCount] = ($Remaining[($WeekCount-1)] - $Cap);
				$RemainingTotal -= $Cap;
				$STweek[$WeekCount] = ($STweek[($WeekCount -1)] + $Cap);
			} else {
				$STweek[$WeekCount] += $Season;
				

			}
		}
		++ $WeekCount;
		
	}

	

	#Now we have to figure out how much the user has already spent
	$Spent = ($Season - $Current);
	#Now we have 

	$Needed = $WeapCost - $Cap;
	$Room = (($STweek[$WeekCount -2] - $Needed) - $Spent);

	$DebugString .= "Spent $Spent $nl Needed $Needed $nl Room $Room $nl";
	$STRoom = $Room;

	
	
	#Making sure we dont divide by 0
	if ($WeekCount > 1) {
		#This variable is how much we will aim to have a surplus each week in order to have 
		#a total of $Needed by weapon week
		$AverageSavingsWanted = ($Needed/($WeekCount -1));
	}
	#----------------------------------------------------------------------------------

	$WeeklyRemainder = 0;
	$BudgetVariance = 0;
	$ItemPlan = array(array());
	$WeeksTotal = count($STweek);
	$STCurrent = 0;
	$NextWeek = TRUE;

	while ($STCurrent < $WeeksTotal) {
		
		$PrepWeek = ($STCurrent == ($WeekCount-2));
		$WeapWeek = ($STCurrent == ($WeekCount-1));
			
	
		
		#time to calculate what is available to spend on current week
		#if we are in our first week
		if ($STCurrent == 0) {
			if ($NextWeek == TRUE) {
				$Available = (($WeeklyCap - $WeeklyEarned) + $Current);
			}
		} else { #we are not in our first week
			#If we can buy multiple items in a week $STCurrent is not incremented and
			#$NextWeek will be false at this point meaninging we dont add on next weeks cap to
			# $Available 
			if ($NextWeek == TRUE) { 
				$Available = ($Cap + $WeeklyRemainder);
			}
			
		}
		#the item buying for the final prep week is much simpler
		# you know exactly how much you have to save and thus you know exactly how
		#much you can spend
		if (($PrepWeek) AND !($WeapWeek)) {
			
			$Available = $Available - $Needed;
			$DebugString .= "New Available: " . $Available .$nl;
			$j = $ItemTiers;
			while ($j >= 1) {
				$RecentlyBought = FALSE;
				if ($Available >= $Item[$j]) {
					if (!(isset($ItemPlan[$STCurrent][$Fit[$j]]))) {
						$ItemPlan[$STCurrent][$j] = 1;
					} else {
						$ItemPlan[$STCurrent][$j] += 1;
					}
					$Available -= $Item[$j];
					$DebugString .= "Available is " .  $Available . $nl;
					$RecentlyBought = TRUE;
					
				}
				if ($RecentlyBought) {
					#Dont increment j
				} else {
					$j --;
				}
			}
		}
		
		
		#Data gathering loop
		$i = $ItemTiers;
		$SupposedWeeklyRemainder = array();
		
		$SupposedVariance = array();

		while (($i >= 1) AND !($PrepWeek) AND !($WeapWeek)) {
			#We figure out how much we would have left this week after buying this item
			$SupposedWeeklyRemainder[$i] = ($Available - $Item[$i]);
			$SupposedVariance[$i] = ($AverageSavingsWanted - $SupposedWeeklyRemainder[$i]);
			
			$i--;
		}

		#Fill Variance array to be sorted
		for ($i = 1; $i <= $ItemTiers; $i++) {
			$Fit[$i] = $i;
		}
		

		#Variance rating Loop just a bubble sort

		if (!($PrepWeek) AND !($WeapWeek)) {
			do {
				
				$Swap = FALSE;
				$Set = 0;
				for ($i = 2; $i <= ($ItemTiers); $i ++) {
					if (!($SupposedVariance[$Fit[$i]] === "")){
						if (abs($SupposedVariance[$Fit[$i-1]]) > abs($SupposedVariance[$Fit[$i]])) {
							#swap them and remember we swapped
							$temp = $Fit[$i];
							$Fit[$i] = $Fit[$i-1];
							$Fit[$i-1] = $temp;
							
#							$Swap = TRUE;
						} else {
							#Don't swap them and remember we didn't swap
							$Set += 1;
						}
						if (abs($SupposedVariance[$Fit[$i-1]]) == abs($SupposedVariance[$Fit[$i]])) {
							$Swap = FALSE;
						}
					} else {
						#one of the elements is null which means we've reached the end of the array
						$DebugString .= "Here is the site of the 2600 crash. $nl";
						$DebugString .= 'SupposedVariance[$Fit[$i]] :';
						$DebugString .= $SupposedVariance[$Fit[$i]] . $nl;
						$DebugString .= '$Fit[$i]: ';
						$DebugString .= $Fit[$i] . $nl;
						$DebugString .= '$i: ';
						$DebugString .= $i . $nl;

						$Swap = TRUE;
					}
				}
				if ($Set == ($ItemTiers-1)) $Swap = FALSE;
			} while (($Swap == TRUE));
			$DebugString .= "The Sorted Rating array $nl";
		$DebugString .= "Fit 1 {$Fit[1]} which is" . $Item[$Fit[1]].  " Which has " . $SupposedVariance[$Fit[1]] . " Variance $nl";
		$DebugString .= "Fit 2 {$Fit[2]} which is" . $Item[$Fit[2]]. "  Which has " . $SupposedVariance[$Fit[2]] . " Variance $nl";
		$DebugString .= "Fit 3 {$Fit[3]} which is" . $Item[$Fit[3]]. "  Which has " . $SupposedVariance[$Fit[3]] . " Variance  $nl";
		}


		

		#Buy Decision Loop
		$i = 1;
		$ItemsBoughtThisweek = 0;
		while  (($i <= $ItemTiers) AND !($PrepWeek) AND !($WeapWeek))   {
			$DebugString .= "Entered Buy decision loop in week $STCurrent $nl";
			$DebugString .= "$Available $nl";
			$RecentlyBought = FALSE;
			if ($Item[$Fit[$i]] <= $Available) {
				$DebugString .= "we have enough $nl";
				$DebugString .= "AverageSavingsWanted: " .  $AverageSavingsWanted . $nl;
				if ($BudgetVariance <> 0){
					$DebugString .= "BudgetVariance is not 0 $nl";
					$DebugString .= "Budget Variance:" .$BudgetVariance .$nl;
					#Boolean values to make following decision easier to see
					$UnderBudget = ($BudgetVariance < 0); #If BudgetVariance is a positive number then we need to over save this week
					$DebugString .= "this should be true: " . $UnderBudget . $nl;
					$OverBudget = ($BudgetVariance > 0); #If BudgetVariance is a negative number then we need to under save this week
					
					$OverSave =  ($SupposedWeeklyRemainder[$Fit[$i]] > $AverageSavingsWanted);
					$UnderSave = ($SupposedWeeklyRemainder[$Fit[$i]] < $AverageSavingsWanted);
					$DebugString .= "OverSave" . $OverSave .$nl;
					$DebugString .= "Supposed variance for current item(" . $Item[$Fit[$i]] . "): " . $SupposedVariance[$Fit[$i]] .$nl;
					$DebugString .= "AverageSavingsWanted: " .  $AverageSavingsWanted . $nl;
					if (($UnderBudget AND $OverSave) OR ($OverBudget AND $UnderSave)) {
						$DebugString .= 'if (($UnderBudget AND $OverSave) OR ($OverBudget AND $UnderSave))';
						$DebugString .= $nl;
						$DebugString .= "is true $nl";
						if (!(isset($ItemPlan[$STCurrent][$Fit[$i]]))) {
							$ItemPlan[$STCurrent][$Fit[$i]] = 1;
						} else {
							$ItemPlan[$STCurrent][$Fit[$i]] += 1;
						}
						$DebugString .= "ItemPlan for item {$Fit[$i]}:  " . $ItemPlan[$STCurrent][$Fit[$i]] . "$nl";
						$BudgetVariance += $SupposedVariance[$Fit[$i]];
						$Available -= $Item[$Fit[$i]];
						$DebugString .= "Available is " .  $Available . $nl;
						$RecentlyBought = TRUE;
						$ItemsBoughtThisweek += 1;
						$DebugString .= '$BudgetVariance += $SupposedVariance[$Fit[$i]];';
						$DebugString .= $nl;
						$DebugString .=  $BudgetVariance ;
						$DebugString .=  " " . $SupposedVariance[$Fit[$i]] . $nl;

					}


				} else {
					$DebugString .= 'if ($BudgetVariance <> 0)';
					$DebugString .= $nl;
					$DebugString .= "is not true $nl";
					if (!(isset($ItemPlan[$STCurrent][$Fit[$i]]))) {
						$ItemPlan[$STCurrent][$Fit[$i]] = 1;
					} else {
						$ItemPlan[$STCurrent][$Fit[$i]] += 1;
					}
					$DebugString .= $Fit[$i] . $nl;
					$DebugString .= "ItemPlan for item {$Fit[$i]}:  " . $ItemPlan[$STCurrent][$Fit[$i]] . "$nl";
					
					$BudgetVariance += $SupposedVariance[$Fit[$i]];
					$Available -= $Item[$Fit[$i]];
					$DebugString .= "Available is " .  $Available . $nl;
					$RecentlyBought = TRUE;
					$ItemsBoughtThisweek += 1;
					$DebugString .= '$BudgetVariance += $SupposedVariance[$Fit[$i]];';
					$DebugString .= $nl;
					$DebugString .=  $BudgetVariance ;
					$DebugString .=  " " . $SupposedVariance[$Fit[$i]] . $nl;
				}

			} else {
				$DebugString .= "Cant afford item " . $i . $Item[$Fit[$i]] . $nl;
			}
			if ($RecentlyBought) {
				$i = $ItemTiers +1;

			} else {
				$i++;
			}
			

		}

		#if its not possible to buy the cheapest item with the remaining available
		if (($Available >= $Item[1])  AND ($ItemsBoughtThisweek > 0)) {
			#Dont increment STCurrent
			$NextWeek = FALSE;
			$DebugString .= "Next week is FALSE $nl";
		} else {
			$STCurrent += 1;
			$NextWeek = TRUE;
			$WeeklyRemainder = $Available;
			$DebugString .= "Next week is TRUE $nl";
		}
	}
	
	#Loop for generating debug output
	for ($i = 0; $i < $WeekCount; $i++) {
		for ($j = 1; $j <= $ItemTiers; $j++) {
			if (isset($ItemPlan[$i][$j])) {
				$DebugString .= "ItemPlan[" . $i . "][" . $j . "]: " . $ItemPlan[$i][$j] . "$nl";
			} else {
				$DebugString .= "ItemPlan[" . $i . "][" . $j . "]: " . 0 . "$nl";
			}
		}
	}

	$Sentence = " ";
	$Sentencem = "";

	$ItemCounter = 0;
	$Itemor = FALSE;

	for ($i = 1; $i <= $ItemTiers; $i++) {
		 if ((isset($ItemPlan[0][$i]))) {
		 	$Itemor = TRUE;
		 	$DebugString .= "Itemor is now TRUE $nl";
		 }	
	}
	$FirstTime = TRUE;
	
	if ($Itemor == TRUE) {
		$Sentence .= "You should get ";
		
		
		for ($i = $ItemTiers; $i >= 1; $i--) {
			if ((isset($ItemPlan[0][$i]))) {
				
				$Sentence .= " (" . strval($ItemPlan[0][$i]) . ")";
				$Sentence .= " ";
				$Sentence = $Sentence . (string)$Item[$i];
				$Sentence .= " piece(s)";
			}
			if ((isset($ItemPlan[0][$i-1]))  AND ($i <> $ItemTiers)) {
					$Sentence .= " and ";

				}
			
		}
	$Sentence .= " this week. $nl";

	} else {
		$Sentence .= "You cannot get another item this week. $nl";
	}
	
	
	$Sentence .= " $nl With your Current projected cap of $Cap, you will get your weapon in " . ($WeekCount -1) . " week(s) from now. "; 
	$Sentencem .= " You will have to set aside $Needed Conquest points the week before you get a weapon. $nl";
	$Sentencem .= " You can spend $Room before you have to start saving. $nl";
	
	
	
	if ($Debug) {
		echo $DebugString . $nl;
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
				<?php @include_once('resultfills/itemplanfill.php'); ?>

			</div>

			<div id='formwrapper'>
				<div id='answer'>
					<p><?php echo $Sentence; ?></p>
					<p id='minoranswer'><?php echo $Sentencem; ?><p>
				</div>
			</div>
			<div id='divider'>

			</div>
		</div>
			
	</body>

</html>
