<?php
session_start();
include '../../database.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}
$current_user_id = (int) $_SESSION['user_id'];

$data      = json_decode(file_get_contents('php://input'), true);
$friend_id = isset($data['friend_id']) ? (int)$data['friend_id'] : 0;

if (!$friend_id || $friend_id === $current_user_id) {
  echo json_encode(['success' => false, 'error' => 'Invalid user.']);
  exit;
}

// Ensure table exists
mysqli_query($connect,
  "CREATE TABLE IF NOT EXISTS friendships (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    friend_id  INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_pair (user_id, friend_id)
  )"
);

// Insert both directions so each user sees the other in their list
mysqli_query($connect, "INSERT IGNORE INTO friendships (user_id, friend_id) VALUES ($current_user_id, $friend_id)");
mysqli_query($connect, "INSERT IGNORE INTO friendships (user_id, friend_id) VALUES ($friend_id, $current_user_id)");

echo json_encode(['success' => true]);
