<!-- Hero section -->
<section class="hero container">
  <div class="hero-text">
    <h1>By Jirawat Thongjankaew 67020180032</h1>
    <h1>รายงานปัญหาในชุมชน</h1>
    <p>แจ้ง ติดตาม และช่วยกันแก้ไขปัญหาในพื้นที่ของคุณ</p>
  </div>
  <?php if (!isLoggedIn()): ?>
  <div class="hero-cta">
    <a href="<?= url('register') ?>" class="btn btn-primary">เริ่มต้นฟรี</a>
    <a href="<?= url('login') ?>" class="btn btn-outline">เข้าสู่ระบบ</a>
  </div>
  <?php else: ?>
  <a href="<?= url('issue/new') ?>" class="btn btn-primary btn-lg">+ แจ้งปัญหาใหม่</a>
  <?php endif; ?>
</section>

<!-- Filters bar -->
<section class="filters-bar container">
  <form method="GET" action="<?= url() ?>" class="filters-form" id="filter-form">
    <!-- Category pills -->
    <div class="filter-scroll">
      <a href="<?= url() ?>" class="pill <?= empty($filters['category_id']) ? 'active' : '' ?>">ทั้งหมด</a>
      <?php foreach ($categories as $cat): ?>
      <a href="<?= url() ?>?<?= http_build_query(array_merge($_GET, ['cat' => $cat['id'], 'page' => 1])) ?>"
         class="pill <?= (int)$filters['category_id'] === (int)$cat['id'] ? 'active' : '' ?>"
         style="--cat-color:<?= e($cat['color']) ?>">
        <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Status, Urgency, Sort -->
    <div class="filter-row">
      <select name="status" onchange="this.form.submit()" aria-label="กรองตามสถานะ">
        <option value="">สถานะทั้งหมด</option>
        <?php foreach (['new'=>'ใหม่','reviewing'=>'กำลังตรวจสอบ','in_progress'=>'กำลังดำเนินการ','resolved'=>'แก้ไขแล้ว','rejected'=>'ปฏิเสธ'] as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filters['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>

      <select name="urgency" onchange="this.form.submit()" aria-label="กรองตามความเร่งด่วน">
        <option value="">ทุกระดับ</option>
        <option value="high"   <?= $filters['urgency']==='high'   ? 'selected':'' ?>>🔴 เร่งด่วน</option>
        <option value="medium" <?= $filters['urgency']==='medium' ? 'selected':'' ?>>🟡 ปานกลาง</option>
        <option value="low"    <?= $filters['urgency']==='low'    ? 'selected':'' ?>>🟢 ต่ำ</option>
      </select>

      <select name="sort" onchange="this.form.submit()" aria-label="เรียงลำดับ">
        <option value="latest" <?= $filters['sort']==='latest' ? 'selected':'' ?>>ล่าสุด</option>
        <option value="votes"  <?= $filters['sort']==='votes'  ? 'selected':'' ?>>โหวตมากสุด</option>
        <option value="oldest" <?= $filters['sort']==='oldest' ? 'selected':'' ?>>เก่าสุด</option>
      </select>

      <?php if ($filters['search'] || $filters['status'] || $filters['urgency'] || $filters['category_id']): ?>
      <a href="<?= url() ?>" class="btn btn-outline btn-sm">✕ ล้างตัวกรอง</a>
      <?php endif; ?>
    </div>
    <!-- Hidden inputs to preserve other filters -->
    <input type="hidden" name="q"    value="<?= e($filters['search']) ?>">
    <input type="hidden" name="cat"  value="<?= e($filters['category_id']) ?>">
  </form>
</section>

<!-- Issue list -->
<section class="container issue-list-section">
  <?php if (empty($issues)): ?>
  <!-- Empty state -->
  <div class="empty-state">
    <div class="empty-icon">📭</div>
    <h2>ยังไม่มีรายงานปัญหา</h2>
    <p>เป็นคนแรกที่แจ้งปัญหาในพื้นที่ของคุณ!</p>
    <a href="<?= url('issue/new') ?>" class="btn btn-primary">+ แจ้งปัญหาแรก</a>
  </div>
  <?php else: ?>

  <div class="issue-grid" id="issue-grid">
    <?php foreach ($issues as $issue): ?>
    <article class="issue-card card <?= urgencyClass($issue['urgency']) ?>-border"
             data-issue-id="<?= $issue['id'] ?>">
      <div class="issue-card-accent"></div>

      <div class="issue-card-body">
        <div class="card-header">
          <?php if ($issue['is_pinned']): ?>
          <span class="pin-badge" title="ปักหมุดโดย Admin">📌 ปักหมุด</span>
          <?php endif; ?>
          <span class="cat-badge" style="background:<?= e($issue['category_color']) ?>20;color:<?= e($issue['category_color']) ?>;border-color:<?= e($issue['category_color']) ?>40">
            <?= e($issue['category_icon']) ?> <?= e($issue['category_name']) ?>
          </span>
          <span class="status-pill <?= statusClass($issue['status']) ?>">
            <?= statusLabel($issue['status']) ?>
          </span>
        </div>

        <h2 class="card-title">
          <a href="<?= url('issue/' . $issue['id']) ?>"><?= e($issue['title']) ?></a>
        </h2>

        <p class="card-excerpt"><?= e(mb_substr($issue['description'], 0, 110)) ?>...</p>

        <div class="card-meta">
          <span class="urgency-tag <?= urgencyClass($issue['urgency']) ?>">
            <?= urgencyLabel($issue['urgency']) ?>
          </span>
          <span class="meta-sep">·</span>
          <span class="vote-count" title="ยืนยันว่าพบปัญหา">👍 <?= e($issue['vote_count']) ?></span>
          <span class="meta-sep">·</span>
          <span class="time-ago" title="<?= e($issue['created_at']) ?>"><?= timeAgo($issue['created_at']) ?></span>
        </div>
      </div>

      <div class="card-footer">
        <span class="user-tag">by <?= e($issue['user_name']) ?></span>
        <span class="ticket-id"><?= e($issue['ticket_id']) ?></span>
      </div>
    </article>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($pag['total_pages'] > 1): ?>
  <nav class="pagination" aria-label="เลขหน้า">
    <?php if ($pag['current_page'] > 1): ?>
    <a href="<?= e($pag['base_url'] . ($pag['current_page']-1)) ?>" class="page-btn" aria-label="หน้าก่อน">‹</a>
    <?php endif; ?>

    <?php for ($p = max(1, $pag['current_page']-2); $p <= min($pag['total_pages'], $pag['current_page']+2); $p++): ?>
    <a href="<?= e($pag['base_url'] . $p) ?>"
       class="page-btn <?= $p === $pag['current_page'] ? 'active' : '' ?>"
       aria-current="<?= $p === $pag['current_page'] ? 'page' : 'false' ?>">
      <?= $p ?>
    </a>
    <?php endfor; ?>

    <?php if ($pag['current_page'] < $pag['total_pages']): ?>
    <a href="<?= e($pag['base_url'] . ($pag['current_page']+1)) ?>" class="page-btn" aria-label="หน้าถัดไป">›</a>
    <?php endif; ?>
  </nav>
  <?php endif; ?>
  
  <?php endif; ?>
</section>
