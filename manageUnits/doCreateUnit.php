<?php
session_start();
if ($_SESSION['permission']['UpdateDatabase']!=NULL)
{
	require ('../common/dbQuery.php');
	
	//get variables from POST
	$unitUIC = strtoupper($_POST['unitUIC']);
	$unitDesignation = strtoupper($_POST['unitDesignation']);
	$unitLocation = strtoupper($_POST['unitLocation']);
	$unitOffLimits = $_POST['unitOffLimits'];
	$unitParentUnit = strtoupper($_POST['unitParentUnit']);
	//function variables
	$errorList = "";
	// error checking!
	//arbitrary lengths
	if (strlen($unitUIC) <> 5)
	{
		$errorList.="<li>The Unit UIC must be exactly 5 characters.</li>";
	}
	if (strlen($unitDesignation) < 4)
	{
		$errorList.="<li>Unit Designation must be at least 4 characters</li>";
	}
	if (strlen($unitLocation) < 5)
	{
		$errorList.="<li>Unit Location must be at least 5 characters</li>";		
	}
	
	//if parent UIC is entered, it must be 5 characters and must exist!
	if (strlen($unitParentUnit) <> 0)
	{
		if (strlen($unitParentUnit) <> 5)
		{
			$errorList.="<li>The Parent Unit UIC must be exactly 5 characters if entered, or blank.</li>";
		}
		
		$query = "SELECT * FROM units WHERE unitUIC = '$unitParentUnit'";
		$result = readDB($query);
		$data = mysql_fetch_array($result, MYSQL_ASSOC);
	
		if ($data == null)
		{
			$errorList .= "<li>The Parent UIC you entered does not exist.</li>";
		}	
		
	}
		
	//check to see if the UIC is already used
	$query = "SELECT * FROM units WHERE unitUIC = '$unitUIC'";
	$result = readDB($query);
	$data = mysql_fetch_array($result, MYSQL_ASSOC);
	
	if ($data != null)
	{
		$errorList .= "<li>The UIC you are attempting to create is already in use.</li>";
	}
	
	//Off Limits has to be selected 
	if ($unitOffLimits == null)
	{
		$errorList.="<li>You must indicate if the unit is off-limits for recruiting.</li>";
	}
	//errors up!
	if(strlen($errorList) != 0)
	{
		echo "<br><p style=\"color:red\">Please correct the following errors and submit the request again:</p>";
		echo "<ul>".$errorList."</ul>";
	}
	else
	{
		//otherwise, create the record
		$query = "INSERT INTO units (unitUIC, unitLocation, unitDesignation, unitParentUnit, unitOffLimits) VALUES
			('$unitUIC','$unitLocation','$unitDesignation','$unitParentUnit','$unitOffLimits')";
		writeDB($query);
		
		//update transactions
		$transdetail = "UNIT CREATE//unit:".$unitUIC."; ".$unitDesignation;
		$transuser = $_SESSION['userLoginName'];
		writeTrans($transuser, $transdetail);	
		?>
		<script type="text/javascript">
			alert("Unit Created!");
			window.setTimeout('location.replace("./index.php")');
		</script>
		<?	
	}
}
else
{
	echo '<p>There has been a serious error. Please call support.</p>';
}
	