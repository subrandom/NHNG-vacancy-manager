<?php
session_start();
if ($_POST['id'] && ($_SESSION['permission']['ManagePositions']))
{
	require ('../common/dbQuery.php');
	$positionID = $_POST['id'];
	$positionNotes = $_POST['notes'];

	//clear POST
	unset ($_POST['id']);
	unset ($_POST['notes']);

	//update position table
	$query = "UPDATE positions SET positionIsHeldNotes=\"$positionNotes\""." WHERE positionID=\"$positionID\"";
	writeDB($query);


	//write transaction data
	$transdetail =  "RESERVATION UPDATE//posID: ".$id." ## notes: ".$notes;
	$transuser = $_SESSION['userLoginName'];
	writeTrans($transuser, $transdetail);	
	
	echo "<br /><h4>Note updated successfully.</h4>";
	return true;
}
else
{
	echo "<p>There has been a serious error. Please return to the main menu and call support.</p>";
	echo '<p><a href="http://ngapps.net/index.php">Main Menu</a></p>';
}
?>