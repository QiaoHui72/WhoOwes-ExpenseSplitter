<?php
$ae_groups = mysqli_fetch_all(mysqli_query($connect,
  "SELECT g.id, g.name FROM groups g
   JOIN group_members gm ON gm.group_id = g.id
   WHERE gm.user_id = $current_user_id
   ORDER BY g.name"
), MYSQLI_ASSOC);
$ae_uid = $current_user_id;
?>
<style>
.ae-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px}
.ae-modal{background:#fff;border-radius:16px;width:100%;max-width:460px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.ae-header{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid #f3f4f6;flex-shrink:0}
.ae-header h2{font-size:1.05rem;font-weight:700;color:#111827;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
.ae-close{width:30px;height:30px;border:none;background:#f3f4f6;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;color:#6b7280;transition:background .15s;line-height:1}
.ae-close:hover{background:#e5e7eb}
.ae-body{flex:1;overflow-y:scroll;padding:18px 24px;display:flex;flex-direction:column;gap:14px;scrollbar-width:none;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
.ae-body::-webkit-scrollbar{display:none}
.ae-footer{padding:14px 24px;border-top:1px solid #f3f4f6;flex-shrink:0}
.ae-lbl{font-size:.67rem;font-weight:700;letter-spacing:.08em;color:#9ca3af;text-transform:uppercase;margin-bottom:6px;display:block}
.ae-input{width:100%;height:44px;padding:0 14px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.9rem;color:#374151;outline:none;transition:border-color .2s;font-family:inherit}
.ae-input:focus{border-color:#1e3a7a;background:#fff}
.ae-amt-row{display:flex;align-items:center;height:44px;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;overflow:hidden;transition:border-color .2s}
.ae-amt-row:focus-within{border-color:#1e3a7a;background:#fff}
.ae-cur-tag{display:flex;align-items:center;padding:0 12px;background:#1e3a7a;color:#fff;font-size:.82rem;font-weight:700;height:100%;flex-shrink:0}
.ae-amt-input{flex:1;height:100%;padding:0 14px;background:transparent;border:none;font-size:1rem;font-weight:600;color:#111827;outline:none;font-family:inherit}
.ae-toggle-row{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#f9fafb;border-radius:10px;border:1.5px solid #f3f4f6}
.ae-toggle-info span{display:block;font-size:.87rem;font-weight:500;color:#374151}
.ae-toggle-info small{font-size:.74rem;color:#9ca3af}
.ae-tgl{position:relative;width:44px;height:24px;flex-shrink:0}
.ae-tgl input{opacity:0;width:0;height:0}
.ae-tgl-sl{position:absolute;cursor:pointer;inset:0;background:#d1d5db;border-radius:99px;transition:background .2s}
.ae-tgl-sl::before{content:'';position:absolute;width:18px;height:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:transform .2s}
.ae-tgl input:checked+.ae-tgl-sl{background:#1e3a7a}
.ae-tgl input:checked+.ae-tgl-sl::before{transform:translateX(20px)}
.ae-select{width:100%;height:44px;padding:0 36px 0 14px;background:#f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat calc(100% - 12px) center;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.9rem;color:#374151;outline:none;appearance:none;cursor:pointer;transition:border-color .2s;font-family:inherit}
.ae-select:focus{border-color:#1e3a7a;background-color:#fff}
.ae-split-btns{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
.ae-split-btn{height:34px;background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.82rem;font-weight:600;color:#6b7280;cursor:pointer;transition:all .15s;font-family:inherit}
.ae-split-btn:hover{background:#e5e7eb}
.ae-split-btn.active{background:#1e3a7a;border-color:#1e3a7a;color:#fff}
.ae-split-section{background:#f9fafb;border-radius:10px;padding:14px}
.ae-split-hint{font-size:.84rem;color:#9ca3af;text-align:center}
.ae-eq-header{font-size:.83rem;color:#6b7280;text-align:center;padding-bottom:8px;border-bottom:1px solid #e5e7eb;margin-bottom:8px}
.ae-eq-header strong{color:#1e3a7a}
.ae-eq-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;font-size:.87rem}
.ae-eq-name{color:#374151;font-weight:500}
.ae-eq-amt{font-weight:700;color:#111827}
.ae-item-person{height:36px;padding:0 24px 0 8px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat calc(100% - 6px) center;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.78rem;color:#374151;outline:none;appearance:none;cursor:pointer;transition:border-color .2s;font-family:inherit;min-width:0;flex:1;max-width:130px}
.ae-item-person:focus{border-color:#1e3a7a}
.ae-person-summary{margin-top:10px;padding-top:10px;border-top:1px solid #e5e7eb}
.ae-person-row{display:flex;justify-content:space-between;align-items:center;padding:4px 0;font-size:.86rem}
.ae-person-name{color:#374151;font-weight:500}
.ae-person-amt{font-weight:700;color:#111827}
.ae-person-total{border-top:1.5px solid #e5e7eb;margin-top:6px;padding-top:8px}
.ae-person-total .ae-person-name{color:#111827;font-weight:700}
.ae-person-total .ae-person-amt{color:#1e3a7a}
.ae-item-row{display:flex;gap:8px;align-items:center;margin-bottom:8px}
.ae-item-name{flex:1;height:36px;padding:0 10px;background:#fff;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.84rem;color:#374151;outline:none;font-family:inherit}
.ae-item-name:focus{border-color:#1e3a7a}
.ae-item-amt-wrap{display:flex;align-items:center;height:36px;background:#fff;border:1.5px solid #e5e7eb;border-radius:8px;overflow:hidden;width:110px;flex-shrink:0}
.ae-item-amt-wrap:focus-within{border-color:#1e3a7a}
.ae-item-cur{padding:0 8px;font-size:.72rem;font-weight:700;color:#9ca3af;flex-shrink:0}
.ae-item-amt{flex:1;height:100%;border:none;background:transparent;font-size:.84rem;color:#374151;outline:none;padding-right:8px;font-family:inherit}
.ae-item-del{width:26px;height:26px;background:#fee2e2;border:none;border-radius:50%;color:#dc2626;font-size:.75rem;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;line-height:1}
.ae-add-item{height:32px;background:transparent;border:1.5px dashed #d1d5db;border-radius:8px;font-size:.82rem;font-weight:500;color:#6b7280;cursor:pointer;width:100%;transition:all .15s;font-family:inherit}
.ae-add-item:hover{border-color:#1e3a7a;color:#1e3a7a}
.ae-items-total{font-size:.8rem;text-align:right;color:#6b7280;margin-top:4px}
.ae-items-total.warn{color:#dc2626}
.ae-cats{display:flex;flex-wrap:wrap;gap:8px}
.ae-cat{height:32px;padding:0 14px;background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:20px;font-size:.82rem;font-weight:500;color:#6b7280;cursor:pointer;transition:all .15s;font-family:inherit}
.ae-cat:hover{background:#e5e7eb}
.ae-cat.active{background:#eef2ff;border-color:#1e3a7a;color:#1e3a7a;font-weight:600}
.ae-summary{background:#f9fafb;border-radius:10px;padding:14px 16px}
.ae-sum-row{display:flex;justify-content:space-between;font-size:.87rem;color:#6b7280;padding:3px 0}
.ae-sum-total{display:flex;justify-content:space-between;font-size:.95rem;font-weight:700;color:#1e3a7a;padding-top:8px;border-top:1px solid #e5e7eb;margin-top:6px}
.ae-submit{width:100%;height:46px;background:#1e3a7a;color:#fff;border:none;border-radius:10px;font-size:.95rem;font-weight:600;cursor:pointer;transition:background .2s;font-family:inherit}
.ae-submit:hover{background:#162d60}
.ae-submit:disabled{background:#9ca3af;cursor:default}
</style>

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
      <label class="ae-tgl"><input type="checkbox" id="aeSst6" onchange="aeCalc()"><span class="ae-tgl-sl"></span></label>
    </div>
    <div class="ae-toggle-row">
      <div class="ae-toggle-info"><span>Include SST (8%)</span><small>SST 8% included</small></div>
      <label class="ae-tgl"><input type="checkbox" id="aeSst8" checked onchange="aeCalc()"><span class="ae-tgl-sl"></span></label>
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

  </div><!-- /.ae-body -->

  <div class="ae-footer">
    <button class="ae-submit" onclick="aeSubmit()">Add Expense</button>
  </div>

</div><!-- /.ae-modal -->
</div><!-- /#aeOverlay -->

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
