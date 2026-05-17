<?php
session_start();
include '../../database.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
  echo json_encode([]);
  exit;
}

$current_user_id = (int)$_SESSION['user_id'];
$group_id = (int)($_GET['group_id'] ?? 0);

if (!$group_id) { echo json_encode([]); exit; }

// Only return members if current user is in this group
$check = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT id FROM group_members WHERE group_id = $group_id AND user_id = $current_user_id LIMIT 1"
));
if (!$check) { echo json_encode([]); exit; }

$result = mysqli_query($connect,
  "SELECT u.id, u.name FROM users u
   JOIN group_members gm ON gm.user_id = u.id
   WHERE gm.group_id = $group_id
   ORDER BY u.name"
);
echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
