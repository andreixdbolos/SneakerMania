<?php
session_start();

include("php/connection.php");
include("php/functions.php");
$user_data = check_login($con);

if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = intval($_POST['update_quantity']);

    // Check if the new quantity is zero, and if so, remove the item from the cart
    if ($new_quantity === 0) {
        $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = {$_SESSION['user_id']}";
        $delete_result = mysqli_query($con, $delete_query);

        // if ($delete_result) {
        //     echo "Item removed from the cart successfully.";
        // } else {
        //     echo "Error removing item from the cart.";
        // }
    }

    // Update the quantity in the cart
    $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = $cart_id AND user_id = {$_SESSION['user_id']}";
    $update_result = mysqli_query($con, $update_query);

    // if ($update_result) {
    //     echo "Quantity updated successfully.";
    // } else {
    //     echo "Error updating quantity.";
    // }
}

if (isset($_POST['delete'])) {
    $cart_id = $_POST['cart_id'];

    // Delete the item from the cart
    $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = {$_SESSION['user_id']}";
    $delete_result = mysqli_query($con, $delete_query);

    // if ($delete_result) {
    //     echo "Item removed from the cart successfully.";
    // } else {
    //     echo "Error removing item from the cart.";
    // }
}

if (isset($_POST['place_order'])) {
    // Get user ID
    $user_id = $_SESSION['user_id'];

    // Get user email from the session
    $user_email = $user_data['user_name'];

    // Get additional order information from the form
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $surname = mysqli_real_escape_string($con, $_POST['surname']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $phone_number = mysqli_real_escape_string($con, $_POST['phone_number']);

    // Calculate total price
    $total_query = "SELECT SUM(p.pret * c.quantity) AS total
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = $user_id";
    $total_result = mysqli_query($con, $total_query);

    if ($total_result && mysqli_num_rows($total_result) > 0) {
        $total_row = mysqli_fetch_assoc($total_result);
        $total_price = $total_row['total'];

        // Generate a 10-digit random order number
        $order_number = mt_rand(1000000000, 9999999999);

        // Insert order into 'orders' table
        $insert_order_query = "INSERT INTO orders (order_number, user_id, total_price, name, surname, address, phone_number, user_email) 
                               VALUES ($order_number, $user_id, $total_price, '$name', '$surname', '$address', '$phone_number', '$user_email')";
        $insert_order_result = mysqli_query($con, $insert_order_query);

        if ($insert_order_result) {
            // Clear the cart by deleting all items
            $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
            $clear_cart_result = mysqli_query($con, $clear_cart_query);

        } 
    } 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping cart</title>
    <link rel="stylesheet" href="css/navbar.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/footer.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/cos.css?<?php echo time(); ?>">  
    <link rel="icon" href="imagini/aj4logo-removebg-preview.png" />

</head>
<body>
<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="index.php"><img src="imagini/aj4logo-removebg-preview.png" /></a>
        </div>
        <div class="meniu">
            <ul class="hidden meniu-dpd">
                <li><a href="index.php">Acasa</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="about.php">Despre</a></li>
                <?php 
                if ($user_data) { ?>
                    <li><a href="logout.php">Log Out</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Log In</a></li>
                    <li><a href="register.php">Register</a></li>
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

<h3 class="title">
    <div class="h3-content">
    Shopping Cart
    <img src="imagini/shopping-cart3.png" alt="">
    </div></h3>
    <div class="produse">
    <table>
        <tr>
            <th><div class="header-tabel">Nume</div></th>
            <th><div class="header-tabel">Imagine</div></th>
            <th><div class="header-tabel">Cantitate</div></th>
            <th><div class="header-tabel">Pret</div></th>
            <th><div class="header-tabel">Action</div></th>
        </tr>
        <?php 
        $user_id = $_SESSION['user_id'];
        $select_cos = mysqli_query($con, "SELECT c.*, p.nume, p.pret, p.img 
                                          FROM cart c
                                          JOIN products p ON c.product_id = p.id
                                          WHERE c.user_id = $user_id") or die('query failed');

        while ($fetch_cos = mysqli_fetch_assoc($select_cos)) {
            $nume = isset($fetch_cos['nume']) ? $fetch_cos['nume'] : '';
            $pret = isset($fetch_cos['pret']) ? $fetch_cos['pret'] : '';
            $image = isset($fetch_cos['img']) ? $fetch_cos['img'] : '';
            ?>
            <tr>
                <td>
                    <div class="model"><?php echo $nume; ?></div>
                </td>
                <td><img src="imagini/<?php echo $image; ?>" alt="Product Image" class="image-fit"></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cos['id']; ?>">
                        <button type="submit" class="minus-btn" name="update_quantity" value="<?php echo max(0, $fetch_cos['quantity'] - 1); ?>">-</button>
                        <span class="cantitatex"><?php echo $fetch_cos['quantity']; ?></span>
                        <button type="submit" name="update_quantity" value="<?php echo $fetch_cos['quantity'] + 1; ?>">+</button>
                    </form>
                </td>
                <td>
                    <div class="price"><?php echo $pret * $fetch_cos['quantity'] . " lei"; ?></div>
                </td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cos['id']; ?>">
                        <button type="submit" name="delete">Remove from Cart</button>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>

<div class="payment-total">
   <?php
    $select_total = mysqli_query($con, "SELECT SUM(p.pret * c.quantity) AS total 
                                        FROM cart c
                                        JOIN products p ON c.product_id = p.id
                                        WHERE c.user_id = $user_id") or die('query failed');

    if (mysqli_num_rows($select_total) > 0) {
        $fetch_total = mysqli_fetch_assoc($select_total);
        ?>
        <h3 class="title"><?php echo "Total payment = " . $fetch_total['total']; ?> lei</h3>
        <?php
    } else {
        ?>
        <h3 class="title">0 lei</h3>
        <?php
    }
    ?>
</div>
<div class="div-payment">
<form method="POST" action="cos.php" class="payment-form">
    <label for="name">Name:</label>
    <input type="text" name="name" required>

    <label for="surname">Surname:</label>
    <input type="text" name="surname" required>

    <label for="address">Address:</label>
    <input type="text" name="address" required>

    <label for="phone_number">Phone Number:</label>
    <input type="text" name="phone_number" required>

    <!-- Other form fields and buttons -->

    <button type="submit" name="place_order" class="payment-btn">Place Order</button>
</form>
</div>

<script src="remove.js"></script>

</body>

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
</html>