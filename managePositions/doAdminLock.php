<?php
session_start();
//permission check
if (!$_SESSION['permission']['ManagePositions'])
{
	echo "<br/><h4>There has been a serious error. Please call support @x1288</h4>";
}
else
{
	require_once ('../common/dbQuery.php');
	
	//get the position data, regardless of the transaction type
	$positionID = $_POST['positionID'];
	
	//check to see what we're doing
	if ($_POST['transType'] == "lock")
	{
		$adminNote = $_POST['heldNote']; //get the user created note
		
		if($adminNote == "") //can't be blank
		{
			echo "<h5>You did not supply the required note. The position was not locked.</h5>";
		}
		else
		{
			//go ahead and lock the position
			$positionLockedBy = $_SESSION['userID'] * -1; //using the generic "a" for all admins so we can unlock each other
			$lockedDate = date('Y-m-d');
			
			$query = "UPDATE positions SET positionIsLocked = 'y', positionIsLockedByID = '$positionLockedBy', positionLockedDate = '$lockedDate', 
				positionIsHeldNotes = '$adminNote' WHERE positionID = '$positionID'";
			
			writeDB($query);
			
			//write the transaction to the log
			$transuser = $_SESSION['userLoginName'];
			$transdetail =  "POSITION LOCK (ADMIN)//Position ID:".$positionID;
			writeTrans($transuser, $transdetail);

		}
	}
	else if ($_POST['transType'] == "unlock")
	{
		$query = "UPDATE positions SET positionIsLocked = 'n', positionIsLockedByID = '0', positionLockedDate = null, 
			positionIsHeldNotes = null WHERE positionID = '$positionID'";
		writeDB($query);
	
		//write the transaction to the log
		$transuser = $_SESSION['userLoginName'];
		$transdetail =  "POSITION UNLOCK (ADMIN)//Position ID:".$id;
		writeTrans($transuser, $transdetail);

	}
	else
	{
		echo "<h5>An unhandeled exception occurred. Please contact the database administrator.</h5>";
	}
	
}
?>	