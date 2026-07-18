<?php
include("../config.php");
$page = "testimonials";
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <title>BSU License Store</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

  </head>

  <body>

    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>  
    <!-- ***** Preloader End ***** -->

    <!-- Header -->
    <?php include("../components/header.php"); ?>

    <!-- Page Content -->
    <div class="page-heading about-heading header-text" style="background-image: url(assets/images/heading-3-1920x500.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>รีวิวจากลูกค้า</h4>
              <h2 class="th">เสียงจากลูกค้า</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
        $reviews = [
          ['name' => 'ธนวัฒน์ ศรีสมบัติ',  'role' => 'พนักงานออฟฟิศ', 'stars' => 5, 'text' => 'สั่ง Windows 11 ได้คีย์ทันทีเลยครับ ใช้งานได้จริง ราคาถูกกว่าที่อื่นเยอะมาก ประทับใจสุด ๆ'],
          ['name' => 'ปาริชาติ ใจดี',      'role' => 'ฟรีแลนซ์',      'stars' => 5, 'text' => 'ซื้อ Office มาใช้ทำงาน ของแท้ 100% แอดมินตอบเร็ว ให้คำแนะนำดีมากค่ะ'],
          ['name' => 'อนุชา วงศ์เจริญ',    'role' => 'เจ้าของร้าน',    'stars' => 4, 'text' => 'บริการดี ส่งของไว มีปัญหาทักไปแก้ให้ทันที คุ้มค่ากับราคาครับ'],
          ['name' => 'ศิริพร แก้วมณี',      'role' => 'นักศึกษา',       'stars' => 5, 'text' => 'ราคานักศึกษาจับต้องได้ ลงโปรแกรมง่าย มีคู่มือให้ ขอบคุณมากค่ะ'],
          ['name' => 'กิตติพงษ์ รักษาดี',  'role' => 'โปรแกรมเมอร์',   'stars' => 5, 'text' => 'ซื้อหลายครั้งแล้ว ไว้ใจได้ทุกครั้ง คีย์ใช้งานได้ตลอด ไม่มีปัญหาเลย'],
          ['name' => 'มนัสนันท์ พูลสุข',   'role' => 'ลูกค้าประจำ',    'stars' => 5, 'text' => 'ประทับใจการดูแลหลังการขายมาก ตอบแชทตลอด แนะนำร้านนี้เลยครับ'],
        ];
      ?>
      <div class="services section-background testi-page">
        <div class="container">
          <div class="section-heading heading-center">
            <h2>ลูกค้าพูดถึงเราอย่างไร</h2>
          </div>
          <div class="row">
            <?php foreach ($reviews as $r): ?>
            <div class="col-md-4 col-sm-6">
              <div class="review-card">
                <span class="quote-mark"><i class="fa fa-quote-left"></i></span>
                <div class="stars">
                  <?php for ($s = 0; $s < 5; $s++) echo '<i class="fa fa-star' . ($s < $r['stars'] ? '' : '-o') . '"></i>'; ?>
                </div>
                <p class="review-text"><?= htmlspecialchars($r['text']) ?></p>
                <div class="review-person">
                  <span class="avatar"><i class="fa fa-user"></i></span>
                  <div class="rp-meta">
                    <h5><?= htmlspecialchars($r['name']) ?></h5>
                    <span class="role"><?= htmlspecialchars($r['role']) ?></span>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    
    <?php include("../components/footer.php"); ?>


    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
  </body>

</html>
