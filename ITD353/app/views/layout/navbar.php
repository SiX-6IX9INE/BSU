<nav class="navbar" role="navigation" aria-label="เมนูหลัก">
  <div class="container nav-inner">
    <!-- Logo -->
    <a href="<?= url() ?>" class="nav-logo" aria-label="<?= APP_NAME ?> – หน้าแรก">
      <span class="logo-icon">🏘️</span>
      <span class="logo-text"><?= APP_NAME ?></span>
    </a>

    <!-- Search (desktop) -->
    <?php if (empty($hideNavSearch)): ?>
    <form class="nav-search" action="<?= url() ?>" method="GET" role="search">
      <label for="nav-search-input" class="sr-only">ค้นหาปัญหา</label>
      <input id="nav-search-input" type="search" name="q"
             placeholder="ค้นหาปัญหา..."
             value="<?= e($_GET['q'] ?? '') ?>"
             autocomplete="off">
      <button type="submit" aria-label="ค้นหา">🔍</button>
    </form>
    <?php endif; ?>

    <!-- Right controls -->
    <div class="nav-actions">
      <!-- Theme toggle -->
      <button id="theme-toggle" class="btn-icon" aria-label="สลับธีม Dark/Light" title="Toggle theme">
        <span class="theme-icon-light">🌙</span>
        <span class="theme-icon-dark">☀️</span>
      </button>

      <?php if (isLoggedIn()): ?>
        <a href="<?= url('issue/new') ?>" class="btn btn-primary btn-sm" aria-label="แจ้งปัญหาใหม่">
          + แจ้งปัญหา
        </a>
        <div class="nav-user-menu" role="navigation" aria-label="เมนูผู้ใช้">
          <button class="btn-user" aria-haspopup="true" aria-expanded="false" id="user-menu-btn">
            <span class="user-avatar"><?= mb_substr(e(auth()['name'] ?? '?'), 0, 1) ?></span>
            <span class="user-name-short"><?= e(auth()['name'] ?? 'ผู้ใช้') ?></span>
            <span>▾</span>
          </button>
          <ul class="user-dropdown" aria-labelledby="user-menu-btn" role="menu">
            <li role="menuitem"><a href="<?= url('me') ?>">👤 โปรไฟล์</a></li>
            <?php if (isAdmin()): ?>
            <li role="menuitem"><a href="<?= url('admin') ?>">⚙️ แผง Admin</a></li>
            <?php endif; ?>
            <li class="divider" role="separator"></li>
            <li role="menuitem"><a href="<?= url('logout') ?>">🚪 ออกจากระบบ</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?= url('login') ?>"    class="btn btn-outline btn-sm">เข้าสู่ระบบ</a>
        <a href="<?= url('register') ?>" class="btn btn-primary btn-sm">สมัครสมาชิก</a>
      <?php endif; ?>

      <!-- Mobile hamburger -->
      <button class="hamburger" id="hamburger" aria-label="เปิดเมนู" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  <!-- Mobile nav -->
  <div class="mobile-nav" id="mobile-nav" hidden>
    <form action="<?= url() ?>" method="GET" class="mobile-search" role="search">
      <input type="search" name="q" placeholder="ค้นหาปัญหา..." value="<?= e($_GET['q'] ?? '') ?>">
      <button type="submit" aria-label="ค้นหา">🔍</button>
    </form>
    <nav aria-label="เมนูมือถือ">
      <a href="<?= url() ?>">🏠 หน้าแรก</a>
      <?php if (isLoggedIn()): ?>
        <a href="<?= url('issue/new') ?>">➕ แจ้งปัญหา</a>
        <a href="<?= url('me') ?>">👤 โปรไฟล์</a>
        <?php if (isAdmin()): ?>
          <a href="<?= url('admin') ?>">⚙️ Admin</a>
        <?php endif; ?>
        <a href="<?= url('logout') ?>">🚪 ออกจากระบบ</a>
      <?php else: ?>
        <a href="<?= url('login') ?>">🔑 เข้าสู่ระบบ</a>
        <a href="<?= url('register') ?>">📝 สมัครสมาชิก</a>
      <?php endif; ?>
      <a href="<?= url('about') ?>">ℹ️ เกี่ยวกับ</a>
    </nav>
  </div>
</nav>
