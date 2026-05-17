<link rel="stylesheet" href="../AddGroup/add_group.css">
<div id="agOverlay" class="ag-overlay">
  <div class="ag-modal">

    <div class="ag-hdr">
      <h2>Start a New Group</h2>
      <p>Keep track of shared expenses with friends and family.</p>
    </div>

    <div class="ag-body">

      <!-- Group Name -->
      <div>
        <label class="ag-lbl" for="agName">Group Name</label>
        <input class="ag-txt" id="agName" type="text"
               placeholder="e.g. European Summer Trip 2024"
               oninput="this.classList.remove('ag-err')">
      </div>

      <!-- Category -->
      <div>
        <label class="ag-lbl">Category</label>
        <div class="ag-cats">

          <button class="ag-cat" data-val="flight_takeoff" type="button">
            <svg viewBox="0 0 24 24">
              <path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/>
            </svg>
            Trip
          </button>

          <button class="ag-cat active" data-val="house" type="button">
            <svg viewBox="0 0 24 24">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Home
          </button>

          <button class="ag-cat" data-val="couple" type="button">
            <svg viewBox="0 0 24 24">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            Couple
          </button>

          <button class="ag-cat" data-val="other" type="button">
            <svg viewBox="0 0 24 24">
              <path d="M5 12h.01M12 12h.01M19 12h.01" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Other
          </button>

        </div>
      </div>

      <!-- Add Members -->
      <div>
        <label class="ag-lbl">Add Members</label>
        <div class="ag-srch-wrap" id="agSrchWrap">
          <i data-lucide="search" class="ag-srch-ico"></i>
          <input class="ag-srch-inp" id="agMemberInput"
                 type="text" placeholder="Search by name or email"
                 autocomplete="off">
          <div id="agDd" class="ag-dd"></div>
        </div>
        <div id="agChips" class="ag-chips"></div>
      </div>

      <button class="ag-btn-create" id="agCreateBtn" onclick="agSubmit()">
        Create Group
      </button>

      <div class="ag-cancel-row">
        <a class="ag-cancel" href="#" onclick="agClose(event)">Cancel</a>
      </div>

      <p class="ag-note">
        You can add more friends or change group settings later at
        any time from the group dashboard.
      </p>

    </div>
  </div>
</div>

<script>
(function () {
  var agMembers  = [];
  var agAllUsers = [];

  function agOpen() {
    document.getElementById('agOverlay').style.display = 'flex';
    setTimeout(function () { document.getElementById('agName').focus(); }, 80);
    fetch('../AddGroup/search_users.php')
      .then(function (r) { return r.json(); })
      .then(function (users) { agAllUsers = users; })
      .catch(function () {});
  }
  window.agOpen = agOpen;

  function agClose(e) {
    if (e) e.preventDefault();
    document.getElementById('agOverlay').style.display = 'none';
    agReset();
  }
  window.agClose = agClose;

  document.getElementById('agOverlay').addEventListener('click', function (e) {
    if (e.target === this) agClose();
  });

  // Triggers: wait for full DOM so .new-group / .new-card (in <main>) exist
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.new-group')?.addEventListener('click', agOpen);
    document.querySelector('.new-card')?.addEventListener('click', agOpen);
  });

  /* ── Reset ── */
  function agReset() {
    agMembers = [];
    var nameEl = document.getElementById('agName');
    nameEl.value = '';
    nameEl.classList.remove('ag-err');
    document.getElementById('agMemberInput').value = '';
    agHideDd();
    agRenderChips();
    document.querySelectorAll('.ag-cat').forEach(function (b) { b.classList.remove('active'); });
    var homeBtn = document.querySelector('.ag-cat[data-val="house"]');
    if (homeBtn) homeBtn.classList.add('active');
    var btn = document.getElementById('agCreateBtn');
    btn.disabled = false;
    btn.textContent = 'Create Group';
  }

  /* ── Category ── */
  document.querySelectorAll('.ag-cat').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.ag-cat').forEach(function (b) { b.classList.remove('active'); });
      this.classList.add('active');
    });
  });

  /* ── Member Search ── */
  function agFilteredUsers() {
    var ids = agMembers.map(function (m) { return m.id; });
    var q   = document.getElementById('agMemberInput').value.trim().toLowerCase();
    return agAllUsers.filter(function (u) {
      if (ids.indexOf(u.id) !== -1) return false;
      if (!q) return true;
      return u.name.toLowerCase().indexOf(q) !== -1 ||
             (u.email || '').toLowerCase().indexOf(q) !== -1;
    });
  }

  document.getElementById('agMemberInput').addEventListener('focus', function () {
    agShowDd(agFilteredUsers());
  });

  document.getElementById('agMemberInput').addEventListener('input', function () {
    agShowDd(agFilteredUsers());
  });

  document.getElementById('agMemberInput').addEventListener('keydown', function (e) {
    if (e.key === 'Escape') agHideDd();
  });

  document.getElementById('agDd').addEventListener('click', function (e) {
    var item = e.target.closest('.ag-dd-item');
    if (!item) return;
    agAddMember(parseInt(item.dataset.id, 10), item.dataset.name);
  });

  document.addEventListener('click', function (e) {
    var wrap = document.getElementById('agSrchWrap');
    if (wrap && !wrap.contains(e.target)) agHideDd();
  });

  function agShowDd(users) {
    var dd = document.getElementById('agDd');
    if (!users.length) { agHideDd(); return; }
    dd.innerHTML = users.map(function (u) {
      var init = u.name.trim().split(/\s+/).map(function (p) { return p[0].toUpperCase(); }).join('').slice(0, 2);
      var ne   = u.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      var ee   = (u.email || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      var na   = u.name.replace(/"/g, '&quot;');
      return '<div class="ag-dd-item" data-id="' + u.id + '" data-name="' + na + '">' +
        '<div class="ag-dd-init">' + init + '</div>' +
        '<div><div class="ag-dd-name">' + ne + '</div><div class="ag-dd-email">' + ee + '</div></div>' +
        '</div>';
    }).join('');
    dd.style.display = 'block';
  }

  function agHideDd() {
    var dd = document.getElementById('agDd');
    if (dd) { dd.style.display = 'none'; dd.innerHTML = ''; }
  }

  function agAddMember(id, name) {
    if (agMembers.find(function (m) { return m.id === id; })) return;
    agMembers.push({ id: id, name: name });
    document.getElementById('agMemberInput').value = '';
    agRenderChips();
    agShowDd(agFilteredUsers());
  }

  window.agRemoveMember = function (id) {
    agMembers = agMembers.filter(function (m) { return m.id !== id; });
    agRenderChips();
  };

  function agRenderChips() {
    var html = '<span class="ag-chip ag-chip-you">You (Admin)</span>';
    agMembers.forEach(function (m) {
      var ne = m.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      html += '<span class="ag-chip ag-chip-mem">' + ne +
        '<button type="button" onclick="agRemoveMember(' + m.id + ')">&#x2715;</button></span>';
    });
    document.getElementById('agChips').innerHTML = html;
  }

  /* ── Submit ── */
  window.agSubmit = function () {
    var nameEl = document.getElementById('agName');
    var name   = nameEl.value.trim();
    if (!name) { nameEl.classList.add('ag-err'); nameEl.focus(); return; }

    var activeCat = document.querySelector('.ag-cat.active');
    var icon = activeCat ? activeCat.dataset.val : 'other';
    var memberIds = agMembers.map(function (m) { return m.id; });

    var btn = document.getElementById('agCreateBtn');
    btn.disabled = true;
    btn.textContent = 'Creating…';

    fetch('../AddGroup/save_group.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: name, icon: icon, members: memberIds })
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success) {
          agClose();
          window.location.reload();
        } else {
          alert(res.error || 'Failed to create group');
          btn.disabled = false;
          btn.textContent = 'Create Group';
        }
      })
      .catch(function () {
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Create Group';
      });
  };

  // Initial chips render
  agRenderChips();
})();
</script>
