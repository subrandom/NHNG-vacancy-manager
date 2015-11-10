<?php 
session_start();
if ($_SESSION['permission']['ManagePositions'] || $_SESSION['permission']['SuperViewer'])
{
	require('toppanel.php');
}
else if ($_SESSION['permission']['TeamLeader'])
{
	require('toppanel_TL.php');
}
else if ($_SESSION['userRecruiterRSID']!= "")
{
	require('toppanel_RR.php');
}
else
{
	require('../common/forbidden.html');
}
?>
