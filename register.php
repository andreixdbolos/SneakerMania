<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("php/connection.php");
include("php/functions.php");

if($_SERVER['REQUEST_METHOD'] == "POST")
{   //something was posted
    $email = $_POST['user_name'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if(!empty($email) && !empty($password) && !empty($confirmPassword) && ($password == $confirmPassword))
    {
        //save to database
        $user_id = random_num(5);
        $query = "insert into users (user_id,user_name,password) values ('$user_id','$email','$password')";
        mysqli_query($con, $query);
        //check if query is empty
        if(mysqli_affected_rows($con) > 0)
        {
            $query = "select * from users where user_id = '$user_id' limit 1";
            $result = mysqli_query($con, $query);
            if($result && mysqli_num_rows($result) > 0)
            {
                $user_data = mysqli_fetch_assoc($result);
                $_SESSION['user_id'] = $user_data['user_id'];
                header("Location: login.php");
                die;
            }
        }
        else
        {
            echo "Please enter some valid information!";
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
    <link rel="stylesheet" href="css/navbar.css?v=2" />
    <link rel="stylesheet" href="css/index.css?v=2" />
    <link rel="stylesheet" href="css/footer.css?v=2" />
    <link rel="stylesheet" href="css/shop.css?v=2" />
    <link rel="stylesheet" href="css/sneaker-page.css?v=2" />
    <link rel="stylesheet" href="css/login.css?v=2" />
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
            name="user_name"
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
      </div>
    </div>

    <script src="navbar.js"></script>
  </body>
</html>
