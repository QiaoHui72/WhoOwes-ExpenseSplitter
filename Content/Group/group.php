<?php
session_start();
include '../../database.php';

if (empty($_SESSION['user_id'])) {
  header('Location: ../../Authorization/login.php');
  exit;
}
$current_user_id = (int) $_SESSION['user_id'];

// Current user (for sidebar)
$u = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT * FROM users WHERE id = $current_user_id LIMIT 1"
));
$user_name     = $u['name'];
$user_initials = strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($user_name)))));

// All groups for this user with balance + progress data
$groups_result = mysqli_query($connect,
  "SELECT g.id, g.name, g.icon, g.updated_at,
     (SELECT COUNT(*) FROM group_members gm2 WHERE gm2.group_id = g.id) AS member_count,
     COALESCE((
       SELECT SUM(es2.amount) FROM expense_splits es2
       JOIN expenses e2 ON e2.id = es2.expense_id
       WHERE e2.paid_by = $current_user_id AND es2.user_id != $current_user_id
         AND es2.is_settled = 0 AND e2.group_id = g.id
     ), 0) AS owed_to_me,
     COALESCE((
       SELECT SUM(es3.amount) FROM expense_splits es3
       JOIN expenses e3 ON e3.id = es3.expense_id
       WHERE es3.user_id = $current_user_id AND e3.paid_by != $current_user_id
         AND es3.is_settled = 0 AND e3.group_id = g.id
     ), 0) AS i_owe,
     COALESCE((SELECT COUNT(*) FROM expense_splits es4
       JOIN expenses e4 ON e4.id = es4.expense_id
       WHERE e4.group_id = g.id), 0) AS total_splits,
     COALESCE((SELECT COUNT(*) FROM expense_splits es5
       JOIN expenses e5 ON e5.id = es5.expense_id
       WHERE e5.group_id = g.id AND es5.is_settled = 1), 0) AS settled_splits,
     COALESCE((SELECT MAX(e6.created_at) FROM expenses e6
       WHERE e6.group_id = g.id), g.updated_at) AS last_activity
   FROM `groups` g
   JOIN group_members gm ON gm.group_id = g.id
   WHERE gm.user_id = $current_user_id
   ORDER BY last_activity DESC"
);
$groups = mysqli_fetch_all($groups_result, MYSQLI_ASSOC);

// Outstanding balance = abs(net across all groups)
$total_owed_to_me = array_sum(array_column($groups, 'owed_to_me'));
$total_i_owe      = array_sum(array_column($groups, 'i_owe'));
$outstanding      = abs($total_owed_to_me - $total_i_owe);

// Helpers
function fmt($n) { return 'RM ' . number_format($n, 2); }

function time_ago($datetime) {
  if (!$datetime) return '';
  $diff = time() - strtotime($datetime);
  if ($diff < 60)    return 'Just now';
  if ($diff < 3600)  return floor($diff / 60) . 'm ago';
  if ($diff < 86400) return floor($diff / 3600) . 'h ago';
  return floor($diff / 86400) . 'd ago';
}

function group_icon_name($icon) {
  return match($icon) {
    'house'          => 'home',
    'flight_takeoff' => 'plane',
    'coffee'         => 'coffee',
    'receipt'        => 'receipt',
    'landscape'      => 'mountain',
    'couple'         => 'heart',
    default          => 'users',
  };
}

function group_icon_style($icon) {
  return match($icon) {
    'house'          => ['bg' => '#ede9fe', 'stroke' => '#7c3aed'],
    'flight_takeoff' => ['bg' => '#e0f2fe', 'stroke' => '#0284c7'],
    'coffee'         => ['bg' => '#dcfce7', 'stroke' => '#16a34a'],
    'receipt'        => ['bg' => '#fee2e2', 'stroke' => '#dc2626'],
    'landscape'      => ['bg' => '#fef3c7', 'stroke' => '#d97706'],
    default          => ['bg' => '#eef2ff', 'stroke' => '#1e3a7a'],
  };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhoOwes — Groups</title>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="group.css">
  <link rel="stylesheet" href="../Sidebar/sidebar.css">
</head>
<body>
<div class="app">

  <?php $active_nav = 'groups'; include '../Sidebar/sidebar.php'; ?>

  <main class="main">

    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h2>Your Groups</h2>
        <p>Track shared expenses with friends &amp; family.</p>
      </div>
    </div>

    <!-- Outstanding Balance Banner -->
    <div class="balance-banner">
      <div class="balance-info">
        <div class="balance-label">OUTSTANDING BALANCE</div>
        <div class="balance-amount"><?= fmt($outstanding) ?></div>
        <div class="balance-sub">Across <?= count($groups) ?> active groups</div>
      </div>
      <button class="btn-settle-all">Settle All</button>
    </div>

    <!-- Groups Grid -->
    <div class="groups-grid">

      <?php foreach ($groups as $grp):
        $net     = (float)$grp['owed_to_me'] - (float)$grp['i_owe'];
        $total   = (int)$grp['total_splits'];
        $settled = (int)$grp['settled_splits'];
        $pct     = $total > 0 ? round($settled / $total * 100) : 100;
        $style   = group_icon_style($grp['icon']);
        $iname   = group_icon_name($grp['icon']);
        $when    = time_ago($grp['last_activity']);
      ?>
      <a class="group-card" href="group_details.php?id=<?= $grp['id'] ?>" style="text-decoration:none;color:inherit;display:block;">
        <div class="group-card-row">
          <div class="group-icon" style="background:<?= $style['bg'] ?>">
            <i data-lucide="<?= $iname ?>" style="stroke:<?= $style['stroke'] ?>;fill:none;width:22px;height:22px;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round"></i>
          </div>
          <div class="group-meta">
            <strong><?= htmlspecialchars($grp['name']) ?></strong>
            <span><?= (int)$grp['member_count'] ?> members<?= $when ? ' &middot; ' . $when : '' ?></span>
            <?php if ($net > 0): ?>
              <div class="balance-text green">You are owed &nbsp;<b><?= fmt($net) ?></b></div>
            <?php elseif ($net < 0): ?>
              <div class="balance-text red">You owe &nbsp;<b><?= fmt(abs($net)) ?></b></div>
            <?php else: ?>
              <div class="balance-text gray">All settled up &nbsp;<b><?= fmt(0) ?></b></div>
            <?php endif; ?>
          </div>
          <span class="chevron">&#8250;</span>
        </div>
        <div class="progress-wrap">
          <div class="progress-track">
            <div class="progress-fill <?= $pct === 100 ? 'full' : '' ?>" style="width:<?= $pct ?>%"></div>
          </div>
          <span class="progress-label"><?= $pct ?>% settled</span>
        </div>
      </a>
      <?php endforeach; ?>

      <!-- Create New Group -->
      <div class="group-card new-card">
        <i data-lucide="user-plus" style="width:28px;height:28px;stroke:#1e3a7a;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round"></i>
        <span>Create New Group</span>
      </div>

    </div>

  </main>
</div>
</body>
</html>
