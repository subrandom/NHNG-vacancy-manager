<?php
session_start();
if ($_SESSION['permission']['ManageUsers']) 
{
	require('../common/dbQuery.php');
	$query = "SELECT * FROM users";
	$result = readDB($query);
?>
<h4>All Users</h4>
<button id="addUser">New User</button>
<br>
<br>
<table id="userlist" class="tablesorter">
	<thead>
		<tr>
			<th>Edit</th>
			<th>Username</th>
			<th>Real Name</th>
			<th>User Type</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
	<?
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { ?>
		<tr>
			<td align="center"><img id="edit" alt="editrecord" src="../images/edit.png" /></td>
			<td class="hidden"><? echo $row['userID'];?></td>
			<td><? echo $row['userLoginName'];?></td>
			<td><? echo $row['userFirstName']." ".$row['userLastName'];?></td>
			<td><? echo $row['userType']; ?></td>
			<td align="center"><img id="delete" alt="delete" src="../images/trash.png" /></td>
			<?
			}
			?>
	</tbody>
</table>
<? 
}
else
{
require_once('../common/forbidden.html');
}
?>
