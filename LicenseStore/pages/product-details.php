<?php
include("../config.php");
$page = "product-details";

$slug = $_GET['name'] ?? '';
$decodedName = str_replace('-', ' ', $slug);

$conn = connDB();
$stmt = $conn->prepare("SELECT * FROM Products WHERE LOWER(name) = ?");
$decodedName = strtolower($decodedName);
$stmt->bind_param("s", $decodedName);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: " . BASE . "Home");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../assets/images/favicon.ico">

  <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">

  <title>BSU License Store</title>

  <!-- Bootstrap core CSS -->
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="../assets/css/fontawesome.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/owl.css">

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
  <div class="page-heading about-heading header-text" style="background-image: url(../assets/images/background_window11_1.jpg);">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="text-content">
            <h4>Product</h4>
            <h2><?= htmlspecialchars($product['name'] ?? 'Product Details') ?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
    $imgSlug = generateSlug($product['name']);
    $images  = [];
    for ($i = 1; $i <= 6; $i++) {
        $f = "assets/images/{$imgSlug}-{$i}.png";
        if (file_exists(dirname(__DIR__) . "/" . $f)) {
            $images[] = BASE . $f;
        }
    }
    if (empty($images)) {
        $images[] = BASE . "assets/images/{$imgSlug}-1.png";
    }
    $old     = $product['price'] * 1.2;
    $now     = $product['price'];
    $savePct = $old > 0 ? (int) round((($old - $now) / $old) * 100) : 0;
    $pName   = htmlspecialchars($product['name']);
  ?>
  <div class="products product-detail">
    <div class="container">
      <div class="detail-panel">
      <div class="row">
        <div class="col-md-5 col-xs-12">
          <div class="detail-media">
            <img id="mainImage" src="<?= $images[0] ?>" alt="<?= $pName ?>" class="img-fluid">
          </div>
          <?php if (count($images) > 1): ?>
          <div class="detail-thumbs">
            <?php foreach ($images as $idx => $img): ?>
              <button type="button" class="detail-thumb <?= $idx === 0 ? 'active' : '' ?>" data-img="<?= $img ?>" aria-label="รูปที่ <?= $idx + 1 ?>">
                <img src="<?= $img ?>" alt="<?= $pName ?> - <?= $idx + 1 ?>">
              </button>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

        <div class="col-md-7 col-xs-12">
          <div class="detail-info">
            <span class="detail-badge"><i class="fa fa-certificate"></i> Genuine License</span>
            <h2><?= $pName ?></h2>

            <div class="price-block">
              <span class="price-now"><?= number_format($now) ?>.00฿</span>
              <?php if ($savePct > 0): ?>
                <span class="price-old"><del><?= number_format($old) ?>.00฿</del></span>
                <span class="price-save">-<?= $savePct ?>%</span>
              <?php endif; ?>
            </div>

            <p class="detail-desc font-kanit"><?= nl2br($product['description']) ?></p>

            <div class="detail-actions">
              <?php if (isset($_SESSION['user'])): ?>
                <a href="<?= BASE ?>Checkout/<?= urlencode($slug); ?>" class="btn btn-primary btn-block"><i class="fa fa-shopping-cart"></i> &nbsp;สั่งซื้อเลย</a>
              <?php else: ?>
                <a href="<?= BASE ?>Login" class="btn btn-primary btn-block"><i class="fa fa-sign-in"></i> &nbsp;เข้าสู่ระบบเพื่อสั่งซื้อ</a>
              <?php endif; ?>
            </div>

            <ul class="detail-perks">
              <li><i class="fa fa-bolt"></i> ส่งคีย์ทันทีหลังชำระเงิน</li>
              <li><i class="fa fa-shield"></i> ลิขสิทธิ์แท้ 100% ใช้งานได้ตลอดชีพ</li>
              <li><i class="fa fa-headphones"></i> ทีมงานซัพพอร์ตหลังการขาย</li>
            </ul>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var main = document.getElementById('mainImage');
      var thumbs = document.querySelectorAll('.detail-thumb');
      if (!main || !thumbs.length) return;
      thumbs.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var src = btn.getAttribute('data-img');
          if (!src) return;
          main.style.opacity = '0';
          setTimeout(function () { main.src = src; main.style.opacity = '1'; }, 150);
          thumbs.forEach(function (b) { b.classList.remove('active'); });
          btn.classList.add('active');
        });
      });
    })();
  </script>

  <!-- <div class="latest-products">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-heading">
            <h2>Similar Products</h2>
            <a href="/Products">view more <i class="fa fa-angle-right"></i></a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="product-item">
            <a href="/Product-Details"><img src="/assets/images/product-1-370x270.jpg" alt=""></a>
            <div class="down-content">
              <a href="/Product-Details"><h4>Omega bicycle</h4></a>
              <h6><small><del>$999.00 </del></small> $779.00</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="product-item">
            <a href="/Product-Details"><img src="/assets/images/product-2-370x270.jpg" alt=""></a>
            <div class="down-content">
              <a href="/Product-Details"><h4>Nike Revolution 5 Shoes</h4></a>
              <h6><small><del>$99.00</del></small> $79.00</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="product-item">
            <a href="/Product-Details"><img src="/assets/images/product-3-370x270.jpg" alt=""></a>
            <div class="down-content">
              <a href="/Product-Details"><h4>Treadmill Orion Sprint</h4></a>
              <h6><small><del>$1999.00</del></small> $1779.00</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->

  <?php include("../components/footer.php"); ?>


  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Additional Scripts -->
  <script src="../assets/js/custom.js"></script>
  <script src="../assets/js/owl.js"></script>

</body>

</html>
