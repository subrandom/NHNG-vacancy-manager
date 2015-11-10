<?php
session_start();
if ($_SESSION['permission']['UpdateDatabase']) 
{
	require('../common/dbQuery.php');
	$query = "SELECT * FROM units";
	$result = readDB($query);
?>
<button id="addUnit">New Unit</button>
<br>
<br>
<table id="unitList" class="tablesorter">
	<thead>
		<tr>
			<th>Edit</th>
			<th>Unit UIC</th>
			<th>Unit Designation</th>
			<th>Unit Location</th>
			<th>Parent Unit UIC</th>
			<th>Off Limits<br>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
	<?
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { ?>
		<tr>
			<td align="center"><img id="edit" alt="editrecord" src="../images/edit.png" /></td>
			<td id="uic"><? echo $row['unitUIC'];?></td>
			<td><? echo $row['unitDesignation'];?></td>
			<td><? echo $row['unitLocation']; ?></td>
			<td><? echo $row['unitParentUnit']; ?></td>
			<td id = "offlimits"><? echo strtoupper($row['unitOffLimits']); ?></td>
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
    <script type="text/javascript">
        $(document).ready(function(){
            $('td[id=offlimits]').click(function(event){
            	event.preventDefault();
            	var str = "doChangeOffLimits.php?id=" + $(this).closest("tr").find("td#uic").text();
            	$('#toppanel').load(str);
            	});
            $('td[id=offlimits]').hover(function(){
            	$(this).css('cursor','pointer');
            },function(){
            	$(this).css('cursor','auto');
            });
        });
</script>