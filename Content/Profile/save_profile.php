<?php
session_start();
include '../../database.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}
$uid = (int) $_SESSION['user_id'];

$data  = json_decode(file_get_contents('php://input'), true);
$name  = trim($data['name']  ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');

if (!$name) {
  echo json_encode(['success' => false, 'error' => 'Name is required.']);
  exit;
}

$name  = mysqli_real_escape_string($connect, $name);
$email = mysqli_real_escape_string($connect, $email);
$phone = mysqli_real_escape_string($connect, $phone);

mysqli_query($connect,
  "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$uid"
);

echo json_encode(['success' => true]);
