<?php
include("../config.php");
$page = "login";

if (isset($_SESSION['user'])) {
  header("Location: " . BASE . "Home");
  unset($_SESSION['user']);
  exit();
}

$conn = connDB();
$loginError    = $_SESSION['loginError']    ?? "";
$registerError = $_SESSION['registerError'] ?? "";
$successMsg    = $_SESSION['successMsg']    ?? "";

unset($_SESSION['loginError'], $_SESSION['registerError'], $_SESSION['successMsg']);

handleLogin($conn, $loginError);
handleRegister($conn, $registerError, $successMsg);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BSU License Store</title>
  <link rel="icon" href="assets/images/favicon.ico" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet" />
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/css/fontawesome.css" />
  <link rel="stylesheet" href="assets/css/style.css" />

  <style>
    body {
      background-color: #f8f9fc;
      font-family: 'Poppins', sans-serif;
    }

    .container-login {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-top: 120px;
    }

    .wrap {
      display: flex;
      width: 100%;
      max-width: 1200px;
      border-radius: 10px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: #fff;
      position: relative;
    }

    .form-side {
      position: relative;
      flex: 1 1 50%;
      padding: 40px;
      min-height: 600px;
    }

    .form-inner {
      position: absolute;
      top: 50px;
      left: 40px;
      right: 40px;
      transition: opacity 0.3s ease;
      
    }

    .form-inner.inactive {
      opacity: 0;
      pointer-events: none;
    }

    .form-inner.active {
      opacity: 1;
      pointer-events: auto;
    }

    .text-side {
      flex: 1 1 50%;
      background: linear-gradient(135deg,rgb(0, 0, 0), #007dff);
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 40px;
    }

    .text-side h2 {
      font-weight: 700;
      margin-bottom: 10px;
    }

    .btn-outline-white {
      color: #fff;
      border: 1px solid #fff;
      border-radius: 25px;
      padding: 10px 30px;
      margin-top: 15px;
      display: inline-block;
      transition: 0.3s ease;
    }

    .btn-outline-white:hover {
      background-color: #fff;
      color: #007dff;
    }

    .form-control {
      height: 48px;
      border-radius: 30px;
      background: rgba(0, 0, 0, 0.05);
      border: none;
      padding: 0 20px;
    }

    .btn-primary {
      background: linear-gradient(to right,rgb(0, 0, 0), #007dff);
      border: none;
      border-radius: 25px;
    }

    .btn-primary:hover {
      opacity: 0.9;
    }

    .social-media .social-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #fff;
      border: 1px solid rgba(0, 0, 0, 0.1);
      margin-left: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.9s ease;
    }

    .social-media .social-icon span {
      color: #555;
      font-size: 16px;
    }

    .social-media .social-icon:hover {
      background: #007dff;
    }

    .social-media .social-icon:hover span {
      color: #fff;
    }

    @media (max-width: 768px) {
      .wrap {
        flex-direction: column;
        max-width: 95%;
      }

      .form-side {
        min-height: auto;
        padding: 30px;
      }

      .form-inner {
        position: relative;
        top: 0;
        left: 0;
        right: 0;
      }

      .text-side {
        order: -1;
        padding: 30px;
      }
    }

    .form-check-label {
      margin-left: 5px;
    }
  </style>
</head>

<body>

<?php include("../components/header.php"); ?>

<div class="container-login">
  <div class="wrap">
    <div class="form-side">
      <div class="form-inner active" id="login-form">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="mb-0">Sign In</h3>
          <div class="social-media d-flex">
            <a href="#" class="social-icon"><span class="fa fa-facebook"></span></a>
            <a href="#" class="social-icon"><span class="fa fa-twitter"></span></a>
          </div>
        </div>

        <form method="POST">
          <div class="form-group mb-3">
            <label>Username</label>
            <input type="text" class="form-control" name="username" required>
          </div>
          <div class="form-group mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="form-group">
            <button type="submit" name="login" class="form-control btn btn-primary">Sign In</button>
          </div>
          <div class="form-group d-flex justify-content-between align-items-center mt-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember" />
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <a href="#" id="forgot-password" class="text-decoration-none">Forgot password?</a>
          </div>
        </form>
      </div>

      <div class="form-inner inactive" id="signup-form">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="mb-0">Sign Up</h3>
          <div class="social-media d-flex">
            <a href="#" class="social-icon"><span class="fa fa-facebook"></span></a>
            <a href="#" class="social-icon"><span class="fa fa-twitter"></span></a>
          </div>
        </div>

        

        <form method="POST">
          <div class="form-group mb-3">
            <label>Username</label>
            <input type="text" class="form-control" name="username" required>
          </div>
          <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="form-group mb-3">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" required>
          </div>
          <div class="form-group mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="form-group">
            <button type="submit" name="register" class="form-control btn btn-primary">Sign Up</button>
          </div>
          <div class="form-group text-center mt-3">
            <a href="#" id="back-to-login">Already have an account?</a>
          </div>
        </form>
      </div>
    </div>

    <div class="text-side">
      <h2 id="toggle-title">Welcome to Sign In</h2>
      <p class="text-white" id="toggle-text">Don't have an account?</p>
      <a href="#" class="btn btn-white btn-outline-white" id="toggle-form-btn">Sign Up</a>
    </div>

  </div>
</div>

<?php include("../components/footer.php"); ?>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  const loginForm = document.getElementById('login-form');
  const signupForm = document.getElementById('signup-form');
  const toggleBtn = document.getElementById('toggle-form-btn');
  const toggleText = document.getElementById('toggle-text');
  const toggleTitle = document.getElementById('toggle-title');

  

  let isLogin = true;

  toggleBtn.addEventListener('click', function (e) {
    e.preventDefault();
    isLogin = !isLogin;

    loginForm.classList.toggle('active');
    loginForm.classList.toggle('inactive');
    signupForm.classList.toggle('active');
    signupForm.classList.toggle('inactive');

    toggleBtn.textContent = isLogin ? 'Sign Up' : 'Sign In';
    toggleText.textContent = isLogin
      ? "Don't have an account?"
      : "Already have an account?";
    toggleTitle.textContent = isLogin
      ? "Welcome to Sign In"
      : "Welcome to Sign Up";
  });

  document.getElementById("back-to-login").addEventListener("click", function (e) {
    e.preventDefault();
    loginForm.classList.add("active");
    loginForm.classList.remove("inactive");
    signupForm.classList.remove("active");
    signupForm.classList.add("inactive");

    isLogin = true;
    toggleBtn.textContent = 'Sign Up';
    toggleText.textContent = "Don't have an account?";
    toggleTitle.textContent = "Welcome to Sign In";
  });

  <?php if (!empty($loginError)): ?>
    Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: '<?= $loginError ?>',
      timer: 3000,
      showConfirmButton: false
    });
  <?php endif; ?>

  <?php if (!empty($registerError)): ?>
    Swal.fire({
      icon: 'error',
      title: 'Register Failed',
      text: '<?= $registerError ?>',
      timer: 3000,
      showConfirmButton: false
    });
  <?php endif; ?>

  <?php if (!empty($successMsg)): ?>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: '<?= $successMsg ?>',
      timer: 3000,
      showConfirmButton: false
    });
  <?php endif; ?>

</script>

</body>
</html>
