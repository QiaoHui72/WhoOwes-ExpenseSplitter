<?php $active_nav = $active_nav ?? 'home'; ?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <h2>WhoOwes</h2>
    <p>Smart Expense Splitting</p>
  </div>

  <button class="btn-add">
    <i data-lucide="plus" style="width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round"></i>
    Add Expense
  </button>

  <nav>
    <a class="nav-item <?= $active_nav === 'home'     ? 'active' : '' ?>" href="../Dashboard/index.php">
      <i data-lucide="home"></i>
      Home
    </a>
    <a class="nav-item <?= $active_nav === 'groups'   ? 'active' : '' ?>" href="../Group/group.php">
      <i data-lucide="users"></i>
      Groups
    </a>
    <a class="nav-item <?= $active_nav === 'activity' ? 'active' : '' ?>" href="../Activity/activity.php">
      <i data-lucide="activity"></i>
      Activity
    </a>
    <a class="nav-item <?= $active_nav === 'friends'  ? 'active' : '' ?>" href="../Friends/friends.php">
      <i data-lucide="user"></i>
      Friends
    </a>
  </nav>

  <div class="sidebar-footer">
    <a class="profile-card <?= $active_nav === 'profile' ? 'active' : '' ?>" href="../Profile/user_profile.php">
      <div class="avatar"><?= htmlspecialchars($user_initials) ?></div>
      <div class="profile-info">
        <strong><?= htmlspecialchars($user_name) ?></strong>
        <span>My Profile</span>
      </div>
    </a>

    <a href="../../Authorization/logout.php" class="btn-logout">
      <i data-lucide="log-out"></i>
      Log Out
    </a>
  </div>
</aside>
<?php include __DIR__ . '/../AddExpense/add_expense_modal.php'; ?>
<?php include __DIR__ . '/../AddGroup/add_group_modal.php'; ?>
<?php include __DIR__ . '/../SettleUp/settle_modal.php'; ?>
<script>document.addEventListener('DOMContentLoaded', function () { lucide.createIcons(); });</script>
