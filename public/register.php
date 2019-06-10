<?php
    require_once __DIR__.'/../app/auth.php';
    require_once __DIR__.'/../app/middlewares/guest.php';
?>

<html>
    <head>
    </head>
    <body>
        <?php require __DIR__.'/../resources/layouts/header.php' ?>
        <form method="post" action="../requests/register.php">
            <div class="errors">
                <?php
                    foreach ($errors as $v) {
                        echo '<div>'.$v.'</div>';
                    }
                ?>
            </div>
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
            <label>name: </label>
            <input type="text" name="name"><br>
            <label>password: </label>
            <input type="password" name="password"><br>
            <label>password confirmation: </label>
            <input type="password" name="password_confirmation"><br>
            <button type="submit">Register</button>
        </form>
    </body>
</html>
