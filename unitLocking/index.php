<?php
session_start();

if($_SESSION['permission']['UnitLocking'])
{
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="../css/main.css" />
		<link rel="stylesheet" type="text/css" href="../css/sheets.css" />
		<link rel="stylesheet" type="text/css" href="./unitlocking.css" />
		<link rel="stylesheet" type="text/css" href="../css/cupertino/jquery-ui.css" />
		<script type="text/javascript" src="../js/jquery.js" language="javascript"></script>
		<script type="text/javascript" src="../js/jquery.tablesorter.js" language="javascript"></script>
		<script type="text/javascript" src="../js/jquery.form.js" language="javascript"></script>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title>Recruiting Apps</title>
	</head>
	<body>
		<div id="header"><h1>User Manager</h1></div>
		<div id = "mainMenu">
			<? require '../common/mainMenu.php'; ?>			
		</div>
		<div id="toppanel"><?php require 'toppanel_default.php';?></div>
		<div id="divider"></div>
		<div id="bottompanel"><?php require 'showPositions.php';?></div>
		<div id="footer"><? require '../common/footer.htm'; ?></div>
		
		<script type="text/javascript">
			window.onresize=function () {setHeight();}
			$(document).ready(function()
			{
				setHeight(); 
			});

			function setHeight() 
			{
				var WinHt = $(window).height();
				var TopHt = $('#toppanel').height();
				var BotHt = WinHt - (TopHt + 195);
				$('#bottompanel').css({"height":BotHt});
			}
</script>

	</body>
</html>

<?
}
else
{
	require_once('../common/forbidden.html');
}
?>