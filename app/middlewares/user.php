<?php
require_once __DIR__.'/../auth.php';

if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    die;
}