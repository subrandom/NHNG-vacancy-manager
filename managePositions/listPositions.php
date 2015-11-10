<?php
session_start();
if ($_SESSION['permission']['ManagePositions'])
{
	require('listPositions_all.php');
}
else if ($_SESSION['permission']['TeamLeader'])
{
	require('listPositions_TL.php');
}
else if ($_SESSION['userRecruiterRSID']!= "")
{
	require('listPositions_RR.php');
}
else if ($_SESSION['permission']['SuperViewer'])
{
	require('listPositions_SU.php');
}
else
{
	require('../common/forbidden.html');
}
?>