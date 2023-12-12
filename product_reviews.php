<?php
session_start();
include("php/connection.php");
include("php/functions.php");

// Verificăm autentificarea utilizatorului
$user_data = check_login($con);

// Get the product ID from the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from the database based on the product ID
    $select_produs = mysqli_query($con, "SELECT * FROM products WHERE id = $product_id");
    $fetch_produs = mysqli_fetch_assoc($select_produs);

    // Fetch product reviews
    $reviews_query = mysqli_query($con, "SELECT * FROM product_reviews WHERE product_id = $product_id");

    $average_rating_query = mysqli_query($con, "SELECT AVG(rating) AS average_rating FROM product_reviews WHERE product_id = $product_id");
    $average_rating_data = mysqli_fetch_assoc($average_rating_query);
    $average_rating = $average_rating_data['average_rating'];
} else {
    // Redirect or handle the case when no product ID is provided
    header("Location: shop.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/product_reviews.css?<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/footer.css?<?php echo time(); ?>" />
    <title><?php echo $fetch_produs['nume']; ?> - Sneaker Mania</title>
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

    <div id="product-details">
        <div class="container">
            <!-- Display product details here -->
            <div class="sneaker">
            <h2><?php echo $fetch_produs['nume']; ?></h2>
            <p><?php echo $fetch_produs['descriere']; ?></p>
            <p><?php echo $fetch_produs['pret']; ?> lei</p>
            <img src="imagini/<?php echo $fetch_produs['img']; ?>" alt="Product Image" class="image-fit">
            <p id="average-rating">Average Rating: <?php echo number_format($average_rating, 1); ?>/5.0 stars</p>
            
            <?php 
            $num_reviews_query = mysqli_query($con, "SELECT COUNT(*) AS num_reviews FROM product_reviews WHERE product_id = $product_id");
            $num_reviews_data = mysqli_fetch_assoc($num_reviews_query);
            $num_reviews = $num_reviews_data['num_reviews'];

            if($num_reviews > 0)
              echo "<div id='num-reviews'><p>Number of Reviews: $num_reviews</p></div>";
            else {
              echo "<div id='num-reviews'><p>No reviews yet.</p></div>";
            }
            ?>
            </div>

            <!-- Display reviews -->
            <div id="product-reviews">
                <h3>Product Reviews</h3>
                <?php
                if (mysqli_num_rows($reviews_query) > 0) {
                    while ($review = mysqli_fetch_assoc($reviews_query)) {
                        // Display each review
                        $user_id = $review['user_id'];
                        $user_query = mysqli_query($con, "SELECT user_name FROM users WHERE id = $user_id");
                        $user_data = mysqli_fetch_assoc($user_query);
                        $username = $user_data['user_name'];


                        echo "<div class='review'>";
                        echo "<p>Rating: {$review['rating']} stars</p>";
                        echo "<p>Comment: {$review['comment']}</p>";
                        echo "<p>By: $username </p>";
                        echo "<hr>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No reviews yet.</p>";
                }
                ?>

                <!-- Form to add a new review -->
                <?php if ($user_data) : ?>
                    <form action="add_review.php" method="post">
                        <h3>Add a Review</h3>
                        <label for="rating">Rating:</label>
                        <select name="rating" id="rating" required>
                            <option value="1">1 star</option>
                            <option value="2">2 stars</option>
                            <option value="3">3 stars</option>
                            <option value="4">4 stars</option>
                            <option value="5">5 stars</option>
                        </select>
                        <label for="comment">Comment:</label>
                        <textarea name="comment" id="comment" rows="4" required></textarea>
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <div class="review-btn">
                        <button type="submit">Submit Review</button>
                        </div>
                    </form>
                <?php else : ?>
                    <p>Please <a href="login.php">log in</a> to leave a review.</p>
                <?php endif; ?>
            </div>


        </div>
    </div>

    <!-- Your footer, scripts, and other HTML content -->
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

</body>

</html>