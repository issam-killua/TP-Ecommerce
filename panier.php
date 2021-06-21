<?php

session_start();

if (isset($_GET['ajouter']) && is_numeric($_GET['ajouter']) ) {


    if ($_SERVER['REQUEST_METHOD'] == "POST") {


        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    
        if (isset($_SESSION['cart'])) {

            $product_id = $_GET['ajouter'];
            $quantity = $_POST['quantity'];
            $name = trim($_POST['name']);
            $price = (float) $_POST['price'];

            $product = array(
                "sku" => $product_id,
                "quantity" => $quantity,
                "name" => trim($name),
                "price" => $price,
            );

            $_SESSION['cart'][] = $product;
            exit();
            
        }


        
    }

}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (isset($_SESSION['cart'])) {

        foreach($_SESSION['cart'] as $key => $product) {
            if ($product['sku'] == $_GET['delete']) {

                unset($_SESSION['cart'][$key]);
                break;
            }
        }

    }   
}


?>