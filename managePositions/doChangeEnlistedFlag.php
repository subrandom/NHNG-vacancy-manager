<?php
	session_start();
	//this function changes the waiver flag from Y to N or vice-versa
	//the change is immediate, there is no confirmation, just a popup when changed.
if (($_SESSION['permission']['ManagePositions'] || $_SESSION['permission']['TeamLeader']) && $_GET['id'])
{	
	require ('../common/dbQuery.php');
	$positionID = $_GET['id'];
	
	//get the current off limits status
	$query = "SELECT positions.positionIsHeldEnlisted FROM positions WHERE positionID = '$positionID'";
	$result = readDB($query);
	$row = mysql_fetch_assoc($result);

	//flip it
	$reservationIsEnlisted = ($row['positionIsHeldEnlisted'] == "y" ? "n" : "y");
	
	//write it
	$query = "UPDATE positions SET positionIsHeldEnlisted = '$reservationIsEnlisted' WHERE positionID = '$positionID'";
	writeDB($query);
		
	//update transactions
	$transdetail = "POSITION UPDATE//ID:".$positionID." set to ENLISTED: ".$reservationIsWaiver;
	$transuser = $_SESSION['userLoginName'];
	writeTrans($transuser, $transdetail);	
	?>
	<script type="text/javascript">
		$('#bottompanel').load('listPositions.php');
	</script>
		<?	
}
else
{
	echo "Something is wrong. Call Support.";
}
	?>