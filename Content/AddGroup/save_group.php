<?php
session_start();
include '../../database.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Unauthorized']); exit;
}
$current_user_id = (int)$_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) { echo json_encode(['error' => 'Invalid data']); exit; }

$name    = mysqli_real_escape_string($connect, trim($data['name'] ?? ''));
$icon    = $data['icon'] ?? 'other';
$members = $data['members'] ?? [];

$allowed = ['house','flight_takeoff','couple','other','coffee','receipt','landscape'];
if (!in_array($icon, $allowed)) $icon = 'other';
$icon = mysqli_real_escape_string($connect, $icon);

if (!$name) { echo json_encode(['error' => 'Group name is required']); exit; }

mysqli_begin_transaction($connect);
$ok = true;

$ok = $ok && mysqli_query($connect,
  "INSERT INTO `groups` (name, icon, updated_at) VALUES ('$name', '$icon', NOW())"
);
$group_id = (int)mysqli_insert_id($connect);

// Creator as first member
$ok = $ok && mysqli_query($connect,
  "INSERT INTO group_members (group_id, user_id, joined_at)
   VALUES ($group_id, $current_user_id, NOW())"
);

foreach ($members as $uid) {
  $uid = (int)$uid;
  if ($uid <= 0 || $uid === $current_user_id) continue;
  $ok = $ok && mysqli_query($connect,
    "INSERT INTO group_members (group_id, user_id, joined_at)
     VALUES ($group_id, $uid, NOW())"
  );
}

if ($ok) {
  mysqli_commit($connect);
  echo json_encode(['success' => true, 'group_id' => $group_id]);
} else {
  mysqli_rollback($connect);
  echo json_encode(['error' => mysqli_error($connect)]);
}
