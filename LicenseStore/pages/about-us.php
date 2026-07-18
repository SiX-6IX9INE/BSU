<?php
include("../config.php");
$page = "about-us";
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
    <div class="page-heading about-heading header-text" style="background-image: url(assets/images/heading-1-1920x500.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>เกี่ยวกับเรา</h4>
              <h2 class="th">BSU License Store</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="best-features about-features">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="right-image about-img">
              <img src="assets/images/about-1-570x350.jpg" alt="เกี่ยวกับ BSU License Store">
            </div>
          </div>
          <div class="col-md-6">
            <div class="left-content about-copy">
              <span class="eyebrow">รู้จักเรา</span>
              <h2>ร้านจำหน่าย License แท้ ราคาเป็นมิตร</h2>
              <p>BSU License Store คือร้านจำหน่ายลิขสิทธิ์ซอฟต์แวร์แท้ ทั้ง Windows, Office และโปรแกรมลิขสิทธิ์อื่น ๆ เรามุ่งมั่นให้ลูกค้าได้รับสินค้าของแท้ 100% ในราคาที่จับต้องได้ พร้อมส่งคีย์ผลิตภัณฑ์ทันทีหลังชำระเงินสำเร็จ</p>
              <p>ทีมงานของเราพร้อมดูแลและให้คำปรึกษาตลอดการใช้งาน เพื่อให้คุณมั่นใจได้ว่าจะได้รับประสบการณ์การซื้อที่ดีที่สุด และใช้งานซอฟต์แวร์ลิขสิทธิ์ได้อย่างสบายใจ</p>
              <ul class="social-icons">
                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-youtube"></i></a></li>
                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="why-us">
      <div class="container">
        <div class="section-heading heading-center">
          <h2>ทำไมต้องเลือกเรา</h2>
        </div>
        <div class="row">
          <div class="col-md-3 col-sm-6">
            <div class="feature-box">
              <span class="fx-icon"><i class="fa fa-certificate"></i></span>
              <h4>ลิขสิทธิ์แท้ 100%</h4>
              <p>สินค้าทุกชิ้นเป็นของแท้ ตรวจสอบได้ ใช้งานได้อย่างมั่นใจตลอดชีพ</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="feature-box">
              <span class="fx-icon"><i class="fa fa-bolt"></i></span>
              <h4>ส่งคีย์ทันที</h4>
              <p>รับคีย์ผลิตภัณฑ์อัตโนมัติทันทีหลังชำระเงินสำเร็จ ไม่ต้องรอนาน</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="feature-box">
              <span class="fx-icon"><i class="fa fa-tags"></i></span>
              <h4>ราคาเป็นมิตร</h4>
              <p>คัดสรรราคาที่ดีที่สุดมาให้ลูกค้า คุ้มค่าในทุกการสั่งซื้อ</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="feature-box">
              <span class="fx-icon"><i class="fa fa-headphones"></i></span>
              <h4>ซัพพอร์ตจริง</h4>
              <p>ทีมงานพร้อมช่วยเหลือและให้คำปรึกษาหลังการขายอย่างเต็มที่</p>
            </div>
          </div>
        </div>

        <div class="stats-band">
          <div class="stat"><span class="num">1,200+</span><span class="lbl">ลูกค้าที่ไว้วางใจ</span></div>
          <div class="stat"><span class="num">100%</span><span class="lbl">ลิขสิทธิ์แท้</span></div>
          <div class="stat"><span class="num">24/7</span><span class="lbl">พร้อมให้บริการ</span></div>
          <div class="stat"><span class="num">4.9/5</span><span class="lbl">คะแนนความพึงพอใจ</span></div>
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
