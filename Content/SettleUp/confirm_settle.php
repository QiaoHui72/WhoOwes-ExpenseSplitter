<?php
session_start();
include '../../database.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
$uid = (int)$_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$targets = $data['target_user_ids'] ?? [];

if (empty($targets)) { echo json_encode(['error' => 'No targets specified']); exit; }

$ids = implode(',', array_filter(array_map('intval', $targets), function ($id) { return $id > 0; }));
if (!$ids) { echo json_encode(['error' => 'Invalid targets']); exit; }

$ok = mysqli_query($connect,
  "UPDATE expense_splits es
   JOIN expenses e ON e.id = es.expense_id
   SET es.is_settled = 1
   WHERE es.user_id = $uid
     AND e.paid_by IN ($ids)
     AND es.is_settled = 0"
);

if ($ok) {
  echo json_encode(['success' => true, 'affected' => mysqli_affected_rows($connect)]);
} else {
  echo json_encode(['error' => mysqli_error($connect)]);
}
