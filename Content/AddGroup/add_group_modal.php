<style>
/* ag-* = add-group modal ──────────────────────────────────────── */
.ag-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
}
.ag-modal {
  background: #fff;
  border-radius: 20px;
  width: min(500px, 100%);
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  scrollbar-width: none;
}
.ag-modal::-webkit-scrollbar { display: none; }

/* Header */
.ag-hdr {
  text-align: center;
  padding: 32px 32px 0;
}
.ag-hdr h2 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #111827;
  margin: 0 0 6px;
}
.ag-hdr p {
  font-size: .87rem;
  color: #6b7280;
  margin: 0;
}

/* Body */
.ag-body {
  padding: 24px 32px 32px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.ag-lbl {
  display: block;
  font-size: .86rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}
.ag-txt {
  width: 100%;
  padding: 12px 16px;
  background: #f3f4f6;
  border: 1.5px solid transparent;
  border-radius: 10px;
  font-size: .92rem;
  color: #111827;
  outline: none;
  transition: border-color .2s, background .2s;
  box-sizing: border-box;
}
.ag-txt:focus { border-color: #1e3a7a; background: #fff; }
.ag-txt::placeholder { color: #9ca3af; }
.ag-txt.ag-err { border-color: #ef4444; }

/* Category chips */
.ag-cats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}
.ag-cat {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 16px 4px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  background: #fff;
  cursor: pointer;
  font-size: .82rem;
  color: #374151;
  font-weight: 600;
  transition: border-color .15s, background .15s, color .15s;
}
.ag-cat svg {
  width: 22px; height: 22px;
  stroke: #6b7280; fill: none;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
  transition: stroke .15s;
}
.ag-cat.active { border-color: #1e3a7a; background: #eef2ff; color: #1e3a7a; }
.ag-cat.active svg { stroke: #1e3a7a; }
.ag-cat:hover:not(.active) { border-color: #9ca3af; }

/* Member search */
.ag-srch-wrap {
  position: relative;
  display: flex;
  align-items: center;
  background: #f3f4f6;
  border: 1.5px solid transparent;
  border-radius: 10px;
  padding: 10px 14px;
  gap: 10px;
  transition: border-color .2s, background .2s;
}
.ag-srch-wrap:focus-within { border-color: #1e3a7a; background: #fff; }
.ag-srch-ico {
  width: 16px; height: 16px;
  stroke: #9ca3af; fill: none; flex-shrink: 0;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.ag-srch-inp {
  border: none; background: none; outline: none;
  flex: 1; font-size: .9rem; color: #111827; min-width: 0;
}
.ag-srch-inp::placeholder { color: #9ca3af; }

/* Dropdown */
.ag-dd {
  position: absolute;
  top: calc(100% + 6px);
  left: 0; right: 0;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,.12);
  max-height: 200px;
  overflow-y: auto;
  display: none;
  z-index: 20;
}
.ag-dd-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  cursor: pointer;
  transition: background .1s;
}
.ag-dd-item:hover { background: #f9fafb; }
.ag-dd-item + .ag-dd-item { border-top: 1px solid #f3f4f6; }
.ag-dd-init {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: #1e3a7a;
  color: #fff;
  font-size: .72rem; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.ag-dd-name { font-size: .88rem; font-weight: 600; color: #111827; }
.ag-dd-email { font-size: .76rem; color: #9ca3af; }

/* Member chips */
.ag-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}
.ag-chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 5px 12px;
  border-radius: 99px;
  font-size: .82rem;
  font-weight: 600;
}
.ag-chip-you { background: #99f6e4; color: #065f46; }
.ag-chip-mem {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #e5e7eb;
}
.ag-chip-mem button {
  background: none; border: none; cursor: pointer;
  font-size: .9rem; line-height: 1; color: #9ca3af;
  padding: 0; display: flex; align-items: center;
}
.ag-chip-mem button:hover { color: #ef4444; }

/* Create button */
.ag-btn-create {
  width: 100%;
  padding: 14px;
  background: #1e3a7a;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: .98rem;
  font-weight: 700;
  cursor: pointer;
  transition: background .15s;
  margin-top: 4px;
}
.ag-btn-create:hover:not(:disabled) { background: #162d5e; }
.ag-btn-create:disabled { opacity: .6; cursor: default; }

/* Cancel */
.ag-cancel-row { text-align: center; }
.ag-cancel {
  font-size: .88rem;
  color: #1e3a7a;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
}
.ag-cancel:hover { text-decoration: underline; }

/* Footer note */
.ag-note {
  font-size: .78rem;
  color: #9ca3af;
  text-align: center;
  line-height: 1.5;
}
</style>

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
          <svg class="ag-srch-ico" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
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
  var agMembers = [];
  var agTimer   = null;

  function agOpen() {
    document.getElementById('agOverlay').style.display = 'flex';
    setTimeout(function () { document.getElementById('agName').focus(); }, 80);
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

  // Triggers
  document.querySelector('.btn-create')?.addEventListener('click', aeOpen);

  // ── Reset ───────────────────────────────────────────────────────
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

  // ── Category ────────────────────────────────────────────────────
  document.querySelectorAll('.ag-cat').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.ag-cat').forEach(function (b) { b.classList.remove('active'); });
      this.classList.add('active');
    });
  });

  // ── Member search ───────────────────────────────────────────────
  document.getElementById('agMemberInput').addEventListener('input', function () {
    clearTimeout(agTimer);
    var q = this.value.trim();
    if (q.length < 2) { agHideDd(); return; }
    agTimer = setTimeout(function () {
      fetch('../AddGroup/search_users.php?q=' + encodeURIComponent(q))
        .then(function (r) { return r.json(); })
        .then(function (users) {
          var ids = agMembers.map(function (m) { return m.id; });
          agShowDd(users.filter(function (u) { return ids.indexOf(u.id) === -1; }));
        })
        .catch(agHideDd);
    }, 280);
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
    agHideDd();
    agRenderChips();
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

  // ── Submit ──────────────────────────────────────────────────────
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
