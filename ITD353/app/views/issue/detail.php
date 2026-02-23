<div class="container issue-detail-wrap">
  <!-- Breadcrumb -->
  <nav class="breadcrumb" aria-label="เส้นทาง">
    <a href="<?= url() ?>">หน้าแรก</a>
    <span aria-hidden="true">›</span>
    <span aria-current="page"><?= e($issue['ticket_id']) ?></span>
  </nav>

  <div class="issue-detail-grid">
    <!-- Main content -->
    <article class="issue-main">
      <!-- Header -->
      <div class="issue-detail-header card">
        <div class="issue-badges">
          <span class="cat-badge" style="background:<?= e($issue['category_color']) ?>20;color:<?= e($issue['category_color']) ?>;border-color:<?= e($issue['category_color']) ?>">
            <?= e($issue['category_icon']) ?> <?= e($issue['category_name']) ?>
          </span>
          <span class="status-pill <?= statusClass($issue['status']) ?>">
            <?= statusLabel($issue['status']) ?>
          </span>
          <span class="urgency-tag <?= urgencyClass($issue['urgency']) ?>">
            <?= urgencyLabel($issue['urgency']) ?>
          </span>
          <?php if ($issue['is_pinned']): ?><span class="pin-chip">📌 ปักหมุด</span><?php endif; ?>
        </div>

        <h1><?= e($issue['title']) ?></h1>

        <div class="issue-meta-row">
          <span>รายงานโดย <strong><?= e($issue['user_name']) ?></strong></span>
          <span>·</span>
          <span title="<?= e($issue['created_at']) ?>"><?= timeAgo($issue['created_at']) ?></span>
          <span>·</span>
          <span class="ticket-id-badge"><?= e($issue['ticket_id']) ?></span>
        </div>

        <!-- Share buttons -->
        <div class="share-row">
          <button id="btn-copy-link" class="btn btn-outline btn-sm" aria-label="คัดลอกลิงก์">🔗 คัดลอกลิงก์</button>
          <?php if ($issue['latitude'] && $issue['longitude']): ?>
          <button id="btn-map-show" class="btn btn-outline btn-sm">🗺️ ดูแผนที่</button>
          <?php endif; ?>
        </div>
      </div>

      <!-- Image carousel -->
      <?php if ($images): ?>
      <div class="issue-carousel card" id="carousel">
        <div class="carousel-track" id="carousel-track">
          <?php foreach ($images as $img): ?>
          <div class="carousel-slide">
            <img src="<?= e(uploadUrl($img['filename'])) ?>"
                 alt="รูปประกอบปัญหา"
                 loading="lazy"
                 onerror="this.src='<?= asset('img/no-image.png') ?>'">
          </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($images) > 1): ?>
        <button class="carousel-btn prev" id="car-prev" aria-label="ก่อนหน้า">‹</button>
        <button class="carousel-btn next" id="car-next" aria-label="ถัดไป">›</button>
        <div class="carousel-dots" id="car-dots">
          <?php foreach ($images as $i => $img): ?>
          <button class="dot <?= $i===0?'active':'' ?>" data-idx="<?= $i ?>" aria-label="รูปที่ <?= $i+1 ?>"></button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Description -->
      <div class="card issue-body">
        <h2 class="section-title">รายละเอียด</h2>
        <p><?= nl2br(e($issue['description'])) ?></p>

        <?php if ($issue['location_text']): ?>
        <div class="location-info">
          <span>📍</span>
          <span><?= e($issue['location_text']) ?></span>
        </div>
        <?php endif; ?>

        <?php if ($issue['admin_note']): ?>
        <div class="admin-note-box">
          <strong>🗒️ หมายเหตุจาก Admin:</strong>
          <p><?= nl2br(e($issue['admin_note'])) ?></p>
        </div>
        <?php endif; ?>
      </div>

      <!-- Map (if lat/lng) -->
      <?php if ($issue['latitude'] && $issue['longitude']): ?>
      <div class="card" id="detail-map-wrap" hidden>
        <h2 class="section-title">ตำแหน่งบนแผนที่</h2>
        <div id="detail-map" class="issue-map" aria-label="แผนที่แสดงตำแหน่งปัญหา"></div>
      </div>
      <script>
      document.getElementById('btn-map-show')?.addEventListener('click', function () {
        const wrap = document.getElementById('detail-map-wrap');
        wrap.hidden = !wrap.hidden;
        if (!wrap.hidden && !wrap._mapInit) {
          wrap._mapInit = true;
          const map = L.map('detail-map').setView([<?= (float)$issue['latitude'] ?>, <?= (float)$issue['longitude'] ?>], 16);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', maxZoom: 19
          }).addTo(map);
          L.marker([<?= (float)$issue['latitude'] ?>, <?= (float)$issue['longitude'] ?>])
            .addTo(map)
            .bindPopup('<?= e($issue['title']) ?>').openPopup();
        }
      });
      </script>
      <?php endif; ?>

      <!-- Comments -->
      <section class="card comments-section" id="comments">
        <h2 class="section-title">💬 ความคิดเห็น (<?= count($comments) ?>)</h2>

        <?php if (empty($comments)): ?>
        <div class="empty-state-sm">
          <span>🗨️</span>
          <p>ยังไม่มีความคิดเห็น เป็นคนแรกที่แสดงความคิดเห็น!</p>
        </div>
        <?php else: ?>
        <div class="comment-list">
          <?php foreach ($comments as $c): ?>
          <div class="comment-item <?= $c['is_pinned'] ? 'pinned' : '' ?>" id="comment-<?= $c['id'] ?>">
            <?php if ($c['is_pinned']): ?><span class="pin-chip">📌 ปักหมุด</span><?php endif; ?>
            <div class="comment-header">
              <span class="comment-author">
                <?= e($c['user_name']) ?>
                <?php if ($c['user_role'] === 'admin'): ?><span class="admin-badge">Admin</span><?php endif; ?>
              </span>
              <span class="comment-time" title="<?= e($c['created_at']) ?>"><?= timeAgo($c['created_at']) ?></span>
            </div>
            <p class="comment-body"><?= nl2br(e($c['body'])) ?></p>
            <div class="comment-actions">
              <?php if (isAdmin()): ?>
              <form method="POST" action="<?= url('admin/comment/' . $c['id'] . '/pin') ?>" style="display:inline">
                <?= csrfField() ?>
                <button type="submit" class="btn-text"><?= $c['is_pinned'] ? '📌 เลิกปักหมุด' : '📌 ปักหมุด' ?></button>
              </form>
              <?php endif; ?>
              <?php if (isAdmin() || (isLoggedIn() && auth()['id'] == $c['user_id'])): ?>
              <form method="POST" action="<?= url('comment/' . $c['id'] . '/delete') ?>" style="display:inline"
                    onsubmit="return confirm('ลบความคิดเห็นนี้?')">
                <?= csrfField() ?>
                <button type="submit" class="btn-text danger">🗑️ ลบ</button>
              </form>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Add comment form -->
        <?php if (isLoggedIn()): ?>
        <form method="POST" action="<?= url('issue/' . $issue['id'] . '/comment') ?>"
              class="comment-form">
          <?= csrfField() ?>
          <div class="form-group">
            <label for="comment-body" class="sr-only">เพิ่มความคิดเห็น</label>
            <textarea id="comment-body" name="body" rows="3"
                      placeholder="แสดงความคิดเห็นหรือข้อมูลเพิ่มเติม..."
                      required minlength="2"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">💬 ส่งความคิดเห็น</button>
        </form>
        <?php else: ?>
        <p class="auth-prompt">
          <a href="<?= url('login') ?>">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น
        </p>
        <?php endif; ?>
      </section>
    </article>

    <!-- Sidebar -->
    <aside class="issue-sidebar">
      <!-- Vote card -->
      <div class="card vote-card">
        <div class="vote-count-big" id="vote-count"><?= e($issue['vote_count']) ?></div>
        <div class="vote-label">คนยืนยันว่าพบปัญหา</div>
        <?php if (isLoggedIn()): ?>
        <button id="vote-btn"
                class="btn <?= $hasVoted ? 'btn-primary' : 'btn-outline' ?> btn-block"
                data-issue="<?= $issue['id'] ?>"
                aria-pressed="<?= $hasVoted ? 'true' : 'false' ?>"
                aria-label="ยืนยันว่าพบปัญหานี้">
          <?= $hasVoted ? '✅ ยืนยันแล้ว' : '👍 ยืนยันว่าพบปัญหา' ?>
        </button>
        <?php else: ?>
        <a href="<?= url('login') ?>" class="btn btn-outline btn-block">🔑 เข้าสู่ระบบเพื่อโหวต</a>
        <?php endif; ?>
      </div>

      <!-- Status timeline -->
      <div class="card">
        <h3 class="section-title">⏱️ ความคืบหน้า</h3>
        <ol class="status-timeline">
          <?php foreach ($statusLogs as $log): ?>
          <li class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <strong><?= statusLabel($log['new_status']) ?></strong>
              <?php if ($log['note']): ?>
              <p><?= e($log['note']) ?></p>
              <?php endif; ?>
              <small><?= e($log['changed_by_name'] ?? 'ระบบ') ?> · <?= timeAgo($log['created_at']) ?></small>
            </div>
          </li>
          <?php endforeach; ?>
        </ol>
      </div>

      <!-- Admin edit link -->
      <?php if (isAdmin()): ?>
      <a href="<?= url('admin/issues/' . $issue['id']) ?>" class="btn btn-outline btn-block">
        ⚙️ จัดการปัญหานี้
      </a>
      <?php endif; ?>
    </aside>
  </div>
</div>

<script>
// Vote button AJAX
document.getElementById('vote-btn')?.addEventListener('click', async function () {
  const btn = this;
  const issueId = btn.dataset.issue;
  btn.disabled = true;

  try {
    const res = await fetch('<?= url('issue/') ?>' + issueId + '/vote', {
      method: 'POST',
      headers: { 'X-CSRF-Token': '<?= csrfToken() ?>' }
    });
    const data = await res.json();
    if (data.ok) {
      document.getElementById('vote-count').textContent = data.count;
      btn.classList.toggle('btn-primary', data.voted);
      btn.classList.toggle('btn-outline', !data.voted);
      btn.setAttribute('aria-pressed', data.voted);
      btn.textContent = data.voted ? '✅ ยืนยันแล้ว' : '👍 ยืนยันว่าพบปัญหา';
    }
  } catch(e) { /* network error */ }
  btn.disabled = false;
});

// Copy link
document.getElementById('btn-copy-link')?.addEventListener('click', function () {
  const url = window.location.href;
  if (navigator.share) {
    navigator.share({ title: document.title, url });
  } else {
    navigator.clipboard.writeText(url).then(() => {
      window.showToast('success', 'คัดลอกลิงก์แล้ว!');
    });
  }
});
</script>
