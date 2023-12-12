<?php
session_start();
include("php/connection.php");
include("php/functions.php");

// Check if the user is logged in
$user_data = check_login($con);
if (!$user_data) {
    // Redirect or handle the case when the user is not logged in
    header("Location: ../login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $product_id = $_POST["product_id"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];
    $user_id = $user_data["id"];

    // Insert the review into the database
    $insert_review_query = mysqli_query($con, "INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES ($product_id, $user_id, $rating, '$comment')");

    if ($insert_review_query) {
        // Successfully inserted the review
        header("Location: product_reviews.php?id=$product_id");
        exit();
    } else {
        // Handle the case when the review insertion fails
        echo "Error: " . mysqli_error($con);
    }
} else {
    // Redirect or handle the case when the form is not submitted
    header("Location: product_reviews.php?id=$product_id");
    exit();
}
?>
