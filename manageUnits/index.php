<?php
session_start();
if ($_SESSION['permission']['UpdateDatabase'])
{
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="../css/main.css" />
		<link rel="stylesheet" type="text/css" href="../css/sheets.css" />
		<link rel="stylesheet" type="text/css" href="./units.css" />
		<link rel="stylesheet" type="text/css"
			href="../css/cupertino/jquery-ui.css" />
		<script type="text/javascript" src="../js/jquery.js" language="javascript"></script>
		<script type="text/javascript" src="../js/jquery.tablesorter.js" language="javascript"></script>
		<script type="text/javascript" src="../js/jquery.form.js" language="javascript"></script>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title>Recruiting Apps</title>
		</head>
<body>
		<div id="header"><h1>Unit Manager</h1></div>
		<div id = "mainMenu">
			<? require '../common/mainMenu.php'; ?>			
		</div>
		<div id="toppanel"><?php require 'toppanel_default.php';?></div>
		<div id="divider"></div>
		<div id="bottompanel"><?php require 'unitList.php';?></div>
		<div id="footer"><? require '../common/footer.htm'; ?></div>		
<script type="text/javascript">
	window.onresize = function() {setHeight();}
    $(document).ready(function(){
    	setHeight();
		$('#unitList').tablesorter({
            headers:{
                0: {sorter:false},
                6: {sorter:false}
                }
            });
            $('#addUnit').click(function(event){
                event.preventDefault();
                $('#bottompanel').load('frmCreateUnit.php');
            });
            $('td img[id=edit]').hover(function(){
                $(this).css('cursor','pointer');
            },function() {
                $(this).css('cursor','auto');
            });
            $('td img[id=edit]').click(function(event){
                event.preventDefault();
                var str = "frmModifyUnit.php?id=" + $(this).closest("tr").find("td#uic").text();
                $('#bottompanel').load(str);
            });
            $('td img[id=delete]').hover(function(){
                $(this).css('cursor','pointer');
            },function() {
                $(this).css('cursor','auto');
            });
            $('td img[id=delete]').click(function(event){
                event.preventDefault();
                var str = "frmDeleteUnit.php?id=" + $(this).closest("tr").find("td#uic").text();
                $('#toppanel').load(str);
            });
            $('td a[id=changeOffLimits]').click(function(event){
            	event.preventDefault();
            	var str = "doChangeOffLimits.php?id=" + $(this).closest("tr").find("td#uic").text();
            	$('#toppanel').load(str);
            	});
    });
            function setHeight() {
			var WinHt = $(window).height();
			var TopHt = $('#toppanel').height();
			var BotHt = WinHt - (TopHt + 160);
			$('#bottompanel').css({
				"height" : BotHt});
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
