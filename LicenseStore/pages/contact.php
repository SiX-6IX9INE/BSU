<?php
include("../config.php");
$page = "contact";
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
    <div class="page-heading contact-heading header-text" style="background-image: url(assets/images/heading-4-1920x500.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>ติดต่อเรา</h4>
              <h2 class="th">ติดต่อเรา</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="contact-page">
      <div class="container">
        <div class="row contact-cards">
          <div class="col-md-3 col-sm-6">
            <div class="info-box">
              <span class="ib-icon"><i class="fa fa-map-marker"></i></span>
              <h5>ที่อยู่</h5>
              <p>มหาวิทยาลัยกรุงเทพธนบุรี<br>ทวีวัฒนา กรุงเทพฯ</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="info-box">
              <span class="ib-icon"><i class="fa fa-phone"></i></span>
              <h5>โทรศัพท์</h5>
              <p>02-123-4567<br>08-1234-5678</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="info-box">
              <span class="ib-icon"><i class="fa fa-envelope"></i></span>
              <h5>อีเมล</h5>
              <p>support@bsustore.com<br>sale@bsustore.com</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="info-box">
              <span class="ib-icon"><i class="fa fa-clock-o"></i></span>
              <h5>เวลาทำการ</h5>
              <p>ทุกวัน 09:00 - 20:00 น.<br>ตอบแชทตลอด 24 ชม.</p>
            </div>
          </div>
        </div>

        <div class="row contact-main">
          <div class="col-md-7">
            <div class="contact-panel">
              <h4 class="panel-title">ส่งข้อความถึงเรา</h4>
              <p class="panel-sub">มีคำถามหรือต้องการสอบถามสินค้า กรอกแบบฟอร์มด้านล่าง เราจะติดต่อกลับโดยเร็วที่สุด</p>
              <form id="contact" action="" method="post">
                <div class="row">
                  <div class="col-md-6"><fieldset>
                    <input name="name" type="text" class="form-control" placeholder="ชื่อ - นามสกุล" required>
                  </fieldset></div>
                  <div class="col-md-6"><fieldset>
                    <input name="email" type="email" class="form-control" placeholder="อีเมล" required>
                  </fieldset></div>
                  <div class="col-md-12"><fieldset>
                    <input name="subject" type="text" class="form-control" placeholder="หัวข้อ" required>
                  </fieldset></div>
                  <div class="col-md-12"><fieldset>
                    <textarea name="message" rows="6" class="form-control" placeholder="ข้อความของคุณ" required></textarea>
                  </fieldset></div>
                  <div class="col-md-12"><fieldset>
                    <button type="submit" class="filled-button"><i class="fa fa-paper-plane"></i> &nbsp;ส่งข้อความ</button>
                  </fieldset></div>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-5">
            <div class="contact-map">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d248057.20375452517!2d100.4683008844426!3d13.724878467311497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d6032280d61f3%3A0x10100b25de24820!2z4LiB4Lij4Li44LiH4LmA4LiX4Lie4Lih4Lir4Liy4LiZ4LiE4Lij!5e0!3m2!1sth!2sth!4v1784340485956!5m2!1sth!2sth" width="100%" height="100%" style="border:0;min-height:420px" allowfullscreen loading="lazy"></iframe>
            </div>
          </div>
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
