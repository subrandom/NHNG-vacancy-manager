<?php
	session_start();
	if ($_SESSION['permission']['ManageUsers'] && $_POST['id'])
	{
		require ('../common/dbQuery.php');
		
		//delete the user from the user database
		$id = $_POST['id'];
		$query = "DELETE FROM users WHERE userID = \"$id\"";
		writeDB($query);

		//update transaction database
		$transuser = $_SESSION['userLoginName'];
		$transdetail = "USER DELETE//user: ".$_POST['username']."  ID: ".$_POST['id'];
		writeTrans($transuser, $transdetail);
		
		unset ($_POST['id']);
		unset ($_POST['username']);
		?>
		<script type="text/javascript">
			alert("User Deleted!");
			window.setTimeout('location.replace("./index.php")');
		</script>
		<?
	}
	else
	{
		echo "<p>There has been a serious error. Please call support.</p>";
	}

?>