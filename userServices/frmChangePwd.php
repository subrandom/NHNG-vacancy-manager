<?php session_start()?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Recruiting Apps</title>
		<link rel="stylesheet" type="text/css" href="../css/main.css" />
		<link rel="stylesheet" type = "text/css" href = "./userServices.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="../images/favicon.ico">
		<script type = "text/javascript" src="../js/jquery.js" language = "javascript"></script>
		<script type = "text/javascript" src="../js/jquery.form.js" language = "javascript"></script>
		<script type = "text/javascript" src="../js/jquery.validate.js" language = "javascript"></script>
	</head>
	
	<body>
		<div id = "header">
			<h1>NHARNG Recruiting Applications</h1>
		</div>
		<div id = "mainMenu">
			<? require '../common/mainMenu.php'; ?>				
		</div>
			<div class="center">
				<?php 
				if (isset($_SESSION['userID']))
				{
					?>
					<br />
					<br />
					<h4>You may change your password here. Passwords must be at least 8 characters long.</h4>
					<form id="pwform" action="./doChangePwd.php" method="post">
						<label>New password: </label>
						<input id="newPW" name="newPW" type="password"/>
						<br /><br />
						<label>Confirm password: </label>
						<input id="confirmPW" name="confirmPW" type="password"/>
						<br /><br />
						<input type="submit" value="Change">
						<button id="cancel">Cancel</button>
					</form>
					
			</div>
			<?php
			}
			else
			{
				echo '<h4>You are not logged in, you cannot change your password</h4>';
			}
			?>
			<script>
				$(document).ready(function(){
					$('#cancel').click(function(event){
						location.replace('../index.php');
					});
				$('#pwform').validate({
					rules: {
						newPW: {
							required: true,
							minlength: 8
							},
						confirmPW: {
							required: true,
							minlength: 8,
							equalTo: "#newPW"
							}
					}
				});
         var options= {
            success: function(){
                window.setTimeout('location.replace("../index.php")',2200);
            }
        };
        $('#pwform').ajaxForm(options);
    });
</script>
	</body>
</html>
