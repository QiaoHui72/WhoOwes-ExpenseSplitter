<?php
session_start();
include '../database.php';

// ── Redirect to login if not authenticated ──
if (empty($_SESSION['user_id'])) {
  header('Location: ../Authorization/login.php');
  exit;
}
$current_user_id = (int) $_SESSION['user_id'];

// ── Fetch current user ──
$u = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT * FROM users WHERE id = $current_user_id LIMIT 1"
));
$user_name    = $u['name'];
$user_initials = strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($user_name)))));

// ── Greeting ──
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');

// ── You Owe (unsettled splits where someone else paid) ──
$r = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(es.amount), 0) AS total
   FROM expense_splits es
   JOIN expenses e ON e.id = es.expense_id
   WHERE es.user_id = $current_user_id
     AND e.paid_by  != $current_user_id
     AND es.is_settled = 0"
));
$you_owe = (float) $r['total'];

// ── You Are Owed (others' unsettled splits on expenses you paid) ──
$r = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COALESCE(SUM(es.amount), 0) AS total
   FROM expense_splits es
   JOIN expenses e ON e.id = es.expense_id
   WHERE e.paid_by   = $current_user_id
     AND es.user_id != $current_user_id
     AND es.is_settled = 0"
));
$you_are_owed = (float) $r['total'];

$total_balance = $you_are_owed - $you_owe;
$balance_positive = $total_balance >= 0;

// ── Active Groups (latest 2 groups the user belongs to) ──
$groups_result = mysqli_query($connect,
  "SELECT g.id, g.name, g.icon,
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
     ), 0) AS i_owe
   FROM groups g
   JOIN group_members gm ON gm.group_id = g.id
   WHERE gm.user_id = $current_user_id
   ORDER BY g.updated_at DESC
   LIMIT 2"
);
$active_groups = mysqli_fetch_all($groups_result, MYSQLI_ASSOC);

// fetch first 3 member initials per group
$group_members_map = [];
foreach ($active_groups as $grp) {
  $gid = $grp['id'];
  $mr  = mysqli_query($connect,
    "SELECT u.name FROM users u
     JOIN group_members gm ON gm.user_id = u.id
     WHERE gm.group_id = $gid ORDER BY gm.joined_at LIMIT 3"
  );
  $group_members_map[$gid] = mysqli_fetch_all($mr, MYSQLI_ASSOC);
}

// ── Recent Activity (last 3 expenses in user's groups) ──
$activity_result = mysqli_query($connect,
  "SELECT e.title, e.category, e.expense_date, e.paid_by,
     u.name AS payer_name,
     es.amount AS my_share,
     es.is_settled,
     DATEDIFF(CURDATE(), e.expense_date) AS days_ago
   FROM expenses e
   JOIN expense_splits es ON es.expense_id = e.id AND es.user_id = $current_user_id
   JOIN users u ON u.id = e.paid_by
   JOIN group_members gm ON gm.group_id = e.group_id AND gm.user_id = $current_user_id
   ORDER BY e.expense_date DESC, e.created_at DESC
   LIMIT 3"
);
$activities = mysqli_fetch_all($activity_result, MYSQLI_ASSOC);

// ── Helpers ──
function fmt($n) { return 'RM ' . number_format($n, 2); }

function date_label($days_ago) {
  if ($days_ago == 0) return 'Today';
  if ($days_ago == 1) return 'Yesterday';
  return $days_ago . ' days ago';
}

// Map DB icon slug → emoji
function group_emoji($icon) {
  return match($icon) {
    'house'          => '🏠',
    'flight_takeoff' => '✈️',
    'coffee'         => '☕',
    'receipt'        => '🧾',
    'landscape'      => '🏔️',
    default          => '👥',
  };
}

// Map category → inline SVG path
function category_icon($cat) {
  return match($cat) {
    'food'          => '<path d="M3 11l19-9-9 19-2-8-8-2z"/>',
    'transport'     => '<rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
    'utilities'     => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
    'shopping'      => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
    'entertainment' => '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>',
    'travel'        => '<path d="M22 2L11 13"/><path d="M22 2L15 22l-4-9-9-4 20-7z"/>',
    'rent'          => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    default         => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
  };
}

// Dot colours for member avatars
$dot_colors = ['#93c5fd','#6366f1','#4b5563','#f9a8d4','#6ee7b7','#fcd34d'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WhoOwes Dashboard</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body { height: 100%; overflow: hidden; }

    body {
      height: 100vh;
      background: #1a1a2e;
      display: flex;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .app { display: flex; width: 100%; height: 100vh; overflow: hidden; }

    /* ══ SIDEBAR ══ */
    .sidebar {
      width: 230px;
      flex-shrink: 0;
      height: 100vh;
      background: #fff;
      display: flex;
      flex-direction: column;
      padding: 28px 20px 20px;
      border-right: 1px solid #f0f0f0;
    }

    .sidebar-brand h2 { font-size: 1.4rem; font-weight: 800; color: #1e3a7a; }
    .sidebar-brand p  { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }

    .btn-add {
      margin-top: 22px; width: 100%; height: 44px;
      background: #1e3a7a; color: #fff; border: none; border-radius: 10px;
      font-size: 0.9rem; font-weight: 600; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: background 0.2s;
    }
    .btn-add:hover { background: #162d60; }

    nav { margin-top: 32px; display: flex; flex-direction: column; gap: 4px; }

    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 10px 14px; border-radius: 10px;
      font-size: 0.92rem; font-weight: 500; color: #4b5563;
      text-decoration: none; cursor: pointer; transition: background 0.15s;
    }
    .nav-item:hover  { background: #f3f4f6; }
    .nav-item.active { background: #eef2ff; color: #1e3a7a; font-weight: 600; }
    .nav-item svg {
      width: 18px; height: 18px; stroke: currentColor; fill: none;
      stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
    }

    .sidebar-footer { margin-top: auto; }

    .profile-card {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px; border-radius: 12px; background: #eef2ff; cursor: pointer;
    }

    .avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: #1e3a7a; color: #fff; font-size: 0.75rem; font-weight: 700;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .profile-info strong { display: block; font-size: 0.85rem; color: #111827; }
    .profile-info span   { font-size: 0.75rem; color: #6b7280; }

    /* ══ MAIN ══ */
    .main {
      flex: 1; height: 100vh; background: #f4f6fb;
      padding: 36px 36px 30px; overflow-y: auto;
    }

    /* Top bar */
    .topbar { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 30px; }
    .topbar-left h1 { font-size: 2rem; font-weight: 800; color: #111827; }
    .topbar-left p  { font-size: 0.9rem; color: #6b7280; margin-top: 4px; }
    .topbar-actions { display: flex; gap: 10px; align-items: center; margin-top: 6px; }

    .btn-settle {
      height: 40px; padding: 0 20px; background: #fff; color: #1e3a7a;
      border: 1.5px solid #d1d5db; border-radius: 20px;
      font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: border-color 0.2s;
    }
    .btn-settle:hover { border-color: #1e3a7a; }

    .btn-new {
      height: 40px; padding: 0 20px; background: #1e3a7a; color: #fff;
      border: none; border-radius: 20px;
      font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: background 0.2s;
    }
    .btn-new:hover { background: #162d60; }

    /* Stat Cards */
    .stats { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px; }

    .stat-card {
      background: #fff; border-radius: 16px; padding: 24px;
      display: flex; flex-direction: column; gap: 8px;
    }
    .stat-card.total { background: #1e3a7a; color: #fff; }

    .stat-label {
      font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em;
      text-transform: uppercase; color: #9ca3af;
    }
    .stat-card.total .stat-label { color: #93c5fd; }

    .stat-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .stat-icon.owe  { background: #fee2e2; }
    .stat-icon.owed { background: #d1fae5; }
    .stat-icon svg  { width: 18px; height: 18px; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; fill: none; }
    .stat-icon.owe  svg { stroke: #ef4444; }
    .stat-icon.owed svg { stroke: #10b981; }

    .stat-amount { font-size: 1.8rem; font-weight: 800; color: #111827; line-height: 1.1; }
    .stat-card.total .stat-amount { color: #fff; font-size: 2rem; }
    .stat-amount.red   { color: #ef4444; }
    .stat-amount.green { color: #10b981; }

    .stat-sub { font-size: 0.78rem; color: #10b981; display: flex; align-items: center; gap: 4px; }
    .stat-sub.neg { color: #ef4444; }

    /* Bottom grid */
    .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

    /* Section headers */
    .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .section-header h3 { font-size: 1.1rem; font-weight: 700; color: #111827; }
    .section-header a  { font-size: 0.82rem; color: #1e3a7a; font-weight: 600; text-decoration: none; }
    .section-header a:hover { text-decoration: underline; }

    /* Group Cards */
    .groups-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .group-card {
      background: #fff; border-radius: 14px; padding: 16px; cursor: pointer;
      transition: box-shadow 0.2s; display: flex; flex-direction: column; gap: 12px;
    }
    .group-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
    .group-card-top { display: flex; align-items: flex-start; justify-content: space-between; }

    .group-img {
      width: 52px; height: 52px; border-radius: 10px;
      background: #e5e7eb; display: flex; align-items: center;
      justify-content: center; font-size: 1.6rem; overflow: hidden;
    }
    .group-chevron { color: #9ca3af; font-size: 1rem; }
    .group-name    { font-size: 0.9rem; font-weight: 600; color: #111827; }
    .group-card-bottom { display: flex; align-items: center; justify-content: space-between; }

    .member-stack { display: flex; }
    .member-dot {
      width: 26px; height: 26px; border-radius: 50%; border: 2px solid #fff;
      margin-left: -6px; display: flex; align-items: center; justify-content: center;
      font-size: 0.6rem; font-weight: 700; color: #fff;
    }
    .member-dot:first-child { margin-left: 0; }

    .group-balance { text-align: right; }
    .group-balance small { display: block; font-size: 0.72rem; color: #6b7280; }
    .group-balance span  { font-size: 0.88rem; font-weight: 700; }
    .group-balance span.red   { color: #ef4444; }
    .group-balance span.green { color: #10b981; }
    .group-balance span.gray  { color: #6b7280; }

    .group-card.new-group {
      border: 2px dashed #d1d5db; background: transparent;
      align-items: center; justify-content: center; min-height: 130px; gap: 10px;
    }
    .group-card.new-group:hover { border-color: #1e3a7a; }
    .group-card.new-group svg {
      width: 32px; height: 32px; stroke: #1e3a7a; fill: none;
      stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
    }
    .group-card.new-group span { font-size: 0.88rem; font-weight: 600; color: #374151; }

    /* Recent Activity */
    .activity-list { background: #fff; border-radius: 14px; overflow: hidden; }

    .activity-item {
      display: flex; align-items: center; gap: 14px;
      padding: 14px 18px; border-bottom: 1px solid #f3f4f6;
    }
    .activity-item:last-child { border-bottom: none; }

    .activity-icon {
      width: 40px; height: 40px; border-radius: 10px; background: #eef2ff;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .activity-icon svg {
      width: 18px; height: 18px; stroke: #1e3a7a; fill: none;
      stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }

    .activity-info { flex: 1; min-width: 0; }
    .activity-info strong {
      display: block; font-size: 0.875rem; font-weight: 600; color: #1e3a7a;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .activity-info small { font-size: 0.75rem; color: #9ca3af; }

    .activity-amount { text-align: right; flex-shrink: 0; }
    .activity-amount small { display: block; font-size: 0.7rem; color: #9ca3af; }
    .activity-amount span  { font-size: 0.88rem; font-weight: 700; }
    .activity-amount span.red   { color: #ef4444; }
    .activity-amount span.green { color: #10b981; }

    .view-history {
      display: block; text-align: right; margin-top: 12px;
      font-size: 0.85rem; font-weight: 600; color: #1e3a7a; text-decoration: none;
    }
    .view-history:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>WhoOwes</h2>
      <p>Smart Expense Splitting</p>
    </div>

    <button class="btn-add">
      <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Add Expense
    </button>

    <nav>
      <a class="nav-item active" href="#">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </a>
      <a class="nav-item" href="#">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Groups
      </a>
      <a class="nav-item" href="#">
        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Activity
      </a>
      <a class="nav-item" href="#">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Friends
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="profile-card">
        <div class="avatar"><?= htmlspecialchars($user_initials) ?></div>
        <div class="profile-info">
          <strong><?= htmlspecialchars($user_name) ?></strong>
          <span>My Profile</span>
        </div>
      </div>
    </div>
  </aside>

  <!-- ══ MAIN ══ -->
  <main class="main">

    <!-- Top bar -->
    <div class="topbar">
      <div class="topbar-left">
        <h1><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $user_name)[0]) ?></h1>
        <p>Here's a breakdown of your finances today.</p>
      </div>
      <div class="topbar-actions">
        <button class="btn-settle">Settle Up</button>
        <button class="btn-new">New Expense</button>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats">

      <div class="stat-card total">
        <div class="stat-label">Total Balance</div>
        <div class="stat-amount"><?= fmt(abs($total_balance)) ?></div>
        <div class="stat-sub <?= $balance_positive ? '' : 'neg' ?>">
          <?= $balance_positive ? '↗ Overall positive' : '↘ Overall negative' ?>
        </div>
      </div>

      <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <div class="stat-label">You Owe</div>
          <div class="stat-icon owe">
            <svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
          </div>
        </div>
        <div class="stat-amount red"><?= fmt($you_owe) ?></div>
      </div>

      <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <div class="stat-label">You Are Owed</div>
          <div class="stat-icon owed">
            <svg viewBox="0 0 24 24"><line x1="17" y1="7" x2="7" y2="17"/><polyline points="17 17 7 17 7 7"/></svg>
          </div>
        </div>
        <div class="stat-amount green"><?= fmt($you_are_owed) ?></div>
      </div>

    </div>

    <!-- Bottom Grid -->
    <div class="bottom-grid">

      <!-- Active Groups -->
      <div>
        <div class="section-header">
          <h3>Active Groups</h3>
          <a href="#">View All Groups</a>
        </div>
        <div class="groups-grid">

          <?php foreach ($active_groups as $grp):
            $net     = $grp['owed_to_me'] - $grp['i_owe'];
            $members = $group_members_map[$grp['id']] ?? [];
          ?>
          <div class="group-card">
            <div class="group-card-top">
              <div class="group-img"><?= group_emoji($grp['icon']) ?></div>
              <span class="group-chevron">›</span>
            </div>
            <div class="group-name"><?= htmlspecialchars($grp['name']) ?></div>
            <div class="group-card-bottom">
              <div class="member-stack">
                <?php foreach ($members as $i => $m):
                  $init  = strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($m['name'])))));
                  $color = $dot_colors[$i % count($dot_colors)];
                ?>
                <div class="member-dot" style="background:<?= $color ?>" title="<?= htmlspecialchars($m['name']) ?>">
                  <?= htmlspecialchars($init) ?>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="group-balance">
                <?php if ($net > 0): ?>
                  <small>You are owed</small>
                  <span class="green"><?= fmt($net) ?></span>
                <?php elseif ($net < 0): ?>
                  <small>You owe</small>
                  <span class="red"><?= fmt(abs($net)) ?></span>
                <?php else: ?>
                  <small>All settled</small>
                  <span class="gray">RM 0.00</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

          <!-- Create New Group -->
          <div class="group-card new-group">
            <svg viewBox="0 0 24 24">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <line x1="19" y1="8" x2="19" y2="14"/>
              <line x1="16" y1="11" x2="22" y2="11"/>
            </svg>
            <span>Create New Group</span>
          </div>

        </div>
      </div>

      <!-- Recent Activity -->
      <div>
        <div class="section-header">
          <h3>Recent Activity</h3>
        </div>
        <div class="activity-list">
          <?php foreach ($activities as $act):
            $paid_by_me = ((int)$act['paid_by'] === $current_user_id);
            $label      = $paid_by_me ? 'Owed to you' : 'Your share';
            $cls        = $paid_by_me ? 'green' : 'red';
            $who        = $paid_by_me ? 'You added' : htmlspecialchars($act['payer_name']);
            $when       = date_label((int)$act['days_ago']);
          ?>
          <div class="activity-item">
            <div class="activity-icon">
              <svg viewBox="0 0 24 24"><?= category_icon($act['category']) ?></svg>
            </div>
            <div class="activity-info">
              <strong><?= htmlspecialchars($act['title']) ?></strong>
              <small><?= $who ?> &bull; <?= $when ?></small>
            </div>
            <div class="activity-amount">
              <small><?= $label ?></small>
              <span class="<?= $cls ?>"><?= fmt($act['my_share']) ?></span>
            </div>
          </div>
          <?php endforeach; ?>

          <?php if (empty($activities)): ?>
          <div class="activity-item">
            <div class="activity-info"><strong style="color:#9ca3af">No recent activity</strong></div>
          </div>
          <?php endif; ?>
        </div>
        <a href="#" class="view-history">View Full History &rarr;</a>
      </div>

    </div>
  </main>
</div>
</body>
</html>
