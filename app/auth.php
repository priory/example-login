<?php
require_once __DIR__.'/../app/error_handler.php';

session_start();

// Create user variable if logged in
$user = $_SESSION['user'] ?? null;

// Get errors from previous state
$errors = $_SESSION['errors'] ?? [];

// Generate a CSRF token
$_SESSION['csrf_token'] ?? $_SESSION['csrf_token'] = hash('sha256', uniqid(rand(), true));

// Verify the CSRF token on a non-GET request
if (($_POST['csrf_token'] ?? null) != ($_SESSION['csrf_token'] ?? true) && ($_SERVER['REQUEST_METHOD'] !== 'GET')) {
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    trigger_error('CSRF Token mismatch from IP-address: '.$ip.' Request headers: '.print_r(getallheaders(), true).' Request body: '.print_r($_POST, true), E_USER_WARNING);
    die;
}

// Clear the errors from session for the next state
$_SESSION['errors'] = [];
