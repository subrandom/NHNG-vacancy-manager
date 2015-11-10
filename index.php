<?php session_start()?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Recruiting Apps</title>
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		<link rel="stylesheet" type="text/css" href="css/sheets.css" />
		<link rel="stylesheet" type="text/css" href="css/forms.css" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
		<script type = "text/javascript" src="js/jquery.js" language = "javascript"></script>
		<script type = "text/javascript" src="js/jquery.form.js" language = "javascript"></script>
		<script type = "text/javascript" src="js/jquery.validate.js" language = "javascript"></script>
	</head>
	
	<body>
		<div id = "header">
			<h1>NHARNG Recruiting Applications BETA</h1>
		</div>
		<div id = "mainMenu">
			<? require 'common/mainMenu.php'; ?>				
		</div>
		<div class = "center">
			<blockquote>
				23 January 2014: Version 2.4 of the Vacancy Manager is live!<br />
				You can view the <a href="https://www.evernote.com/shard/s3/sh/a89cedbe-00c5-48bd-87b2-d1b0885935da/cdbbf48c94f179d25db4fbbd3f223621" target="_blank">Change Log here</a>
			</blockquote>
		</div>
		<div class = "center">
			<?php
			if (!isset($_SESSION['userID']))
			{
			?>
				<p>Please log in.</p>
				<form id="login" action="common/doLogin.php" method="post">
					username:<input name="username" type="text" />
					<br>
					<br>
					password:<input type="password" name="password" />
					<br>
					<br>
					<button type="submit" value="Login">Login</button>
				</form>
			<?php
			}
			else
			{
				echo "<br /><br />";
				echo '<h4>Welcome back, '.$_SESSION['userFullName'].'!</h4>';
			}
				?>
				<br>
				<p>
					Donate to the R&amp;R Sunshine Fund!
					<br>
					You do NOT need a PayPal account to donate, you can use any major credit/debit card.
				</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="8NJEMA3T2RWQA">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
	</body>
</html>	