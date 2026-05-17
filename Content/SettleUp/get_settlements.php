<?php
session_start();
include '../../database.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
$uid = (int)$_SESSION['user_id'];

// What I owe each person (others paid, I have a split)
$r1 = mysqli_query($connect,
  "SELECT e.paid_by AS user_id, u.name, SUM(es.amount) AS amount
   FROM expense_splits es
   JOIN expenses e ON e.id = es.expense_id
   JOIN users u ON u.id = e.paid_by
   WHERE es.user_id = $uid AND e.paid_by != $uid AND es.is_settled = 0
   GROUP BY e.paid_by, u.name
   ORDER BY amount DESC"
);
$i_owe = mysqli_fetch_all($r1, MYSQLI_ASSOC);

// What each person owes me (I paid, they have a split)
$r2 = mysqli_query($connect,
  "SELECT es.user_id, u.name, SUM(es.amount) AS amount
   FROM expense_splits es
   JOIN expenses e ON e.id = es.expense_id
   JOIN users u ON u.id = es.user_id
   WHERE e.paid_by = $uid AND es.user_id != $uid AND es.is_settled = 0
   GROUP BY es.user_id, u.name
   ORDER BY amount DESC"
);
$owed_to_me = mysqli_fetch_all($r2, MYSQLI_ASSOC);

echo json_encode(['i_owe' => $i_owe, 'owed_to_me' => $owed_to_me]);
