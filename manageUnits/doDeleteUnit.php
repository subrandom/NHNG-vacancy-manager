<?php
	session_start();
	if ($_SESSION['permission']['UpdateDatabase'] && $_POST['unitUIC'])
	{
		require ('../common/dbQuery.php');
		
		//delete the unit from the database
		$id = $_POST['unitUIC'];
		$query = "DELETE FROM units WHERE unitUIC = '$id'";
		writeDB($query);

		//update transaction database
		$transuser = $_SESSION['userLoginName'];
		$transdetail = "UNIT DELETE//UNIT: ".$_POST['unitUIC'];
		writeTrans($transuser, $transdetail);

		?>
		<script type="text/javascript">
			alert("Unit Deleted!");
			window.setTimeout('location.replace("./index.php")');
		</script>
		<?
	}
	else
	{
		echo "<p>There has been a serious error. Please call support.</p>";
	}

