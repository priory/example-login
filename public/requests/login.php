<?php
require_once __DIR__.'/../../app/pdo.php';
require_once __DIR__.'/../../app/auth.php';
require_once __DIR__.'/../../app/middlewares/guest.php';
require_once __DIR__.'/../../app/login_handler.php';

// Check login attempts for IP-address
if (get_login_attempts_ip() > 2) {
    $_SESSION['errors'][] = 'Your IP-address has been locked out, please try again later';
    header('Location: /login.php');
    die;
}

// Check if parameters exist
$_POST['name'] ?? die;
$_POST['password'] ?? die;

// Attempt to select the requested user
$sth = $pdo->prepare('SELECT `id`, `name`, `password` FROM `users` WHERE `name` = :name');
$sth->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_OBJ);

// Check if the user exists
if (count($result) !== 1) {
    $_SESSION['errors'][] = 'Invalid credentials';
    header('Location: /login.php');
    log_login_attempt($_POST['name'], false);
    login_attempt_ip();
    die;
}

// Verify that the passwords match
if (password_verify($_POST['password'], $result[0]->password)) {
    // If the passowrds match, store the user in the session
    $_SESSION['user'] = [
        'id' => $result[0]->id,
        'name' => $result[0]->name,
    ];
    header('Location: /dashboard.php');
    log_login_attempt($_POST['name'], true);
    die;
}

// If anything fails, it will just invalidate the credentials
$_SESSION['errors'][] = 'Invalid credentials';
header('Location: /login.php');
log_login_attempt($_POST['name'], false);
login_attempt_ip();
die;