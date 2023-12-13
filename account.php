<?php
include("php/connection.php");
include("php/functions.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$user_data = check_login($con);

function getUserDataFromDatabase($userId) {
    global $con;

    $query = "SELECT * FROM users WHERE id = $userId";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($con));
    }

    $userData = mysqli_fetch_assoc($result);

    // Close the result set, not the connection
    mysqli_free_result($result);

    return $userData;
}

// Example usage
$userId = $user_data['id'];
$userData = getUserDataFromDatabase($userId);

// Display user data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>
    <link rel="stylesheet" href="css/navbar.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/account-page.css?<?php echo time(); ?>"> <!-- You can link your styles here -->
</head>
<body>

<!-- Navbar -->
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

<!-- User Account Section -->
<section class="account-section">
    <h2>User Account</h2>

    <?php
    // Check if the user is logged in (you may need more robust authentication)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id'])) {
        // Fetch and display user data
        $user_id = $_SESSION['user_id'];
        // You should replace the below with your database query logic
        $user_data = getUserDataFromDatabase($user_id);

        // Display user information
        echo '<div class="user-data">
            <p><strong>User ID:</strong> ' . $user_data['id'] . '</p>
            <p><strong>Username:</strong> ' . $user_data['user_name'] . '</p>
            <p><strong>Email:</strong> ' . $user_data['email'] . '</p>
        </div>';
        // Add more user information as needed
    } else {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }
    ?>

    <!-- Add more account details as needed -->

    <div id="log-out-btn">
        <div id="buton-log">
        <a href="logout.php">Logout</a>
        </div> </div><!-- Assuming you have a logout page -->
</section>

</body>
</html>