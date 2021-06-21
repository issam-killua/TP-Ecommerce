<?php
    session_start();
    include "init.php";
?>

<?php

    $step = 3;
    
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }

    $stmt = $con->prepare("SELECT * FROM products LIMIT $step OFFSET $page");

    if (isset($_GET['ordre'])) {
        if ($_GET['ordre'] == 'asc') {
            $stmt = $con->prepare("SELECT * FROM products ORDER BY price ASC LIMIT $step OFFSET $page");
        } elseif ($_GET['ordre'] == 'desc') {
            $stmt = $con->prepare("SELECT * FROM products ORDER BY price DESC LIMIT $step OFFSET $page");
        }
    }


    $stmt->execute();
    $products = $stmt->fetchAll();

    // categories

    $cat_stmt = $con->prepare("SELECT * FROM categories LIMIT 10");
    $cat_stmt->execute();
    $categories = $cat_stmt->fetchAll();

    if (isset($_GET['categoryID'])) {
        $cat_id = $_GET['categoryID'];
        $stmt = $con->prepare("SELECT * FROM products WHERE sku IN (SELECT product_sku FROM product_categories WHERE category_id = ? )
        LIMIT $step OFFSET $page
        ");
        $stmt->execute([$cat_id]);
        $products = $stmt->fetchAll();
    } else {
        $cat_id = "";
    }

?>

<div class="jumbotron bg-info text-center text-light">
    <h3><i class="fa fa-shopping-bag"></i> Boutique</h3>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugit, ab!</p>
</div>

<?php

    if (isset($_SESSION['account_created'])) {
        alert($_SESSION['account_created'], 'success');
        unset($_SESSION['account_created']);
    }

?>


    <button data-toggle='modal' data-target='#cart' class='btn btn-success' ><i class='fa fa-shopping-bag' ></i> mon panier</button>



            <!-- Modal -->
            <div class="modal fade" id="cart" tabindex="-1" role="dialog" aria-hidden="true">
            <div style="width:90%" class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Mon panier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                            <th scope="col">#name</th>
                            <th scope="col">Quantité</th>
                            <th scope="col">supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                                if (isset($_SESSION['cart'])) {
                                    $cart_products = $_SESSION['cart'];

                                    foreach($_SESSION['cart'] as $product) {
                                        echo '
                                        <tr>
                                            <td>'.$product['name'].'</td>
                                            <td>'.$product['quantity'].'</td>
                                            <td><a class="remove-from-cart" href="panier.php?delete='.$product['sku'].'" ><i class="fa fa-trash" ></i></a></td>
                                        </tr>
                                        ';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                    <?php
                        if (!isset($_SESSION['user'])) {
                            alert('vous devez <a href="login.php" >s\'authentifier</a> afin d\'acheter', 'warning');
                        } else {


                                ?>

                                <form action="checkout.php" method="POST">
                                    <button class=" payer btn btn-success" type="submit" ><i class="fa fa-money-bill-alt"></i> payer la facture totale</button>
                                </form>

                            <?php

                        }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>


<hr>
<div class="row">
    <div class="col-md-3">
        <div class="sidebar">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-tags"></i> Categories
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                            foreach($categories as $cat) {
                                $count_stmt = $con->prepare("SELECT COUNT(*) FROM product_categories WHERE category_id = ?");
                                $count_stmt->execute([$cat['id']]);
                                $number = $count_stmt->fetch();
                                echo '<li><a href="?categoryID='.$cat['id'].'">'.$cat['name'].'</a><span class="badge badge-success">'.$number[0].'</span></li>';
                            }
                        ?>
                    
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
    
        <div class="items"> 
            <div class="row">
                <div class="col-md-6 text-left"><a class='btn btn-info' href="index.php?ordre=asc"><i class="fa fa-arrow-up" ></i> tris asc</a></div>
                <div class="col-md-6 text-right"><a class='btn btn-info' href="index.php?ordre=desc"><i class="fa fa-arrow-down" ></i> tris desc</a></div>
            </div>
            <h4 class="text-center" >les produits</h4>
            <hr>
                <?php
                    foreach($products as $product) {
                        $features = explode(';', $product['description']);
                        ?>

                    <!-- Modal -->
                    <div class="modal fade" id="cartForm<?php echo $product['sku'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formModalLabel">l'ajout au panier</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="panier.php?ajouter=<?php echo $product['sku'] ?>">
                                <div class="form-group">
                                    <label for="quantity">Quantité voulue</label>
                                    <input name="quantity" type="text" class="form-control" id="quantity" placeholder="3">
                                    <input name="name" type="hidden" value="<?php echo $product['name'] ?>">
                                    <input name="sku" type="hidden" value="<?php echo $product['sku'] ?>">
                                    <input name="price" type="hidden" value="<?php echo $product['price'] ?>">
                                </div>
                                <button type="submit" class="add btn btn-primary">Ajouter</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        </div>
                    </div>
                    </div>



                        <div class="item">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="<?php echo $product['image'] ?>" alt="">
                                </div>
                                <div class="col-md-9">
                                    <h5><?php echo $product['name'] ?></h5>
                                    <ul class="features" >
                                    <?php
                                        foreach($features as $feature) {
                                            echo "<li>";
                                            echo $feature;
                                            echo "</li>";
                                        }
                                    ?>
                                    </ul>
                                    <div class="categories">
                                        <?php
                                            $stmt2 = $con->prepare("SELECT product_sku, category_id, name FROM
                                                product_categories
                                                INNER JOIN categories ON product_categories.category_id = categories.id
                                                WHERE product_sku = ?
                                            ");
                                            $stmt2->execute([$product['sku']]);
                                            $categories = $stmt2->fetchAll();
                                        ?>
                                        <i class="fa fa-tags"></i> categories 
                                        <ul class="list-group list-group-horizontal-sm">
                                            <?php
                                                foreach($categories as $cat) {
                                                    echo '<li class="list-group-item">'.$cat['name'].'</li>';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                    <span class="badge badge-success"><?php echo $product['type'] ?></span>
                                    <div class="row text-center info">
                                        <div class="col-md-4">
                                            <div class="price">
                                                <?php echo $product['price'] ?> <i class="fa fa-dollar-sign"></i> 
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <a data-toggle="modal" data-target="#cartForm<?php echo $product['sku'] ?>" href="" class="btn btn-warning"><i class="fa fa-cart-plus"></i> Ajouter aux panier</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
        </div>
        
        <div class="pagination">
            <div class="col-md-6 text-center ">
                <?php 
                    if ($page > 2) {
                        ?>
                            <a href="?page=<?php echo $page - 1 ?>"><i class="fa fa-arrow-left"></i></a>
                        <?php
                    }
                ?>
            </div>
            <?php
                if ($cat_id == "") {
                    ?>
                            <div class="col-md-6 text-center"><a href="?page=<?php echo $page + 1 ?>"><i class="fa fa-arrow-right"></i></a></div>

                    <?php
                } else {
                    ?>
                            <div class="col-md-6 text-center"><a href="?page=<?php echo $page + 1 ?>&categoryID=<?php echo $cat_id ?>"><i class="fa fa-arrow-right"></i></a></div>
                    <?php
                }
            ?>
        </div>

    </div>
</div>

<?php

include $tpl . "footer.php";
?>