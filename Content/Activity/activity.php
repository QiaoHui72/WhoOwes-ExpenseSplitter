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

// Month-to-date transaction count
$month_count = (int) mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COUNT(*) AS cnt FROM expenses e
   JOIN group_members gm ON gm.group_id = e.group_id AND gm.user_id = $current_user_id
   WHERE MONTH(e.expense_date) = MONTH(CURDATE())
     AND YEAR(e.expense_date)  = YEAR(CURDATE())"
))['cnt'];

// Top 2 categories this month
$top_cats = mysqli_fetch_all(mysqli_query($connect,
  "SELECT e.category, COUNT(*) AS cnt FROM expenses e
   JOIN group_members gm ON gm.group_id = e.group_id AND gm.user_id = $current_user_id
   WHERE MONTH(e.expense_date) = MONTH(CURDATE())
     AND YEAR(e.expense_date)  = YEAR(CURDATE())
   GROUP BY e.category ORDER BY cnt DESC LIMIT 2"
), MYSQLI_ASSOC);

// Overall settled percentage
$sp = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COUNT(*) AS total, SUM(is_settled) AS settled
   FROM expense_splits WHERE user_id = $current_user_id"
));
$settled_pct = ($sp['total'] > 0) ? round($sp['settled'] / $sp['total'] * 100) : 100;

// Month-to-date: owed to me 
$month_owed = (float) mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(es.amount), 0) AS total
   FROM expense_splits es JOIN expenses e ON e.id = es.expense_id
   WHERE e.paid_by = $current_user_id AND es.user_id != $current_user_id
     AND MONTH(e.expense_date) = MONTH(CURDATE()) AND YEAR(e.expense_date) = YEAR(CURDATE())"
))['total'];

// Month-to-date: my share on others' expenses
$month_spent = (float) mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(es.amount), 0) AS total
   FROM expense_splits es JOIN expenses e ON e.id = es.expense_id
   WHERE es.user_id = $current_user_id AND e.paid_by != $current_user_id
     AND MONTH(e.expense_date) = MONTH(CURDATE()) AND YEAR(e.expense_date) = YEAR(CURDATE())"
))['total'];

$month_net = $month_owed - $month_spent;

// Recent expenses across user's groups
$expenses = mysqli_fetch_all(mysqli_query($connect,
  "SELECT e.id, e.title, e.category, e.created_at,
     e.paid_by, u.name AS actor_name, g.name AS group_name,
     es.amount AS my_amount
   FROM expenses e
   JOIN users u ON u.id = e.paid_by
   JOIN groups g ON g.id = e.group_id
   JOIN expense_splits es ON es.expense_id = e.id AND es.user_id = $current_user_id
   JOIN group_members gm ON gm.group_id = e.group_id AND gm.user_id = $current_user_id
   ORDER BY e.created_at DESC LIMIT 15"
), MYSQLI_ASSOC);

// Recent settlements involving the user (guard against missing table)
$_sr = mysqli_query($connect,
  "SELECT s.id, s.amount, s.created_at, s.paid_by, s.paid_to,
     g.name AS group_name,
     uf.name AS from_name, ut.name AS to_name
   FROM settlements s
   JOIN groups g ON g.id = s.group_id
   JOIN users uf ON uf.id = s.paid_by
   JOIN users ut ON ut.id = s.paid_to
   WHERE s.paid_by = $current_user_id OR s.paid_to = $current_user_id
   ORDER BY s.created_at DESC LIMIT 10"
);
$settlements = $_sr ? mysqli_fetch_all($_sr, MYSQLI_ASSOC) : [];

// Merge and sort
$feed = [];
foreach ($expenses   as $e) $feed[] = ['_type' => 'expense',    '_time' => $e['created_at']] + $e;
foreach ($settlements as $s) $feed[] = ['_type' => 'settlement', '_time' => $s['created_at']] + $s;
usort($feed, fn($a, $b) => strtotime($b['_time']) - strtotime($a['_time']));
$feed = array_slice($feed, 0, 20);

// Group by date label
$today_str     = date('Y-m-d');
$yesterday_str = date('Y-m-d', strtotime('-1 day'));
$grouped_feed  = [];
foreach ($feed as $item) {
  $d = date('Y-m-d', strtotime($item['_time']));
  if ($d === $today_str)         $lbl = 'TODAY';
  elseif ($d === $yesterday_str) $lbl = 'YESTERDAY';
  else                           $lbl = strtoupper(date('M j, Y', strtotime($d)));
  $grouped_feed[$lbl][] = $item;
}

// ── Helpers ──
function fmt($n) { return 'RM ' . number_format($n, 2); }

function activity_time($datetime) {
  $diff = time() - strtotime($datetime);
  if ($diff < 3600)  return floor($diff / 60) . ' minutes ago';
  if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
  $d = date('Y-m-d', strtotime($datetime));
  if ($d === date('Y-m-d', strtotime('-1 day'))) return 'Yesterday, ' . date('g:i A', strtotime($datetime));
  return date('M j, g:i A', strtotime($datetime));
}

function cat_display($cat) {
  return match($cat) {
    'food'          => 'Dining Out',
    'utilities'     => 'House Bills',
    'shopping'      => 'Shopping',
    'transport'     => 'Transport',
    'entertainment' => 'Entertainment',
    'travel'        => 'Travel',
    'rent'          => 'Rent & Bills',
    default         => ucfirst($cat),
  };
}

function act_icon_style($cat, $type) {
  if ($type === 'settlement') return ['bg' => '#dcfce7', 'stroke' => '#16a34a'];
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

function act_icon_lucide($cat, $type) {
  if ($type === 'settlement') return 'check';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhoOwes — Activity</title>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="activity.css">
  <link rel="stylesheet" href="../Sidebar/sidebar.css">
</head>
<body>
<div class="app">

  <?php $active_nav = 'activity'; include '../Sidebar/sidebar.php'; ?>

  <main class="main">

    <!-- Page Title -->
    <h1 class="page-title">Recent Activity</h1>

    <!-- Summary Banner -->
    <div class="summary-banner">
      <div class="banner-left">
        <div class="banner-label">MONTH TO DATE</div>
        <div class="banner-heading"><?= $month_count ?> transactions this month</div>
        <div class="banner-chips">
          <span class="banner-chip"><?= $month_count ?> transactions</span>
          <span class="banner-chip">&#8593; <?= fmt($month_owed) ?> owed</span>
          <span class="banner-chip">&#8595; <?= fmt($month_spent) ?> spent</span>
        </div>
      </div>
      <div class="banner-right">
        <div class="banner-net-lbl">Net balance</div>
        <div class="banner-net-val <?= $month_net >= 0 ? '' : 'neg' ?>"><?= ($month_net >= 0 ? '+' : '') . fmt(abs($month_net)) ?></div>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="tab active" data-filter="all">All</button>
      <button class="tab" data-filter="expense">Expenses</button>
      <button class="tab" data-filter="settlement">Settlements</button>
      <button class="tab" data-filter="you-paid">You Paid</button>
      <button class="tab" data-filter="you-owe">You Owe</button>
    </div>

    <!-- Activity Feed -->
    <div class="feed">

      <?php if (empty($grouped_feed)): ?>
      <p class="empty-state">No recent activity to show.</p>
      <?php endif; ?>

      <?php foreach ($grouped_feed as $date_lbl => $items): ?>
      <div class="date-group">
        <div class="date-label"><?= $date_lbl ?></div>

        <?php foreach ($items as $item):
          $type  = $item['_type'];
          $cat   = $type === 'expense' ? ($item['category'] ?? '') : '';
          $style = act_icon_style($cat, $type);
          $iname = act_icon_lucide($cat, $type);
          $when  = activity_time($item['_time']);

          if ($type === 'expense') {
            $paid_by_me = ((int)$item['paid_by'] === $current_user_id);
            $actor  = $paid_by_me ? 'You' : '<strong>' . htmlspecialchars($item['actor_name']) . '</strong>';
            $desc   = $actor . ' added &ldquo;' . htmlspecialchars($item['title']) . '&rdquo; in <span class="group-link">' . htmlspecialchars($item['group_name']) . '</span>';
            $amount = fmt($item['my_amount']);
            $alabel = $paid_by_me ? 'OWED TO YOU' : 'YOU OWE';
            $acls   = $paid_by_me ? 'green' : 'red';
          } else {
            $paid_by_me = ((int)$item['paid_by'] === $current_user_id);
            if ($paid_by_me) {
              $desc   = 'You settled with <strong>' . htmlspecialchars($item['to_name']) . '</strong> in <span class="group-link">' . htmlspecialchars($item['group_name']) . '</span>';
              $alabel = 'PAID';
              $acls   = 'gray';
            } else {
              $desc   = '<strong>' . htmlspecialchars($item['from_name']) . '</strong> settled with you in <span class="group-link">' . htmlspecialchars($item['group_name']) . '</span>';
              $alabel = 'RECEIVED';
              $acls   = 'green';
            }
            $amount = fmt($item['amount']);
          }
        ?>
        <div class="activity-item" data-filter="<?= $type === 'settlement' ? 'settlement' : ('expense ' . ($paid_by_me ? 'you-paid' : 'you-owe')) ?>">
          <div class="act-icon" style="background:<?= $style['bg'] ?>">
            <i data-lucide="<?= $iname ?>" style="stroke:<?= $style['stroke'] ?>;fill:none;width:20px;height:20px;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"></i>
          </div>
          <div class="act-info">
            <p class="act-desc"><?= $desc ?></p>
            <span class="act-time"><?= $when ?></span>
          </div>
          <div class="act-amount">
            <span class="amount-val <?= $acls ?>"><?= $amount ?></span>
            <span class="amount-label <?= $acls ?>"><?= $alabel ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>

    </div>

  </main>
</div>

<script>
(function () {
  var tabs  = document.querySelectorAll('.filter-tabs .tab');
  var items = document.querySelectorAll('.activity-item');
  var groups = document.querySelectorAll('.date-group');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) { t.classList.remove('active'); });
      this.classList.add('active');

      var filter = this.dataset.filter;

      items.forEach(function (item) {
        var types = item.dataset.filter.split(' ');
        var show  = filter === 'all' || types.indexOf(filter) !== -1;
        item.classList.toggle('act-hidden', !show);
      });

      groups.forEach(function (group) {
        var hasVisible = group.querySelector('.activity-item:not(.act-hidden)');
        group.classList.toggle('act-hidden', !hasVisible);
      });
    });
  });
})();
</script>
</body>
</html>
