<section class="auth-wrap container">
  <div class="auth-card card">
    <div class="auth-header">
      <h1>สมัครสมาชิก</h1>
      <p>เข้าร่วมชุมชนและช่วยกันรายงานปัญหา 🏘️</p>
    </div>

    <form method="POST" action="<?= url('register') ?>" class="auth-form" novalidate>
      <?= csrfField() ?>

      <div class="form-group">
        <label for="name">ชื่อ-นามสกุล</label>
        <input type="text" id="name" name="name"
               value="<?= e($_POST['name'] ?? '') ?>"
               placeholder="ชื่อของคุณ"
               required minlength="2" autocomplete="name"
               aria-required="true">
      </div>

      <div class="form-group">
        <label for="email">อีเมล</label>
        <input type="email" id="email" name="email"
               value="<?= e($_POST['email'] ?? '') ?>"
               placeholder="your@email.com"
               required autocomplete="email"
               aria-required="true">
      </div>

      <div class="form-group">
        <label for="password">รหัสผ่าน</label>
        <div class="input-password">
          <input type="password" id="password" name="password"
                 placeholder="อย่างน้อย 8 ตัวอักษร"
                 required minlength="8" autocomplete="new-password"
                 aria-required="true">
          <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อนรหัสผ่าน">👁️</button>
        </div>
      </div>

      <div class="form-group">
        <label for="confirm">ยืนยันรหัสผ่าน</label>
        <div class="input-password">
          <input type="password" id="confirm" name="confirm"
                 placeholder="พิมพ์รหัสผ่านอีกครั้ง"
                 required autocomplete="new-password"
                 aria-required="true">
          <button type="button" class="toggle-pw" aria-label="แสดง/ซ่อนรหัสผ่าน">👁️</button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block">สมัครสมาชิก</button>
    </form>

    <p class="auth-switch">
      มีบัญชีแล้ว? <a href="<?= url('login') ?>">เข้าสู่ระบบ</a>
    </p>
  </div>
</section>
