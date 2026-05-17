<link rel="stylesheet" href="../SettleUp/settle_modal.css">
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

  // Open / Close 
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

  // Fetch data
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

  // Left: render summary 
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

  // Right: render "who to pay" chips 
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

  // Update amount + confirm button label 
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

  // Payment method highlight
  window.suUpdatePm = function () {
    ['suPm1', 'suPm2', 'suPm3'].forEach(function (id) {
      var el = document.getElementById(id);
      el.classList.toggle('active', el.querySelector('input').checked);
    });
  };

  // Confirm 
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

  // Helpers 
  function ini(name) {
    return name.trim().split(/\s+/).map(function (p) { return p[0].toUpperCase(); }).join('').slice(0, 2);
  }
  function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }
})();
</script>
