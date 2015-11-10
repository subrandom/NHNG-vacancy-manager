<?php
session_start();
if ($_SESSION['permission']['ManageUsers'])
{
	require('../common/dbQuery.php');

	$id = $_GET['id'];
	$query = "SELECT * FROM users WHERE userID='$id'";
	$result = readDB($query);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
?>
	<h4>Modify User</h4>
	<br>
	<form id="userForm" name="userForm" action="doModifyUser.php" method="post">
	
		<!--- If we aren't changing the password, don't show the option. We will also use the radiobutton value during processing --->
		<label>I want to change the password:</label>
			<input type="radio" name="doPwdChange" value="yes" onchange="showPasswordPanel('yes')"/>Yes
			<input type="radio" name="doPwdChange" value="no" checked="y" onchange="showPasswordPanel('no')" />No
		<div id="PwdChangePanel" style="display:none">
			<br>
			<label>New Password:</label>
				<input id="newPassword" name="newPassword" type="password" /><br>
			<label>Confirm Password:</label>
				<input id="confirmPassword" name="confirmPassword" type="password" /><br>
		</div>
		
		<!--- If the user is UNIT user, show the option to change UICs, or keep it hidden --->
		<div <? echo ($row['userType'] == "UNIT" ? "style=\"display:block\"" : "style=\"display:none\"");?>>
			<br>
			<label>Change unit UIC</label>
				<select name="userUIC">
				<option value="<? echo $row['userUIC'] ?>" selected><? echo $row['userUIC'] ?></option>
				<?php
					$query="SELECT units.unitUIC FROM units";
					$result = readDB($query);
					while($units = mysql_fetch_array($result, MYSQL_ASSOC))
					{ 
						$uic = $units['unitUIC'];
						echo "<option value=\"$uic\">$uic</option>";
					}?>
		</select>
		</div>
		
		<!--- If the user is RRBN type, show the option to change permissions, or else keep it hidden --->
		<div id="permissionPanel" <? echo ($row['userType'] == "RRBN" ? "style=\"display:block\"" : "style=\"display:none\"");?>>
			<p>---------- User Permissions ----------</p>
			<input type="checkbox" name="managePositions" <?echo($row['userCanManagePositions']=="Y")?checked:NULL;?>>Manage Positions</input>
			<input type="checkbox" name="updateDatabase" <?echo($row['userCanUpdateDatabase']=="Y")?checked:NULL;?>>Update Database</input>
			<input type="checkbox" name="manageUsers" <?echo($row['userCanManageUsers']=="Y")?checked:NULL;?>>Manage Users</input>
			<br />
			<input type="checkbox" name="viewTransactions" <?echo($row['userCanViewTransactions']=="Y")?checked:NULL;?>>View Transactions</input>
			<input type="checkbox" name="clearTransactions" <?echo ($row['userCanClearTransactions']=="Y")?checked:NULL;?>>Clear Transactions</input>
			<input type="checkbox" name="managerView" <?echo($row['userIsSuperviwer']=="Y")?checked:NULL;?>>Manager View (read only)</input>
			<p class="redtext">If you do not choose any permissions, the user will have basic view-only. This is the default RRNCO view.</p>
		</div>
		
		<!--- If the user is an RRNCO, show the team leader option, or hide it otherwise --->
		<div <? echo($row['userType'] == "RRNCO" ? "style=\"display:block\"" : "style=\"display:none\"");?>>
			<br>
			<label>This RRNCO is a Team Leader</label>
				<input type="checkbox" name="teamLeader" <? echo ($row['userIsTeamLeader'] == "Y" ? checked : null);?> />
		</div>
		<input name="userID" type="hidden" value="<? echo $row['userID'];?>">
		<input name="userType" type="hidden" value="<? echo $row['userType'];?>">
		<input name="userName" type="hidden" value="<? echo $row['userLoginName'];?>">
		<div id="formButtons">
			<br>
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
                location.replace('./index.php');
            });
    });
	function showPasswordPanel(onoff)
	{
		if (onoff == 'yes')
		{
			document.getElementById('PwdChangePanel').style.display = "block";
		}
		if (onoff == 'no')
		{
			document.getElementById('PwdChangePanel').style.display = "none";
		}
	}