<?php
session_start();
if ($_SESSION['permission']['ManageUsers']!=NULL && $_POST['recruiterID'])
{
	require ('../common/dbQuery.php');
	
	//get variables from POST
	$recruiterID = $_POST['recruiterID'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$emailAddress = $_POST['emailAddress'];
	$rsid = strtoupper($_POST['recruiterRSID']);
	$teamLeader = strtoupper($_POST['teamLeaderRSID']);
	
	//function variables
	$errorList = "";
	
	//basic error checking!
	
	//arbitrary name lengths
	if (strlen($firstName) < 3)
	{
		$errorList.="<li>User first name must be greater than 3 characters.</li>";
	}
	if (strlen($lastName) < 2)
	{
		$errorList.="<li>User last name must be at least 2 characters.</li>";
	}
	
	//DISA email address
	if (substr($emailAddress, -9) <> "@mail.mil")
	{
		$errorList .= "<li>You must enter the DISA (@mail.mil) email address.</li>";
	}
	
	//4-digit RSID, no more, no less, and begins with NH
	if (strlen($rsid) <> 4 || substr($rsid, 0,2) <> "NH")
	{
		$errorList .= "<li>RSID must be exactly 4 characters and must start with NH</li>";
	}
	
	//team leader RSID, same deal
	if (strlen($teamLeader) <> 4 || substr($teamLeader, 0,2) <> "NH")
	{
		$errorList .= "<li>Team Leader RSID must be exactly 4 characters and must start with NH</li>";
	}
	
	
	//errors up!
	if(strlen($errorList) != 0)
	{
		echo "<br><p style=\"color:red\">Please correct the following errors and submit the request again:</p>";
		echo "<ul>".$errorList."</ul>";
	}
	else
	{
		//otherwise, create the record
		$query = "UPDATE recruiters SET recruiterFirstName = \"$firstName\", recruiterLastName = \"$lastName\", recruiterEmail = \"$emailAddress\",
		recruiterRSID = \"$rsid\", recruiterTeamLeaderRSID = \"$teamLeader\" WHERE recruiterID = \"$recruiterID\"";
		writeDB($query);
		
		//update transactions
		$transdetail = "RRNCO UPDATE//RRNCO:".strtoupper($firstName)." ".strtoupper($lastName)."; RSID:".$rsid;
		$transuser = $_SESSION['userLoginName'];
		writeTrans($transuser, $transdetail);	
		?>
		<script type="text/javascript">
			alert("Recruiter updated!");
			window.setTimeout('location.replace("./index.php")');
		</script>
		<?	
	}
}
else
{
	echo '<p>There has been a serious error. Please call support.</p>';
}
	