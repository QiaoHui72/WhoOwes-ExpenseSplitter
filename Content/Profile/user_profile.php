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
          <div class="row-icon">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <span>Edit Profile</span>
        </div>
        <svg class="chevron" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
      <a class="settings-row" href="#">
        <div class="row-left">
          <div class="row-icon">
            <svg viewBox="0 0 24 24"><line x1="3" y1="9" x2="21" y2="9"/><path d="M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/></svg>
          </div>
          <span>Payment Methods</span>
        </div>
        <svg class="chevron" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
      <a class="settings-row" href="#">
        <div class="row-left">
          <div class="row-icon">
            <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          </div>
          <span>Notifications</span>
        </div>
        <svg class="chevron" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>

    <!-- Preferences -->
    <div class="settings-card">
      <div class="settings-label">PREFERENCES</div>
      <div class="settings-row">
        <div class="row-left">
          <div class="row-icon">
            <svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
          </div>
          <div>
            <div class="row-title">Default Currency</div>
            <div class="row-sub">Malaysian Ringgit (RM)</div>
          </div>
        </div>
        <span class="pref-value">MYR</span>
      </div>
      <div class="settings-row">
        <div class="row-left">
          <div class="row-icon">
            <svg viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          </div>
          <span>Dark Mode</span>
        </div>
        <label class="toggle">
          <input type="checkbox">
          <span class="toggle-slider"></span>
        </label>
      </div>
      <div class="settings-row">
        <div class="row-left">
          <div class="row-icon">
            <svg viewBox="0 0 24 24"><polyline points="6 2 3 6 3 20 21 20 21 6 18 2 6 2"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </div>
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
