<?php
$dbhost = "localhost"; 
$dbuser = "root";
$dbpass = "";
$dbname = "thechatterup_db";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname); //connect to db

if (!$conn) { //checks if $connection is not sucsessful, otherwise this script terminates
    die("Failed to connect!");
}
?>