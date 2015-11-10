<?php
session_start();
if ($_SESSION['permission']['ManageUsers'])
{
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="../css/main.css" />
		<link rel="stylesheet" type="text/css" href="../css/sheets.css" />
		<link rel="stylesheet" type="text/css" href="./usermanager.css" />
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
		<div id="bottompanel"><?php require 'userlist.php';?></div>
		<div id="footer"><? require '../common/footer.htm'; ?></div>
<script type="text/javascript">
	window.onresize = function() {setHeight();}
    $(document).ready(function(){
    	setHeight();
		$('#userlist').tablesorter({
            headers:{
                0: {sorter:false},
                5: {sorter:false}
                }
            });
            $('#addUser').click(function(event){
                event.preventDefault();
                $('#bottompanel').load('frmCreateUser.php');
            });
            $('td img[id=edit]').hover(function(){
                $(this).css('cursor','pointer');
            },function() {
                $(this).css('cursor','auto');
            });
            $('td img[id=edit]').click(function(event){
                event.preventDefault();
                var str = "frmModifyUser.php?id=" + $(this).closest("tr").find("td.hidden").text();
                $('#bottompanel').load(str);
            });
            $('td img[id=delete]').hover(function(){
                $(this).css('cursor','pointer');
            },function() {
                $(this).css('cursor','auto');
            });
            $('td img[id=delete]').click(function(event){
                event.preventDefault();
                var str = "frmDeleteUser.php?id=" + $(this).closest("tr").find("td.hidden").text();
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
