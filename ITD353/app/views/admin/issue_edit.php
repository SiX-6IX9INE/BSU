<div class="container admin-layout">
  <aside class="admin-sidebar" aria-label="เมนู Admin">
    <nav>
      <a href="<?= url('admin') ?>"            class="admin-nav-link">📊 Dashboard</a>
      <a href="<?= url('admin/issues') ?>"     class="admin-nav-link active">📋 จัดการปัญหา</a>
      <a href="<?= url('admin/categories') ?>" class="admin-nav-link">🏷️ หมวดหมู่</a>
      <a href="<?= url('admin/users') ?>"      class="admin-nav-link">👥 ผู้ใช้</a>
      <hr><a href="<?= url() ?>" class="admin-nav-link">🏠 กลับหน้าหลัก</a>
    </nav>
  </aside>

  <div class="admin-main">
    <div class="admin-page-header">
      <a href="<?= url('admin/issues') ?>" class="btn btn-outline btn-sm">← กลับ</a>
      <h1>✏️ แก้ไขปัญหา: <?= e($issue['ticket_id']) ?></h1>
      <a href="<?= url('issue/' . $issue['id']) ?>" class="btn btn-outline btn-sm" target="_blank">👁️ ดูหน้าสาธารณะ</a>
    </div>

    <div class="profile-grid">
      <!-- Update status form -->
      <div class="card">
        <h2 class="card-section-title">อัปเดตสถานะ</h2>
        <div class="card-body">
        <form method="POST" action="<?= url('admin/issues/'.$issue['id'].'/update') ?>">
          <?= csrfField() ?>
          <div class="form-group">
            <label for="status">สถานะ</label>
            <select id="status" name="status">
              <?php foreach (['new'=>'ใหม่','reviewing'=>'กำลังตรวจสอบ','in_progress'=>'กำลังดำเนินการ','resolved'=>'แก้ไขแล้ว','rejected'=>'ปฏิเสธ'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= $issue['status']===$v?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="urgency">ความเร่งด่วน</label>
            <select id="urgency" name="urgency">
              <option value="low"    <?= $issue['urgency']==='low'   ?'selected':''  ?>>🟢 ต่ำ</option>
              <option value="medium" <?= $issue['urgency']==='medium'?'selected':'' ?>>🟡 ปานกลาง</option>
              <option value="high"   <?= $issue['urgency']==='high'  ?'selected':''  ?>>🔴 เร่งด่วน</option>
            </select>
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" name="is_pinned" value="1" <?= $issue['is_pinned']?'checked':'' ?>>
              📌 ปักหมุดเรื่องนี้
            </label>
          </div>
          <div class="form-group">
            <label for="note">หมายเหตุ (สาธารณะ)</label>
            <textarea id="note" name="note" rows="3" placeholder="อัปเดตความคืบหน้าให้ผู้แจ้งทราบ..."><?= e($issue['admin_note'] ?? '') ?></textarea>
          </div>
          <div class="form-row">
            <button type="submit" class="btn btn-primary">💾 บันทึก</button>
            <form method="POST" action="<?= url('admin/issues/'.$issue['id'].'/delete') ?>"
                  style="display:inline" onsubmit="return confirm('ลบปัญหานี้ถาวร?')">
              <?= csrfField() ?>
              <button type="submit" class="btn btn-danger">🗑️ ลบ</button>
            </form>
          </div>
        </form>
        </div>
      </div>

      <!-- Issue info -->
      <div class="card">
        <h2 class="card-section-title">ข้อมูลปัญหา</h2>
        <div class="card-body">
        <dl class="info-list">
          <dt>หัวข้อ</dt><dd><?= e($issue['title']) ?></dd>
          <dt>หมวดหมู่</dt><dd><?= e($issue['category_icon'].' '.$issue['category_name']) ?></dd>
          <dt>รายงานโดย</dt><dd><?= e($issue['user_name']) ?> (<?= e($issue['user_email']) ?>)</dd>
          <dt>วันที่แจ้ง</dt><dd><?= e($issue['created_at']) ?></dd>
          <dt>โหวต</dt><dd><?= $issue['vote_count'] ?></dd>
          <?php if ($issue['location_text']): ?>
          <dt>สถานที่</dt><dd><?= e($issue['location_text']) ?></dd>
          <?php endif; ?>
        </dl>
        <p><?= nl2br(e($issue['description'])) ?></p>
        <!-- Images -->
        <?php if ($images): ?>
        <div class="image-thumbs">
          <?php foreach ($images as $img): ?>
          <img src="<?= e(uploadUrl($img['filename'])) ?>" alt="รูปประกอบ"
               style="width:80px;height:60px;object-fit:cover;border-radius:6px">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        </div><!-- /.card-body -->
      </div><!-- /.card issue-info -->
    </div><!-- /.profile-grid -->

    <!-- Status timeline -->
    <div class="card mt-1">
      <h2 class="card-section-title">⏱️ ประวัติสถานะ</h2>
      <div class="card-body">
      <ol class="status-timeline">
        <?php foreach ($statusLogs as $log): ?>
        <li class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <strong><?= statusLabel($log['new_status']) ?></strong>
            <?php if ($log['note']): ?><p><?= e($log['note']) ?></p><?php endif; ?>
            <small><?= e($log['changed_by_name'] ?? 'ระบบ') ?> · <?= e($log['created_at']) ?></small>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
      </div>
    </div>

    <!-- Comments (admin view) -->
    <div class="card mt-1">
      <h2 class="card-section-title">💬 ความคิดเห็น (<?= count($comments) ?>)</h2>
      <div class="card-body">
      <?php foreach ($comments as $c): ?>
      <div class="comment-item <?= $c['is_pinned']?'pinned':'' ?>">
        <div class="comment-header">
          <strong><?= e($c['user_name']) ?></strong>
          <?php if ($c['is_pinned']): ?><span class="pin-chip">📌 ปักหมุด</span><?php endif; ?>
          <span class="comment-time"><?= e($c['created_at']) ?></span>
        </div>
        <p><?= nl2br(e($c['body'])) ?></p>
        <div class="comment-actions">
          <form method="POST" action="<?= url('admin/comment/'.$c['id'].'/pin') ?>" style="display:inline">
            <?= csrfField() ?>
            <button type="submit" class="btn-text"><?= $c['is_pinned']?'เลิกปักหมุด':'📌 ปักหมุด' ?></button>
          </form>
          <form method="POST" action="<?= url('comment/'.$c['id'].'/delete') ?>" style="display:inline"
                onsubmit="return confirm('ลบ?')">
            <?= csrfField() ?>
            <button type="submit" class="btn-text danger">🗑️ ลบ</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
