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
	<h4>Modify Recruiter</h4>
	<br>
	<form id="userForm" name="userForm" action="doModifyRecruiter.php" method="post">
	    <label>First Name:</label>
    		<input type="text" name="firstName" value="<? echo $row['recruiterFirstName'] ?>" />
    	<br>
    	<label>Last Name:</label>
    		<input type="text" name="lastName" value="<? echo $row['recruiterLastName'] ?>" />
    	<br>
    	<label>DISA email address:</label>
    		<input type="email" name="emailAddress" size="40" value="<? echo $row['recruiterEmail'] ?>"/>
    	<br>
    	<label>Recruiter RSID:</label>
    		<input type="text" name="recruiterRSID" size="6" value="<? echo $row['recruiterRSID'] ?>"/>
    	<br>
    	<label>Team Leader RSID:</label>
    		<input type="text" class="hasHelp" name="teamLeaderRSID" title="For Team Leaders, this is the same as Recruiter RSID!" size="6" 
    			value="<? echo $row['recruiterTeamLeaderRSID'] ?>"/>
    	<br>
    	<br>
    	<div id="formButtons">
    		<input type="hidden" name="recruiterID" value="<? echo $row['recruiterID']; ?>" />
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
            $('#userForm').ajaxForm(options);
            $('#cancel').click(function(event){
                event.preventDefault();
                location.replace("./index.php");
            });
    });
</script>