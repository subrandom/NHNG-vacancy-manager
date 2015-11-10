<?php
session_start();

if (!$_SESSION['permission']['UnitLocking'])
{
	require_once('../common/forbidden.html');
} 
else 
{
	require_once('../common/dbQuery.php');

	//read vacancy table to find out if the position is currently held
	$positionID = $_GET['id'];
	$query="SELECT * FROM positions WHERE positionID = '$positionID'";
	$result = readDB($query);
	$position = mysql_fetch_array($result, MYSQL_ASSOC);
	$myUID = $_SESSION['userID'];


	if ($position['positionIsLockedByID'] < 0) //check if it's locked by an admin. That's a no-go
	{
		echo "<h5>This position is locked by a recruiting administrator.<br/> You may not unlock it.</h5>";
	}
	//if the position is locked by the unit, unlock it
	else if ($position['positionIsLocked'] == "y")
	{
		$query = "UPDATE positions SET positionIsLocked = 'n', positionIsLockedByID = '0', positionLockedDate = null,
		positionIsHeldNotes = null WHERE positionID = '$positionID'";
		writeDB($query);
	
		//write the transaction to the log
		$transuser = $_SESSION['userLoginName'];
		$transdetail =  "POSITION UNLOCK (UNIT)//Position ID:".$positionID;
		writeTrans($transuser, $transdetail);

		echo "<h5>Position Unlocked.</h5>";
		
	}
	else if ($position['positionIsHeld'] == "y") 
	{ 
		//the position is held by recruiting, so it can't be locked by the unit
			echo "<h5>This position is reserved for a pending enlistment. <br/>You may not lock it. 
			<br/>If there is an issue with the position, contact recruiting operations for assistance.</h5>";
	} 
	else 
	{ 
	//the position is not held at all, enter a hold by the unit
		$lockedDate = date('Y-m-d');
		$query = "UPDATE positions SET positionIsLocked = 'y', positionIsLockedByID = '$myUID', positionLockedDate = '$lockedDate' WHERE positionID = '$positionID'";
		writeDB($query);
	//write the transaction to the log
		$transuser = $_SESSION['userLoginName'];
		$transdetail =  "POSITION LOCK (UNIT)//Position ID:".$positionID;
		writeTrans($transuser, $transdetail);
	}
}
?>
<script type = "text/javascript">
	$('#bottompanel').load('showPositions.php');
	setHeight();
	
	function setHeight() 
	{
		var WinHt = $(window).height();
		var TopHt = $('#toppanel').height();
		var BotHt = WinHt - (TopHt + 195);
		$('#bottompanel').css({"height":BotHt});
	}
</script>
	