<?php
session_start();

include("php/connection.php");
include("php/functions.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Path to Composer autoload

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = stripslashes($_POST['username']);
    $username = mysqli_real_escape_string($con, $username);
    $email = stripslashes($_POST['email']);
    $email = mysqli_real_escape_string($con, $email);
    $password = stripslashes($_POST['password']);
    $password = mysqli_real_escape_string($con, $password);
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(50));

// Insert user data into the database
    $query = "INSERT into `users` (user_name, password, email, token, isEmailConfirmed)
              VALUES ('$username', '$password_hashed', '$email', '$token', '0')";
    $result = mysqli_query($con, $query);



    if ($result) {
        // Send email verification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bolosandrei4@gmail.com'; // Your Gmail username
            $mail->Password = '-'; // Your Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('bolosandrei4@gmail.com', 'SneakerMania'); // Your Gmail address and your name
            $mail->addAddress($email, $username); // User's email and username
            $mail->addReplyTo('bolosandrei4@gmail.com', 'SneakerMania'); // Your Gmail address and your name

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "Click the following link to verify your email: <a href='http://localhost/SneakerMania/verified.php?token=$token'>Verify Email</a>";

            $mail->send();
            // echo "
            //   <div id='success-message'>
            //     <h3>You are registered successfully. Please check your email for verification.</h3>
            //     <p class='link'>Click here to <a href='login.php'>Login</a></p>
            //   </div>";

        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Please enter some valid information!";
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
      href="css/mobile.css?v=2"
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
          <ul class="hidden meniu-dpd">
            <!-- <li><a href="index.php">Acasa</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="about.php">Despre</a></li>
            <li><a href="login.php">Login</a></li> -->
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

    <div id="login-content">
      <div class="login-form">
        <h2>Register</h2>
        <form method="post">
          <input

            type="text"
            name="username"
            placeholder="Username"
            id="username-field"

          />

          <input
            type="email"
            name="email"
            placeholder="Email"
            id="email-field"
          />
          <br />
          <input
            type="password"
            name="password"
            placeholder="Password"
            id="password-field"
          />
          <br />
          <input
            type="password"
            name="confirmPassword"
            placeholder="Confirm Password"
            id="password-field"
          />
          <div id="login-container">
            <!-- <a href="login.php" id="login-button">Register</a> -->
            <input class="login-button" type="submit" value="Register" id="login-button">
            <a href="login.php" id="log-button"
              >Already have an account? Log in!</a
            >
          </div>
        </form>

        <?php
    // Check if the registration was successful and display the success message
    if (isset($result) && $result) {
      $_SESSION['id'] = mysqli_insert_id($con);
      $_SESSION['username'] = $username; // Change this line
  
      echo "
          <div id='success-message'>
              <h3>You are registered successfully. Please check your email for verification.</h3>
              <p class='link'>Click here to <a href='login.php'>Login</a></p>
          </div>";
  }
  
    ?>
      </div>
    </div>

    <script src="navbar.js"></script>
  </body>
</html>
