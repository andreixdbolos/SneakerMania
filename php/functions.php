<?php

function check_login($con)
{

	if(isset($_SESSION['user_id']))
	{

		$id = $_SESSION['user_id'];
		$query = "select * from users where user_id = '$id' limit 1";

		$result = mysqli_query($con,$query);
		if($result && mysqli_num_rows($result) > 0)
		{

			$user_data = mysqli_fetch_assoc($result);
			return $user_data;
		}
	}

	//redirect to login
	header("Location: login.php");
	die;

}

function random_num($length)
{

	$text = "";
	if($length < 5)
	{
		$length = 5;
	}

	$len = rand(4,$length);

	for ($i=0; $i < $len; $i++) { 

		$text .= rand(0,9);
	}

	return $text;
}
function adauga_in_cos($con, $nume, $image_data) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Check if the product exists in the products table
        $check_product_query = "SELECT id FROM products WHERE nume = ?";
        $check_stmt = mysqli_prepare($con, $check_product_query);
        mysqli_stmt_bind_param($check_stmt, "s", $nume);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $product_id = $row['id'];

            // Check if the product is already in the user's cart
            $check_cart_query = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $check_cart_stmt = mysqli_prepare($con, $check_cart_query);
            mysqli_stmt_bind_param($check_cart_stmt, "ii", $user_id, $product_id);
            mysqli_stmt_execute($check_cart_stmt);
            $cart_result = mysqli_stmt_get_result($check_cart_stmt);

            if ($cart_row = mysqli_fetch_assoc($cart_result)) {
                // If the product is already in the cart, update the quantity
                $new_quantity = $cart_row['quantity'] + 1;
                $update_query = "UPDATE cart SET quantity = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_row['id']);
                mysqli_stmt_execute($update_stmt);

                echo "Quantity updated successfully.";
            } else {
                // If the product is not in the cart, insert a new row
                $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
                $stmt = mysqli_prepare($con, $insert_query);
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
                mysqli_stmt_execute($stmt);

                echo "Produsul a fost adăugat în coș!";
            }
        } else {
            echo "Produsul nu există în magazin!";
        }
    } else {
        echo "Trebuie să fii autentificat pentru a adăuga produse în coș!";
    }
}


function is_admin($con, $user_id) {
    $admin_user_id = 1;  // Replace with the actual user ID for administrators

    if ($user_id == $admin_user_id) {
        return true;
    } else {
        return false;
    }
}

// function get_order_history($con, $user_id) {
//     $order_history = array();

//     $query = "SELECT * FROM orders WHERE user_id = $user_id";
//     $result = mysqli_query($con, $query);

//     if ($result) {
//         while ($row = mysqli_fetch_assoc($result)) {
//             $order_history[] = $row;
//         }
//     }

//     return $order_history;
// }