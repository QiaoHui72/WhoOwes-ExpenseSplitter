<?php
//create a connection to the database
$servername = "localhost"; //127.0.0.1 //computer name
$username = "root"; //default username admin
$password = ""; //default password is empty
$dbname = "whoowes"; //database name

<<<<<<< HEAD
$host     = getenv('DB_HOST') ?: 'db';
$user     = getenv('DB_USER') ?: 'whoowes';
$password = getenv('DB_PASS') ?: 'whoowes_pass';
$database = getenv('DB_NAME') ?: 'whoowes';
$port     = (int)(getenv('DB_PORT') ?: 3306);

try {
    $connect = new mysqli($host, $user, $password, $database, $port);
    if ($connect->connect_error) {
        die('Connection failed: ' . $connect->connect_error);
    }
} catch (Exception $e) {
    die('Connection failed: ' . $e->getMessage());
=======
$connect = mysqli_connect($servername, $username, $password, $dbname);
if (!$connect) {
    die("Connection Failed: " . mysqli_connect_error());
>>>>>>> parent of 8f362aa (Update database.php)
}
?>
