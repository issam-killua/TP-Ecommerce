<?php
    session_start();
    include "init.php";
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

            
            foreach($_SESSION['cart'] as $product) {
                $stmt = $con->prepare("INSERT INTO orders (product_sku, quantity, total_price, user_id) 
                                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $product['sku'],
                    $product['quantity'],
                    $product['price']*$product['quantity'],
                    $_SESSION['user'][0]

                ]);
            }

            unset($_SESSION['cart']);
            exit();
        }


    }
?>