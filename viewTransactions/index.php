<?php
session_start();
if($_SESSION['permission']['ViewTransactions'])
{
	require_once ('../common/dbQuery.php');
	?>

	<!DOCTYPE HTML>
	<html lang="en">
	<head>
	<link rel="stylesheet" type="text/css" href="../css/main.css" />
	<link rel="stylesheet" type="text/css" href="../css/sheets.css" />
	<link rel="stylesheet" type="text/css" href="../css/cupertino/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="./transactions.css" />
	
	<script type="text/javascript" src="../js/jquery.js" language="javascript"></script>
	<script type="text/javascript" src="../js/jquery.tablesorter.js" language="javascript"></script>
	<script type="text/javascript" src="../js/jquery-ui.js" language="javascript"></script>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>Recruiting Apps</title>
	</head>
	<body>
	<div id="ngapps">
	<div id="header"> <h1>Transaction Viewer</h1> </div>
	<div id = "mainMenu">
		<? require '../common/mainMenu.php'; ?>			
	</div>
	<div id="toppanel"><? require 'toppanel_default.php';?></div>
	<div id="divider"></div>
	<div id="bottompanel"><? require 'transactionlist.php';?></div>
	<div id="footer"><? require '../common/footer.htm'; ?></div>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
	    setHeight();
	    window.onresize=function () {setHeight();}
	});
	
	
	function setHeight() {
	  	 var WinHt = $(window).height();
	     var TopHt = $('#toppanel').height();
	     var BotHt = WinHt - (TopHt + 155);
	     $('#bottompanel').css({"height":BotHt});
	}
	</script>
	</body>
	</html>
	<?php
}
else
{
	require_once('../common/forbidden.html');
}
?>


