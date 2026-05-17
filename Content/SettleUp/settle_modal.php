<style>
/* su-* = settle-up modal ──────────────────────────────────────── */
.su-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
}
.su-modal {
  background: #fff;
  border-radius: 20px;
  width: min(820px, 100%);
  max-height: 90vh;
  display: flex;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
}

/* ── Left panel ── */
.su-left {
  width: 320px;
  flex-shrink: 0;
  background: #f9fafb;
  border-right: 1px solid #f0f0f0;
  padding: 28px 24px;
  overflow-y: auto;
  scrollbar-width: none;
}
.su-left::-webkit-scrollbar { display: none; }

/* ── Right panel ── */
.su-right {
  flex: 1;
  padding: 28px 28px 24px;
  overflow-y: auto;
  scrollbar-width: none;
  display: flex;
  flex-direction: column;
  gap: 22px;
  min-width: 0;
}
.su-right::-webkit-scrollbar { display: none; }

/* Section label */
.su-lbl {
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 12px;
}

.su-panel-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #111827;
  margin-bottom: 20px;
}

/* ── Debt cards (left) ── */
.su-debt-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 12px;
  margin-bottom: 8px;
}
.su-debt-card.owe  { background: #fff1f0; }
.su-debt-card.owed { background: #f0fdf4; }

.su-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .76rem; font-weight: 700;
  flex-shrink: 0;
  letter-spacing: .5px;
}
.su-debt-card.owe  .su-avatar { background: #9f1239; color: #fff; }
.su-debt-card.owed .su-avatar { background: #14532d; color: #fff; }

.su-debt-info { flex: 1; min-width: 0; }
.su-debt-text { font-size: .88rem; color: #374151; }
.su-debt-text strong { font-weight: 700; color: #111827; }
.su-debt-amt  { font-size: .9rem; font-weight: 700; flex-shrink: 0; }
.su-debt-card.owe  .su-debt-amt { color: #be123c; }
.su-debt-card.owed .su-debt-amt { color: #15803d; }

.su-no-debt {
  text-align: center;
  padding: 32px 0;
  color: #9ca3af;
  font-size: .88rem;
}

/* ── Pay chips (right) ── */
.su-pay-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.su-pay-chip {
  padding: 8px 16px;
  border-radius: 99px;
  border: 1.5px solid #e5e7eb;
  background: #fff;
  font-size: .83rem;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: border-color .15s, background .15s, color .15s;
  white-space: nowrap;
}
.su-pay-chip.active { border-color: #1e3a7a; background: #eef2ff; color: #1e3a7a; }
.su-pay-chip:hover:not(.active) { border-color: #9ca3af; }

/* ── Payment method cards ── */
.su-pm-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 13px 16px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  cursor: pointer;
  transition: border-color .15s;
  margin-bottom: 8px;
  user-select: none;
}
.su-pm-card:last-child { margin-bottom: 0; }
.su-pm-card.active { border-color: #1e3a7a; }
.su-pm-card input[type=radio] {
  accent-color: #1e3a7a;
  width: 17px; height: 17px;
  flex-shrink: 0;
  cursor: pointer;
}
.su-pm-icon {
  width: 28px; height: 28px;
  stroke: #1e3a7a; fill: none;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
  flex-shrink: 0;
}
.su-pm-label { font-size: .9rem; font-weight: 600; color: #111827; }

/* ── Amount display ── */
.su-amount-box {
  background: #f3f4f6;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: baseline;
  gap: 6px;
}
.su-amount-cur { font-size: 1rem; font-weight: 700; color: #6b7280; }
.su-amount-val { font-size: 2rem; font-weight: 800; color: #111827; }

/* ── Action buttons ── */
.su-actions { display: flex; flex-direction: column; gap: 10px; margin-top: auto; }
.su-btn-confirm {
  width: 100%;
  padding: 15px;
  background: #1e3a7a;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: .98rem;
  font-weight: 700;
  cursor: pointer;
  transition: background .15s;
}
.su-btn-confirm:hover:not(:disabled) { background: #162d5e; }
.su-btn-confirm:disabled { opacity: .6; cursor: default; }

.su-btn-cancel {
  width: 100%;
  padding: 13px;
  background: #fff;
  color: #374151;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  font-size: .92rem;
  font-weight: 600;
  cursor: pointer;
  transition: border-color .15s;
}
.su-btn-cancel:hover { border-color: #9ca3af; }

.su-loading { text-align: center; padding: 40px 0; color: #9ca3af; font-size: .88rem; }
</style>

<div id="suOverlay" class="su-overlay">
  <div class="su-modal">

    <!-- Left: Settlement Summary -->
    <div class="su-left">
      <div class="su-panel-title">Settle Up</div>
      <div class="su-lbl">Settlement Summary</div>
      <div id="suSummary"><div class="su-loading">Loading…</div></div>
    </div>

    <!-- Right: Pay section -->
    <div class="su-right">

      <!-- Who to pay -->
      <div id="suPaySection">
        <div class="su-lbl">Who to Pay</div>
        <div id="suPayChips" class="su-pay-chips"></div>
      </div>

      <!-- Payment Method -->
      <div>
        <div class="su-lbl">Payment Method</div>

        <label class="su-pm-card active" id="suPm1">
          <input type="radio" name="su_pm" value="bank" checked onchange="suUpdatePm()">
          <i data-lucide="landmark" class="su-pm-icon"></i>
          <span class="su-pm-label">Bank Transfer (Maybank)</span>
        </label>

        <label class="su-pm-card" id="suPm2">
          <input type="radio" name="su_pm" value="duitnow" onchange="suUpdatePm()">
          <i data-lucide="qr-code" class="su-pm-icon"></i>
          <span class="su-pm-label">DuitNow QR</span>
        </label>

        <label class="su-pm-card" id="suPm3">
          <input type="radio" name="su_pm" value="card" onchange="suUpdatePm()">
          <i data-lucide="credit-card" class="su-pm-icon"></i>
          <span class="su-pm-label">Credit / Debit Card</span>
        </label>
      </div>

      <!-- Amount -->
      <div>
        <div class="su-lbl">Amount to Settle</div>
        <div class="su-amount-box">
          <span class="su-amount-cur">RM</span>
          <span class="su-amount-val" id="suAmtVal">0.00</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="su-actions">
        <button class="su-btn-confirm" id="suConfirmBtn" onclick="suConfirm()">
          Confirm Payment — RM 0.00
        </button>
        <button class="su-btn-cancel" onclick="suClose()">Cancel</button>
      </div>

    </div>
  </div>
</div>

<script>
(function () {
  var suData     = { i_owe: [], owed_to_me: [] };
  var suSelected = 'all';

  // ── Open / Close ────────────────────────────────────────────────
  function suOpen(preSelectUserId) {
    document.getElementById('suOverlay').style.display = 'flex';
    suLoad(preSelectUserId || null);
  }
  window.suOpen = suOpen;

  function suClose() {
    document.getElementById('suOverlay').style.display = 'none';
  }
  window.suClose = suClose;

  document.getElementById('suOverlay').addEventListener('click', function (e) {
    if (e.target === this) suClose();
  });

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.btn-settle-all')?.addEventListener('click', suOpen);
    document.querySelector('.btn-settle')?.addEventListener('click', suOpen);
  });

  // ── Fetch data ──────────────────────────────────────────────────
  function suLoad(preSelectUserId) {
    document.getElementById('suSummary').innerHTML = '<div class="su-loading">Loading…</div>';
    document.getElementById('suPayChips').innerHTML = '';
    suSelected = 'all';

    fetch('../SettleUp/get_settlements.php')
      .then(function (r) { return r.json(); })
      .then(function (data) {
        suData = data;
        suRenderSummary();
        suRenderChips(preSelectUserId);
        suUpdateAmount();
      })
      .catch(function () {
        document.getElementById('suSummary').innerHTML = '<div class="su-loading">Failed to load.</div>';
      });
  }

  // ── Left: render summary ─────────────────────────────────────────
  function suRenderSummary() {
    var html = '';

    suData.i_owe.forEach(function (d) {
      html += '<div class="su-debt-card owe">' +
        '<div class="su-avatar">' + ini(d.name) + '</div>' +
        '<div class="su-debt-info"><div class="su-debt-text">You owe <strong>' + esc(d.name) + '</strong></div></div>' +
        '<div class="su-debt-amt">RM ' + parseFloat(d.amount).toFixed(2) + '</div>' +
        '</div>';
    });

    suData.owed_to_me.forEach(function (d) {
      html += '<div class="su-debt-card owed">' +
        '<div class="su-avatar">' + ini(d.name) + '</div>' +
        '<div class="su-debt-info"><div class="su-debt-text"><strong>' + esc(d.name) + '</strong> owes you</div></div>' +
        '<div class="su-debt-amt">RM ' + parseFloat(d.amount).toFixed(2) + '</div>' +
        '</div>';
    });

    if (!html) html = '<div class="su-no-debt">All settled up!</div>';
    document.getElementById('suSummary').innerHTML = html;
  }

  // ── Right: render "who to pay" chips ────────────────────────────
  function suRenderChips(preSelectUserId) {
    var section = document.getElementById('suPaySection');

    if (!suData.i_owe.length) {
      section.style.display = 'none';
      return;
    }
    section.style.display = '';

    if (preSelectUserId) {
      var hit = suData.i_owe.find(function (d) { return String(d.user_id) === String(preSelectUserId); });
      if (hit) suSelected = String(preSelectUserId);
    }

    var total = suData.i_owe.reduce(function (s, d) { return s + parseFloat(d.amount); }, 0);
    var html  = '<div class="su-pay-chip ' + (suSelected === 'all' ? 'active' : '') + '" data-id="all">All (RM ' + total.toFixed(2) + ')</div>';

    suData.i_owe.forEach(function (d) {
      html += '<div class="su-pay-chip ' + (String(d.user_id) === String(suSelected) ? 'active' : '') + '" data-id="' + d.user_id + '">' +
        esc(d.name) + ' (RM ' + parseFloat(d.amount).toFixed(2) + ')</div>';
    });

    var wrap = document.getElementById('suPayChips');
    wrap.innerHTML = html;

    wrap.querySelectorAll('.su-pay-chip').forEach(function (chip) {
      chip.addEventListener('click', function () {
        wrap.querySelectorAll('.su-pay-chip').forEach(function (c) { c.classList.remove('active'); });
        this.classList.add('active');
        suSelected = this.dataset.id;
        suUpdateAmount();
      });
    });
  }

  // ── Update amount + confirm button label ─────────────────────────
  function suUpdateAmount() {
    var amt = 0;
    if (suSelected === 'all') {
      amt = suData.i_owe.reduce(function (s, d) { return s + parseFloat(d.amount); }, 0);
    } else {
      var hit = suData.i_owe.find(function (d) { return String(d.user_id) === String(suSelected); });
      if (hit) amt = parseFloat(hit.amount);
    }
    document.getElementById('suAmtVal').textContent = amt.toFixed(2);
    var btn = document.getElementById('suConfirmBtn');
    btn.textContent = 'Confirm Payment — RM ' + amt.toFixed(2);
    btn.disabled = amt <= 0;
  }

  // ── Payment method highlight ─────────────────────────────────────
  window.suUpdatePm = function () {
    ['suPm1', 'suPm2', 'suPm3'].forEach(function (id) {
      var el = document.getElementById(id);
      el.classList.toggle('active', el.querySelector('input').checked);
    });
  };

  // ── Confirm ──────────────────────────────────────────────────────
  window.suConfirm = function () {
    var btn = document.getElementById('suConfirmBtn');
    btn.disabled = true;
    btn.textContent = 'Processing…';

    var targets = suSelected === 'all'
      ? suData.i_owe.map(function (d) { return d.user_id; })
      : [parseInt(suSelected, 10)];

    fetch('../SettleUp/confirm_settle.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ target_user_ids: targets })
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success) {
          suClose();
          window.location.reload();
        } else {
          alert(res.error || 'Settlement failed');
          btn.disabled = false;
          suUpdateAmount();
        }
      })
      .catch(function () {
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        suUpdateAmount();
      });
  };

  // ── Helpers ──────────────────────────────────────────────────────
  function ini(name) {
    return name.trim().split(/\s+/).map(function (p) { return p[0].toUpperCase(); }).join('').slice(0, 2);
  }
  function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }
})();
</script>
