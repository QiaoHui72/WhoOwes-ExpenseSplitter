<?php
//create a connection to the database
$servername = "localhost"; //127.0.0.1 //computer name
$username = "root"; //default username admin
$password = ""; //default password is empty
$dbname = "whoowes"; //database name

$connect = mysqli_connect($servername, $username, $password, $dbname);
//1. check connection
if(!$connect){
//    echo "Connection Successful<br>";
//}else{
    die("Connection Failed: " . mysqli_connect_error());
}
?>