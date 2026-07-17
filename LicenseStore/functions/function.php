<?php

function AddComma($number) {
    $number = str_replace(',', '', $number);
    return number_format($number, 0, '.', ',');
}

function generateSlug($text) {
    $text = trim($text);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{Thai}\p{L}\p{N}]+/u', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');

    return $text;
}

function shortDescription($text, $width = 100, $maxLines = 2) {
    $wrapped = wordwrap($text, $width, "\n");
    $lines = explode("\n", $wrapped);
    
    if (count($lines) > $maxLines) {
        return implode("\n", array_slice($lines, 0, $maxLines)) . '...';
    }
    
    return $text;
}

function handleLogin($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
      $username = mysqli_real_escape_string($conn, $_POST['username']);
      $password = $_POST['password'];
  
      $sql = "SELECT * FROM users WHERE username = '$username'";
      $result = mysqli_query($conn, $sql);
      if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
          $_SESSION['user'] = $user['username'];
          header("Location: " . BASE . "Dashboard");
          exit();
        } else {
          $_SESSION['loginError'] = "User not found or Invalid password.";
        }
      } else {
        $_SESSION['loginError'] = "User not found or Invalid password.";
      }
  
      header("Location: " . BASE . "Login");
      exit();
    }
  }
  

  function handleRegister($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
      $username = mysqli_real_escape_string($conn, $_POST['username']);
      $email    = mysqli_real_escape_string($conn, $_POST['email']);
      $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  
      $check = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE username = '$username' 
           OR email = '$email' 
           OR phone = '$phone'
      ");
  
      if (mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);
  
        if ($existing['username'] === $username) {
          $_SESSION['registerError'] = "Username already exists.";
        } elseif ($existing['email'] === $email) {
          $_SESSION['registerError'] = "Email already registered.";
        } elseif ($existing['phone'] === $phone) {
          $_SESSION['registerError'] = "Phone number already in use.";
        } else {
          $_SESSION['registerError'] = "Duplicate information found.";
        }
  
      } else {
        $insert = mysqli_query($conn, "
          INSERT INTO users (username, email, phone, password) 
          VALUES ('$username', '$email', '$phone', '$password')
        ");
  
        if ($insert) {
          $_SESSION['successMsg'] = "Registered successfully! Please sign in.";
        } else {
          $_SESSION['registerError'] = "Registration failed.";
        }
      }
  
      header("Location: " . BASE . "Login");
      exit();
    }
}  