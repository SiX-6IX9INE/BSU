</main><!-- /.main-content -->

<footer class="site-footer" role="contentinfo">
  <div class="container footer-inner">
    <div class="footer-left">
      <span class="logo-icon">🏘️</span>
      <span><?= APP_NAME ?> v<?= APP_VERSION ?></span>
    </div>
    <nav class="footer-links" aria-label="ลิงก์ท้ายหน้า">
      <a href="<?= url() ?>">หน้าแรก</a>
      <a href="<?= url('about') ?>">เกี่ยวกับ</a>
      <a href="<?= url('issue/new') ?>">แจ้งปัญหา</a>
    </nav>
    <div class="footer-right">
      <small>© <?= date('Y') ?> Community Issue Reporter</small>
      <small>By Jirawat Thongjankaew 67020180032</small>
    </div>
  </div>
</footer>

<!-- Toast container -->
<div id="toast-container" aria-live="polite" aria-atomic="false"></div>

<!-- App JS -->
<script src="<?= asset('js/app.js') ?>"></script>

<?php if (!empty($includeLeaflet)): ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLs=" crossorigin=""></script>
<?php endif; ?>

</body>
</html>
