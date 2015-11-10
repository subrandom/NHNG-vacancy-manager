<?php
session_start();
if ($_SESSION['permission']['ManageUsers'])
{
	require('../common/dbQuery.php');
?>
    <h4>Create User</h4>
    <form id="userForm" name="userForm" action="doCreateUser.php" method="post">
		<p>Choose the type of user:
		<input id="userTypeRRNCO" type="radio" value="rrnco" name="userGroup" onchange="showRRNCOPanel()">Production NCO
		<input id="userRRBN" type="radio" value="rrbn" name="userGroup" onchange="showNormalUserPanel()">RRBN User
		<input id="userTypeUnit" type="radio" value="unit" name="userGroup" onchange="showUnitPanel()">External Unit           	
		</p>
		<div style="display:none" id="loginPanel">
			<label>Username:</label><input name="userLoginName" id="userLoginName" size="15" type="text" /><br>
			<label>Password:</label><input name="userPassword" id="userPassword" size="15" type="password" /><br>
			<label>Confirm Password:</label><input id ="confirm_password" name="confirm_password" size="15" type="password"><br>
			<br>
		</div>
		<div style="display:none" id="namePanel">
			<label>First Name:</label><input id="userFirstName" name="userFirstName" size="15" type="text" /><br>
			<label>Last Name:</label><input id="userLastName" name="userLastName" size="15" type="text" /><br>
		</div>
	<!---- handle specific user types ---->
	<!---- for non-recruiter RRBN users, select permissions ---->
	
	<div style="display:none" id="permissionPanel">
		<p>========== User Permissions ==========</p>
		<input type="checkbox" name="managePositions">Manage Positions</input>
		<input type="checkbox" name="updateDatabase">Update Database</input>
		<input type="checkbox" name="manageUsers">Manage Users</input>
		<br />
		<input type="checkbox" name="viewTransactions">View Transactions</input>
		<input type="checkbox" name="clearTransactions">Clear Transactions</input>
		<input type="checkbox" name="managerView">Manager View (read only)</input>
		<p class="redtext">If you do not choose any permissions, the user will have basic view-only. This is the default RRNCO view.</p>
	</div>
	
	<!---- for RRNCO users, select their RSID and indicate team leaders ---->
		<div style="display:none" id="rsidPanel">
		<label>Select RRNCO RSID:</label>
		<select name="rsid">
			<option value="" selected>Select...</option>
		<?php
			$query="SELECT recruiters.recruiterRSID FROM recruiters ORDER BY recruiterRSID";
			$result = readDB($query);
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{ 
				$rsid = $row["recruiterRSID"];
				echo "<option value=\"$rsid\">$rsid</option>";
			}?>
		</select>
		<br>
		<label>This RRNCO is a Team Leader</label>
		<input type="checkbox" name="teamLeader">
		</div>
	<!---- for unit users, select thier unit ---->
		<div style="display:none" id="unitPanel">
		<br>
		<label>Select User's UIC:</label>
		<select name="unitUIC">
		<option value="" selected>Select...</option>
		<?php
			$query="SELECT units.unitUIC FROM units";
			$result = readDB($query);
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{ 
				$uic = $row["unitUIC"];
				echo "<option value=\"$uic\">$uic</option>";
			}?>
		</select>

		</div>
	
	<!----- form buttons, initially hidden ---->
	<br><br>
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
	function showNormalUserPanel()
	{
		document.getElementById('loginPanel').style.display = "block";
		document.getElementById('namePanel').style.display = "block";
		document.getElementById('permissionPanel').style.display = "block";	
		document.getElementById('unitPanel').style.display = "none";
		document.getElementById('rsidPanel').style.display = "none";
	}
	
	function showRRNCOPanel()
	{
		document.getElementById('loginPanel').style.display = "block";
		document.getElementById('rsidPanel').style.display="block";	
		document.getElementById('namePanel').style.display = "none";			
		document.getElementById('unitPanel').style.display = "none";
		document.getElementById('permissionPanel').style.display="none";
		
	}
	
	function showUnitPanel()
	{
		document.getElementById('loginPanel').style.display = "block";	
		document.getElementById('namePanel').style.display = "block";	
		document.getElementById('unitPanel').style.display="block";	
		document.getElementById('permissionPanel').style.display="none";
		document.getElementById('rsidPanel').style.display="none";			
	}
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