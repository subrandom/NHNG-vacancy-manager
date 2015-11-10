<?php
session_start();
if (!$_SESSION['permission']['ManagePositions'] && !$_SESSION['permission']['TeamLeader'])
{
	require_once('../common/forbidden.html');
}
else
{
	require_once('../common/dbQuery.php');

	echo '<h4>Modify Note</h4>';
	$id = $_GET['id'];
	$query="SELECT * FROM positions WHERE positionID='$id'";

	$result = readDB($query);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
?>
	<form id="form" name="form" action="doUpdateNote.php" method="post">
		<p>Current Note:</p>
		<textarea id="notes" name="notes" cols="85" rows="2"><?echo $row['positionIsHeldNotes'];?></textarea><br/>
		<br>
		<input name="id" type="hidden" value="<?echo $row['positionID'];?>">
		<button name="submit" type="submit" value="Commit">Commit</button>  
		<button id="cancel">Cancel</button>
		<br>
		<br>
		</form>
<?
	}
?>
<script	type="text/javascript">
    $(document).ready(function(){
        setHeight();
 var options= {
            target: '#toppanel',
            success: function(){ 
            	$('#bottompanel').load('listPositions.php');
            	setHeight();
            	}
        };
        $('#form').ajaxForm(options);
        $('#cancel').click(function(event){
            event.preventDefault();
            $('#toppanel').load('toppanel_default.php');
        });
    });
</script>