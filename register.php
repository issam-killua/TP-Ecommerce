<?php

    session_start();
    include "init.php";

    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }



    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'],  FILTER_SANITIZE_EMAIL);
        $password = sha1($_POST['password']);
        $password2 = sha1($_POST['password2']);

        $formErrors = array();

        if (strlen($_POST['password']) < 5) {
            $formErrors[] = 'le mot de pass est petite';
        }

        if ($password != $password2) {
            $formErrors[] = 'Les mots de pass ne sont pas les mêmes';
        }

        // $stmt = $con->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        // $stmt->execute([
        //     $username,
        //     $email
        // ]);

        // $already_exists = $stmt->rowCount();

        // if ($already_exists > 0) {

        //     $formErros[] = "l'email ou le username choisie déja existe";

        // }

        if (empty($formErrors)) {
            $stmt2 = $con->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt2->execute([
                $username,
                $email,
                $password,

            ]);

            $_SESSION['account_created'] = "votre compte est créer vous pouvez s'authentifier";
            header("Location: index.php");

        }

        
    }

?>

<div class="jumbotron bg-info text-center text-light">
    <h3><i class="fa fa-plus"></i> Créer un compte</h3>
    <p>remplissez le formulaire ci dessous pour créer un compte</p>
</div>


<div class="card">
    <div class="card-header">
        Sign Up
    </div>
    <div class="card-body">
        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" >
        <div class="form-group row">
                <label for="inputUsername" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                <input name="username" type="text" class="form-control" id="inputUsername" placeholder="Username">
                </div>
            </div>
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
                <label for="inputPassword2" class="col-sm-2 col-form-label">Réecrire le Password</label>
                <div class="col-sm-10">
                <input name="password2" type="password" class="form-control" id="inputPassword2" placeholder="Password">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">créer le compte</button>
                </div>
            </div>
        </form>

        <?php
            if (!empty($formErrors)) {
                foreach($formErrors as $err) {
                    alert($err, 'danger');
                }
            }
        ?>

    </div>
</div>


<?php
    include $tpl . "footer.php";
?>