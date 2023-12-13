<?php
session_start();
include("php/connection.php");
include("php/functions.php");

$user_data = check_login($con);
if (!isset($_SESSION['redirected']) && !isset($_SESSION['logged_in'])) {
  $_SESSION['redirected'] = true;
  header("Location: login.php");
}

if (isset($_SESSION['loggedIn'])) {
  $loggedIn = true;
} else {
  $loggedIn = false;
}

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

    if ($username === 'admin') {
        // For the admin user, compare the password directly (no hashing)
        if ($password === '1234') {
            echo "Admin Login Successful!<br>";
            $_SESSION['loggedIn'] = true;
            header('Location: index.php');
            exit();
        } else {
            echo "Admin Passwords do not match! Please check your credentials.<br>";
        }
    } else {
        // For regular users, validate the login as before
        $user_data = validate_login($con, $username, $password);

        if ($user_data) {
            // Set user session upon successful login
            $_SESSION['id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['user_name'];
            $_SESSION['loggedIn'] = true;

            echo "User Login Successful!<br>"; // Add this for debugging
            header("Location: index.php"); // Redirect to your welcome page or dashboard
            exit();
        } else {
            echo "User Login failed. Please check your credentials.<br>";
        }
    }
}

if (isset($_POST['logout'])) {
  unset($_SESSION['loggedIn']);
  header('Location: index.php');
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/navbar.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/index.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/footer.css?<?php echo time(); ?>" />
    <link rel="icon" href="imagini/aj4logo-removebg-preview.png" />
    <link
      rel="stylesheet"
      media="screen and (max-width: 1080px)"
      href="css/mobile.css?<?php echo time(); ?>"
    />
    <title>Sneaker Mania</title>
  </head>
  <body>
    <nav class="navbar">
      <div class="container">
        <div class="logo">
          <a href="index.php"
            ><img src="imagini/aj4logo-removebg-preview.png"
          /></a>
        </div>
        <div class="meniu">
          <ul class="hidden meniu-dpd">
            <li><a href="index.php">Acasa</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="about.php">Despre</a></li>
            <li><a href="cos.php"><img src="imagini/shopping-cart3.png" alt=""></a></li>
            <?php 
                if ($user_data) { ?>
                    <li id="buton-cont"><a href="account.php"><img src="imagini/user.png"></a></li>
                    <li><a href="logout.php">Log Out</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Log In</a></li>
                    <li><a href="register.php ">Register</a></li>
                <?php } ?>  
            
          </ul>

          <div class="hamburger">
            <div class="middle-bar">
              <div class="top-bar"></div>
              <div class="bottom-bar"></div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div id="showcase">
      <div class="container showcase-content">
        <div class="showcase-text"><h2>WELCOME TO SNEAKER MANIA!</h2></div>
        <div class="showcase-button"><a href="shop.php
      ">SHOP NOW</a></div>
      </div>
    </div>

    <div id="preview">
      <div class="preview-content container">
        <div class="preview-sneaker"><img src="imagini/dunkblack.jpg" /></div>
        <div class="preview-sneaker"><img src="imagini/af1.jpg" /></div>
        <div class="preview-sneaker"><img src="imagini/aj1blue.jpg" /></div>
      </div>
    </div>

    <footer id="footer">
      <div class="footer-content">
        <div class="social-media">
          <a href="https://www.instagram.com/andreixbolos"
            ><img src="imagini/instagram-logo (1).png"
          /></a>
          <a href="https://www.linkedin.com/in/andrei-bolo%C8%99-408ab1254/"
            ><img src="imagini/linkedin.png"
          /></a>
          <a href="https://github.com/andreixdbolos"
            ><img src="imagini/github-sign.png"
          /></a>
        </div>
      </div>
    </footer>

    <script src="navbar.js"></script>
  </body>
</html>
