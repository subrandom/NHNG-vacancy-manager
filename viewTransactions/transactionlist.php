<?php
session_start();
if($_SESSION['permission']['ViewTransactions'])
{
	require_once('../common/dbQuery.php');
    $query = "SELECT * FROM transactions";
    $result = readDB($query);
?>
    <table id ="transactionlist" class="tablesorter">
        <thead>
            <tr>
            	<th>ID</th>
                <th>TIMESTAMP</th>
                <th>USER</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
        <? while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
 ?>
            <tr>
                <td><? echo $row['transid']; ?></td>
                <td><? echo $row['timestamp']; ?></td>
                <td><? echo $row['user']; ?></td>
                <td><? echo $row['detail']; ?></td>
            </tr>
<? 
			}	 ?>
    </tbody>
</table>
<? 
}
else
{
require_once('../common/forbidden.html');
}
?>

<script
	type="text/javascript" src="../js/jquery.js"></script>
<script
	type="text/javascript" src="../js/jquery.tablesorter.js"></script>
<script>
    $(document).ready(function(){
        $('#transactionlist').tablesorter({
        	sortList: [[0,1]]
        	});
    });
    </script>