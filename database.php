<?php

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
}
?>
