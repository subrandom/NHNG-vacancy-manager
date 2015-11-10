<?php
session_start();
if ($_SESSION['permission']['ManageUsers'])
{
	require('../common/dbQuery.php');
?>
    <h4>Create New RRNCO</h4>
    <form id="userForm" name="userForm" action="doCreateRecruiter.php" method="post">
    	<label>First Name:</label>
    		<input type="text" name="firstName" />
    	<br>
    	<label>Last Name:</label>
    		<input type="text" name="lastName" />
    	<br>
    	<label>DISA email address:</label>
    		<input type="email" name="emailAddress" size="40" />
    	<br>
    	<label>Recruiter RSID:</label>
    		<input type="text" name="recruiterRSID" size="6" />
    	<br>
    	<label>Team Leader RSID:</label>
    		<input type="text" class="hasHelp" name="teamLeaderRSID" title="For Team Leaders, this is the same as Recruiter RSID!" size="6" />
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
            $('#userForm').ajaxForm(options);
            $('#cancel').click(function(event){
                event.preventDefault();
                location.replace("./index.php");
            });
    });
</script>