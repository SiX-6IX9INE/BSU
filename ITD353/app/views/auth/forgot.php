<section class="auth-wrap container">
  <div class="auth-card card">
    <div class="auth-header">
      <h1>ลืมรหัสผ่าน</h1>
      <p>ระบุอีเมลที่ลงทะเบียนไว้ เราจะส่งลิงก์รีเซ็ตให้</p>
    </div>
    <form method="POST" action="<?= url('forgot-password') ?>" class="auth-form">
      <?= csrfField() ?>
      <div class="form-group">
        <label for="email">อีเมล</label>
        <input type="email" id="email" name="email" required placeholder="your@email.com">
      </div>
      <button type="submit" class="btn btn-primary btn-block">ส่งลิงก์รีเซ็ต</button>
    </form>
    <p class="auth-switch"><a href="<?= url('login') ?>">← กลับหน้าเข้าสู่ระบบ</a></p>
  </div>
</section>
