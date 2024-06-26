<?php
session_start();

include("php/connection.php");
include("php/functions.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
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
    
            // Send order confirmation email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bolosandrei4@gmail.com'; // Your Gmail username
                $mail->Password = '-'; // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
    
                // Recipients
                $mail->setFrom('bolosandrei4@gmail.com', 'SneakerMania');
    
                // Fetch user email based on user_id from users table
                $user_email_query = "SELECT email, user_name FROM users WHERE id = $user_id";
                $user_email_result = mysqli_query($con, $user_email_query);
    
                if ($user_email_row = mysqli_fetch_assoc($user_email_result)) {
                    $user_email = $user_email_row['email'];
                    $user_name = $user_email_row['user_name'];
    
                    $mail->addAddress($user_email, $user_name);
    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Order Confirmation';
    
                    // Fetch order details to include in the email
                    $order_details_query = "SELECT o.order_number, o.total_price, o.order_date, p.nume, p.img, c.quantity
                        FROM orders o
                        JOIN cart c ON o.user_id = c.user_id
                        JOIN products p ON c.product_id = p.id
                        WHERE o.order_number = $order_number AND o.user_id = $user_id";
                    $order_details_result = mysqli_query($con, $order_details_query);

                    $email_body = "Order #$order_number confirmed. It will arrive to you soon.<br><br>";

                    while ($row = mysqli_fetch_assoc($order_details_result)) {
                        $product_name = $row['nume'];
                        $product_image_base64 = $row['img'];
                        $product_quantity = $row['quantity'];
                    
                        // Decode the base64 image data
                        $product_image = base64_decode($product_image_base64);
                    
                        // Include product details in the email
                        $email_body .= "<b>$product_name</b><br>";
                        $email_body .= "Quantity: $product_quantity<br>";

                        
                        // Include image in the email
                        $email_body .= "<img src='imagini/$product_image.png' alt='$product_name'><br><br>";
                    }
                    
                    $email_body .= "Total price: $total_price lei<br><br>";
                    $email_body .= "Payment method: Ramburs<br><br>";
                    $mail->Body = $email_body;
    
                    $mail->send();
                        
                    $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
                    $clear_cart_result = mysqli_query($con, $clear_cart_query);
                    // Redirect to the success page
                    header("Location: cos.php?success=1");
                    exit();
                }
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        }


    header("Location: cos.php?success=1");
    exit();
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

    <script>
        // Function to show a success message modal
        function showSuccessMessage() {
            var modal = document.getElementById("successModal");
            modal.style.display = "block";
        }

        // Trigger the function when the document is ready
        document.addEventListener("DOMContentLoaded", function () {
            // Check if the URL has a success parameter (you can set this after a successful order)
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has("success")) {
                showSuccessMessage();
            }
        });
    </script>
</head>
<body>
<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="index.php"><img src="imagini/aj4logo-removebg-preview.png" /></a>
        </div>
        <div class="meniu">
            <ul class="meniu-dpd">
                <li><a href="index.php">Acasa</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="about.php">Despre</a></li>
                <?php 
                if ($user_data) { ?>
                    <li id="buton-cont"><a href="account.php"><img src="imagini/user.png"></a></li>
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
            $product_id = $fetch_cos['product_id'];
            $ordered_quantity = $fetch_cos['quantity'];

            // Update the product quantity in the products table
            $update_quantity_query = "UPDATE products SET quantity = quantity - $ordered_quantity WHERE id = $product_id";
            $update_quantity_result = mysqli_query($con, $update_quantity_query);
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

        <label for="payment_method">Metoda plata:</label>
        <select name="payment_method" id="payment_method">
                <option value="card">Ramburs</option>

        </select>

        <!-- Other form fields and buttons -->

        <button type="submit" name="place_order" class="payment-btn">Place Order</button>
    </form>
</div>
<div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('successModal').style.display='none'">&times;</span>
            <p>Order placed successfully!</p>
        </div>
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
