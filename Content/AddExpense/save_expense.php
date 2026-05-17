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

$title    = mysqli_real_escape_string($connect, trim($data['title'] ?? ''));
$total    = (float)($data['total'] ?? 0);
$group_id = (int)($data['group_id'] ?? 0);
$paid_by  = (int)($data['paid_by'] ?? 0);
$category = mysqli_real_escape_string($connect, $data['category'] ?? 'food');
$splits   = $data['splits'] ?? [];

if (!$title || $total <= 0 || !$group_id || !$paid_by || empty($splits)) {
  echo json_encode(['error' => 'Missing required fields']); exit;
}

// Verify current user is in this group
$check = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT id FROM group_members WHERE group_id = $group_id AND user_id = $current_user_id LIMIT 1"
));
if (!$check) { echo json_encode(['error' => 'Not a member of this group']); exit; }

$today = date('Y-m-d');

mysqli_begin_transaction($connect);
$ok = true;

$ok = $ok && mysqli_query($connect,
  "INSERT INTO expenses (title, category, expense_date, paid_by, group_id, created_at)
   VALUES ('$title', '$category', '$today', $paid_by, $group_id, NOW())"
);
$expense_id = (int)mysqli_insert_id($connect);

foreach ($splits as $s) {
  $uid = (int)$s['user_id'];
  $amt = round((float)$s['amount'], 2);
  $ok  = $ok && mysqli_query($connect,
    "INSERT INTO expense_splits (expense_id, user_id, amount, is_settled)
     VALUES ($expense_id, $uid, $amt, 0)"
  );
}

if ($ok) {
  mysqli_query($connect, "UPDATE groups SET updated_at = NOW() WHERE id = $group_id");
  mysqli_commit($connect);
  echo json_encode(['success' => true, 'expense_id' => $expense_id]);
} else {
  mysqli_rollback($connect);
  echo json_encode(['error' => mysqli_error($connect)]);
}
