<?php
require_once __DIR__.'/error_handler.php';

$dbname = 'pdo';
$user = 'root';
$password = 'qwe123';

$dsn = 'mysql:dbname='.$dbname.';host=localhost';

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: '.$e->getMessage();
}