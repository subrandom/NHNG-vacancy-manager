<?php
session_start();
if (!$_SESSION['permission']['ManagePositions'])
{
	require_once('../common/forbidden.html');
} 
else 
{
	require_once('../common/dbQuery.php');
	echo "<h4>Position Lock (Admin)</h4>";
	
	//read vacancy table to find out if the position is currently held, locked, or vacant.
	$id = $_GET['id'];
	$query="SELECT * FROM vwPositions WHERE positionID='$id'";
	$result = readDB($query);
	$position = mysql_fetch_array($result, MYSQL_ASSOC);

	//held by unit? Give error message. We don't unlock positions locked by units
	if ($position['positionIsLocked'] == "y" && $position['positionIsLockedByID'] > 0)
	{
		$userLockingID = $position['positionIsLockedByID'];
		//get the name of the user who locked the position
		$query = "SELECT * from users WHERE userID = '$userLockingID'";
		$result = readDB($query);
		$lock = mysql_fetch_array($result, MYSQL_ASSOC);
		
		//explain the problem
		echo "<h5>The position is marked unavailable by the owning unit.<br />
		 Please contact the unit to have them unlock it.</h5>";
		echo "<h5>Locking user: ".$lock['userFirstName']." ".$lock['userLastName']."<br />"; 
		echo "Date locked: ".$position['positionLockedDate']."</h5>";
	}
	else if ($position['positionIsHeld'] == 'y') 
	{
		//we can't lock a held position, explain the problem
?>	
		<h5>This position reserved.<br>
			RRNCO: <?php echo $position['recruiterFirstName']." ".$position['recruiterLastName'];?><br />
			Applicant: <?php echo $position['positionIsHeldForApplicant'];?><br />
			<br />
			You must CANCEL the reservation before you can lock the position.
			<br />
<?php
	} 
	else if ($position['positionIsLocked'] == "y" && $position['positionIsLockedByID'] < 0)  
	{ 
		//we can unlock a position that is locked by us
		?>
		<form id="unlock" name="unlock" action="doAdminLock.php" method="post">
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
				<label class = "plainLeft">Admin notes:</label><br/>
				<textarea class="userInput" id="heldNote" name="heldNote" cols="85" rows="2" readonly><? echo $position['positionIsHeldNotes']; ?></textarea><br/>
				<input name="positionID" type="hidden" value="<?php echo $position['positionID'];?>">
				<input name="transType" type="hidden" value = "unlock">
				<br />
				<br />
				<button id="submit" name="submit" type="submit" value="unlock">Unlock</button>
				<button id = "cancel" type="reset">Cancel</button>
			</form>
	<?
	}
	else if ($position['positionIsLocked'] == "n")
	{
		//we can lock a position that is not locked or held
		
		//we want to display the position data and require a note
		?>
		<form id="lock" name="lock" action="doAdminLock.php" method="post">
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
				<label class = "plainLeft">Admin notes (Required):</label><br/>
				<textarea class="userInput" id="heldNote" name="heldNote" cols="85" rows="2" placeholder="A note is required to lock a position."></textarea><br/>
				<input name="positionID" type="hidden" value="<?php echo $position['positionID'];?>">
				<input name="transType" type="hidden" value = "lock">
				<br />
				<br />
				<button id="submit" name="submit" type="submit" value="lock">Lock</button>
				<button id = "cancel" type="reset">Cancel</button>
			</form>
<?
	}
	else
	{
		echo "<h5>An unhandeled condition has occurred. Please contact the administrator and let him know.";
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
	$('#lock').ajaxForm(options);
	$('#unlock').ajaxForm(options);
});
</script>
