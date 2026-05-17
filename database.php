<?php

$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASS");
$database = getenv("DB_NAME");
$port = getenv("DB_PORT");

$connect = new mysqli($host, $user, $password, $database, $port);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}
?>