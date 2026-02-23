<!DOCTYPE html>
<html lang="th" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? APP_NAME) ?></title>
  <meta name="description" content="ระบบรับแจ้งปัญหาในชุมชน – รายงาน ติดตาม และแก้ไขปัญหาร่วมกัน">

  <!-- Google Font: Kanit (Thai + Latin) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- App CSS -->
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

  <?php if (!empty($includeLeaflet)): ?>
  <!-- Leaflet CSS (map pages only) -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
  <?php endif; ?>
</head>
<body>
<?php
// Include navbar
require APP_PATH . '/views/layout/navbar.php';

// Flash messages (rendered by JS toast after DOM load)
$flashes = getFlash();
if ($flashes):
?>
<script>
window.__flashes = <?= json_encode($flashes, JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php endif; ?>
<main class="main-content" id="main-content">
