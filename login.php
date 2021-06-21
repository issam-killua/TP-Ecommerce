<?php

session_start();
include "init.php";

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}


//

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // unset($_SESSION['ID']);
    // unset($_SESSION['username']);
    
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = sha1($_POST['password']);

    $stmt = $con->prepare("SELECT id, username FROM users WHERE email = ? AND password = ? ");
    $stmt->execute([
        $email,
        $password
    ]);

    $count = $stmt->rowCount();
    $row = $stmt->fetch();
    
    $loginErrors = array();

    if ($count > 0) {
        $_SESSION['user'] = [
            $row['id'],
            $row['username']
        ];
        header('Location: index.php');
        exit();

    } else {
        $loginErrors[] = 'les informations sont incorrectes';
    }
    
}


?>

<div class="jumbotron bg-info text-center text-light">
    <h3><i class="fa fa-user"></i> Page d'authentification</h3>
    <p>remplissez le formulaire ci dessous pour s'authentifier</p>
</div>


<div class="card">
    <div class="card-header">
        Login
    </div>
    <div class="card-body">
        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                <input name="email" type="email" class="form-control" id="inputEmail" placeholder="Email">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                <input name="password" type="password" class="form-control" id="inputPassword" placeholder="Password">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">s'authentifier</button>
                </div>
            </div>
        </form>

        <?php
            if (!empty($loginErrors)) {
                foreach($loginErrors as $err) {
                    alert($err, 'danger');
                }
            }
        ?>

    </div>
</div>

<?php

include $tpl . "footer.php";

?>