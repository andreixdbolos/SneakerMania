<?php
session_start();
include("php/connection.php");
include("php/functions.php");

// Verifying authentication and obtaining user data
$user_data = check_login($con);

// Check if the user is an admin
if (!is_admin($con, $user_data['id'])) {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}

if (isset($_POST['submit'])) {
    $nume = mysqli_real_escape_string($con, $_POST['nume']);
    $descriere = mysqli_real_escape_string($con, $_POST['descriere']);
    $pret = mysqli_real_escape_string($con, $_POST['pret']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']); // New line

    // Process image upload
    $image_name = $_FILES['poza']['name'];
    $image_tmp = $_FILES['poza']['tmp_name'];
    $image_size = $_FILES['poza']['size'];

    $image_folder = "imagini/";

    move_uploaded_file($image_tmp, $image_folder . $image_name);

    // Insert product into the database
    $insert_query = "INSERT INTO products (nume, descriere, img, pret, quantity) VALUES ('$nume', '$descriere', '$image_name', '$pret', '$quantity')"; // Updated line
    mysqli_query($con, $insert_query);

    echo "Product added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/add_product.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/navbar.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/footer.css?<?php echo time(); ?>">
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

    <h2>Add Product (admin)</h2>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="nume">Product Name:</label>
        <input type="text" name="nume" required><br>

        <label for="descriere">Product Description:</label>
        <input type="text" name="descriere" required><br>

        <label for="pret">Product Price: (RON)</label>
        <input type="number" name="pret" required><br>

        <label for="poza">Product Image:</label>
        <input type="file" name="poza" accept="image/*" required><br>

        <label for="quantity">Quantity in Stock:</label>
        <input type="number" name="quantity" required><br>

        <input type="submit" name="submit" value="Add Product">
    </form>
</body>


</html>
