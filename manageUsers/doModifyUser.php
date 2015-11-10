<?php
	session_start();
	
	if ($_SESSION['permission']['ManageUsers']!=NULL && $_POST['userID'])
	{	
		require ('../common/dbQuery.php');
		
		$userID = $_POST['userID'];
		$userName= $_POST['userName'];
		$userType = $_POST['userType'];
		$newPassword = $_POST['newPassword'];
		$confirmPassword = $_POST['confirmPassword'];
		$doPwdChange = $_POST['doPwdChange'];
		$userUIC = $_POST['userUIC'];
		$errorList = "";		
		if ($newPassword != NULL){
			$passwordMD5 = md5($newPassword);}
		
		
		$userCanManagePositions = "N";
		$userCanUpdateDatabase = "N";
		$userCanViewTransactions = "N";
		$userCanClearTransactions = "N";
		$userIsSuperviewer = "N";
		$userCanManageUsers = "N";
		$userIsUnitUser = "N"; //this never gets changed. There isn't currently code to convert someone to or from a user type
		$userIsTeamLeader = "N";		
		
		//error checking! Really it's only the password we have to check
		if ($doPwdChange == "yes")
		{
			if (strlen($newPassword) < 8)
			{
				$errorList.= "<li>Password must be 8 characters or more.</li>";
			}
				
			//does the confirmation password match?
			if ($newPassword != $confirmPassword)
			{
				$errorList.="<li>The password and password confirmation must match.</li>";
			}
		}
		
		//if there are errors, echo them and make the user fix them before continuing
		if(strlen($errorList) != 0)
		{
			echo "<br><p style=\"color:red\">Please correct the following errors and submit the request again:</p>";
			echo "<ul>".$errorList."</ul>";
		}
		else
		{
			//no errors? Do the updates
			//password update
			if ($doPwdChange == "yes")
			{
				$query = "UPDATE users SET userPassword = \"$passwordMD5\" WHERE userID = \"$userID\"";
				writeDB($query);
			}
			//uic update
			$query = "UPDATE users SET userUIC = '$userUIC' WHERE userID = '$userID'";
			writeDB($query);
			
		
			//permissions update for RRBN and RRNCO users (RRNCOs just have the teamleader option, but we'll do them all in one call
			//rather than two, to save round trips
			
			if ($userType == "RRBN" || $userType == "RRNCO")
			{
				$userCanManagePositions = ($_POST['managePositions'] == "on" ? "Y": "N");
				$userCanUpdateDatabase = ($_POST['updateDatabase'] == "on" ? "Y" : "N");
				$userCanManageUsers = ($_POST['manageUsers'] == "on" ? "Y" : "N");
				$userCanViewTransactions = ($_POST['viewTransactions'] == "on" ? "Y" :"N");
				$userCanClearTransactions = ($_POST['clearTransactions'] == "on" ? "Y" :"N");
				$userIsSuperviewer = ($_POST['managerView'] == "on" ? "Y" :"N");
				$userIsTeamLeader = ($_POST['teamLeader']=="on" ? "Y":"N");						
			
				$query = "UPDATE users SET userCanManagePositions = \"$userCanManagePositions\", userCanUpdateDatabase = \"$userCanUpdateDatabase\", 
				userCanManageUsers = \"$userCanManageUsers\", userCanViewTransactions = \"$userCanViewTransactions\", 
				userCanClearTransactions = \"$permClearTransactions\", userIsSuperviewer = \"$userIsSuperviewer\", 
				userIsUnitUser = \"$userIsUnitUser\",userIsTeamLeader = \"$userIsTeamLeader\" WHERE userID = \"$userID\"";
				
				writeDB($query);
		
				//update transacton database
				$transdetail = "USER MODIFY//USER:".$userName;
				$transuser = $_SESSION['userLoginName'];
				writeTrans($transuser, $transdetail);
			}
				$_POST = null;
				?>
		<script type="text/javascript">
		alert("User Updated Successfully");
		window.setTimeout('location.replace("./index.php")');
		</script>
		<?
		}
	}
else
{
	echo "<p>There has been a serious error. Please call support.</p>"; 
}
?>
