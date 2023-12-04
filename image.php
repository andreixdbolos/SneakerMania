<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("php/connection.php");

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    echo "Product ID: " . $product_id; // Debug statement

    $query = "SELECT img FROM products WHERE id = $product_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        header("Content-type: image/jpeg");
        echo $row['img'];
        exit;
    } else {
        echo "Image not found in the database.";
    }
} else {
    echo "Product ID not provided.";
}
?>
