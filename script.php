<?php

    include "connect.php";

    $file = file_get_contents('products.json');

    $products = json_decode($file, true);

    foreach($products as $product) {
        $stmt = $con->prepare('INSERT INTO products
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $product["sku"],
            $product["name"],
            $product["type"],
            $product["price"],
            $product["upc"],
            (float) $product["shipping"],
            $product["description"],
            $product["manufacturer"],
            $product["model"],
            $product["url"],
            $product["image"]
            
        ]);

        // $stmt2 = $con->prepare("INSERT IGNORE INTO categories VALUES (?, ?)");
        // $stmt2->execute([
        //     $product["category"]["id"],
        //     $product["category"]["name"],
        // ]);

        foreach($product["category"] as $category) {

            $stmt2 = $con->prepare("INSERT IGNORE INTO categories VALUES (?, ?)");
            $stmt2->execute([
                $category["id"],
                $category["name"],
            ]);

            $stmt3 = $con->prepare("INSERT INTO product_categories VALUES (?, ?)");
            $stmt3->execute([
                $product["sku"],
                $category["id"]
            ]);
        }
    }