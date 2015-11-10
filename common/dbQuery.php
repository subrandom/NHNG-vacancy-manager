<?php
function openDB(){
    require_once ('../../secureInclude/ngappsbetaCredentials.php');
    //require_once ('../../../secureInclude/ngappsCredentials.php');
    mysql_connect('mysql.ngapps.net', dbusername, dbpassword);
    mysql_select_db(dbname) or die("Unable to select database");
}
function writeDB($query){
    openDB();
    mysql_query($query) or die("Couldn't write to the database!");
    mysql_close();
    return;
}
function readDB($query) {
    openDB();
    $result = mysql_query($query) or die("Couldn't query the database.");
    return $result;
    mysql_close();
}
function writeTrans($user,$detail)
{
	$now = time();
	$timestamp = date("Y-m-d @ G:i:s",$now);
	openDB();
	$query = "INSERT INTO transactions (timestamp,user,detail) VALUES ('$timestamp','$user','$detail')";
	writeDB($query);
}
?>
