<section class="auth-wrap container">
  <div class="auth-card card">
    <div class="auth-header">
      <h1>ตั้งรหัสผ่านใหม่</h1>
    </div>
    <form method="POST" action="<?= url('reset-password') ?>" class="auth-form">
      <?= csrfField() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="form-group">
        <label for="password">รหัสผ่านใหม่</label>
        <div class="input-password">
          <input type="password" id="password" name="password" required minlength="8" placeholder="อย่างน้อย 8 ตัวอักษร">
          <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อน">👁️</button>
        </div>
      </div>
      <div class="form-group">
        <label for="confirm">ยืนยันรหัสผ่านใหม่</label>
        <div class="input-password">
          <input type="password" id="confirm" name="confirm" required placeholder="พิมพ์อีกครั้ง">
          <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อน">👁️</button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">บันทึกรหัสผ่านใหม่</button>
    </form>
  </div>
</section>
