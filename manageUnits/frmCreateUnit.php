<?php
session_start();
if ($_SESSION['permission']['UpdateDatabase'])
{
	require('../common/dbQuery.php');
?>
    <h4>Create New Unit</h4>
    <form id="unitForm" name="unitForm" action="doCreateUnit.php" method="post">
    	<label>Unit UIC:</label>
    		<input type="text" name="unitUIC" />
    	<br>
    	<label>Unit Designation:</label>
    		<input type="text" name="unitDesignation" />
    	<br>
    	<label>Unit Location:</label>
    		<input type="text" name="unitLocation" size="40" />
    	<br>
    	<label>Parent Unit:</label>
    		<input type="text" name="unitParentUnit" size="6" />
    	<br>
    	<label>Unit Off Limits:</label>
    		<select name="unitOffLimits">
    			<option value = "">Select...</option>
    			<option value = "y">Yes</option>
    			<option value = "n">No</option>
    		</select>
    	<br>
    	<br>
    	<div id="formButtons">
	        <button name="submit" type="submit" value ="Create">Create</button>
	        <button id="cancel">Cancel</button>
		</div>
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
            $('#unitForm').ajaxForm(options);
            $('#cancel').click(function(event){
                event.preventDefault();
                location.replace("./index.php");
            });
    });
</script>