<?php
session_start();
if (!$_SESSION['permission']['ManagePositions'] && !$_SESSION['permission']['TeamLeader'])
{
	require_once('../common/forbidden.html');
} 
else 
{
	require_once('../common/dbQuery.php');
	echo "<h4>Position Delete</h4>";
	
	//read position table
	$id = $_GET['id'];
	$query="SELECT * FROM vwPositions WHERE positionID='$id'";
	$result = readDB($query);
	$position = mysql_fetch_array($result, MYSQL_ASSOC);
	
	if($position['positionIsHeldEnlisted'] != 'y')
	{
		echo "<h5>You can only delete a position that is marked as enlisted. <br/>";
		echo "If you need to delete a position for some other reason, contact the database administrator. </h5><br/>";
	}
	else
	{
		echo "<h5>You should only delete a position once the applicant has enlisted and is showing on the UMR!<br/><br/>";
		echo "If you need to delete a position for some other reason, contact the database administrator.</h5><br/>";
		?>
		<form id="delete" name="delete" action="doDeletePosition.php" method="post">
			<button name="delete" type="submit" value="delete">Delete</button>
			<button id="cancel" type="reset">Cancel</button>
			<input name="positionID" type="hidden" value="<?php echo $position['positionID'];?>">
		</form>
		<?
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
							setHeight();
							}
		};
	$('#cancel').click(function(event){
		event.preventDefault();
		$('#toppanel').load("toppanel_default.php");
		//$('#bottompanel').load('listPositions.php');
		});
	$('#delete').ajaxForm(options);
});
</script>
