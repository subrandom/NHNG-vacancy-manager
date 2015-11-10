<?php
session_start();
if (!$_SESSION['permission']['ManagePositions'] && !$_SESSION['permission']['TeamLeader'])
{
	require_once('../common/forbidden.html');
} 
else 
{
	require_once('../common/dbQuery.php');
	echo "<h4>Position Reserve/Un-reserve</h4>";
	
	//read vacancy table to find out if the position is currently held, locked, or vacant.
	$id = $_GET['id'];
	$query="SELECT * FROM vwPositions WHERE positionID='$id'";
	$result = readDB($query);
	$position = mysql_fetch_array($result, MYSQL_ASSOC);
	$myRSID = $_SESSION['userRecruiterRSID'];
	
	//held by unit? Give error message. Held by recruiter or system? Offer to unreserve. Otherwise, offer to reserve
	if ($position['positionIsLocked'] == "y")
	{
		$userLockingID = $position['positionIsLockedByID'];
		//get the name of the user who locked the position
		$query = "SELECT * from users WHERE userID = abs('$userLockingID')";
		$result = readDB($query);
		$lock = mysql_fetch_array($result, MYSQL_ASSOC);
		
		//explain the problem
		echo "<h5>The position is marked unavailable by the owning unit.<br />
		 Please contact the unit to have them unlock it.</h5>";
		echo "<h5>Locking user: ".$lock['userFirstName']." ".$lock['userLastName']."<br />"; 
		echo "Date locked: ".$position['positionLockedDate']."</h5>";
	}
	else if ($position['positionIsHeld'] == 'y' && $position['recruiterTeamLeaderRSID'] != $myRSID && !$_SESSION['permission']['ManagePositions']) {
		echo "<h5>The position is held by another team. You do not have the authority to cancel the reservation. <br>
		Contact the team leader who reserved it, or OPS for assistance.</h5>";
	}
	else if ($position['positionIsHeld'] == 'y') 
	{
?>
		<h5>This position reserved.<br>
			RRNCO: <?php echo $position['recruiterFirstName']." ".$position['recruiterLastName'];?><br />
			Applicant: <?php echo $position['positionIsHeldForApplicant'];?><br />
			<br />
			To cancel the reservation, click the button.
			<br />
			There is no confirmation, the change is immediate.</h5>
			
			<form id="unreserve" name="unreserve" action="doReserveUnReserve.php" method="post">
				<label>Message to RRNCO (optional)</label><br/>
				<textarea id="messageToRRNCO" name="messageToRRNCO" cols="70" rows="5"></textarea>
				<br/>
				<input type="checkbox" name="noEmailOption" value="noEmail">Do not email
				<br />
				<br />
				<button name="undores" type="submit" value="Un-Reserve">Un-Reserve</button>
				<button id="cancel" type="reset">Cancel</button>
				<input name="positionID" type="hidden" value="<?php echo $position['positionID'];?>">
				<input name="transType" type="hidden" value = "unreserve">
		</form>
<?php
		} 
		else 
		{ ?>
			<form id="reserve" name="reserve" action="doReserveUnReserve.php" method="post">
				<label>UIC</label>
				<label>PARA/LINE</label>
				<label>MOS</label>
				<label>GRADE</label>
				<label>DESCRIPTION</label>				
				<br />
				<label class = "rodata"><?php echo $position['positionUIC'];?></label>					
				<label class = "rodata"><?php echo $position['positionPara'] . "/" . $position['positionLine']?></label>
				<label class = "rodata"><?php echo $position['positionMOS'];?></label>
				<label class = "rodata"><?php echo $position['positionGrade'];?></label>
				<label class = "rodata"><?php echo $position['positionDescription'];?></label>
				<br />
				<br />
				<label>RECRUITER</label>
				<label>APPLICANT</label>
				<? //we dont' want to see the waiver or date fields for Team Leader reservations
					if($_SESSION['permission']['ManagePositions'])
					{
						echo '<label>WAIVER</label>';
						echo '<label>DATE HELD</label>';
						//and they get to select all recruiters
						$namesq = "SELECT * from recruiters ORDER BY recruiterLastName";
					}
					else
					{
						//TLs just get their own team
						$namesq = "SELECT * from recruiters WHERE recruiterTeamLeaderRSID = \"".$_SESSION['userRecruiterRSID']."\""." ORDER BY recruiterLastName";
					}
					?>
					<br />
				<select class = "userInput" name="heldRRNCOID" id="heldRRNCOID">
					<?php 
						$namesq = readDB($namesq); 
						while($names = mysql_fetch_array($namesq, MYSQL_ASSOC))
						{
							?><option value="<?php echo $names['recruiterID'] ?>"><?php echo $names['recruiterLastName']?></option>
					 <? }?>
				</select>
				<input class = "userInput" type="text" name="heldApplicantName" id="heldApplicantName" />
				<? //Taking care of the waiver and date fields, actual input
					if($_SESSION['permission']['ManagePositions'])
					{
						echo '
						<select class="userInput" name = "isWaiver" id = "isWaiver">
							<option value = "y">YES</option>
							<option selected value = "n">NO</option>
						</select>
						<input class = "userInput" type="text" name="heldDate" id="heldDate" value= '.date('Y-m-d');
					}
					else
					{
						echo '
						<input type = "hidden" name = "isWaiver" value = "n">
						<input type="hidden" name="heldDate" id="heldDate" value= '.date('Y-m-d');
					}
					?>
				<br />
				<br />
				<label class = "plainLeft">Admin notes (will display in list):</label><br/>
				<textarea id="heldNote" name="heldNote" cols="85" rows="2"></textarea><br/>
				<label class = "plainLeft">Message to RRNCO (optional):</label><br/>
				<textarea id="messageToRRNCO" name="messageToRRNCO" cols="85" rows="5"></textarea>
				<br />
				<input type="checkbox" name="noEmailOption" value="noEmail">Do not email
				<input name="positionID" type="hidden" value="<?php echo $position['positionID'];?>">
				<input name="transType" type="hidden" value = "reserve">
				<br />
				<br />
				<button id="submit" name="submit" type="submit" value="Reserve">Reserve</button>
				<button id = "cancel" type="reset">Cancel</button>
			</form>
<br />
<br />
<?php
	}
}
?>
<script type="text/javascript">
	$(document).ready(function() {
		var options = {
			target : '#toppanel',
			success : function() 
						{ 
							$('#bottompanel').load('listPositions.php');	
							$('#toppanel').load('toppanel_default.php');
							setHeight();
							}
		};
	$('#cancel').click(function(event){
		event.preventDefault();
		$('#toppanel').load("toppanel_default.php");
		$('#bottompanel').load('listPositions.php');
		});
	$('#heldNote').focus(function(event){
		$('#heldNote').val('');
		});
	$('#reserve').ajaxForm(options);
	$('#unreserve').ajaxForm(options);
	$('#heldDate').datepicker({
			showOtherMonths : true,
			selectOtherMonths : true,
			dateFormat : 'yy-mm-dd',
			defaultDate : +1
		});
});
</script>
