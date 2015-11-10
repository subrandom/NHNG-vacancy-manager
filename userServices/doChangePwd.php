<?php
session_start();
require_once ('../common/dbQuery.php');
$newPW = md5($_POST["newPW"]);
$id = $_SESSION["userID"];
$query = "UPDATE users SET userPassword = \"$newPW\" WHERE userID = \"$id\"";

writeDB($query);
?>
<br><br>
<h4>Update Complete</h4>