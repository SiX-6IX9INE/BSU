<div class="container" style="max-width:760px">
  <div class="page-header">
    <h1>👤 โปรไฟล์ของฉัน</h1>
  </div>

  <div class="profile-grid">
    <!-- Profile info card -->
    <div class="card">
      <h2 class="card-section-title">ข้อมูลส่วนตัว</h2>
      <div class="card-body">
      <form method="POST" action="<?= url('me/update') ?>">
        <?= csrfField() ?>
        <div class="form-group">
          <label for="name">ชื่อ-นามสกุล</label>
          <input type="text" id="name" name="name"
                 value="<?= e($user['name']) ?>" required minlength="2">
        </div>
        <div class="form-group">
          <label for="email_disp">อีเมล</label>
          <input type="email" id="email_disp" value="<?= e($user['email']) ?>" disabled
                 style="opacity:.6">
          <small>ไม่สามารถเปลี่ยนอีเมลได้</small>
        </div>
        <div class="form-group">
          <label for="phone">เบอร์โทรศัพท์</label>
          <input type="tel" id="phone" name="phone"
                 value="<?= e($user['phone'] ?? '') ?>" placeholder="0xx-xxx-xxxx">
        </div>
        <div class="form-group">
          <label>บทบาท</label>
          <input type="text" value="<?= $user['role'] === 'admin' ? '⚙️ Admin' : '👤 User' ?>" disabled style="opacity:.6">
        </div>
        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
      </form>
      </div>
    </div>

    <!-- Change password card -->
    <div class="card">
      <h2 class="card-section-title">เปลี่ยนรหัสผ่าน</h2>
      <div class="card-body">
      <form method="POST" action="<?= url('me/password') ?>">
        <?= csrfField() ?>
        <div class="form-group">
          <label for="current_password">รหัสผ่านปัจจุบัน</label>
          <div class="input-password">
            <input type="password" id="current_password" name="current_password" required>
            <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อน">👁️</button>
          </div>
        </div>
        <div class="form-group">
          <label for="new_password">รหัสผ่านใหม่</label>
          <div class="input-password">
            <input type="password" id="new_password" name="new_password" required minlength="8" placeholder="อย่างน้อย 8 ตัวอักษร">
            <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อน">👁️</button>
          </div>
        </div>
        <div class="form-group">
          <label for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-outline">เปลี่ยนรหัสผ่าน</button>
      </form>
      </div>
    </div>
  </div>

  <!-- My issues -->
  <div class="card mt-2">
    <h2 class="card-section-title">ปัญหาที่ฉันรายงาน (<?= count($myIssues) ?>)</h2>
    <?php if (empty($myIssues)): ?>
    <div class="card-body">
    <div class="empty-state-sm">
      <span>💭</span>
      <p>คุณยังไม่ได้รายงานปัญหาใด <a href="<?= url('issue/new') ?>">แจ้งปัญหาแรก</a></p>
    </div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Ticket</th><th>หัวข้อ</th><th>สถานะ</th><th>โหวต</th><th>วันที่</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($myIssues as $i): ?>
          <tr>
            <td><a href="<?= url('issue/' . $i['id']) ?>"><?= e($i['ticket_id']) ?></a></td>
            <td><?= e(mb_substr($i['title'], 0, 40)) ?></td>
            <td><span class="status-pill <?= statusClass($i['status']) ?>"><?= statusLabel($i['status']) ?></span></td>
            <td><?= e($i['vote_count']) ?></td>
            <td><?= e(date('d/m/y', strtotime($i['created_at']))) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
