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
	<h4>Modify Unit</h4>
	<br>
	<form id="unitForm" name="unitForm" action="doModifyUnit.php" method="post">
    	<input type="hidden" name="unitUIC" value="<? echo $row['unitUIC'] ?>" />
    	<br>
    	<label>Unit Designation:</label>
    		<input type="text" name="unitDesignation" size="35"value="<? echo $row['unitDesignation'] ?>" />
    	<br>
    	<label>Unit Location:</label>
    		<input type="text" name="unitLocation" value="<? echo $row['unitLocation'] ?>"/>
    	<br>
    	<label>Parent UIC:</label>
    		<input type="text" name="unitParentUnit" size="6" value="<? echo $row['unitParentUnit'] ?>"/>
    	<br>
    	<label>Unit Off-Limits:</label>
    		<select name="unitOffLimits">
    			<option value = "<? echo $row['unitOffLimits']; ?>" selected><? echo ($row['unitOffLimits'] == "y" ? "Yes" : "No"); ?></option>
    			<option value = "y">Yes</option>
    			<option value = "n">No</option>
    		</select>
    	<br>
    	<br>
    	<div id="formButtons">
	        <button name="submit" type="submit" value ="Update">Update</button>
	        <button id="cancel">Cancel</button>
		</div>
	</form>
	
<?php
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
            $('#unitForm').ajaxForm(options);
            $('#cancel').click(function(event){
                event.preventDefault();
                location.replace("./index.php");
            });
    });
</script>