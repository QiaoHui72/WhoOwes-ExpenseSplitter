<?php
session_start();
include '../../database.php';

if (empty($_SESSION['user_id'])) {
  header('Location: ../../Authorization/login.php');
  exit;
}
$current_user_id = (int) $_SESSION['user_id'];
$group_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$group_id) { header('Location: group.php'); exit; }

// Verify membership
$chk = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT 1 FROM group_members WHERE group_id = $group_id AND user_id = $current_user_id LIMIT 1"
));
if (!$chk) { header('Location: group.php'); exit; }

// Current user (sidebar)
$u = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT * FROM users WHERE id = $current_user_id LIMIT 1"
));
$user_name     = $u['name'];
$user_initials = strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($user_name)))));

// Group info + expense date range
$grp = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT g.id, g.name, g.icon, g.created_at,
     (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) AS member_count,
     (SELECT MIN(e.expense_date) FROM expenses e WHERE e.group_id = g.id) AS first_expense,
     (SELECT MAX(e.expense_date) FROM expenses e WHERE e.group_id = g.id) AS last_expense
   FROM `groups` g WHERE g.id = $group_id LIMIT 1"
));

// Total group spend
$total_spend = (float) mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE group_id = $group_id"
))['total'];

// My balance in this group
$owed_to_me = (float) mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(es.amount), 0) AS total
   FROM expense_splits es JOIN expenses e ON e.id = es.expense_id
   WHERE e.group_id = $group_id AND e.paid_by = $current_user_id
     AND es.user_id != $current_user_id AND es.is_settled = 0"
))['total'];

$i_owe = (float) mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(es.amount), 0) AS total
   FROM expense_splits es JOIN expenses e ON e.id = es.expense_id
   WHERE e.group_id = $group_id AND es.user_id = $current_user_id
     AND e.paid_by != $current_user_id AND es.is_settled = 0"
))['total'];

$my_balance = $owed_to_me - $i_owe;

// Group settlement progress (all splits in group)
$sp = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COUNT(*) AS total_splits,
     COALESCE(SUM(es.is_settled), 0) AS settled_splits,
     COALESCE(SUM(CASE WHEN es.is_settled = 1 THEN es.amount ELSE 0 END), 0) AS settled_amount,
     COALESCE(SUM(es.amount), 0) AS total_amount
   FROM expense_splits es
   JOIN expenses e ON e.id = es.expense_id
   WHERE e.group_id = $group_id"
));
$total_splits   = (int)$sp['total_splits'];
$settled_splits = (int)$sp['settled_splits'];
$settled_pct    = $total_splits > 0 ? round($settled_splits / $total_splits * 100) : 100;
$settled_amount = (float)$sp['settled_amount'];
$total_amount   = (float)$sp['total_amount'];

// All expenses in this group (newest first)
$expenses = mysqli_fetch_all(mysqli_query($connect,
  "SELECT e.id, e.title, e.category, e.expense_date, e.amount,
     u.name AS paid_by_name, e.paid_by
   FROM expenses e
   JOIN users u ON u.id = e.paid_by
   WHERE e.group_id = $group_id
   ORDER BY e.expense_date DESC, e.created_at DESC
   LIMIT 30"
), MYSQLI_ASSOC);

// Group expenses by date
$grouped_expenses = [];
foreach ($expenses as $exp) {
  $grouped_expenses[$exp['expense_date']][] = $exp;
}

// Members with per-member balances vs current user
$members = mysqli_fetch_all(mysqli_query($connect,
  "SELECT u.id, u.name,
     COALESCE((SELECT SUM(es.amount) FROM expense_splits es JOIN expenses e ON e.id = es.expense_id
       WHERE e.group_id = $group_id AND e.paid_by = $current_user_id AND es.user_id = u.id AND es.is_settled = 0), 0) AS they_owe_me,
     COALESCE((SELECT SUM(es2.amount) FROM expense_splits es2 JOIN expenses e2 ON e2.id = es2.expense_id
       WHERE e2.group_id = $group_id AND e2.paid_by = u.id AND es2.user_id = $current_user_id AND es2.is_settled = 0), 0) AS i_owe_them
   FROM users u
   JOIN group_members gm ON gm.user_id = u.id AND gm.group_id = $group_id
   ORDER BY u.name"
), MYSQLI_ASSOC);

// Helpers
function fmt($n) { return 'RM ' . number_format($n, 2); }

function cat_icon($cat) {
  return match($cat) {
    'food'          => 'utensils',
    'utilities'     => 'zap',
    'shopping'      => 'shopping-bag',
    'transport'     => 'car',
    'entertainment' => 'tv-2',
    'travel'        => 'plane',
    'rent'          => 'home',
    default         => 'circle',
  };
}

function cat_style($cat) {
  return match($cat) {
    'food'          => ['bg' => '#e0f2fe', 'stroke' => '#0284c7'],
    'utilities'     => ['bg' => '#ede9fe', 'stroke' => '#7c3aed'],
    'shopping'      => ['bg' => '#e0f2fe', 'stroke' => '#0369a1'],
    'transport'     => ['bg' => '#fef3c7', 'stroke' => '#d97706'],
    'entertainment' => ['bg' => '#fce7f3', 'stroke' => '#db2777'],
    'travel'        => ['bg' => '#e0f2fe', 'stroke' => '#0284c7'],
    'rent'          => ['bg' => '#fee2e2', 'stroke' => '#dc2626'],
    default         => ['bg' => '#eef2ff', 'stroke' => '#1e3a7a'],
  };
}

function gd_group_icon($icon) {
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

function date_lbl($d) {
  $today     = date('Y-m-d');
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  if ($d === $today)     return 'Today';
  if ($d === $yesterday) return 'Yesterday';
  return date('M j, Y', strtotime($d));
}

$avatar_colors = ['#14b8a6','#3b82f6','#6366f1','#8b5cf6','#f59e0b','#10b981','#0284c7','#db2777'];

// Date range for hero
if ($grp['first_expense']) {
  $f_date = date('M Y', strtotime($grp['first_expense']));
  $l_date = date('M Y', strtotime($grp['last_expense']));
  $date_range = ($f_date === $l_date) ? $f_date : "$f_date – $l_date";
} else {
  $date_range = 'No expenses yet';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhoOwes — <?= htmlspecialchars($grp['name']) ?></title>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="group_details.css">
  <link rel="stylesheet" href="../Sidebar/sidebar.css">
</head>
<body>
<div class="app">

  <?php $active_nav = 'groups'; include '../Sidebar/sidebar.php'; ?>

  <main class="main">

    <!-- Back -->
    <a href="group.php" class="gd-back">
      <i data-lucide="arrow-left" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round"></i>
      Back to Groups
    </a>

    <!-- Hero -->
    <div class="gd-hero">
      <div class="gd-hero-icon">
        <i data-lucide="<?= gd_group_icon($grp['icon']) ?>" style="width:28px;height:28px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"></i>
      </div>
      <div class="gd-hero-info">
        <div class="gd-hero-name"><?= htmlspecialchars($grp['name']) ?></div>
        <div class="gd-hero-meta">
          <span><?= (int)$grp['member_count'] ?> member<?= (int)$grp['member_count'] !== 1 ? 's' : '' ?></span>
          <span><?= htmlspecialchars($date_range) ?></span>
        </div>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="gd-stats">

      <div class="gd-stat-card">
        <div class="gd-stat-lbl">Total Group Spend</div>
        <div class="gd-stat-val"><?= fmt($total_spend) ?></div>
      </div>

      <div class="gd-stat-card">
        <div class="gd-stat-lbl">Your Balance</div>
        <?php if ($my_balance > 0): ?>
          <div class="gd-stat-val green"><?= fmt($my_balance) ?><span class="gd-badge owed">OWED</span></div>
        <?php elseif ($my_balance < 0): ?>
          <div class="gd-stat-val red"><?= fmt(abs($my_balance)) ?><span class="gd-badge owe">OWE</span></div>
        <?php else: ?>
          <div class="gd-stat-val"><?= fmt(0) ?><span class="gd-badge settled">SETTLED</span></div>
        <?php endif; ?>
      </div>

      <div class="gd-stat-card">
        <div class="gd-stat-lbl">Settlement</div>
        <div class="gd-stat-val"><?= $settled_pct ?>%</div>
        <button class="gd-settle-btn" onclick="suOpen()">Settle Up</button>
      </div>

    </div>

    <!-- Progress Bar -->
    <div class="gd-progress-card">
      <div class="gd-progress-header">
        <span class="gd-progress-title">Overall Settlement Progress</span>
        <span class="gd-progress-pct"><?= $settled_pct ?>% settled</span>
      </div>
      <div class="gd-progress-track">
        <div class="gd-progress-fill <?= $settled_pct === 100 ? 'full' : '' ?>" style="width:<?= $settled_pct ?>%"></div>
      </div>
      <div class="gd-progress-sub"><?= fmt($settled_amount) ?> of <?= fmt($total_amount) ?> settled</div>
    </div>

    <!-- Two-column grid -->
    <div class="gd-grid">

      <!-- Expenses -->
      <div class="gd-panel">
        <div class="gd-panel-title">Recent Expenses</div>

        <?php if (empty($grouped_expenses)): ?>
        <div class="gd-empty">No expenses yet. Add the first one!</div>
        <?php else: ?>
        <?php foreach ($grouped_expenses as $date => $exps): ?>
        <div class="gd-date-lbl"><?= date_lbl($date) ?></div>
        <?php foreach ($exps as $exp):
          $cs = cat_style($exp['category']);
          $ci = cat_icon($exp['category']);
          $paid_by_me = ((int)$exp['paid_by'] === $current_user_id);
          $payer = $paid_by_me ? 'You paid' : htmlspecialchars($exp['paid_by_name']) . ' paid';
        ?>
        <div class="gd-exp-row">
          <div class="gd-exp-ico" style="background:<?= $cs['bg'] ?>">
            <i data-lucide="<?= $ci ?>" style="stroke:<?= $cs['stroke'] ?>;fill:none;width:18px;height:18px;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"></i>
          </div>
          <div class="gd-exp-info">
            <div class="gd-exp-title"><?= htmlspecialchars($exp['title']) ?></div>
            <div class="gd-exp-sub"><?= $payer ?></div>
          </div>
          <div class="gd-exp-amt">
            <div class="gd-exp-total"><?= fmt($exp['amount']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Members -->
      <div class="gd-panel">
        <div class="gd-panel-title">Members (<?= (int)$grp['member_count'] ?>)</div>
        <?php foreach ($members as $m):
          $net   = (float)$m['they_owe_me'] - (float)$m['i_owe_them'];
          $init  = strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($m['name'])))));
          $color = $avatar_colors[(int)$m['id'] % count($avatar_colors)];
          $is_me = ((int)$m['id'] === $current_user_id);
        ?>
        <div class="gd-member-row">
          <div class="gd-mem-avatar" style="background:<?= $color ?>">
            <?= htmlspecialchars($init) ?>
          </div>
          <div class="gd-mem-info">
            <div class="gd-mem-name"><?= htmlspecialchars($m['name']) ?><?= $is_me ? ' (you)' : '' ?></div>
            <?php if ($is_me): ?>
              <div class="gd-mem-status gray">You</div>
            <?php elseif ($net > 0): ?>
              <div class="gd-mem-status green">Owes you <?= fmt($net) ?></div>
            <?php elseif ($net < 0): ?>
              <div class="gd-mem-status red">You owe <?= fmt(abs($net)) ?></div>
            <?php else: ?>
              <div class="gd-mem-status gray">All settled</div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>

    <!-- Add Expense -->
    <button class="gd-add-btn" onclick="aeOpen()">
      <i data-lucide="plus" style="width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round"></i>
      Add Expense to this Group
    </button>

  </main>
</div>
</body>
</html>
