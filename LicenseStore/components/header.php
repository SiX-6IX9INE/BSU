<?php
  $prefix = defined('BASE') ? BASE : '/BSU/LicenseStore/';
?>

<header class="">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="<?= $prefix ?>Home">
        <h2><em>BSU</em> License Store</h2>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
        aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item <?= $page === 'home' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= $prefix ?>Home">Home
              <?= $page === 'Home' ? '<span class="sr-only">(current)</span>' : '' ?>
            </a>
          </li>

          <li class="nav-item <?= in_array($page, ['products', 'product-details', 'checkout']) ? 'active' : '' ?>">
            <a class="nav-link" href="<?= $prefix ?>Products">Products</a>
          </li>

          <li class="nav-item dropdown <?= in_array($page, ['about-us', 'blog', 'testimonials', 'terms']) ? 'active' : '' ?>">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
              aria-expanded="false">More</a>

            <div class="dropdown-menu">
              <a class="dropdown-item <?= $page === 'testimonials' ? 'active' : '' ?>" href="<?= $prefix ?>Testimonials">Testimonials</a>
              <a class="dropdown-item <?= $page === 'about-us' ? 'active' : '' ?>" href="<?= $prefix ?>About-Us">About Us</a>
              <a class="dropdown-item <?= $page === 'blog' ? 'active' : '' ?>" href="<?= $prefix ?>Blog">Blog</a>
              <a class="dropdown-item <?= $page === 'terms' ? 'active' : '' ?>" href="<?= $prefix ?>Terms">Terms</a>
            </div>
          </li>

          <li class="nav-item <?= $page === 'contact' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= $prefix ?>Contact">Contact Us</a>
          </li>

          <li class="nav-item <?= $page === 'login' ? 'active' : '' ?>">
            <?php if (isset($_SESSION['user'])): ?>
              <a class="nav-link" href="<?= $prefix ?>Login">Logout</a>
            <?php else: ?>
              <a class="nav-link" href="<?= $prefix ?>Login">Login</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
