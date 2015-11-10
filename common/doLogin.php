<?php
if (substr($_SERVER["HTTP_REFERER"],0,23) == "https://www.ngapps.net/") 
{
	require_once ('../common/dbQuery.php');
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	$username = addslashes($_POST['username']);
	$password = md5($_POST['password']);
	$username = strtolower($username);
	$query = "SELECT * FROM vwUsersLJRecruiters WHERE userPassword='$password' AND userLoginName='$username'";

	$result = readDB($query);
	$user = mysql_fetch_array($result,MYSQL_ASSOC);

	if (!$user['userID'])
	{
		//user failed the login
		//update transaction database
		$transuser = $username;
		$transdetail = "FAILED LOGIN//using password: ".$_POST['password'];
		writeTrans($transuser, $transdetail);
		echo '<p = class="redtext">Incorrect credentials. Please try again<br>or contact support.</p>';
	}
	else
	{
		//user successfully logged in
		session_start(); 
		//get user data for the session
		$_SESSION['userID'] = $user['userID'];
		$_SESSION['userLastName'] = $user['userLastName'];
		$_SESSION['userFullName'] = $user['userFirstName']." ".$user['userLastName'];
		$_SESSION['userLoginName'] = $user['userLoginName'];
		$_SESSION['userRecruiterRSID'] = $user['recruiterRSID'];
		$_SESSION['userRecruiterID'] = $user['recruiterID'];
		
		//get user permissions
		$_SESSION['permission']['ManageUsers'] = ($user['userCanManageUsers'] == "Y" ? "Y" : null);
		$_SESSION['permission']['ManagePositions'] = ($user['userCanManagePositions'] == "Y" ? "Y" : null);
		$_SESSION['permission']['UpdateDatabase'] = ($user['userCanUpdateDatabase'] == "Y" ? "Y" :null);
		$_SESSION['permission']['ViewTransactions'] = ($user['userCanViewTransactions'] == "Y" ? "Y" : null);
		$_SESSION['permission']['ClearTransactions'] = ($user['userCanClearTransactions'] == "Y" ? "Y" : null);
		$_SESSION['permission']['UnitLocking'] = ($user['userIsUnitUser'] == "Y" ? "Y" : null);
		$_SESSION['permission']['TeamLeader'] = ($user['userIsTeamLeader'] == "Y" ? "Y" : null);
		$_SESSION['permission']['SuperViewer'] = ($user['userIsSuperViewer'] == "Y" ? "Y" :null);
		
		//is the user a unit?
		$_SESSION['userUIC'] = $user['userUIC']; 
		
		session_commit();
		?>
		<script type="text/javascript">
		window.setTimeout('location.replace("../index.php")');
		</script>
		<?
	}
}
else
{
	echo "<h2>ERROR. This page cannot be accessed directly.</h2>";
	echo substr(getenv("HTTP_REFERER"),0,23);
}?>