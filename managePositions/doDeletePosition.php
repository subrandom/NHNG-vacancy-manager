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
	
	$query = "DELETE FROM positions WHERE positionID = \"$positionID\"";
	writeDB($query);
	
	//update transaction database
	$transuser = $_SESSION['userLoginName'];
	$transdetail = "DELETE NON-VACANT POSITION//id: ".$_POST['positionID'];
	writeTrans($transuser, $transdetail);
}
?>
