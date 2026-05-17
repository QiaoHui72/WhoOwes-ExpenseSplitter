<?php
//create a connection to the database
$servername = "localhost"; //127.0.0.1 //computer name
$username = "root"; //default username admin
$password = ""; //default password is empty
$dbname = "whoowes"; //database name

$connect = mysqli_connect($servername, $username, $password, $dbname);
if (!$connect) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>