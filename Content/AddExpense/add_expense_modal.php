<?php
$ae_groups = mysqli_fetch_all(mysqli_query($connect,
  "SELECT g.id, g.name FROM groups g
   JOIN group_members gm ON gm.group_id = g.id
   WHERE gm.user_id = $current_user_id
   ORDER BY g.name"
), MYSQLI_ASSOC);
$ae_uid = $current_user_id;
?>

<link rel="stylesheet" href="../AddExpense/add_expense.css">
<div id="aeOverlay" class="ae-overlay" style="display:none" onclick="if(event.target===this)aeClose()">
<div class="ae-modal">

  <div class="ae-header">
    <h2>Add New Expense</h2>
    <button class="ae-close" onclick="aeClose()">&#x2715;</button>
  </div>

  <div class="ae-body">

    <!-- Description -->
    <div>
      <label class="ae-lbl">Description</label>
      <input id="aeDesc" class="ae-input" type="text" placeholder="e.g. Dinner">
    </div>

    <!-- Total Amount -->
    <div>
      <label class="ae-lbl">Total Amount</label>
      <div class="ae-amt-row">
        <span class="ae-cur-tag">RM</span>
        <input id="aeAmt" class="ae-amt-input" type="number" min="0" step="0.01" placeholder="0.00" oninput="aeCalc()">
      </div>
    </div>

    <!-- Taxes -->
    <div class="ae-toggle-row">
      <div class="ae-toggle-info"><span>Include SST (6%)</span><small>SST 6% included</small></div>
      <label class="ae-tgl"><input type="checkbox" id="aeSst6" checked onchange="aeCalc()"><span class="ae-tgl-sl"></span></label>
    </div>
    <div class="ae-toggle-row">
      <div class="ae-toggle-info"><span>Include SST (8%)</span><small>SST 8% included</small></div>
      <label class="ae-tgl"><input type="checkbox" id="aeSst8" onchange="aeCalc()"><span class="ae-tgl-sl"></span></label>
    </div>
    <div class="ae-toggle-row">
      <div class="ae-toggle-info"><span>Service Charge (10%)</span><small>Service charge 10% included</small></div>
      <label class="ae-tgl"><input type="checkbox" id="aeSc10" checked onchange="aeCalc()"><span class="ae-tgl-sl"></span></label>
    </div>
    

    <!-- Group -->
    <div>
      <label class="ae-lbl">Group</label>
      <select id="aeGroup" class="ae-select" onchange="aeFetchMembers()">
        <option value="">Select a group</option>
        <?php foreach ($ae_groups as $g): ?>
        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Paid By -->
    <div>
      <label class="ae-lbl">Paid By</label>
      <select id="aePaidBy" class="ae-select">
        <option value="">Select group first</option>
      </select>
    </div>

    <!-- Split Method -->
    <div>
      <label class="ae-lbl">Split Method</label>
      <div class="ae-split-btns">
        <button class="ae-split-btn active" data-method="equal"      onclick="aeSetMethod(this)">Equal</button>
        <button class="ae-split-btn"        data-method="exact"      onclick="aeSetMethod(this)">Exact</button>
      </div>
    </div>

    <!-- Split: Equal -->
    <div id="aeSplitEqual" class="ae-split-section">
      <p class="ae-split-hint">Select a group to see the split</p>
    </div>

    <!-- Split: Exact (itemized) -->
    <div id="aeSplitExact" class="ae-split-section" style="display:none">
      <label class="ae-lbl" style="margin-bottom:8px">Items</label>
      <div id="aeItemsList"></div>
      <button class="ae-add-item" onclick="aeAddItem()">+ Add Item</button>
      <div id="aeItemsTotal" class="ae-items-total">Items total: RM 0.00</div>
      <div id="aePersonSummary" class="ae-person-summary" style="display:none"></div>
    </div>

    <!-- Category -->
    <div>
      <label class="ae-lbl">Category</label>
      <div class="ae-cats">
        <button class="ae-cat active" data-cat="food"          onclick="aeSetCat(this)">🍔 Food</button>
        <button class="ae-cat"        data-cat="travel"        onclick="aeSetCat(this)">✈️ Travel</button>
        <button class="ae-cat"        data-cat="rent"          onclick="aeSetCat(this)">🏠 Rent</button>
        <button class="ae-cat"        data-cat="shopping"      onclick="aeSetCat(this)">🛍️ Shopping</button>
        <button class="ae-cat"        data-cat="entertainment" onclick="aeSetCat(this)">🎮 Fun</button>
        <button class="ae-cat"        data-cat="medical"       onclick="aeSetCat(this)">💊 Medical</button>
        <button class="ae-cat"        data-cat="utilities"     onclick="aeSetCat(this)">⚡ Utilities</button>
      </div>
    </div>

    <!-- Summary -->
    <div class="ae-summary">
      <div class="ae-sum-row"><span>Subtotal</span>         <span id="aeSumSub">RM 0.00</span></div>
      <div class="ae-sum-row" id="aeSst6Row" style="display:none"><span>SST (6%)</span><span id="aeSumSst6">RM 0.00</span></div>
      <div class="ae-sum-row" id="aeSst8Row"><span>SST (8%)</span>          <span id="aeSumSst8">RM 0.00</span></div>
      <div class="ae-sum-row" id="aeSc10Row"><span>Service Charge (10%)</span><span id="aeSumSc10">RM 0.00</span></div>
      <div class="ae-sum-total"><span>Total</span>          <span id="aeSumTotal">RM 0.00</span></div>
    </div>

  </div>

  <div class="ae-footer">
    <button class="ae-submit" onclick="aeSubmit()">Add Expense</button>
  </div>

</div>
</div>

<script>
(function () {
  var aeMembers   = [];
  var aeItemCount = 0;
  var AE_UID      = <?= (int)$ae_uid ?>;

  /* ── open / close ── */
  window.aeOpen  = function () { document.getElementById('aeOverlay').style.display = 'flex'; };
  window.aeClose = function () { document.getElementById('aeOverlay').style.display = 'none'; };

  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') aeClose(); });
  document.querySelector('.btn-add')?.addEventListener('click', aeOpen);

  /* ── helpers ── */
  function rm(n) { return 'RM ' + n.toFixed(2); }
  function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function aeGetTotal() {
    var sub  = parseFloat(document.getElementById('aeAmt').value) || 0;
    var sst8 = document.getElementById('aeSst8').checked ? sub * 0.08 : 0;
    var sst6 = document.getElementById('aeSst6').checked ? sub * 0.06 : 0;
    var sc10 = document.getElementById('aeSc10').checked ? sub * 0.10 : 0;
    return sub + sst8 + sst6 + sc10;
  }

  /* ── tax summary ── */
  window.aeCalc = function () {
    var sub  = parseFloat(document.getElementById('aeAmt').value) || 0;
    var sst8 = document.getElementById('aeSst8').checked ? sub * 0.08 : 0;
    var sst6 = document.getElementById('aeSst6').checked ? sub * 0.06 : 0;
    var sc10 = document.getElementById('aeSc10').checked ? sub * 0.10 : 0;
    var tot  = sub + sst8 + sst6 + sc10;

    document.getElementById('aeSumSub').textContent   = rm(sub);
    document.getElementById('aeSumSst8').textContent  = rm(sst8);
    document.getElementById('aeSumSst6').textContent  = rm(sst6);
    document.getElementById('aeSumSc10').textContent  = rm(sc10);
    document.getElementById('aeSumTotal').textContent = rm(tot);

    document.getElementById('aeSst8Row').style.display = document.getElementById('aeSst8').checked ? '' : 'none';
    document.getElementById('aeSst6Row').style.display = document.getElementById('aeSst6').checked ? '' : 'none';
    document.getElementById('aeSc10Row').style.display = document.getElementById('aeSc10').checked ? '' : 'none';

    aeUpdateSplit(tot);
  };

  /* ── fetch group members ── */
  window.aeFetchMembers = function () {
    var gid = document.getElementById('aeGroup').value;
    if (!gid) return;
    fetch('../AddExpense/get_group_members.php?group_id=' + gid)
      .then(function (r) { return r.json(); })
      .then(function (members) {
        aeMembers = members;

        var pb = document.getElementById('aePaidBy');
        pb.innerHTML = '';
        members.forEach(function (m) {
          var opt         = document.createElement('option');
          opt.value       = m.id;
          opt.textContent = (m.id == AE_UID) ? m.name + ' (You)' : m.name;
          if (m.id == AE_UID) opt.selected = true;
          pb.appendChild(opt);
        });

        aeRefreshItemPersonSelects();
        aeUpdateSplit(aeGetTotal());
      });
  };

  /* ── split method ── */
  window.aeSetMethod = function (btn) {
    document.querySelectorAll('.ae-split-btn').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('aeSplitEqual').style.display = btn.dataset.method === 'equal' ? '' : 'none';
    document.getElementById('aeSplitExact').style.display = btn.dataset.method === 'exact' ? '' : 'none';
    aeUpdateSplit(aeGetTotal());
  };

  function aeCurrentMethod() {
    return (document.querySelector('.ae-split-btn.active') || {}).dataset?.method || 'equal';
  }

  function aeUpdateSplit(tot) {
    var m = aeCurrentMethod();
    if (m === 'equal') aeRenderEqual(tot);
    else if (m === 'exact') aeUpdateExactPersons();
  }

  /* Equal split */
  function aeRenderEqual(tot) {
    var sec = document.getElementById('aeSplitEqual');
    if (!aeMembers.length) { sec.innerHTML = '<p class="ae-split-hint">Select a group to see the split</p>'; return; }
    var per  = aeMembers.length > 0 ? tot / aeMembers.length : 0;
    var rows = aeMembers.map(function (m) {
      var label = (m.id == AE_UID) ? m.name + ' (You)' : m.name;
      return '<div class="ae-eq-row"><span class="ae-eq-name">' + esc(label) + '</span><span class="ae-eq-amt">' + rm(per) + '</span></div>';
    }).join('');
    sec.innerHTML = '<div class="ae-eq-header">Each person pays <strong>' + rm(per) + '</strong></div>' + rows;
  }

  /* Exact split – itemized with per-person assignment */
  function aeBuildPersonSelect() {
    var opts = '<option value="">Split equally</option>';
    aeMembers.forEach(function (m) {
      var label = (m.id == AE_UID) ? m.name + ' (You)' : m.name;
      opts += '<option value="' + m.id + '">' + esc(label) + '</option>';
    });
    return '<select class="ae-item-person" onchange="aeUpdateItemsTotal()">' + opts + '</select>';
  }

  function aeRefreshItemPersonSelects() {
    document.querySelectorAll('.ae-item-person').forEach(function (sel) {
      var oldVal = sel.value;
      sel.innerHTML = '<option value="">Split equally</option>';
      aeMembers.forEach(function (m) {
        var label   = (m.id == AE_UID) ? m.name + ' (You)' : m.name;
        var opt     = document.createElement('option');
        opt.value   = m.id;
        opt.textContent = label;
        if (String(m.id) === String(oldVal)) opt.selected = true;
        sel.appendChild(opt);
      });
    });
    aeUpdateExactPersons();
  }

  function aeUpdateExactPersons() {
    var summary = document.getElementById('aePersonSummary');
    if (!summary) return;
    var rows = document.querySelectorAll('.ae-item-row');
    if (!aeMembers.length || !rows.length) { summary.style.display = 'none'; return; }

    var personTotals = {};
    var unassigned   = 0;
    aeMembers.forEach(function (m) { personTotals[m.id] = 0; });

    rows.forEach(function (row) {
      var amt = parseFloat(row.querySelector('.ae-item-amt')?.value) || 0;
      var uid = row.querySelector('.ae-item-person')?.value;
      if (uid && personTotals.hasOwnProperty(uid)) {
        personTotals[uid] += amt;
      } else {
        unassigned += amt;
      }
    });

    var itemsBase    = Object.values(personTotals).reduce(function (a, b) { return a + b; }, 0) + unassigned;
    var finalTotal   = aeGetTotal();
    var ratio        = itemsBase > 0 ? finalTotal / itemsBase : 1;
    var perUnassigned = aeMembers.length > 0 ? unassigned / aeMembers.length : 0;

    var html      = '<div style="font-size:.72rem;font-weight:700;letter-spacing:.07em;color:#9ca3af;text-transform:uppercase;margin-bottom:8px">Total by person (incl. taxes)</div>';
    var grandTotal = 0;
    aeMembers.forEach(function (m) {
      var label    = (m.id == AE_UID) ? m.name + ' (You)' : m.name;
      var finalAmt = ((personTotals[m.id] || 0) + perUnassigned) * ratio;
      grandTotal  += finalAmt;
      html += '<div class="ae-person-row"><span class="ae-person-name">' + esc(label) + '</span><span class="ae-person-amt">' + rm(finalAmt) + '</span></div>';
    });
    html += '<div class="ae-person-row ae-person-total"><span class="ae-person-name">Total</span><span class="ae-person-amt">' + rm(grandTotal) + '</span></div>';

    summary.innerHTML     = html;
    summary.style.display = '';
  }

  window.aeAddItem = function () {
    aeItemCount++;
    var id  = aeItemCount;
    var row = document.createElement('div');
    row.className    = 'ae-item-row';
    row.dataset.item = id;
    row.innerHTML =
      '<input class="ae-item-name" type="text" placeholder="Item name">' +
      '<div class="ae-item-amt-wrap"><span class="ae-item-cur">RM</span>' +
      '<input class="ae-item-amt" type="number" min="0" step="0.01" placeholder="0.00" oninput="aeUpdateItemsTotal()"></div>' +
      aeBuildPersonSelect() +
      '<button class="ae-item-del" onclick="aeDelItem(' + id + ')">&#x2715;</button>';
    document.getElementById('aeItemsList').appendChild(row);
    aeUpdateItemsTotal();
  };

  window.aeDelItem = function (id) {
    var row = document.querySelector('.ae-item-row[data-item="' + id + '"]');
    if (row) { row.remove(); aeUpdateItemsTotal(); }
  };

  window.aeUpdateItemsTotal = function () {
    var sum      = 0;
    document.querySelectorAll('.ae-item-amt').forEach(function (el) { sum += parseFloat(el.value) || 0; });
    var expected = parseFloat(document.getElementById('aeAmt').value) || 0;
    var el       = document.getElementById('aeItemsTotal');
    var diff     = Math.abs(sum - expected);
    el.textContent = 'Items total: ' + rm(sum);
    el.className   = 'ae-items-total' + (diff < 0.01 || expected === 0 ? '' : ' warn');
    aeUpdateExactPersons();
  };

  /* ── category ── */
  window.aeSetCat = function (btn) {
    document.querySelectorAll('.ae-cat').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
  };

  /* ── submit ── */
  window.aeSubmit = function () {
    var title    = document.getElementById('aeDesc').value.trim();
    var sub      = parseFloat(document.getElementById('aeAmt').value) || 0;
    var group_id = document.getElementById('aeGroup').value;
    var paid_by  = document.getElementById('aePaidBy').value;
    var category = (document.querySelector('.ae-cat.active') || {}).dataset?.cat || 'food';
    var method   = aeCurrentMethod();
    var total    = aeGetTotal();

    if (!title)             { alert('Please enter a description.'); return; }
    if (sub <= 0)           { alert('Please enter an amount.'); return; }
    if (!group_id)          { alert('Please select a group.'); return; }
    if (!paid_by)           { alert('Please select who paid.'); return; }
    if (!aeMembers.length)  { alert('No members found. Please select a group.'); return; }

    var splits = [];

    if (method === 'equal') {
      var per = total / aeMembers.length;
      splits  = aeMembers.map(function (m) { return { user_id: m.id, amount: per }; });

    } else {
      /* exact: derive per-person totals from item assignments */
      var personTotals = {};
      var unassigned   = 0;
      aeMembers.forEach(function (m) { personTotals[m.id] = 0; });

      document.querySelectorAll('.ae-item-row').forEach(function (row) {
        var amt = parseFloat(row.querySelector('.ae-item-amt')?.value) || 0;
        var uid = row.querySelector('.ae-item-person')?.value;
        if (uid && personTotals.hasOwnProperty(uid)) {
          personTotals[uid] += amt;
        } else {
          unassigned += amt;
        }
      });

      var itemsBase     = Object.values(personTotals).reduce(function (a, b) { return a + b; }, 0) + unassigned;
      var ratio         = itemsBase > 0 ? total / itemsBase : (1 / aeMembers.length);
      var perUnassigned = aeMembers.length > 0 ? unassigned / aeMembers.length : 0;

      splits = aeMembers.map(function (m) {
        return { user_id: m.id, amount: ((personTotals[m.id] || 0) + perUnassigned) * ratio };
      });
    }

    var btn = document.querySelector('.ae-submit');
    btn.disabled    = true;
    btn.textContent = 'Saving…';

    fetch('../AddExpense/save_expense.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title: title, total: total, group_id: group_id, paid_by: paid_by, category: category, splits: splits })
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res.success) {
        aeClose();
        location.reload();
      } else {
        alert('Error: ' + (res.error || 'Unknown error'));
        btn.disabled    = false;
        btn.textContent = 'Add Expense';
      }
    })
    .catch(function () {
      alert('Network error. Please try again.');
      btn.disabled    = false;
      btn.textContent = 'Add Expense';
    });
  };
})();
</script>
