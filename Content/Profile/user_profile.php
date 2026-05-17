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
$user_email    = $u['email'] ?? '';
$user_phone    = $u['phone'] ?? '';
$since_year    = !empty($u['created_at']) ? date('Y', strtotime($u['created_at'])) : date('Y');

// Stats
$r = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COUNT(*) AS cnt FROM group_members WHERE user_id = $current_user_id"
));
$groups_count = (int)$r['cnt'];

$r = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COUNT(*) AS cnt FROM expense_splits WHERE user_id = $current_user_id"
));
$expenses_count = (int)$r['cnt'];

$r = mysqli_fetch_assoc(mysqli_query($connect,
  "SELECT COUNT(DISTINCT u.id) AS cnt FROM users u
   JOIN group_members gm ON gm.user_id = u.id
   JOIN group_members gm_me ON gm_me.group_id = gm.group_id AND gm_me.user_id = $current_user_id
   WHERE u.id != $current_user_id"
));
$friends_count = (int)$r['cnt'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhoOwes — Profile</title>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="user_profile.css">
  <link rel="stylesheet" href="../Sidebar/sidebar.css">
</head>
<body>
<div class="app">

  <?php $active_nav = 'profile'; include '../Sidebar/sidebar.php'; ?>

  <main class="main">

    <!-- Page Title -->
    <h1 class="page-title">Profile</h1>

    <!-- Hero Banner -->
    <div class="profile-hero">
      <div class="hero-avatar"><?= htmlspecialchars($user_initials) ?></div>
      <div class="hero-info">
        <div class="hero-name"><?= htmlspecialchars($user_name) ?></div>
        <?php if ($user_email): ?>
        <div class="hero-detail"><?= htmlspecialchars($user_email) ?></div>
        <?php endif; ?>
        <?php if ($user_phone): ?>
        <div class="hero-detail"><?= htmlspecialchars($user_phone) ?></div>
        <?php endif; ?>
        <div class="hero-badges">
          <span class="badge-white">Pro Member</span>
          <span class="badge-teal">Since <?= $since_year ?></span>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-num"><?= $groups_count ?></div>
        <div class="stat-desc">Groups</div>
      </div>
      <div class="stat-card">
        <div class="stat-num"><?= $expenses_count ?></div>
        <div class="stat-desc">Expenses</div>
      </div>
      <div class="stat-card">
        <div class="stat-num"><?= $friends_count ?></div>
        <div class="stat-desc">Friends</div>
      </div>
    </div>

    <!-- Account Settings -->
    <div class="settings-card">
      <div class="settings-label">ACCOUNT</div>
      <a class="settings-row" href="#">
        <div class="row-left">
          <div class="row-icon"><i data-lucide="user"></i></div>
          <span>Edit Profile</span>
        </div>
        <i data-lucide="chevron-right" class="chevron"></i>
      </a>
      <a class="settings-row" href="#">
        <div class="row-left">
          <div class="row-icon"><i data-lucide="credit-card"></i></div>
          <span>Payment Methods</span>
        </div>
        <i data-lucide="chevron-right" class="chevron"></i>
      </a>
      <a class="settings-row" href="#">
        <div class="row-left">
          <div class="row-icon"><i data-lucide="bell"></i></div>
          <span>Notifications</span>
        </div>
        <i data-lucide="chevron-right" class="chevron"></i>
      </a>
    </div>

    <!-- Preferences -->
    <div class="settings-card">
      <div class="settings-label">PREFERENCES</div>
      <div class="settings-row">
        <div class="row-left">
          <div class="row-icon"><i data-lucide="arrow-left-right"></i></div>
          <div>
            <div class="row-title">Default Currency</div>
            <div class="row-sub">Malaysian Ringgit (RM)</div>
          </div>
        </div>
        <span class="pref-value">MYR</span>
      </div>
      <div class="settings-row">
        <div class="row-left">
          <div class="row-icon"><i data-lucide="moon"></i></div>
          <span>Dark Mode</span>
        </div>
        <label class="toggle">
          <input type="checkbox">
          <span class="toggle-slider"></span>
        </label>
      </div>
      <div class="settings-row">
        <div class="row-left">
          <div class="row-icon"><i data-lucide="tag"></i></div>
          <span>Include SST by default</span>
        </div>
        <label class="toggle">
          <input type="checkbox" checked>
          <span class="toggle-slider"></span>
        </label>
      </div>
    </div>

  </main>
</div>
</body>
</html>
