<?php
session_start();
//permission check
if (!$_SESSION['permission']['ManagePositions'] && !$_SESSION['permission']['TeamLeader'])
{
	echo "<br/><h4>There has been a serious error. Please call support @x1288</h4>";
}
else
{
	require_once ('../common/dbQuery.php');
	
	//get the position data, regardless of the transaction type
	$positionID = $_POST['positionID'];
	
	//check to see what we're doing
	if ($_POST['transType'] == "reserve")
	{
		//grab the POST data into variables
		
		$heldRRNCOID = $_POST['heldRRNCOID'];
		$heldApplicantName = $_POST['heldApplicantName'];
		$heldDate = $_POST['heldDate'];
		$heldNote = $_POST['heldNote'];
		$messageToRRNCO = $_POST['messageToRRNCO'];
		$isWaiver = $_POST['isWaiver'];
		$adminID = $_SESSION['userID'];

		//quick error check
		if ($heldApplicantName == "")  //can't reserve on an empty applicant
		{
			echo "<br /><h4 class='redtext'>The Applicant Name cannot be blank. The reservation failed.</h4>";
			return;
		}
				
		//ok, reserve the position
		$query = "UPDATE positions SET positionIsHeld = 'y', positionIsHeldByID = $heldRRNCOID, positionIsHeldForApplicant = '$heldApplicantName',
			positionHeldDate = '$heldDate', positionIsHeldWaiver = '$isWaiver', positionIsHeldNotes = '$heldNote', positionIsHeldEnlisted = 'n',
			positionIsHeldByAdminID = '$adminID', positionExpireWarningSent = 'n' WHERE positionID = '$positionID'";
		writeDB($query);
		
		//Position details
		$query = "SELECT * FROM vwPositions WHERE positionID = '$positionID'";
		$result = readDB($query);
		$position = mysql_fetch_array($result, MYSQL_ASSOC);
		
		//log the transaction
		$transUser = $_SESSION['userLoginName'];
		$transDetail = "POSITION RESERVE//ID: $positionID, UIC/PARA/LIN: ".$position['positionUIC']."/".$position['positionPara'].
		"/".$position['positionLine']." RRNCO: ".$positon['recruiterLastName']." APP: ".$heldApplicantName;

		writeTrans($transUser, $transDetail);		

		//handle email
		if ($_POST['noEmailOption'] != 'noEmail')
		{
			$message = "The following vacancy reservation has been CREATED:".PHP_EOL.
			PHP_EOL.
			"RECRUITER: ".strtoupper($position['recruiterLastName']).PHP_EOL.
			"APPLICANT: ".strtoupper($heldApplicantName).PHP_EOL.
			"UIC: ".$position['positionUIC'].PHP_EOL. 
			"Para/Line: ".$position['positionPara']."/".$position['positionLine']."  MOS: ".$position['positionMOS'].PHP_EOL.
			"POSITION TITLE: ".$position['positionDescription'].PHP_EOL.
			PHP_EOL.
			"Notes ".PHP_EOL.$heldNote.
			PHP_EOL.PHP_EOL."Message from OPS/Team Leader:".PHP_EOL.$messageToRRNCO.PHP_EOL.
			PHP_EOL.
			"This is an email from an automated system. Do not reply to this email, replies will not be received.".PHP_EOL.
			"Contact the Operations NCO, your Team Leader, or NCOIC with questions/problems.".PHP_EOL.
			PHP_EOL.
			"Remember: All reservations expire on MIDNIGHT on the eleventh day after being created, unless they are pending a waiver.";

			//add the subject and the FROM headerline
			$subject = 'Vacancy Reservation';
			$headers = 'From: "NGApps Vacancy Manager" <no-reply@ngapps.net>';			
			$to = "";
			//address the message
			//all reservations and cancellations go through OPS. 
			//$to = "robert.p.eaton1.mil@mail.mil, stephen.m.bradley3.mil@mail.mil";
			//currently disabled
			
			//the recruiter gets it too, of course
			$to = $to.$position['recruiterEmail'];
			
			//the TL needs a copy
			//look up the TL email based on the RRNCO TL RSID
			$recruiterTL = $position['recruiterTeamLeaderRSID'];
			$query = "SELECT * FROM recruiters WHERE recruiterRSID = '$recruiterTL'";
			$result = readDB($query);
			$teamLeader = mysql_fetch_array($result, MYSQL_ASSOC);
			//add to $to
			$to = $to.",".$teamLeader['recruiterEmail'];
			
			//finally, the NCOIC gets one
			//substring search, hard-code the NCOIC emails because I'm lazy
				$team = substr($position['recruiterRSID'], 0, 3);
				if ($team == "NHA")
				{
					$to = $to.", sage.j.ladieu.mil@mail.mil";
				}
				else if ($team == "NHB")
				{
					$to = $to.", paul.g.lampron.mil@mail.mil";
				}
				else
				{
					echo "Serious error. Unable to determine team leader. Contact support.";
				}
			
			//send the email
			mail($to, $subject, $message, $headers);
		}
		
		echo "<br/><h4>The position reservation was created successfully.</h4>";		
	}
	else if ($_POST['transType'] == "unreserve")
	{
		//did we provide a message?
		$messageToRRNCO = ($_POST['messageToRRNCO'] == "Message to RRNCO here, if desired..." ? "" : $_POST['messageToRRNCO']);

		//position data
		$query = "SELECT * FROM vwPositions WHERE positionID = '$positionID'";
		$result = readDB($query);
		$position = mysql_fetch_array($result, MYSQL_ASSOC);

		//unreserve the position
		$query = "UPDATE positions SET positionIsHeld = 'n', positionIsHeldByID = 0, positionIsHeldForApplicant = null, positionHeldDate = null, 
			positionIsHeldWaiver = 'n', positionIsHeldNotes = null, positionIsHeldEnlisted = null, positionIsHeldByAdminID = 0, positionExpireWarningSent = 'n' 
			WHERE positionID = '$positionID'";
		writeDB($query);
		
		//record the transaction in the log
		$transUser = $_SESSION['userLoginName'];
		$transDetail = "POSITION UNRESERVE//ID: $positionID, UIC/PARA/LIN: ".$position['positionUIC']."/".$position['positionPara'].
		"/".$position['positionLine']." RRNCO: ".$position['recruiterLastName']." APP: ".$position['positionIsHeldForApplicant'];

		writeTrans($transUser, $transDetail);
		//handle email
		if ($_POST['noEmailOption'] != 'noEmail')
		{
			$message = "The following vacancy reservation has been CANCELLED:".PHP_EOL.
			PHP_EOL.
			"RECRUITER: ".strtoupper($position['recruiterLastName']).PHP_EOL.
			"APPLICANT: ".strtoupper($position['positionIsHeldForApplicant']).PHP_EOL.
			"UIC: ".$position['positionUIC'].PHP_EOL. 
			"Para/Line: ".$position['positionPara']."/".$position['positionLine']."  MOS: ".$position['positionMOS'].PHP_EOL.
			"POSITION TITLE: ".$position['positionDescription'].PHP_EOL.
			PHP_EOL."Message from OPS/Team Leader:".PHP_EOL.$messageToRRNCO.PHP_EOL.
			PHP_EOL.
			"This is an email from an automated system. Do not reply to this email, replies will not be received.".PHP_EOL.
			"Contact the Operations NCO, your Team Leader, or NCOIC with questions/problems.".PHP_EOL;

			//add the subject and the FROM headerline
			$subject = 'Vacancy Reservation Cancelled';
			$headers = 'From: "NGApps Vacancy Manager" <no-reply@ngapps.net>';			
			$to = "";
			//address the message
			//all reservations and cancellations go through OPS. Not right now though. 
			//$to = "robert.p.eaton1.mil@mail.mil, stephen.m.bradley3.mil@mail.mil";
			
			//the recruiter gets it too, of course
			$to = $to.$position['recruiterEmail'];
			
			//the TL needs a copy
			//look up the TL email based on the RRNCO TL RSID
			$recruiterTL = $position['recruiterTeamLeaderRSID'];
			$query = "SELECT * FROM recruiters WHERE recruiterRSID = '$recruiterTL'";
			$result = readDB($query);
			$teamLeader = mysql_fetch_array($result, MYSQL_ASSOC);
			//add to $to
			$to = $to.",".$teamLeader['recruiterEmail'];

			
			//finally, the NCOIC gets one
			//substring search, hard-code the NCOIC emails because I'm lazy
				$team = substr($position['recruiterRSID'], 0, 3);
				if ($team == "NHA")
				{
					$to = $to.", sage.j.ladieu.mil@mail.mil";
				}
				else if ($team == "NHB")
				{
					$to = $to.", paul.g.lampron.mil@mail.mil";
				}
				else
				{
					echo "Serious error. Unable to determine team leader. Contact support.";
				}
			
			//send the email
			mail($to, $subject, $message, $headers);
		}
		echo "<br/><h4>The position reservation was cancelled successfully.</h4>";
	}
	else
	{
		echo "<br/><h4>There has been a serious error. Please call support @x1288</h4>";
	}
}

