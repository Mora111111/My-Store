<style>
.fin-range { border: 1.5px solid #e2e8f0; background: #fff; color: #475569; padding: 10px 22px; border-radius: 40px; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; }
.fin-range:hover { border-color: #38bdf8; color: #0284c7; }
.fin-range.active { background: linear-gradient(135deg, #38bdf8, #2dd4bf); color: #0f172a; border-color: transparent; box-shadow: 0 8px 16px rgba(56,189,248,0.3); }
.fin-chart { width: 100%; height: auto; display: block; }
.fin-legend { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
.fin-legend-item { display: inline-flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 40px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: #334155; }
.fin-legend-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
.fin-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 25px; }
@media (max-width: 900px) { .fin-grid { grid-template-columns: 1fr; } }
</style>

<div style="display:flex; gap:10px; margin-bottom:25px; flex-wrap:wrap;" id="finance-ranges">
  <button type="button" class="fin-range<?= ($activeRange ?? '7d') === 'today' ? ' active' : '' ?>" data-fin-range="today"><?= lang('finance_today') ?></button>
  <button type="button" class="fin-range<?= ($activeRange ?? '7d') === '7d' ? ' active' : '' ?>" data-fin-range="7d"><?= lang('finance_7d') ?></button>
  <button type="button" class="fin-range<?= ($activeRange ?? '7d') === '30d' ? ' active' : '' ?>" data-fin-range="30d"><?= lang('finance_30d') ?></button>
</div>

<div class="stats-container" style="margin-bottom:25px;">
  <div class="stat-card">
    <div class="stat-info"><h3><?= lang('kpi_gross') ?></h3><p><span id="kpi-gross"><?= number_format((float)($kpis['gross'] ?? 0), 2) ?></span> <small style="font-size:14px; color:#64748b;"><?= lang('currency_egp') ?></small></p></div>
    <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
  </div>
  <div class="stat-card">
    <div class="stat-info"><h3><?= lang('kpi_net') ?></h3><p><span id="kpi-net"><?= number_format((float)($kpis['net'] ?? 0), 2) ?></span> <small style="font-size:14px; color:#64748b;"><?= lang('currency_egp') ?></small></p></div>
    <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
  </div>
  <div class="stat-card">
    <div class="stat-info"><h3><?= lang('kpi_orders') ?></h3><p id="kpi-orders"><?= (int)($kpis['orders'] ?? 0) ?></p></div>
    <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
  </div>
  <div class="stat-card">
    <div class="stat-info"><h3><?= lang('kpi_aov') ?></h3><p><span id="kpi-aov"><?= number_format((float)($kpis['aov'] ?? 0), 2) ?></span> <small style="font-size:14px; color:#64748b;"><?= lang('currency_egp') ?></small></p></div>
    <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
  </div>
</div>

<div class="fin-grid">
  <div class="card" style="margin-bottom:0;">
    <h2><i class="fa-solid fa-chart-line"></i> <?= lang('chart_trend') ?></h2>
    <div id="fin-line"></div>
  </div>
  <div class="card" style="margin-bottom:0;">
    <h2><i class="fa-solid fa-chart-pie"></i> <?= lang('chart_status') ?></h2>
    <div id="fin-donut"></div>
    <div class="fin-legend" id="fin-legend"></div>
  </div>
</div>

<div class="card" id="fin-empty" style="display:none; margin-top:25px; margin-bottom:0; text-align:center; color:#64748b; font-weight:600;">
  <i class="fa-solid fa-inbox" style="font-size:32px; margin-bottom:10px; display:block; opacity:0.5;"></i>
  <?= lang('finance_empty') ?>
</div>

<script>
(function () {
  var palette = ['#38bdf8', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'];
  var daily = <?= json_encode($daily ?? [], JSON_UNESCAPED_UNICODE) ?>;
  var donut = <?= json_encode($donut ?? [], JSON_UNESCAPED_UNICODE) ?>;
  var emptyBox = document.getElementById('fin-empty');

  function esc(s) {
    return String(s === null || s === undefined ? '' : s).replace(/[&<>"']/g, function (c) {
      return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
    });
  }

  function fmt(n) {
    var v = parseFloat(n);
    if (isNaN(v)) v = 0;
    return v.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function renderLine(rows) {
    var box = document.getElementById('fin-line');
    if (!rows.length) { box.innerHTML = ''; return; }
    var vals = rows.map(function (r) { return parseFloat(r.net) || 0; });
    var max = Math.max.apply(null, vals.concat([1]));
    var W = 600, H = 240, P = 34;
    var step = rows.length > 1 ? (W - P * 2) / (rows.length - 1) : 0;
    var pts = vals.map(function (v, i) {
      var x = P + step * i;
      var y = H - P - ((v / max) * (H - P * 2));
      return x.toFixed(1) + ',' + y.toFixed(1);
    });
    var grid = '';
    var gi;
    for (gi = 0; gi <= 3; gi++) {
      var gy = P + ((H - P * 2) * gi / 3);
      var gl = (max * (1 - gi / 3)).toFixed(0);
      grid += '<line x1="' + P + '" y1="' + gy + '" x2="' + (W - 12) + '" y2="' + gy + '" stroke="#eef2f7" stroke-width="1" />' +
        '<text x="4" y="' + (gy + 4) + '" font-size="10" fill="#94a3b8">' + gl + '</text>';
    }
    var labels = rows.map(function (r, i) {
      var x = P + step * i;
      var d = String(r.day || '').slice(5);
      return '<text x="' + x + '" y="' + (H - 8) + '" font-size="10" fill="#94a3b8" text-anchor="middle">' + esc(d) + '</text>';
    }).join('');
    box.innerHTML = '<svg class="fin-chart" viewBox="0 0 ' + W + ' ' + H + '" role="img">' + grid +
      '<polyline points="' + pts.join(' ') + '" fill="none" stroke="#0284c7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />' +
      labels + '</svg>';
  }

  function renderDonut(rows) {
    var box = document.getElementById('fin-donut');
    var legend = document.getElementById('fin-legend');
    if (!rows.length) { box.innerHTML = ''; legend.innerHTML = ''; return; }
    var total = 0;
    rows.forEach(function (r) { total += parseInt(r.orders_count, 10) || 0; });
    if (!total) { box.innerHTML = ''; legend.innerHTML = ''; return; }
    var R = 54, C = 2 * Math.PI * R, acc = 0, segs = '', leg = '';
    rows.forEach(function (r, i) {
      var cnt = parseInt(r.orders_count, 10) || 0;
      var frac = cnt / total;
      var color = palette[i % palette.length];
      var dash = (frac * C).toFixed(1);
      var off = (-acc * C).toFixed(1);
      segs += '<circle cx="90" cy="90" r="' + R + '" fill="none" stroke="' + color + '" stroke-width="26" stroke-dasharray="' + dash + ' ' + C.toFixed(1) + '" stroke-dashoffset="' + off + '" transform="rotate(-90 90 90)" />';
      acc += frac;
      leg += '<span class="fin-legend-item"><span class="fin-legend-dot" style="background:' + color + ';"></span>' + esc(r.status) + ' (' + cnt + ')</span>';
    });
    box.innerHTML = '<svg class="fin-chart" viewBox="0 0 180 180" role="img" style="max-width:260px; margin:0 auto;">' + segs +
      '<text x="90" y="86" text-anchor="middle" font-size="26" font-weight="800" fill="#0f172a">' + total + '</text>' +
      '<text x="90" y="106" text-anchor="middle" font-size="11" fill="#64748b">' + esc(document.getElementById('kpi-orders').textContent) + '</text></svg>';
    legend.innerHTML = leg;
  }

  function paint(payload) {
    var k = payload.kpis || {};
    document.getElementById('kpi-gross').textContent = fmt(k.gross);
    document.getElementById('kpi-net').textContent = fmt(k.net);
    document.getElementById('kpi-orders').textContent = parseInt(k.orders, 10) || 0;
    document.getElementById('kpi-aov').textContent = fmt(k.aov);
    renderLine(payload.daily || []);
    renderDonut(payload.donut || []);
    var has = (payload.daily && payload.daily.length) || (payload.donut && payload.donut.length);
    emptyBox.style.display = has ? 'none' : 'block';
  }

  paint({kpis: <?= json_encode($kpis ?? [], JSON_UNESCAPED_UNICODE) ?>, daily: daily, donut: donut});

  document.querySelectorAll('[data-fin-range]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('[data-fin-range]').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      fetch('/admin/finance/data?range=' + btn.getAttribute('data-fin-range'), {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      }).then(function (res) { return res.json(); }).then(paint);
    });
  });
})();
</script>
