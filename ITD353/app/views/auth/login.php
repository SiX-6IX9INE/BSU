<section class="auth-wrap container">
  <div class="auth-card card">
    <div class="auth-header">
      <h1>เข้าสู่ระบบ</h1>
      <p>ยินดีต้อนรับกลับ 👋</p>
      <h1>(Email: test) <br>(Password: 123)</h1>
    </div>

    <form method="POST" action="<?= url('login') ?>" class="auth-form" novalidate>
      <?= csrfField() ?>

      <div class="form-group">
        <label for="email">อีเมล</label>
        <input type="email" id="email" name="email"
               value="<?= e($_POST['email'] ?? '') ?>"
               placeholder="your@email.com"
               required autocomplete="email"
               aria-required="true">
      </div>

      <div class="form-group">
        <label for="password">
          รหัสผ่าน
          <a href="<?= url('forgot-password') ?>" class="form-label-link">ลืมรหัสผ่าน?</a>
        </label>
        <div class="input-password">
          <input type="password" id="password" name="password"
                 placeholder="••••••••"
                 required autocomplete="current-password"
                 aria-required="true">
          <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อนรหัสผ่าน">👁️</button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
    </form>

    <p class="auth-switch">
      ยังไม่มีบัญชี? <a href="<?= url('register') ?>">สมัครสมาชิก</a>
    </p>
  </div>
</section>
