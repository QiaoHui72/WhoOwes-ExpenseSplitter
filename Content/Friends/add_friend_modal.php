<link rel="stylesheet" href="add_friend.css">
<div id="afOverlay" class="af-overlay">
  <div class="af-modal">
    <div class="af-header">
      <span class="af-title">Add Friend</span>
      <button class="af-close" onclick="afClose()">&#215;</button>
    </div>
    <div class="af-search-wrap">
      <i data-lucide="search"></i>
      <input id="afSearch" class="af-search-input" type="text" placeholder="Search by name…" autocomplete="off">
    </div>
    <div id="afResults" class="af-results"></div>
  </div>
</div>

<script>
(function () {
  var afAdded = {};

  function afOpen() {
    afAdded = {};
    document.getElementById('afOverlay').style.display = 'flex';
    document.getElementById('afSearch').value = '';
    afFetch('');
    setTimeout(function () { document.getElementById('afSearch').focus(); }, 50);
  }
  window.afOpen = afOpen;

  function afClose() {
    document.getElementById('afOverlay').style.display = 'none';
  }
  window.afClose = afClose;

  document.getElementById('afOverlay').addEventListener('click', function (e) {
    if (e.target === this) afClose();
  });

  var afTimer;
  document.getElementById('afSearch').addEventListener('input', function () {
    clearTimeout(afTimer);
    var q = this.value;
    afTimer = setTimeout(function () { afFetch(q); }, 200);
  });

  function afFetch(q) {
    fetch('../AddGroup/search_users.php?q=' + encodeURIComponent(q))
      .then(function (r) { return r.json(); })
      .then(function (users) { afRender(users); })
      .catch(function () {
        document.getElementById('afResults').innerHTML = '<div class="af-empty">Failed to load users.</div>';
      });
  }

  function afRender(users) {
    if (!users.length) {
      document.getElementById('afResults').innerHTML = '<div class="af-empty">No users found.</div>';
      return;
    }
    var html = '';
    users.forEach(function (u) {
      var ini = u.name.trim().split(/\s+/).map(function (p) { return p[0].toUpperCase(); }).join('').slice(0, 2);
      html += '<div class="af-user-row">' +
        '<div class="af-avatar">' + ini + '</div>' +
        '<div class="af-user-name">' + esc(u.name) + '</div>' +
        (afAdded[u.id]
          ? '<span class="af-added-chip">Added &#10003;</span>'
          : '<button class="af-add-btn" data-id="' + u.id + '">Add</button>'
        ) +
        '</div>';
    });
    document.getElementById('afResults').innerHTML = html;

    document.querySelectorAll('#afResults .af-add-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var uid = this.dataset.id;
        var b   = this;
        b.disabled    = true;
        b.textContent = '…';
        fetch('save_friend.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ friend_id: parseInt(uid, 10) })
        })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (res.success) {
              afAdded[uid] = true;
              b.outerHTML = '<span class="af-added-chip">Added &#10003;</span>';
            } else {
              b.disabled    = false;
              b.textContent = 'Add';
              alert(res.error || 'Failed to add friend.');
            }
          })
          .catch(function () {
            b.disabled    = false;
            b.textContent = 'Add';
          });
      });
    });
  }

  function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.btn-add-friend')?.addEventListener('click', afOpen);
  });
})();
</script>
