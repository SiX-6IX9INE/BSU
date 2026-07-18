<?php
include("../config.php");
$page = "terms";
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
    <div class="page-heading about-heading header-text" style="background-image: url(assets/images/heading-5-1920x500.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>ข้อกำหนดการใช้บริการ</h4>
              <h2 class="th">ข้อกำหนดและเงื่อนไข</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="terms-page">
      <div class="container">
        <?php
          $terms = [
            ['การสั่งซื้อและการชำระเงิน', 'ลูกค้าสามารถสั่งซื้อสินค้าผ่านหน้าเว็บไซต์ได้ตลอด 24 ชั่วโมง โดยต้องเข้าสู่ระบบก่อนทำการสั่งซื้อ ราคาสินค้าทั้งหมดเป็นสกุลเงินบาท (฿) และถือเป็นราคาสุทธิ เมื่อชำระเงินสำเร็จ ระบบจะจัดส่งคีย์ผลิตภัณฑ์ให้โดยอัตโนมัติ'],
            ['สินค้าลิขสิทธิ์แท้', 'สินค้าทุกชิ้นที่จำหน่ายเป็นลิขสิทธิ์แท้ 100% สามารถเปิดใช้งาน (Activate) ได้อย่างถูกต้องตามกฎหมาย ทางร้านไม่จำหน่ายสินค้าละเมิดลิขสิทธิ์หรือคีย์ที่ผิดกฎหมายทุกกรณี'],
            ['การจัดส่งคีย์ผลิตภัณฑ์', 'คีย์ผลิตภัณฑ์จะถูกจัดส่งผ่านระบบทันทีหลังการชำระเงินได้รับการยืนยัน กรุณาตรวจสอบข้อมูลในบัญชีของท่าน หากไม่ได้รับคีย์ภายใน 24 ชั่วโมง สามารถติดต่อทีมงานได้ตลอดเวลา'],
            ['นโยบายการคืนเงิน', 'เนื่องจากเป็นสินค้าดิจิทัล ทางร้านขอสงวนสิทธิ์ในการคืนเงินหลังจากที่คีย์ถูกเปิดใช้งานแล้ว ยกเว้นกรณีคีย์ไม่สามารถใช้งานได้ ซึ่งทางร้านจะเปลี่ยนคีย์ใหม่หรือคืนเงินให้เต็มจำนวน'],
            ['ความรับผิดชอบและการรับประกัน', 'ทางร้านรับประกันการใช้งานของคีย์ผลิตภัณฑ์ หากพบปัญหาการเปิดใช้งานที่เกิดจากตัวคีย์ ทีมงานยินดีดูแลและแก้ไขให้จนกว่าจะใช้งานได้ตามปกติ'],
            ['ความเป็นส่วนตัวของข้อมูล', 'ข้อมูลส่วนบุคคลของลูกค้าจะถูกเก็บรักษาเป็นความลับ และใช้เพื่อการให้บริการเท่านั้น เราจะไม่เปิดเผยข้อมูลของท่านให้แก่บุคคลที่สามโดยไม่ได้รับความยินยอม'],
          ];
        ?>
        <div class="terms-card">
          <?php foreach ($terms as $i => $t): ?>
            <div class="terms-block">
              <h3><span class="tnum"><?= $i + 1 ?></span> <?= htmlspecialchars($t[0]) ?></h3>
              <p><?= htmlspecialchars($t[1]) ?></p>
            </div>
          <?php endforeach; ?>
          <p class="terms-note"><i class="fa fa-info-circle"></i> การสั่งซื้อและใช้บริการถือว่าท่านยอมรับข้อกำหนดและเงื่อนไขข้างต้นทั้งหมด ทางร้านขอสงวนสิทธิ์ในการเปลี่ยนแปลงเงื่อนไขโดยไม่ต้องแจ้งให้ทราบล่วงหน้า</p>
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
