<?php
	session_start();
	//this function changes off limits from Y to N or vice-versa
	//the change is immediate, there is no confirmation, just a popup when changed.
if ($_SESSION['permission']['UpdateDatabase'] && $_GET['id'])
{	
	require ('../common/dbQuery.php');
	$unitUIC = $_GET['id'];
	
	//get the current off limits status
	$query = "SELECT units.unitOffLimits FROM units WHERE unitUIC = '$unitUIC'";
	$result = readDB($query);
	$row = mysql_fetch_assoc($result);

	//flip it
	$unitOffLimits = ($row['unitOffLimits'] == "y" ? "n" : "y");
	
	//write it
	$query = "UPDATE units SET unitOffLimits = '$unitOffLimits' WHERE unitUIC = '$unitUIC'";
	writeDB($query);
		
	//update transactions
	$transdetail = "UNIT UPDATE//UNIT:".$unitUIC." set to OFF-LIMITS: ".$unitOffLimits;
	$transuser = $_SESSION['userLoginName'];
	writeTrans($transuser, $transdetail);	
	?>
	<script type="text/javascript">
		$('#bottompanel').load('unitList.php');
	</script>
	
		<?	
}
else
{
	echo "Something is wrong. Call Support.";
}
	?>