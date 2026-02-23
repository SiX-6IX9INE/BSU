<div class="container admin-layout">
  <aside class="admin-sidebar" aria-label="เมนู Admin">
    <nav>
      <a href="<?= url('admin') ?>"            class="admin-nav-link">📊 Dashboard</a>
      <a href="<?= url('admin/issues') ?>"     class="admin-nav-link">📋 จัดการปัญหา</a>
      <a href="<?= url('admin/categories') ?>" class="admin-nav-link active">🏷️ หมวดหมู่</a>
      <a href="<?= url('admin/users') ?>"      class="admin-nav-link">👥 ผู้ใช้</a>
      <hr><a href="<?= url() ?>" class="admin-nav-link">🏠 กลับหน้าหลัก</a>
    </nav>
  </aside>

  <div class="admin-main">
    <div class="admin-page-header"><h1>🏷️ จัดการหมวดหมู่</h1></div>

    <!-- Add form -->
    <div class="card" style="margin-bottom:1rem">
      <h2 class="card-section-title">➕ เพิ่มหมวดหมู่ใหม่</h2>
      <form method="POST" action="<?= url('admin/categories/create') ?>" class="form-inline">
        <?= csrfField() ?>
        <div class="form-row" style="flex-wrap:wrap;gap:.5rem;align-items:flex-end">
          <div class="form-group" style="flex:2;min-width:140px">
            <label for="new-name">ชื่อ <span class="required">*</span></label>
            <input type="text" id="new-name" name="name" required placeholder="ชื่อหมวดหมู่">
          </div>
          <div class="form-group" style="flex:1;min-width:120px">
            <label for="new-slug">Slug <span class="required">*</span></label>
            <input type="text" id="new-slug" name="slug" required placeholder="road">
          </div>
          <div class="form-group" style="width:80px">
            <label for="new-icon">ไอคอน</label>
            <input type="text" id="new-icon" name="icon" value="📌" style="text-align:center">
          </div>
          <div class="form-group" style="width:90px">
            <label for="new-color">สี</label>
            <input type="color" id="new-color" name="color" value="#3b82f6" style="height:38px;padding:2px">
          </div>
          <div class="form-group" style="width:80px">
            <label for="new-order">ลำดับ</label>
            <input type="number" id="new-order" name="sort_order" value="0" min="0">
          </div>
          <button type="submit" class="btn btn-primary">เพิ่ม</button>
        </div>
      </form>
    </div>

    <!-- List -->
    <div class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>ไอคอน</th><th>ชื่อ</th><th>Slug</th><th>สี</th><th>ลำดับ</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
          <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
              <td style="font-size:1.4rem;text-align:center"><?= e($cat['icon']) ?></td>
              <td><?= e($cat['name']) ?></td>
              <td><code><?= e($cat['slug']) ?></code></td>
              <td>
                <div style="display:inline-block;width:24px;height:24px;border-radius:50%;
                            background:<?= e($cat['color']) ?>;vertical-align:middle"></div>
                <small><?= e($cat['color']) ?></small>
              </td>
              <td><?= $cat['sort_order'] ?></td>
              <td>
                <span class="status-pill <?= $cat['is_active'] ? 'badge-resolved' : 'badge-rejected' ?>">
                  <?= $cat['is_active'] ? 'ใช้งาน' : 'ปิด' ?>
                </span>
              </td>
              <td>
                <!-- Inline edit modal trigger -->
                <button class="btn btn-outline btn-xs"
                        onclick="openEditCat(<?= e(json_encode($cat)) ?>)">✏️ แก้ไข</button>
                <form method="POST" action="<?= url('admin/categories/'.$cat['id'].'/delete') ?>"
                      style="display:inline"
                      onsubmit="return confirm('ลบหมวดหมู่นี้?')">
                  <?= csrfField() ?>
                  <button type="submit" class="btn btn-outline btn-xs btn-danger">🗑️</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Edit modal -->
<div id="edit-cat-modal" class="modal-overlay" hidden>
  <div class="modal-box card">
    <h2>✏️ แก้ไขหมวดหมู่</h2>
    <form method="POST" id="edit-cat-form">
      <?= csrfField() ?>
      <div class="form-group"><label>ชื่อ</label><input type="text" name="name" id="ec-name" required></div>
      <div class="form-group"><label>Slug</label><input type="text" name="slug" id="ec-slug" required></div>
      <div class="form-row" style="gap:.5rem">
        <div class="form-group" style="flex:1"><label>ไอคอน</label><input type="text" name="icon" id="ec-icon"></div>
        <div class="form-group" style="width:90px"><label>สี</label><input type="color" name="color" id="ec-color" style="height:38px"></div>
        <div class="form-group" style="width:80px"><label>ลำดับ</label><input type="number" name="sort_order" id="ec-order" min="0"></div>
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="is_active" value="1" id="ec-active"> เปิดใช้งาน</label>
      </div>
      <div class="form-row" style="gap:.5rem">
        <button type="submit" class="btn btn-primary">บันทึก</button>
        <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-cat-modal').hidden=true">ยกเลิก</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditCat(cat) {
  document.getElementById('edit-cat-form').action = '<?= url("admin/categories/") ?>' + cat.id + '/update';
  document.getElementById('ec-name').value   = cat.name;
  document.getElementById('ec-slug').value   = cat.slug;
  document.getElementById('ec-icon').value   = cat.icon;
  document.getElementById('ec-color').value  = cat.color;
  document.getElementById('ec-order').value  = cat.sort_order;
  document.getElementById('ec-active').checked = !!parseInt(cat.is_active);
  document.getElementById('edit-cat-modal').hidden = false;
}
</script>
