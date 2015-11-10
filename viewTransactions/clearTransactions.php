<?php 
session_start();

if($_SESSION['permission']['ClearTransactions'])
{
	require_once ('../common/dbQuery.php');
	$now = time();
	//duplicate the current transaction database for backup
	$backupTable = "trans_".date("Ymd",$now);
	$query = "CREATE TABLE ".$backupTable." SELECT * FROM transactions";
	echo $query;
	writeDB($query);
	
	$query = "TRUNCATE TABLE transactions";
	echo $query;
	writeDB($query);
	?>
	<script type="text/javascript">
		window.setTimeout('location.replace("./index.php")');
	</script>	
	<?
}
else
{
	require_once('../common/forbidden.html');
}
?>