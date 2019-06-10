<?php
require_once __DIR__.'/../../app/pdo.php';
require_once __DIR__.'/../../app/auth.php';
require_once __DIR__.'/../../app/middlewares/guest.php';

// Check if parameters exist
$_POST['name'] ?? die;
$_POST['password'] ?? die;
$_POST['password_confirmation'] ?? die;

// Check if the name doesn't start with a number
preg_match('/^[0-9].*$/', $_POST['name'], $matches);
if (count($matches) !== 0) $_SESSION['errors'][] = 'Name can\'t start with a number';

// Check if the name doesn't contain any symbols
preg_match('/^[[:alnum:]]*$/', $_POST['name'], $matches);
if ($_POST['name'] !== ($matches[0] ?? null)) $_SESSION['errors'][] = 'Name can only contain letters and numbers';

// Check if the name doesn't contain three of the same characters
preg_match('/(.)\1\1/', $_POST['name'], $matches);
if (count($matches) !== 0) $_SESSION['errors'][] = 'Name can\'t contain more than three consecutive same characters';

// Check if the name is atleast three characters long
if (strlen($_POST['name']) < 3) $_SESSION['errors'][] = 'Name must atleast be 3 characters long';

// Check if the name is not longer than 32 characters
if (strlen($_POST['name']) > 32) $_SESSION['errors'][] = 'Password can\'t be longer than 255 characters';

// Check if the password doesn't contain any non-ASCII printable characters
preg_match('/[\x20-\xa2]*/', $_POST['password'], $matches);
if ($_POST['password'] !== ($matches[0] ?? null)) $_SESSION['errors'][] = 'Password can only contain letters, numbers and symbols';

// Check if the password doesn't contain three of the same characters
preg_match('/(.)\1\1/', $_POST['password'], $matches);
if (count($matches) !== 0) $_SESSION['errors'][] = 'Password can\'t contain more than three consecutive same characters';

// Check if the password contains atleast one lowercase letter
preg_match('/(?:.*[a-z].*)/', $_POST['password'], $matches);
if (count($matches) === 0) $_SESSION['errors'][] = 'Password must atleast contain one lowercase letter';

// Check if the password contains atleast one uppercase letter
preg_match('/(?:.*[A-Z].*)/', $_POST['password'], $matches);
if (count($matches) === 0) $_SESSION['errors'][] = 'Password must atleast contain one uppercase letter';

// Check if the password contains atleast one uppercase letter
preg_match('/(?:.*[0-9].*)/', $_POST['password'], $matches);
if (count($matches) === 0) $_SESSION['errors'][] = 'Password must atleast contain one number';

// Check if the password contains atleast one symbol
preg_match('/(?:.*[\x20-\x2f\x3a-\x40\x5b-\x60\x7b-\x7e].*)/', $_POST['password'], $matches);
if (count($matches) === 0) $_SESSION['errors'][] = 'Password must atleast contain one symbol';

// Check if the password is atleast twelve characters long
if (strlen($_POST['password']) < 12) $_SESSION['errors'][] = 'Password must be atleast 12 characters long';

// Check if the password is not longer than 255 characters
if (strlen($_POST['password']) > 255) $_SESSION['errors'][] = 'Password can\'t be longer than 255 characters';

// Check if the password matches the password confirmation
if ($_POST['password'] !== $_POST['password_confirmation']) $_SESSION['errors'][] = 'Passwords doesn\'t match the password confirmation';

// Check if the name is unique
$sth = $pdo->prepare('SELECT COUNT(`id`) as \'count\' FROM users WHERE `name` = :name');
$sth->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
$sth->execute();

if (!((int)$sth->fetch(PDO::FETCH_OBJ)->count === 0)) {
    $_SESSION['errors'][] = 'Name is already taken';
}

if ($sth->errorInfo()[1]) {
    trigger_error('MySQL error', E_USER_ERROR);
    log_mysql_error($sth);
    die;
}

// Redirect back if there are any errors
if ($_SESSION['errors']) {
    header('Location: /register.php');
    die;
}

// Insert registering user in the database
$sth = $pdo->prepare('INSERT INTO `users`(`name`, `password`) VALUES (:name, :password)');
$sth->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
$sth->bindValue(':password', password_hash($_POST['password'], PASSWORD_BCRYPT), PDO::PARAM_STR);

$sth->execute();

if ($sth->errorInfo()[1]) {
    trigger_error('MySQL error', E_USER_ERROR);
    log_mysql_error($sth);
    die;
}

// Redirect to login if the user is registered
header('Location: /login.php');
