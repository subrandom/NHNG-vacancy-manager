<?php
session_start();
if ($_SESSION['permission']['UpdateDatabase'] && $_FILES['datafile'])
{
	require_once ('../common/dbQuery.php');
	echo '<blockquote class = "wide">';
	echo "Uploading data file...";
	
	//before we do anything else, we're going to upload the file to web storage
	$now = time();
	$target_path = "../vacancyImportFiles/";
	$target_path = $target_path . date("Ymd-Gis",$now).".csv";

	if(move_uploaded_file($_FILES['datafile']['tmp_name'], $target_path)) 
	{
		echo "done <br/>";
		
		echo "Backing up existing position data...";
		//next up, copy the existing database to a backup table
		$backupTable = "positions_".date("YmdGis",$now);
		$query = "CREATE TABLE ".$backupTable." SELECT * FROM positions";
		writeDB($query);
		echo "done <br />";
		
		//bring the file into the array
		//open the file
		echo "Converting file...";
		
		if (($file = fopen($target_path,"r")) !== FALSE)
		{
			//first, clear the existing incomingData table
			writeDB("TRUNCATE TABLE incomingData");
			
			//read the file and process
			while (!feof($file))
			{
				$row = fgetcsv($file,1000, ";");
				//trim and convert
				$row[0] = trim($row[0]);
				$row[1] = trim($row[1]);
				$row[2] = trim($row[2]);
				$row[3] = trim($row[3]);
				$row[4] = trim($row[4]);
				$row[5] = (int) trim($row[5]);
				$row[6] = trim($row[6]);
				$row[7] = trim($row[7]);
				
				if ($row[0] == "")
				{
					break;
				}
				
				//loop handles multiple identical positions; 999L and 125% have "0" in their
				//vacant field number, but it's really "1" so we bump that up to one 
				if ($row[5] == 0)
				{
					$row[5] = 1;
				}
				//now we loop through and we use the $count value to appent to the key to keep it unique
				for($count = 0; $count < $row[5]; $count++)
				{
					$mos = $row[0];				
					$rank = $row[1];
					
					$para = $row[2];
					//line needs to be at least 2 characters
					if (strlen($row[3]) < 2)
					{
						$row[3] = "0".$row[3];
					}
					$line = $row[3];
					$description = $row[4];
					$uic = $row[6];
					
					$OSP = "n";
					$excess = "n";
					//break out our 125% and 999L
					if ($row[7] == "125%OSP")
					{
						$OSP = "y";
					}
					if ($row[7] == "B9")
					{
						$excess = "y";
					}
					
					//create the ID
					$id = "{$row[6]}{$row[2]}{$row[3]}{$OSP}{$excess}{$count}";
					$id = str_replace(" ","",$id);
					
					//write to the incomingData table
					$query = "INSERT INTO incomingData (incomingID, incomingUIC, incomingPara, incomingLine, incomingMOS, incomingGrade, 
						incomingDescription, incomingIsOverstrength, incomingIsPendingLoss, incomingReconciled) VALUES
						 ('$id','$uic','$para','$line','$mos','$rank','$description','$OSP','$excess','n')";
					writeDB($query);
				}
			}
			fclose($file);
			echo "done <br />";
			
			//clear all reconciled & invalid flags in the positions table
			echo "Clear any existing reconciliation flags...";
			$query = "UPDATE positions SET positionReconciled = 'n', positionIsInvalid = 'n'";
			writeDB($query);
			echo "done <br />";
			
			//reconile 125% positions
			reconileOSP();
			
			//reconcile all other positions
			reconcileAllOthers();

			//reconcile the positions held by recruiters, NOT enlisted
			reconcileHeldPosition();

			//all positions that can be matched, are matched
			
			//flag unreconciled positions that are held by recruiters and NOT enlisted
			echo "Flagging HELD, UNRECONCILED positions as invalid....";
			$query = "UPDATE positions SET positionIsInvalid = 'y' WHERE positionIsHeld = 'y' AND positionReconciled = 'n'";
			writeDB($query);
			echo "done <br/>";
			
			//delete unreconciled positions that are not held by recruiters, or that are held and enlisted
			echo "Deleting unreconciled positions that are un-held...";
			$query = "DELETE FROM positions WHERE (positionReconciled = 'n' AND positionIsHeld = 'n')";
			writeDB($query);
			echo "done <br/>";

			//add unreconciled imports as new items
			echo "Adding new positions from imported data...";
			$query = "INSERT INTO positions (positionID, positionUIC, positionPara, positionLine, positionMOS, positionGrade, positionDescription, positionIsOverstrength, positionIsPendingLoss, positionReconciled)
				SELECT * FROM incomingData WHERE incomingReconciled = 'n'";	
			writeDB($query);
			echo "done <br/>";
			echo "</blockquote>";
			//update transaction DB
			$transuser = $_SESSION['userLoginName'];
			$transdetail = "UPDATEDB//imported vacancy data, file: ".$target_path;
			writeTrans($transuser, $transdetail);
			
			return true;
		}
		else
		{
			echo "File could not be opened/read. Contact support. <br/>";
		}
		
	} 
	else
	{
	?>
		<script type="text/javascript">
		alert("There was an error uploading the file. Please contact support.");
		</script>
	<?php
		return false;
	}	
}
else
{
	echo "A serious error has occurred. Please contact support.";
}

function reconileOSP()
{
	echo "Reconciling 125% OSP...";
	//first, set the reconciled flag on all OSPs that exactly match an incoming OSP
	$query = "UPDATE positions, incomingData SET positionReconciled = 'y', incomingReconciled = 'y' 
		WHERE (incomingID = positionID AND (positionIsOverstrength = 'y' and positionReconciled = 'n' and incomingReconciled = 'n'))";
	writeDB($query);
	
	echo "done <br />";
	
}

function reconcileHeldPosition()
{
	//first we will reconcile held positions that are not showing "enlisted" against their exact match
	//any that don't reconcile, we will attempt to reconcile against the same uic/para/line ignoring the key and
	//whether or not it's 999L. 
	echo "Reconciling positions HELD NOT ENLISTED...";
	$query = "UPDATE positions, incomingData SET positionReconciled = 'y', incomingReconciled = 'y'
		WHERE (incomingID = positionID AND (positionIsHeld = 'y' AND positionIsHeldEnlisted = 'n' AND positionReconciled = 'n' AND incomingReconciled = 'n'))";
	writeDB($query);
	
	echo "PASS 1...done...PASS 2...reconciling held positions that were unmatched...";
	
	//alright. If we have held positions that were unmatched, we have to do this the really hard way.
	//Look at every held unmatched position, get the part of it's ID that covers UIC/Para/Line and compare it to incoming
	//data. We're looking for another identical position, but with a different unique key that is still vacant.

	$query = "SELECT positionID FROM positions WHERE (positionIsHeld = 'y' AND positionIsHeldEnlisted = 'n' AND positionReconciled = 'n')";
	$result = readDB($query);
	while ($potentialInvalid = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//use just the slot data from the key, no unique bit
		$searchForPosition = substr($potentialInvalid['positionID'],0, -2)."__";
		
		//check that against the import data, looking for a match with a different unique bit
		$query = "SELECT incomingID FROM incomingData WHERE incomingReconciled = 'n' AND incomingID LIKE '$searchForPosition'";
		$result = readDB($query);
		$potentialMatch = mysql_fetch_array($result,MYSQL_ASSOC);
		if($potentialMatch['incomingID'] == "")
		{
			//no match!
			continue;
		}
		else
		{
			//found match!
			$matchID = $potentialMatch['incomingID'];
			$invalidID = $potentialInvalid['positionID'];
			
			//update the existing position to reflect the found ID
			$query = "UPDATE positions SET positionID = '$matchID', positionReconciled = 'y' WHERE positionID = '$invalidID'";
			echo PHP_EOL.$query.PHP_EOL;
			writeDB($query);
			
			//mark incoming reconciled
			$query = "UPDATE incomingData SET incomingReconciled = 'y' WHERE incomingID = '$matchID'";
			writeDB($query);
			echo PHP_EOL.$query.PHP_EOL;
		}
		
	}
	echo "done <br/>";
}

function reconcileAllOthers()
{
	//this is our last reconcile pass, any positions that we haven't covered, i.e. hard slots that aren't held for anyone
	//straight up exact match. Anything not matched here will be either a new position (in importedData) or a filled/invalid
	//position (in 'positions').
	echo "Reconciling remaining positions...";
	$query = "UPDATE positions, incomingData SET positionReconciled = 'y', incomingReconciled = 'y' 
		WHERE (incomingID = positionID AND (positionReconciled = 'n' and incomingReconciled = 'n'))";
	writeDB($query);
	echo "done <br/>";
}
?>
