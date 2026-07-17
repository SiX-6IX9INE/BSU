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

  <div class="products">
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-xs-12">
          <div class="product-item">
            <img src="<?= BASE ?>assets/images/<?= generateSlug($product['name']) ?>-1.png" alt="" class="img-fluid wc-image">
          </div>
          <br>
            <div class="row">
            <?php for ($i = 2; $i <= 4; $i++): ?>
              <?php $imageFile = "assets/images/" . generateSlug($product['name']) . "-$i.png"; ?>

              <?php if (file_exists(dirname(__DIR__) . "/" . $imageFile)): ?>
                <div class="col-sm-4 col-xs-6">
                  <div class="product-item">
                  <img src="<?= BASE . $imageFile ?>" alt="" class="img-fluid">
                </div>
                <br>
              </div>
              <?php endif; ?>
            <?php endfor; ?>
            </div>
        </div>

        <div class="col-md-8 col-xs-12">
          <form action="#" method="post" class="form">
            <h2><?= $product['name'] ?></h2>

            <br>

            <p class="lead">
              <small><del><?= $product['price']*1.2 ?>.00฿</del></small><strong class="text-primary"> <?= $product['price'] ?>.00฿</strong>
            </p>

            <br>

            <p class="small font-kanit">
              <?= $product['description'] ?>
            </p>

            <br>

            <div class="row">
              <div class="col-sm-6 ml-auto">
                <?php if (isset($_SESSION['user'])): ?>
                  <a href="<?= BASE ?>Checkout/<?= urlencode($slug); ?>" class="btn btn-primary btn-block">Buy</a>
                <?php else: ?>
                  <a href="<?= BASE ?>Login" class="btn btn-primary btn-block">Login to Buy</a>
                <?php endif; ?>
                </div>
              <!-- <div class="col-sm-4">
                <label class="control-label">Extra 1</label>
                <div class="form-group">
                  <select class="form-control">
                    <option value="0">18 gears</option>
                    <option value="1">21 gears</option>
                    <option value="2">27 gears</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-8">
                <label class="control-label">Quantity</label>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="1">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <a href="/Checkout" class="btn btn-primary btn-block">Buy</a>
                  </div>
                </div>
              </div> -->
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

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
