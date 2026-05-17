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

// Recent settlements involving the user
$settlements = mysqli_fetch_all(mysqli_query($connect,
  "SELECT s.id, s.amount, s.created_at, s.paid_by, s.paid_to,
     g.name AS group_name,
     uf.name AS from_name, ut.name AS to_name
   FROM settlements s
   JOIN groups g ON g.id = s.group_id
   JOIN users uf ON uf.id = s.paid_by
   JOIN users ut ON ut.id = s.paid_to
   WHERE s.paid_by = $current_user_id OR s.paid_to = $current_user_id
   ORDER BY s.created_at DESC LIMIT 10"
), MYSQLI_ASSOC);

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

function act_icon_svg($cat, $type) {
  if ($type === 'settlement') return '<polyline points="20 6 9 17 4 12"/>';
  return match($cat) {
    'food'          => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><line x1="7" y1="2" x2="7" y2="11"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/>',
    'utilities'     => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
    'shopping'      => '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
    'transport'     => '<rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
    'entertainment' => '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>',
    'travel'        => '<path d="M22 2L11 13"/><path d="M22 2L15 22l-4-9-9-4 20-7z"/>',
    'rent'          => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    default         => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>',
  };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhoOwes — Activity</title>
  <link rel="stylesheet" href="activity.css">
  <link rel="stylesheet" href="../Sidebar/sidebar.css">
</head>
<body>
<div class="app">

  <?php $active_nav = 'activity'; include '../Sidebar/sidebar.php'; ?>

  <main class="main">

    <!-- Page Title -->
    <h1 class="page-title">Recent Activity</h1>

    <!-- Summary Row -->
    <div class="summary-row">

      <div class="month-card">
        <div class="month-label">MONTH TO DATE</div>
        <div class="month-heading">
          You've participated in <span><?= $month_count ?> transactions</span> this month
        </div>
        <div class="cat-badges">
          <?php foreach ($top_cats as $tc): ?>
          <span class="cat-badge"><?= (int)$tc['cnt'] ?> <?= htmlspecialchars(cat_display($tc['category'])) ?></span>
          <?php endforeach; ?>
          <?php if (empty($top_cats)): ?>
          <span class="cat-badge">No activity yet</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="settle-card">
        <div class="settle-icon-wrap">
          <svg viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
        </div>
        <div class="settle-pct"><?= $settled_pct ?>% Settled</div>
        <div class="settle-sub">Outstanding debts are down<br>this period</div>
      </div>

    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="tab active">All Activity</button>
      <button class="tab">Expenses</button>
      <button class="tab">Payments</button>
      <button class="tab">Group Updates</button>
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
          $svg   = act_icon_svg($cat, $type);
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
        <div class="activity-item">
          <div class="act-icon" style="background:<?= $style['bg'] ?>">
            <svg viewBox="0 0 24 24" style="stroke:<?= $style['stroke'] ?>"><?= $svg ?></svg>
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

    <!-- View Full History -->
    <div class="history-footer">
      <a href="#" class="history-link">
        <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.93"/></svg>
        View full activity history
      </a>
    </div>

  </main>
</div>
</body>
</html>
