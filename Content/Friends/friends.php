<?php
session_start();
include '../../database.php';

if (empty($_SESSION['user_id'])) {
  header('Location: ../../Authorization/login.php');
  exit;
}
$current_user_id = (int) $_SESSION['user_id'];

$u = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT * FROM users WHERE id = $current_user_id LIMIT 1"
));
$user_name     = $u['name'];
$user_initials = strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($user_name)))));

// Friends = co-members of any shared group, with per-friend balances
$friends = mysqli_fetch_all(mysqli_query($connect,
  "SELECT u.id, u.name,
     COUNT(DISTINCT gm_me.group_id) AS shared_groups,
     COALESCE((
       SELECT SUM(es.amount) FROM expense_splits es
       JOIN expenses e ON e.id = es.expense_id
       WHERE e.paid_by = $current_user_id AND es.user_id = u.id AND es.is_settled = 0
     ), 0) AS they_owe_me,
     COALESCE((
       SELECT SUM(es2.amount) FROM expense_splits es2
       JOIN expenses e2 ON e2.id = es2.expense_id
       WHERE e2.paid_by = u.id AND es2.user_id = $current_user_id AND es2.is_settled = 0
     ), 0) AS i_owe_them
   FROM users u
   JOIN group_members gm ON gm.user_id = u.id
   JOIN group_members gm_me ON gm_me.group_id = gm.group_id AND gm_me.user_id = $current_user_id
   WHERE u.id != $current_user_id
   GROUP BY u.id, u.name
   ORDER BY u.name"
), MYSQLI_ASSOC);

$total_friends = count($friends);
$youre_owed    = 0.0;
$you_owe       = 0.0;
foreach ($friends as $f) {
  $net = (float)$f['they_owe_me'] - (float)$f['i_owe_them'];
  if ($net > 0) $youre_owed += $net;
  elseif ($net < 0) $you_owe += abs($net);
}
$pending_invites = 0;

function fmt($n) { return 'RM ' . number_format($n, 2); }

function get_initials($name) {
  return strtoupper(implode('', array_map(fn($p) => $p[0], explode(' ', trim($name)))));
}

$avatar_colors = ['#14b8a6','#3b82f6','#6366f1','#8b5cf6','#f59e0b','#10b981','#0284c7','#db2777'];
function avatar_color($id) {
  global $avatar_colors;
  return $avatar_colors[$id % count($avatar_colors)];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhoOwes — Friends</title>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="friends.css">
  <link rel="stylesheet" href="../Sidebar/sidebar.css">
</head>
<body>
<div class="app">

  <?php $active_nav = 'friends'; include '../Sidebar/sidebar.php'; ?>

  <main class="main">

    <!-- Page Title -->
    <h1 class="page-title">Friends</h1>

    <!-- Search -->
    <div class="search-wrap">
      <i data-lucide="search"></i>
      <input class="search-input" type="text" placeholder="Search friends..." id="searchInput">
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-num blue"><?= $total_friends ?></div>
        <div class="stat-desc">Total Friends</div>
      </div>
      <div class="stat-card green-tint">
        <div class="stat-num green"><?= fmt($youre_owed) ?></div>
        <div class="stat-desc">You're Owed</div>
      </div>
      <div class="stat-card red-tint">
        <div class="stat-num red"><?= fmt($you_owe) ?></div>
        <div class="stat-desc">You Owe</div>
      </div>
      <div class="stat-card">
        <div class="stat-num blue"><?= $pending_invites ?></div>
        <div class="stat-desc">Pending Invites</div>
      </div>
    </div>

    <!-- Section Header -->
    <div class="friends-header">
      <h3 class="friends-title">All Friends</h3>
      <button class="btn-add-friend">
        <i data-lucide="user-plus"></i>
        Add Friend
      </button>
    </div>

    <!-- Friends List -->
    <?php if (empty($friends)): ?>
    <p class="empty-state">No friends yet. Join a group to connect with others!</p>
    <?php else: ?>
    <div class="friends-list" id="friendsList">
      <?php foreach ($friends as $f):
        $net      = (float)$f['they_owe_me'] - (float)$f['i_owe_them'];
        $initials = get_initials($f['name']);
        $color    = avatar_color((int)$f['id']);
        $groups   = (int)$f['shared_groups'];

        if ($net > 0) {
          $amount_cls = '';
          $desc_cls   = '';
          $desc_text  = 'Owes you';
          $amount     = fmt($net);
          $settled    = false;
        } elseif ($net < 0) {
          $amount_cls = 'red';
          $desc_cls   = 'red';
          $desc_text  = 'You owe';
          $amount     = fmt(abs($net));
          $settled    = false;
        } else {
          $amount_cls = 'gray';
          $desc_cls   = 'gray';
          $desc_text  = 'All settled';
          $amount     = fmt(0);
          $settled    = true;
        }
      ?>
      <div class="friend-row">
        <div class="friend-avatar" style="background:<?= $color ?>">
          <?= htmlspecialchars($initials) ?>
        </div>
        <div class="friend-info">
          <div class="friend-name"><?= htmlspecialchars($f['name']) ?></div>
          <div class="friend-groups"><?= $groups ?> shared group<?= $groups !== 1 ? 's' : '' ?></div>
        </div>
        <div class="friend-balance">
          <span class="balance-amount <?= $amount_cls ?>"><?= $amount ?></span>
          <span class="balance-desc <?= $desc_cls ?>"><?= $desc_text ?></span>
        </div>
        <div class="friend-actions">
          <?php if ($settled): ?>
          <span class="settled-chip">Settled &#10003;</span>
          <?php else: ?>
          <button class="settle-btn">Settle</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </main>
</div>

<script>
document.getElementById('searchInput')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.friend-row').forEach(row => {
    const name = row.querySelector('.friend-name').textContent.toLowerCase();
    row.style.display = name.includes(q) ? '' : 'none';
  });
});
</script>
</body>
</html>
