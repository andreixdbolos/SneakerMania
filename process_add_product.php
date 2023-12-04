<?php
session_start();
include("php/connection.php");

if (isset($_POST['submit'])) {
    $nume = mysqli_real_escape_string($con, $_POST['nume']);
    $descriere = mysqli_real_escape_string($con, $_POST['descriere']);
    $pret = mysqli_real_escape_string($con, $_POST['pret']);

    // Process image upload
    $image_name = $_FILES['poza']['name'];
    $image_tmp = $_FILES['poza']['tmp_name'];
    $image_size = $_FILES['poza']['size'];

    $image_folder = "imagini/";

    move_uploaded_file($image_tmp, $image_folder . $image_name);

    // Insert product into the database
    $insert_query = "INSERT INTO products (nume, descriere, img, pret) VALUES ('$nume', '$descriere', '$image_name', '$pret')";
    mysqli_query($con, $insert_query);

    echo "Product added successfully!";
} else {
    echo "Form submission failed.";
}
?>
