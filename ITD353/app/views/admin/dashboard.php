<div class="container admin-layout">
  <!-- Admin sidebar nav -->
  <aside class="admin-sidebar" aria-label="เมนู Admin">
    <nav>
      <a href="<?= url('admin') ?>"            class="admin-nav-link active">📊 Dashboard</a>
      <a href="<?= url('admin/issues') ?>"     class="admin-nav-link">📋 จัดการปัญหา</a>
      <a href="<?= url('admin/categories') ?>" class="admin-nav-link">🏷️ หมวดหมู่</a>
      <a href="<?= url('admin/users') ?>"      class="admin-nav-link">👥 ผู้ใช้</a>
      <hr>
      <a href="<?= url() ?>" class="admin-nav-link">🏠 กลับหน้าหลัก</a>
    </nav>
  </aside>

  <div class="admin-main">
    <div class="admin-page-header">
      <h1>📊 Dashboard</h1>
      <small>วันนี้ <?= date('d F Y') ?></small>
    </div>

    <!-- Summary stats -->
    <?php
    $statusMap = [];
    foreach ($statsByStatus as $s => $c) $statusMap[$s] = $c;
    $totalIssues = array_sum($statsByStatus);

    $todayTotal = 0;
    foreach ($statsToday as $r) $todayTotal += $r['cnt'];
    ?>
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-num"><?= $totalIssues ?></div>
        <div class="stat-label">ปัญหาทั้งหมด</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🆕</div>
        <div class="stat-num"><?= $statusMap['new'] ?? 0 ?></div>
        <div class="stat-label">รอดำเนินการ</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-num"><?= $statusMap['resolved'] ?? 0 ?></div>
        <div class="stat-label">แก้ไขแล้ว</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-num"><?= $todayTotal ?></div>
        <div class="stat-label">วันนี้</div>
      </div>
    </div>

    <!-- Charts row -->
    <div class="charts-row">
      <!-- Weekly bar chart (CSS/Canvas) -->
      <div class="card chart-card">
        <h2 class="section-title">รายงาน 7 วันล่าสุด</h2>
        <canvas id="weekly-chart" width="400" height="200" aria-label="กราฟรายงานรายวัน" role="img"></canvas>
      </div>

      <!-- By status donut -->
      <div class="card chart-card">
        <h2 class="section-title">สถานะปัญหา</h2>
        <canvas id="status-chart" width="200" height="200" aria-label="สัดส่วนสถานะ" role="img"></canvas>
        <ul class="legend-list" id="status-legend"></ul>
      </div>
    </div>

    <!-- By category -->
    <div class="card mt-1">
      <h2 class="card-section-title">ปัญหาตามหมวดหมู่</h2>
      <div class="card-body">
      <div class="cat-bars">
        <?php
        $maxCat = max(array_column($statsByCategory, 'cnt') ?: [1]);
        foreach ($statsByCategory as $c):
          $pct = $maxCat ? round($c['cnt'] / $maxCat * 100) : 0;
        ?>
        <div class="cat-bar-row">
          <span class="cat-bar-label"><?= e($c['icon']) ?> <?= e($c['name']) ?></span>
          <div class="cat-bar-track">
            <div class="cat-bar-fill" style="width:<?= $pct ?>%;background:<?= e($c['color']) ?>"></div>
          </div>
          <span class="cat-bar-num"><?= $c['cnt'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      </div>
    </div>

    <!-- Recent issues -->
    <div class="card mt-1">
      <h2 class="card-section-title">ปัญหาล่าสุด</h2>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Ticket</th><th>หัวข้อ</th><th>สถานะ</th><th>ความเร่งด่วน</th><th>วันที่</th></tr></thead>
          <tbody>
            <?php foreach ($recentIssues as $i): ?>
            <tr>
              <td><a href="<?= url('admin/issues/' . $i['id']) ?>"><?= e($i['ticket_id']) ?></a></td>
              <td><?= e(mb_substr($i['title'],0,45)) ?></td>
              <td><span class="status-pill <?= statusClass($i['status']) ?>"><?= statusLabel($i['status']) ?></span></td>
              <td><span class="urgency-tag <?= urgencyClass($i['urgency']) ?>"><?= urgencyLabel($i['urgency']) ?></span></td>
              <td><?= e(date('d/m/y H:i', strtotime($i['created_at']))) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="card-table-footer">
        <a href="<?= url('admin/issues') ?>" class="btn btn-outline btn-sm">ดูทั้งหมด →</a>
      </div>
    </div>
  </div>
</div>

<script>
// ---- Weekly bar chart (vanilla Canvas) ----
(function () {
  const weekData = <?= json_encode($statsWeek, JSON_UNESCAPED_UNICODE) ?>;
  const canvas = document.getElementById('weekly-chart');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const W = canvas.width, H = canvas.height;
  const pad = { t:20, r:20, b:40, l:40 };
  const data = weekData.map(d => ({ label: d.day.slice(5), val: parseInt(d.cnt) }));
  // fill missing days
  const allDays = [];
  for (let i=6; i>=0; i--) {
    const d = new Date(); d.setDate(d.getDate()-i);
    const key = (d.getMonth()+1).toString().padStart(2,'0')+'-'+d.getDate().toString().padStart(2,'0');
    const found = data.find(x => x.label === key);
    allDays.push({ label: key, val: found ? found.val : 0 });
  }

  const maxVal = Math.max(...allDays.map(d=>d.val), 1);
  const bw = (W - pad.l - pad.r) / allDays.length;
  const getColor = () => getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#3b82f6';

  ctx.clearRect(0,0,W,H);
  allDays.forEach((d,i) => {
    const bh = ((d.val / maxVal) * (H - pad.t - pad.b));
    const x = pad.l + i * bw + bw*0.1;
    const y = H - pad.b - bh;
    ctx.fillStyle = getColor();
    ctx.globalAlpha = 0.85;
    ctx.beginPath();
    ctx.roundRect?.(x, y, bw*0.8, bh, 4) || ctx.rect(x, y, bw*0.8, bh);
    ctx.fill();
    ctx.globalAlpha = 1;
    // label
    ctx.fillStyle = getComputedStyle(document.body).getPropertyValue('color') || '#111';
    ctx.font = '10px Prompt, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(d.label, x + bw*0.4, H - pad.b + 14);
    if (d.val > 0) ctx.fillText(d.val, x + bw*0.4, y - 4);
  });
})();

// ---- Status donut (Canvas) ----
(function () {
  const statusData = <?= json_encode(array_map(
    fn($s,$c) => ['label' => statusLabel($s), 'val' => $c, 'color' => match($s) {
      'new'=>'#3b82f6','reviewing'=>'#f59e0b','in_progress'=>'#8b5cf6',
      'resolved'=>'#22c55e','rejected'=>'#ef4444', default=>'#6b7280'
    }],
    array_keys($statsByStatus), array_values($statsByStatus)
  ), JSON_UNESCAPED_UNICODE) ?>;

  const canvas = document.getElementById('status-chart');
  if (!canvas || !statusData.length) return;
  const ctx = canvas.getContext('2d');
  const cx = canvas.width/2, cy = canvas.height/2, r = 75, ri = 45;
  const total = statusData.reduce((s,d)=>s+d.val,0) || 1;
  let start = -Math.PI/2;
  const legend = document.getElementById('status-legend');
  statusData.forEach(d => {
    const slice = (d.val/total) * 2 * Math.PI;
    ctx.beginPath();
    ctx.moveTo(cx,cy);
    ctx.arc(cx,cy,r,start,start+slice);
    ctx.closePath();
    ctx.fillStyle = d.color;
    ctx.fill();
    start += slice;
    // Legend
    const li = document.createElement('li');
    li.innerHTML = `<span class="dot" style="background:${d.color}"></span>${d.label} (${d.val})`;
    legend.appendChild(li);
  });
  // Donut hole
  ctx.beginPath();
  ctx.arc(cx,cy,ri,0,Math.PI*2);
  ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--color-surface') || '#fff';
  ctx.fill();
  ctx.fillStyle = getComputedStyle(document.body).color || '#111';
  ctx.font = 'bold 20px Prompt,sans-serif';
  ctx.textAlign='center';ctx.textBaseline='middle';
  ctx.fillText(total, cx, cy);
})();
</script>
