<?php

session_start();

include("php/connection.php");
include("php/functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_input = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($user_input) && !empty($password)) {
        // Validate email format or check if username is "admin"
        if (filter_var($user_input, FILTER_VALIDATE_EMAIL) || strtolower($user_input) === 'admin') {
            //read from database
            $query = "SELECT * FROM users WHERE user_name = '$user_input' LIMIT 1";
            $result = mysqli_query($con, $query);

            if ($result) {
                if ($result && mysqli_num_rows($result) > 0) {
                    $user_data = mysqli_fetch_assoc($result);
                    if ($user_data['password'] === $password) {
                        $_SESSION['user_id'] = $user_data['user_id'];
                        header("Location: index.php");
                        die;
                    }
                }
            }
        } 
    } 
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
    <link rel="stylesheet" href="css/shop.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/sneaker-page.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/login.css?<?php echo time(); ?>" />
    <link rel="icon" href="imagini/aj4logo-removebg-preview.png" />
    <link
      rel="stylesheet"
      media="screen and (max-width: 1080px)"
      href="css/mobile.css?<?php echo time(); ?>"
    />
    <title>Proiect EWD</title>
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
          <!-- <ul class="hidden meniu-dpd">
            <li><a href="index.php">Acasa</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="about.php">Despre</a></li>
            <li><a href="login.php">Login</a></li>
          </ul> -->

          <div class="hamburger">
            <div class="middle-bar">
              <div class="top-bar"></div>
              <div class="bottom-bar"></div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div id="login-content">
      <div class="login-form">
        <h2>Log in</h2>
        <form method="post">
          <input
            type="text"
            name="username"
            placeholder="Username"
            id="username-field"
          />
          <br />
          <input
            type="password"
            name="password"
            placeholder="Password"
            id="password-field"
          />
          <br />
          <div id="login-container">
            <input type="submit" value="Log in" id="login-button" />
            <a href="register.php" id="register-button"
              >Don't have an account? Register!</a
            >
          </div>
          <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (!empty($user_input) && !empty($password)) {
                // Validate email format or check if username is "admin"
                if (filter_var($user_input, FILTER_VALIDATE_EMAIL) || strtolower($user_input) === 'admin') {
                    //read from database
                    $query = "SELECT * FROM users WHERE user_name = '$user_input' LIMIT 1";
                    $result = mysqli_query($con, $query);

                    if ($result) {
                        if ($result && mysqli_num_rows($result) > 0) {
                            $user_data = mysqli_fetch_assoc($result);
                            if ($user_data['password'] === $password) {
                                $_SESSION['user_id'] = $user_data['user_id'];
                                header("Location: index.php");
                                die;
                            }
                        }
                    }

                    echo '<div id="error-message">Wrong username or password!</div>';
                    echo '<script>
                        setTimeout(function(){
                            var errorMessage = document.getElementById("error-message");
                            errorMessage.parentNode.removeChild(errorMessage);
                        }, 1000); // Adjust the timeout as needed
                      </script>';
                } else {
                    echo '<div id="error-message">Invalid email format or username!</div>';
                    echo '<script>
                        setTimeout(function(){
                            var errorMessage = document.getElementById("error-message");
                            errorMessage.parentNode.removeChild(errorMessage);
                        }, 1000); // Adjust the timeout as needed
                      </script>';
                }
            } else {
                echo '<div id="error-message">Please enter both username and password!</div>';
                echo '<script>
                        setTimeout(function(){
                            var errorMessage = document.getElementById("error-message");
                            errorMessage.parentNode.removeChild(errorMessage);
                        }, 1000); // Adjust the timeout as needed
                      </script>';
            }
        }
        ?>
        </form>
      </div>
    </div>

    <script src="navbar.js"></script>
  </body>
</html>
