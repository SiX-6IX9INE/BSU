<?php
include("../config.php");
$page = "products";

$conn = connDB();
$perPage    = 6;
$totalRows  = (int) (mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0] ?? 0);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$current    = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$current    = max(1, min($current, $totalPages));
$offset     = ($current - 1) * $perPage;
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
    <div class="page-heading about-heading header-text" style="background-image: url(assets/images/background_window11_1.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>Lorem ipsum dolor sit amet</h4>
              <h2>Products</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="products product-list">
      <div class="container">
        <div class="row">

        <?php $query = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC LIMIT $offset, $perPage");
          while ($row = mysqli_fetch_array($query)) {
            $slug = generateSlug($row['name']);?>
            <div class="col-md-4">
              <div class="product-item">
                <a href="Product-Details/<?= urlencode($slug); ?>">
                  <img src="assets/images/<?= $slug ?>-1.png" alt="">
                </a>
                <div class="down-content">
                  <a href="Product-Details/<?= urlencode($slug); ?>">
                    <h4><?= $row['name']; ?></h4>
                  </a>
                  <h6><small><del><?= ($row['price']*1.2); ?>.00฿ </del></small> <?= ($row['price']); ?>.00฿</h6>
                  <p><?= nl2br(shortDescription($row['description'])); ?></p>
                </div>
              </div>
            </div>
        <?php } ?>


          <!-- <div class="col-md-4">
            <div class="product-item">
              <a href="Product-Details"><img src="assets/images/product-1-370x270.jpg" alt=""></a>
              <div class="down-content">
                <a href="Product-Details"><h4>Lorem ipsum dolor sit amet.</h4></a>
                <h6><small><del>$999.00 </del></small> $779.00</h6>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum dicta voluptas quia dolor fuga odit.</p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="Product-Details"><img src="assets/images/product-2-370x270.jpg" alt=""></a>
              <div class="down-content">
                <a href="Product-Details"><h4>Lorem ipsum dolor sit amet.</h4></a>
                <h6><small><del>$99.00</del></small>  $79.00</h6>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non beatae soluta, placeat vitae cum maxime culpa itaque minima.</p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="Product-Details"><img src="assets/images/product-3-370x270.jpg" alt=""></a>
              <div class="down-content">
                <a href="Product-Details"><h4>Lorem ipsum dolor sit amet.</h4></a>
                <h6><small><del>$1999.00</del></small>   $1779.00</h6>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt nisi quia aspernatur, harum facere delectus saepe enim?</p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="Product-Details"><img src="assets/images/product-4-370x270.jpg" alt=""></a>
              <div class="down-content">
                <a href="Product-Details"><h4>Lorem ipsum dolor sit amet.</h4></a>
                <h6><small><del>$999.00 </del></small> $779.00</h6>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum dicta voluptas quia dolor fuga odit.</p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="Product-Details"><img src="assets/images/product-5-370x270.jpg" alt=""></a>
              <div class="down-content">
                <a href="Product-Details"><h4>Lorem ipsum dolor sit amet.</h4></a>
                <h6><small><del>$99.00</del></small>  $79.00</h6>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non beatae soluta, placeat vitae cum maxime culpa itaque minima.</p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="Product-Details"><img src="assets/images/product-6-370x270.jpg" alt=""></a>
              <div class="down-content">
                <a href="Product-Details"><h4>Lorem ipsum dolor sit amet.</h4></a>
                <h6><small><del>$1999.00</del></small>   $1779.00</h6>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt nisi quia aspernatur, harum facere delectus saepe enim?</p>
              </div>
            </div>
          </div> -->

          <?php if ($totalPages > 1): ?>
          <div class="col-md-12">
            <ul class="pages">
              <li class="<?= $current <= 1 ? 'disabled' : '' ?>">
                <a href="<?= $current <= 1 ? '#' : '?page=' . ($current - 1) ?>" aria-label="ก่อนหน้า"><i class="fa fa-angle-double-left"></i></a>
              </li>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="<?= $p === $current ? 'active' : '' ?>"><a href="?page=<?= $p ?>"><?= $p ?></a></li>
              <?php endfor; ?>
              <li class="<?= $current >= $totalPages ? 'disabled' : '' ?>">
                <a href="<?= $current >= $totalPages ? '#' : '?page=' . ($current + 1) ?>" aria-label="ถัดไป"><i class="fa fa-angle-double-right"></i></a>
              </li>
            </ul>
          </div>
          <?php endif; ?>
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
