<?php session_start();?>
<h4>Welcome back <? echo $_SESSION['firstname'];?>!</h4>
<p class="center">The table below shows all transactions that have occured in the database during the accounting period. <br/>
	The transaction database is periodically purged for performance. If you need to see purged transactions, contact SFC Bradley.</p>
	<?
	if ($_SESSION['permission']['ClearTransactions'])
	{
		echo '<a class = "button" href = "./clearTransactions.php">Clear Log</a>';
	}
	?>
<script type="text/javascript">
 $(document).ready(function(){
        setHeight();
        });
</script>
