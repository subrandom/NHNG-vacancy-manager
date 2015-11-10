<?php
	session_start();
	
	if($_GET['id'] == "asmcifu3948ytnp349ytpqcovf")
	{
		require_once ('./dbQuery.php');
		
		//first, send out emails for positions that are 4+ days old and update their flag
		
		//get the position list from the SQL view
		$query = "select * from vwHoldAged5Days";
		$result = readDB($query);
		while($position = mysql_fetch_array($result))
		{
			//email
			$message = "The following vacancy has been reserved for 5 or more days. It will automatically expire at midnight on the 11th day.".PHP_EOL.
			PHP_EOL.
			"RECRUITER: ".strtoupper($position['recruiterLastName']).PHP_EOL.
			"APPLICANT: ".strtoupper($position['positionIsHeldForApplicant']).PHP_EOL.			
			"UIC: ".$position['positionUIC'].PHP_EOL. 
			"Para/Line: ".$position['positionPara']."/".$position['positionLine']."  MOS: ".$position['positionMOS'].PHP_EOL.
			"POSITION TITLE: ".$position['positionDescription'].PHP_EOL.
			PHP_EOL.
			"This is an email from an automated system. Do not reply to this email, replies will not be received.".PHP_EOL.
			"Contact the Operations NCO, your Team Leader, or NCOIC with questions/problems.".PHP_EOL;

			//add the subject and the FROM headerline
			$subject = 'Vacancy Reservation Auto-Expire Warning';
			$headers = 'From: "NGApps Vacancy Manager" <no-reply@ngapps.net>';			
			$to = "";
			//address the message
			
			//the recruiter gets it too, of course
			$to = $to.$position['recruiterEmail'];
			
			//look up the TL email based on the RRNCO TL RSID
			$recruiterTL = $position['recruiterTeamLeaderRSID'];
			$recQ = "SELECT * FROM recruiters WHERE recruiterRSID = '$recruiterTL'";
			$resultQ = readDB($recQ);
			$teamLeader = mysql_fetch_array($resultQ, MYSQL_ASSOC);
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
				$to = $to;
			}
		
			//send the email
			mail($to, $subject, $message, $headers);

			//update the position to set the flag
			$positionID = $position['positionID'];
			$query = "UPDATE positions set positionExpireWarningSent = 'y' WHERE positionID = '$positionID'";
			writeDB($query);
			
			//record the transaction
			$transUser = "AutoExpire Daemon";
			$transDetail = "POSITION WARNING//ID: $positionID, UIC/PARA/LIN: ".$position['positionUIC']."/".$position['positionPara'].
				"/".$position['positionLine']." RRNCO: ".$position['recruiterLastName']." APP: ".$position['positionIsHeldForApplicant'];
				
			writeTrans($transUser, $transDetail);	
		}
		//next, send out emails for positions that are 10+ days, and unreserve them	
		$query = "select * from vwHoldAged10Days";
		$result = readDB($query);
		
		while($position = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			//email
			$message = "The following vacancy has been reserved for 10 or more days and is not pending a waiver. It has been  automatically released.".PHP_EOL.
			PHP_EOL.
			"RECRUITER: ".strtoupper($position['recruiterLastName']).PHP_EOL.
			"APPLICANT: ".strtoupper($position['positionIsHeldForApplicant']).PHP_EOL.			
			"UIC: ".$position['positionUIC'].PHP_EOL. 
			"Para/Line: ".$position['positionPara']."/".$position['positionLine']."  MOS: ".$position['positionMOS'].PHP_EOL.
			"POSITION TITLE: ".$position['positionDescription'].PHP_EOL.
			PHP_EOL.
			"This is an email from an automated system. Do not reply to this email, replies will not be received.".PHP_EOL.
			"Contact the Operations NCO, your Team Leader, or NCOIC with questions/problems.".PHP_EOL;

			//add the subject and the FROM headerline
			$subject = 'Vacancy Reservation Auto-Expired Notification';
			$headers = 'From: "NGApps Vacancy Manager" <no-reply@ngapps.net>';			
			$to = "";
			//address the message
			
			//the recruiter gets it too, of course
			$to = $to.$position['recruiterEmail'];
			
			//look up the TL email based on the RRNCO TL RSID
			$recruiterTL = $position['recruiterTeamLeaderRSID'];
			$recQ = "SELECT * FROM recruiters WHERE recruiterRSID = '$recruiterTL'";
			$resultQ = readDB($recQ);
			$teamLeader = mysql_fetch_array($resultQ, MYSQL_ASSOC);
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
				$to = $to;
			}
		
			//send the email
			mail($to, $subject, $message, $headers);

			//update the position
			$positionID = $position['positionID'];
			$query = "UPDATE positions SET positionIsHeld = 'n', positionIsHeldByID = 0, positionIsHeldForApplicant = null, positionHeldDate = null, 
			positionIsHeldWaiver = 'n', positionIsHeldNotes = null, positionIsHeldEnlisted = null, positionIsHeldByAdminID = 0, positionExpireWarningSent = 'n' 
			WHERE positionID = '$positionID'";
			writeDB($query);
			
			//record the transaction
			$transUser = "AutoExpire Daemon";
			$transDetail = "POSITION UNRESERVE//ID: $positionID, UIC/PARA/LIN: ".$position['positionUIC']."/".$position['positionPara'].
				"/".$position['positionLine']." RRNCO: ".$position['recruiterLastName']." APP: ".$position['positionIsHeldForApplicant'];
				
			writeTrans($transUser, $transDetail);					
		}
			
	}
	else
	{
		echo "Unauthorized.";
	}
	
?>
	