<?php
session_start();
if ($_SESSION['permission']['ManageUsers']!=NULL)
{
	require ('../common/dbQuery.php');
		
	$username = $_POST['userLoginName'];
	$username = strtolower($username);
	$password = $_POST['userPassword'];
	$passwordMD5 = md5($_POST['userPassword']);
	$confirm_password = $_POST['confirm_password'];
	$errorList = null;
	$userType = $_POST['userGroup'];
	$firstname = $_POST['userFirstName'];
	$lastname = $_POST['userLastName'];
	$rsid = null;	
	$uic = null;
	$recruiterID = null;
	
	//set all permissions to "no" for starters, we'll collect them in a bit
	$permManagePositions = "N";
	$permUpdateDatabase = "N";
	$permViewTransactions = "N";
	$permClearTransactions = "N";
	$permManagerView = "N";
	$permManageUsers = "N";
	$permUnitUser = "N";
	$permTeamLeader = "N";
	
	
	//first, some sanity checks. Are all the basic fields filled out?

	//does the username already exist? They must be unique
	$query = "SELECT * from users WHERE userLoginName = '$username'";
	$result = readDB($query);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	if ($row != null)
	{ 
		$errorList.= "<li>The username you chose is already in use. Please choose another.</li>";
	}	
	
	//is the username long enough?
	if (strlen($username) < 5)
	{
		$errorList = $errorList."<li>Username cannot be blank and must be more than 5 characters.</li>";
	}
	
	//how about the password?
	if (strlen($password) < 8)
	{
		$errorList.= "<li>Password must be 8 characters or more.</li>";
	}
	
	//does the confirmation password match?
	if ($password != $confirm_password)
	{
		$errorList.="<li>The password and password confirmation must match.</li>";
	}
	
	//If we are creating an external user or RRBN user, we need first and last name. RRNCO users get theirs auto-filled from the
	//recruiter table
	
	if ($userType == "rrbn" || $userType == "unit")
	{
		if (strlen($lastname) < 2)
		{
			$errorList.="<li>User last name must be at least 2 characters.</li>";
		}
			if (strlen($firstname) < 3)
		{
			$errorList.="<li>User first name must be greater than 3 characters.</li>";
		}
	}
	
	//If we are creating an RRNCO
	if ($userType == "rrnco")
	{
		//check to make sure the RSID is selected
		$rsid = $_POST['rsid'];
		if ($rsid == null)
		{
			//error if not
			$errorList.="<li>You must select an RSID for this RRNCO.</li>";
		}
		else
		{
			//otherwise retreive the first and last names and the recruiterID from the recruiter table
			//the recruiterID is the primary key and will stay with the user even if the RSID changes
			$query = "SELECT * from recruiters WHERE recruiterRSID = '$rsid'";
			$result = readDB($query);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$firstname = $row['recruiterFirstName'];
			$lastname = $row['recruiterLastName'];
			$recruiterID = $row['recruiterID'];
			$permTeamLeader = ($_POST['teamLeader']=="on" ? "Y":"N");			
		}
		
	}
	
	//If creating a unit user
	if ($userType == "unit")
	{
		if ($_POST['unitUIC'] == null)
		{			
			$errorList.= "<li>You must select a UIC for this user.</li>";
		}
		else
		{
			$uic = $_POST['unitUIC'];
			$permUnitUser = "Y";
			
		}
	}
	
	//for a regular user, RRBN HQ or whatever, we need to grab permissions
	if ($userType == "rrbn")
	{
		$recruiterID = 0;
		$permClearTransactions = ($_POST['clearTransactions'] == "on" ? "Y" : "N");
		$permViewTransactions = ($_POST['viewTransactions'] == "on" ? "Y" : "N");
		$permManagePositions = ($_POST['managePositions'] == "on" ? "Y" : "N");
		$permManagerView = ($_POST['managerView'] == "on" ? "Y" : "N");
		$permManageUsers = ($_POST['manageUsers'] == "on" ? "Y" : "N");
		$permUpdateDatabase = ($_POST['updateDatabase'] == "on" ? "Y" : "N");
		$permUnitUser = "N";
		$permTeamLeader = "N";
	}

	if(strlen($errorList) != 0)
	{
		echo "<br><p style=\"color:red\">Please correct the following errors and submit the request again:</p>";
		echo "<ul>".$errorList."</ul>";
	}
	else
	{
		//add record to the user table
		$userType = strtoupper($userType);
		
		$query = "INSERT INTO users (userType, userFirstName, userLastName, userLoginName, userPassword, userUIC, userRecruiterID,
		userCanManagePositions, userCanUpdateDatabase, userCanViewTransactions, userCanClearTransactions, userIsSuperviewer, userCanManageUsers,
		userIsUnitUser, userIsTeamLeader) VALUES ('$userType','$firstname', '$lastname','$username','$passwordMD5','$uic','$recruiterID',
		'$permManagePositions','$permUpdateDatabase','$permViewTransactions','$permClearTransactions','$permManagerView','$permManageUsers',
		'$permUnitUser','$permTeamLeader')";
		writeDB($query);
		
		//update transacton database
		$transdetail = "USER CREATE//USER:".strtoupper($firstname)." ".strtoupper($lastname)."; USERNAME:".$username."; TYPE:".$userType;
		$transuser = $_SESSION['userLoginName'];
		writeTrans($transuser, $transdetail);
		
		echo "The user was created successfully, returning you to the user list";
		?>
		<script type="text/javascript">
		window.setTimeout('location.replace("./index.php")');
		</script>
		<?
	}
}
else
{
	echo '<p>There has been a serious error. Please call support.</p>';
}
?>
