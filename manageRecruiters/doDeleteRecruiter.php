<?php
	session_start();
	if ($_SESSION['permission']['ManageUsers'] && $_POST['id'])
	{
		require ('../common/dbQuery.php');
		
		//delete the user from the user database
		$id = $_POST['id'];
		$query = "DELETE FROM recruiters WHERE recruiterID = \"$id\"";
		writeDB($query);

		//update transaction database
		$transuser = $_SESSION['userLoginName'];
		$transdetail = "RRNCO DELETE//RRNCO: ".$_POST['rrncoName']."  ID: ".$_POST['id'];
		writeTrans($transuser, $transdetail);

		?>
		<script type="text/javascript">
			alert("Recruiter Deleted!");
			window.setTimeout('location.replace("./index.php")');
		</script>
		<?
	}
	else
	{
		echo "<p>There has been a serious error. Please call support.</p>";
	}

