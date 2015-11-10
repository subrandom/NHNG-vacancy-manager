<?php
	session_start();
	
	if ($_SESSION['permission']['ManageUsers']) 
	{
		require('../common/dbQuery.php');

		$id = $_GET['id'];
		$query = "SELECT * FROM recruiters WHERE recruiterID='$id'";
		$result = readDB($query);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		?>
		<br>
		<form id="deleteForm" name="deleteForm" action="doDeleteRecruiter.php" method="post">
		<p>Confirm that you wish to delete:</p>
        RRNCO Name:<? echo "  " . $row['recruiterFirstName']." ".$row['recruiterLastName']; ?>
        <br><br>
        RSID :<? echo "  " . $row['recruiterRSID']; ?>
        <br><br>
        <input name="id" type="hidden" value="<? echo $row['recruiterID']; ?>" />
        <input name="rrncoName" type="hidden" value="<? echo $row['recruiterFirstName']." ".$row['recruiterLastName'];?>" />
        <button name="submit" type="submit" value ="Delete User">Delete</button>
        <button id="cancel" type="reset">Cancel</button>
        <p>Note: You cannot undo a delete.</p>
    </form>
    <?
}
else
{
require_once('../common/forbidden.html');
}
?>
<script type="text/javascript">
    $(document).ready(function(){
        var options= {
            target: '#toppanel'
        };
        $('#deleteForm').ajaxForm(options);
        $('#cancel').click(function(event){
            event.preventDefault();
            $('#toppanel').load('toppanel_default.php');
        });
    });
</script>