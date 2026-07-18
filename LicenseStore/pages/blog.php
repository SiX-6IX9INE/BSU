<?php
include("../config.php");
$page = "blog";
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
    <div class="page-heading about-heading header-text" style="background-image: url(assets/images/heading-6-1920x500.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>Lorem ipsum dolor sit amet</h4>
              <h2>Blog</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
      $posts = [
        ['img' => 'blog-1-370x270.jpg', 'title' => 'อัปเดต Windows 11 มีอะไรใหม่ที่ควรรู้'],
        ['img' => 'blog-2-370x270.jpg', 'title' => 'วิธีเลือกซื้อ License แท้ให้คุ้มค่าที่สุด'],
        ['img' => 'blog-3-370x270.jpg', 'title' => 'ตั้งค่าความปลอดภัยเบื้องต้นหลังติดตั้งวินโดวส์'],
        ['img' => 'blog-4-370x270.jpg', 'title' => 'เปรียบเทียบ Windows 11 Home กับ Pro'],
        ['img' => 'blog-5-370x270.jpg', 'title' => 'รวมทริคใช้งาน Windows ให้ลื่นขึ้น'],
        ['img' => 'blog-6-370x270.jpg', 'title' => 'License หมดอายุทำอย่างไร แนวทางต่ออายุ'],
      ];
      $recent = [
        'อัปเดต Windows 11 มีอะไรใหม่ที่ควรรู้',
        'วิธีเลือกซื้อ License แท้ให้คุ้มค่าที่สุด',
        'License หมดอายุทำอย่างไร แนวทางต่ออายุ',
      ];
      $tags = ['Windows', 'Office', 'License', 'Tips', 'Security'];
    ?>
    <div class="products blog-page">
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <div class="row">
              <?php foreach ($posts as $post): ?>
              <div class="col-md-6">
                <article class="service-item blog-card">
                  <a href="<?= BASE ?>Blog" class="services-item-image">
                    <img src="<?= BASE ?>assets/images/<?= $post['img'] ?>" class="img-fluid" alt="<?= htmlspecialchars($post['title']) ?>">
                  </a>
                  <div class="down-content">
                    <h4><a href="<?= BASE ?>Blog"><?= htmlspecialchars($post['title']) ?></a></h4>
                    <ul class="blog-meta">
                      <li><i class="fa fa-user"></i> John Doe</li>
                      <li><i class="fa fa-calendar"></i> 12/06/2025</li>
                      <li><i class="fa fa-eye"></i> 114</li>
                    </ul>
                  </div>
                </article>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="col-md-4">
            <aside class="blog-sidebar">
              <div class="widget">
                <h5 class="widget-title">ค้นหาบทความ</h5>
                <form class="search-box" action="<?= BASE ?>Blog" method="get">
                  <input type="text" name="q" class="form-control" placeholder="พิมพ์คำค้นหา...">
                  <button class="filled-button" type="submit" aria-label="ค้นหา"><i class="fa fa-search"></i></button>
                </form>
              </div>

              <div class="widget">
                <h5 class="widget-title">บทความล่าสุด</h5>
                <ul class="recent-list">
                  <?php foreach ($recent as $r): ?>
                    <li><a href="<?= BASE ?>Blog"><i class="fa fa-angle-right"></i> <?= htmlspecialchars($r) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="widget">
                <h5 class="widget-title">หมวดหมู่</h5>
                <ul class="tag-list">
                  <?php foreach ($tags as $t): ?>
                    <li><a href="<?= BASE ?>Blog"><?= htmlspecialchars($t) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Book Now</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="contact-form">
              <form action="#" id="contact">
                  <div class="row">
                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Pick-up location" required="">
                          </fieldset>
                       </div>

                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Return location" required="">
                          </fieldset>
                       </div>
                  </div>

                  <div class="row">
                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Pick-up date/time" required="">
                          </fieldset>
                       </div>

                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Return date/time" required="">
                          </fieldset>
                       </div>
                  </div>
                  <input type="text" class="form-control" placeholder="Enter full name" required="">

                  <div class="row">
                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Enter email address" required="">
                          </fieldset>
                       </div>

                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Enter phone" required="">
                          </fieldset>
                       </div>
                  </div>
              </form>
           </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Book Now</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
  </body>

</html>
