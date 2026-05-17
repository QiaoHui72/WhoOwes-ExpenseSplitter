<style>
/* af-* = add-friend modal */
.af-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
}
.af-modal {
  background: #fff;
  border-radius: 20px;
  width: min(440px, 100%);
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  overflow: hidden;
}
.af-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 22px 24px 16px;
  flex-shrink: 0;
}
.af-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: #111827;
}
.af-close {
  width: 32px; height: 32px;
  border-radius: 50%;
  border: none;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 1.1rem;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  line-height: 1;
}
.af-close:hover { background: #e5e7eb; color: #111827; }
.af-search-wrap {
  position: relative;
  padding: 0 24px 16px;
  flex-shrink: 0;
}
.af-search-wrap svg {
  position: absolute;
  left: 38px; top: 12px;
  width: 16px; height: 16px;
  stroke: #9ca3af; fill: none; stroke-width: 2;
  stroke-linecap: round; stroke-linejoin: round;
  pointer-events: none;
}
.af-search-input {
  width: 100%; height: 44px;
  padding: 0 16px 0 42px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: .88rem; color: #374151;
  outline: none; transition: border-color .15s;
}
.af-search-input:focus { border-color: #1e3a7a; }
.af-search-input::placeholder { color: #9ca3af; }
.af-results {
  flex: 1;
  overflow-y: auto;
  padding: 0 12px 16px;
}
.af-results::-webkit-scrollbar { width: 4px; }
.af-results::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
.af-user-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 12px; border-radius: 10px;
  transition: background .1s;
}
.af-user-row:hover { background: #f9fafb; }
.af-avatar {
  width: 38px; height: 38px; border-radius: 50%;
  background: #1e3a7a; color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: .74rem; font-weight: 700; flex-shrink: 0;
  letter-spacing: .5px;
}
.af-user-name { flex: 1; font-size: .88rem; font-weight: 600; color: #111827; min-width: 0; }
.af-add-btn {
  height: 32px; padding: 0 16px;
  background: #1e3a7a; color: #fff;
  border: none; border-radius: 20px;
  font-size: .8rem; font-weight: 600;
  cursor: pointer; transition: background .15s;
  flex-shrink: 0;
}
.af-add-btn:hover:not(:disabled) { background: #162d5e; }
.af-add-btn:disabled { opacity: .6; cursor: default; }
.af-added-chip {
  height: 32px; padding: 0 14px;
  background: #f0fdf4; color: #16a34a;
  border-radius: 20px;
  font-size: .8rem; font-weight: 600;
  display: inline-flex; align-items: center;
  flex-shrink: 0; white-space: nowrap;
}
.af-empty {
  text-align: center; padding: 32px 0;
  color: #9ca3af; font-size: .88rem;
}
</style>

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
