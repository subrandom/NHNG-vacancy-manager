<?php
session_start();
if ($_SESSION['permission']['ManageUsers']) 
{
	require('../common/dbQuery.php');
	$query = "SELECT * FROM recruiters";
	$result = readDB($query);
?>
<button id="addRRNCO">New RRNCO</button>
<br>
<br>
<table id="rrncoList" class="tablesorter">
	<thead>
		<tr>
			<th>Edit</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email Address</th>
			<th>RSID</th>
			<th>Team Leader<br>
					RSID</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
	<?
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { ?>
		<tr>
			<td align="center"><img id="edit" alt="editrecord" src="../images/edit.png" /></td>
			<td class="hidden"><? echo $row['recruiterID'];?></td>
			<td><? echo $row['recruiterFirstName'];?></td>
			<td><? echo $row['recruiterLastName'];?></td>
			<td><? echo $row['recruiterEmail']; ?></td>
			<td><? echo $row['recruiterRSID']; ?></td>
			<td><? echo $row['recruiterTeamLeaderRSID']; ?></td>
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
