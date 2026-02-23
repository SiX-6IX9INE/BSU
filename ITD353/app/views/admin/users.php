<div class="container admin-layout">
  <aside class="admin-sidebar" aria-label="เมนู Admin">
    <nav>
      <a href="<?= url('admin') ?>"            class="admin-nav-link">📊 Dashboard</a>
      <a href="<?= url('admin/issues') ?>"     class="admin-nav-link">📋 จัดการปัญหา</a>
      <a href="<?= url('admin/categories') ?>" class="admin-nav-link">🏷️ หมวดหมู่</a>
      <a href="<?= url('admin/users') ?>"      class="admin-nav-link active">👥 ผู้ใช้</a>
      <hr><a href="<?= url() ?>" class="admin-nav-link">🏠 กลับหน้าหลัก</a>
    </nav>
  </aside>

  <div class="admin-main">
    <div class="admin-page-header"><h1>👥 จัดการผู้ใช้ (<?= $pag['total'] ?>)</h1></div>

    <form method="GET" action="<?= url('admin/users') ?>" class="admin-filter card">
      <div class="filter-row">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="ค้นหาชื่อ / อีเมล..." style="flex:1">
        <button type="submit" class="btn btn-primary btn-sm">🔍 ค้นหา</button>
        <?php if ($search): ?><a href="<?= url('admin/users') ?>" class="btn btn-outline btn-sm">ล้าง</a><?php endif; ?>
      </div>
    </form>

    <div class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>ID</th><th>ชื่อ</th><th>อีเมล</th><th>บทบาท</th><th>สถานะ</th><th>สมัครเมื่อ</th><th>จัดการ</th></tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u['id'] ?></td>
              <td><?= e($u['name']) ?></td>
              <td><?= e($u['email']) ?></td>
              <td>
                <span class="status-pill <?= $u['role']==='admin'?'badge-reviewing':'badge-new' ?>">
                  <?= $u['role'] === 'admin' ? '⚙️ Admin' : '👤 User' ?>
                </span>
              </td>
              <td>
                <span class="status-pill <?= $u['is_banned'] ? 'badge-rejected' : 'badge-resolved' ?>">
                  <?= $u['is_banned'] ? '🚫 แบน' : '✅ ปกติ' ?>
                </span>
              </td>
              <td><?= e(date('d/m/y', strtotime($u['created_at']))) ?></td>
              <td>
                <?php if ($u['id'] !== auth()['id']): // ห้ามแก้ตัวเอง ?>
                <div class="action-row">
                  <!-- Toggle role -->
                  <form method="POST" action="<?= url('admin/users/'.$u['id'].'/update') ?>" style="display:inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="role" value="<?= $u['role']==='admin'?'user':'admin' ?>">
                    <button type="submit" class="btn btn-outline btn-xs"
                            onclick="return confirm('เปลี่ยน role?')">
                      <?= $u['role']==='admin' ? '⬇ เปลี่ยนเป็น User' : '⬆ เปลี่ยนเป็น Admin' ?>
                    </button>
                  </form>
                  <!-- Toggle ban -->
                  <form method="POST" action="<?= url('admin/users/'.$u['id'].'/update') ?>" style="display:inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="ban" value="<?= $u['is_banned'] ? '0' : '1' ?>">
                    <button type="submit" class="btn btn-outline btn-xs <?= $u['is_banned']?'':'btn-danger' ?>"
                            onclick="return confirm('<?= $u['is_banned']?'ปลดแบน':'แบน' ?>ผู้ใช้นี้?')">
                      <?= $u['is_banned'] ? '✅ ปลดแบน' : '🚫 แบน' ?>
                    </button>
                  </form>
                </div>
                <?php else: ?>
                <small style="opacity:.5">บัญชีตัวเอง</small>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pag['total_pages'] > 1): ?>
      <div class="card-table-footer">
      <nav class="pagination">
        <?php for ($p=1; $p<=$pag['total_pages']; $p++): ?>
        <a href="<?= e(url('admin/users').'?q='.urlencode($search).'&page='.$p) ?>"
           class="page-btn <?= $p===$pag['current_page']?'active':'' ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
