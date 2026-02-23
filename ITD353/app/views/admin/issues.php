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
      <h1>📋 จัดการปัญหา (<?= $pag['total'] ?>)</h1>
    </div>

    <!-- Filters -->
    <form method="GET" action="<?= url('admin/issues') ?>" class="filters-form card" style="padding:1rem">
      <div class="filter-row" style="flex-wrap:wrap;gap:.5rem">
        <input type="text" name="q" value="<?= e($filters['search']) ?>" placeholder="ค้นหา..." style="flex:1;min-width:160px">
        <select name="status">
          <option value="">สถานะทั้งหมด</option>
          <?php foreach (['new'=>'ใหม่','reviewing'=>'กำลังตรวจสอบ','in_progress'=>'กำลังดำเนินการ','resolved'=>'แก้ไขแล้ว','rejected'=>'ปฏิเสธ'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= $filters['status']===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
        <select name="urgency">
          <option value="">ทุกระดับ</option>
          <option value="high"   <?= $filters['urgency']==='high'  ?'selected':'' ?>>🔴 เร่งด่วน</option>
          <option value="medium" <?= $filters['urgency']==='medium'?'selected':'' ?>>🟡 ปานกลาง</option>
          <option value="low"    <?= $filters['urgency']==='low'   ?'selected':'' ?>>🟢 ต่ำ</option>
        </select>
        <select name="cat">
          <option value="">หมวดหมู่ทั้งหมด</option>
          <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= (int)$filters['category_id']===$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">🔍 กรอง</button>
        <a href="<?= url('admin/issues') ?>" class="btn btn-outline btn-sm">ล้าง</a>
      </div>
    </form>

    <!-- Table -->
    <div class="card" style="margin-top:1rem">
      <?php if (empty($issues)): ?>
      <div class="empty-state-sm"><span>📭</span><p>ไม่พบรายการ</p></div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Ticket</th><th>หัวข้อ</th><th>หมวด</th>
              <th>สถานะ</th><th>ความเร่งด่วน</th><th>โหวต</th><th>วันที่</th><th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($issues as $i): ?>
            <tr>
              <td><code><?= e($i['ticket_id']) ?></code></td>
              <td>
                <?php if ($i['is_pinned']): ?><span title="ปักหมุด">📌</span><?php endif; ?>
                <?= e(mb_substr($i['title'],0,40)) ?>
              </td>
              <td>
                <span class="cat-badge" style="background:<?= e($i['category_color']) ?>20;color:<?= e($i['category_color']) ?>;border-color:<?= e($i['category_color']) ?>">
                  <?= e($i['category_icon']) ?> <?= e($i['category_name']) ?>
                </span>
              </td>
              <td><span class="status-pill <?= statusClass($i['status']) ?>"><?= statusLabel($i['status']) ?></span></td>
              <td><span class="urgency-tag <?= urgencyClass($i['urgency']) ?>"><?= urgencyLabel($i['urgency']) ?></span></td>
              <td><?= $i['vote_count'] ?></td>
              <td><?= date('d/m/y', strtotime($i['created_at'])) ?></td>
              <td>
                <a href="<?= url('admin/issues/'.$i['id']) ?>" class="btn btn-outline btn-xs">✏️ แก้ไข</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pag['total_pages'] > 1): ?>
      <nav class="pagination" style="margin-top:1rem">
        <?php for ($p=1; $p<=$pag['total_pages']; $p++): ?>
        <a href="<?= e($pag['base_url'].$p) ?>" class="page-btn <?= $p===$pag['current_page']?'active':'' ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
