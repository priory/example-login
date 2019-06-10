<?php
    require_once __DIR__.'/../app/auth.php';
    require_once __DIR__.'/../app/middlewares/user.php';
?>

<html>
    <head>
    </head>
    <body>
        <?php require __DIR__.'/../resources/layouts/header.php' ?>
        <h2>You are logged in</h2>
    </body>
</html>