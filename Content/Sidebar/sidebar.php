<?php $active_nav = $active_nav ?? 'home'; ?>
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
    <a class="nav-item <?= $active_nav === 'home'     ? 'active' : '' ?>" href="../Dashboard/dashboard.php">
      <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Home
    </a>
    <a class="nav-item <?= $active_nav === 'groups'   ? 'active' : '' ?>" href="../Group/group.php">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Groups
    </a>
    <a class="nav-item <?= $active_nav === 'activity' ? 'active' : '' ?>" href="../Activity/activity.php">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Activity
    </a>
    <a class="nav-item <?= $active_nav === 'friends'  ? 'active' : '' ?>" href="../Friends/friends.php">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
      <svg viewBox="0 0 24 24">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      Log Out
    </a>
  </div>
</aside>
<?php include __DIR__ . '/../AddExpense/add_expense_modal.php'; ?>
