<?php
session_start();
include("php/connection.php");
include("php/functions.php");

$user_data = check_login($con);

// Check if the user is an admin
if (!$user_data || !is_admin($con, $user_data['id'])) {
    header("Location: index.php"); // Redirect non-admin users to the home page
    exit();
}

// Fetch order history for admin
$order_history_query = "SELECT o.*, u.user_name AS user_email, o.payment_method
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        ORDER BY o.order_date DESC";


$order_history_result = mysqli_query($con, $order_history_query);

$total_orders_query = "SELECT COUNT(*) as total_orders FROM orders";
$total_orders_result = mysqli_query($con, $total_orders_query);
$total_orders_data = mysqli_fetch_assoc($total_orders_result);
$total_orders = $total_orders_data['total_orders'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_delivered'])) {
  foreach ($_POST['mark_delivered'] as $order_number) {
      // Update the isDelivered column to 1 for the selected orders
      $update_query = "UPDATE orders SET isDelivered = 1 WHERE order_number = $order_number";
      mysqli_query($con, $update_query);
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link rel="stylesheet" href="css/navbar.css?<?php echo time(); ?>"> 
    <link rel="stylesheet" href="css/order_history.css?<?php echo time(); ?>">
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

    <div class="container">
        <h2>Order History</h2>

        <div class="total-orders">
            <p>No. of orders: <?php echo $total_orders; ?></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Username</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Payment method</th>
                    <th>Mark as delivered</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($order = mysqli_fetch_assoc($order_history_result)) {
                    echo "<tr>";
                    echo "<td id='order-number'>{$order['order_number']}</td>";
                    echo "<td>{$order['user_email']}</td>";
                    echo "<td>{$order['total_price']} lei</td>";
                    echo "<td>{$order['order_date']}</td>";
                    echo "<td>{$order['name']}</td>";
                    echo "<td>{$order['surname']}</td>";
                    echo "<td>{$order['address']}</td>";
                    echo "<td>{$order['phone_number']}</td>";
                    echo "<td>{$order['payment_method']}</td>";
                    echo '<td>';
                    echo '<form method="POST" action="">';
                    echo '<input type="checkbox" name="mark_delivered[]" value="' . $order['order_number'] . '"';
                    if ($order['isDelivered'] == 1) {
                        echo ' checked'; 
                    }
                    echo ' class="payment-checkbox" />'; 
                    echo '<input type="submit" name="submit_order" value="Submit" class="submit-btn">';
                    echo '</form>';
                    echo '</td>';

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
