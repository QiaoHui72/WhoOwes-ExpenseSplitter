<?php
session_start();
include '../../database.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) { echo json_encode([]); exit; }
$current_user_id = (int)$_SESSION['user_id'];

$q = mysqli_real_escape_string($connect, trim($_GET['q'] ?? ''));

if ($q === '') {
  $result = mysqli_query($connect,
    "SELECT id, name, email FROM users
     WHERE id != $current_user_id
     ORDER BY name
     LIMIT 50"
  );
} else {
  $result = mysqli_query($connect,
    "SELECT id, name, email FROM users
     WHERE id != $current_user_id
       AND (name LIKE '%$q%' OR email LIKE '%$q%')
     ORDER BY name
     LIMIT 50"
  );
}
echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
