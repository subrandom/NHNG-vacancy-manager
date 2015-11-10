<?php
	session_start();
	
	if ($_SESSION['permission']['UpdateDatabase']) 
	{
		require('../common/dbQuery.php');
		$id = $_GET['id'];
		
		$query = "SELECT * FROM units WHERE unitUIC='$id'";
		$result = readDB($query);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		?>
		<br>
		<form id="deleteForm" name="deleteForm" action="doDeleteUnit.php" method="post">
		<p class="redtext">Confirm that you wish to delete:</p>
        <label>Unit UIC:  </label><? echo $id; ?>
        <br><br>
        <lable>Unit Designation:  </lable><? echo $row['unitDesignation']; ?>
        <br><br>
        <input name="unitUIC" type="hidden" value="<? echo $id ?>" />
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