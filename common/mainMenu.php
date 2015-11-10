<?php 
	
	$baseURL = substr($_SERVER["HTTP_REFERER"],0,31);

	if($_SESSION['permission']['UnitLocking'])
	{
		echo '<a class="button" href = "'.$baseURL.'/unitLocking/index.php">Manage Positions</a>';
	}
	if($_SESSION['permission']['ManagePositions'] || $_SESSION['permission']['TeamLeader'] || $_SESSION['userRecruiterRSID'] != "" || $_SESSION['permission']['SuperViewer'])
	{
		echo '<a class="button" href = "'.$baseURL.'/managePositions/index.php">Manage Positions</a>';
	}
	if($_SESSION['permission']['ManageUsers'])
	{
		echo '<a class="button" href = "'.$baseURL.'/manageUsers/index.php">Manage Users</a>';
		echo '<a class="button" href = "'.$baseURL.'/manageRecruiters/index.php">Manage RRNCOs</a>';
	}
	if($_SESSION['permission']['UpdateDatabase'])
	{
		echo '<a class="button" href = "'.$baseURL.'/manageUnits/index.php">Manage Units</a>';
		echo '<a class="button" href = "'.$baseURL.'/importData/index.php">Import Data</a>';
	}
	if($_SESSION['permission']['ViewTransactions'])
	{
		echo '<a class="button" href = "'.$baseURL.'/viewTransactions/index.php">View Log</a>';
	}
	if (isset($_SESSION['userID']))
	{
		echo '<a class="button" href = "'.$baseURL.'/index.php">Home Screen</a>';	
		echo '<a class="button" href = "'.$baseURL.'/userServices/frmChangePwd.php">Change Password</a>';
		echo '<a class="button" href = "'.$baseURL.'/common/doLogout.php">Log Off</a>';
	} 
	if (!isset($_SESSION['userID']))
	{
		echo '<h4>You must log in to see the menu</h4>';
	}
?>	