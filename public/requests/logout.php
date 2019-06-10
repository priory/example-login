<?php
require_once __DIR__.'/../../app/pdo.php';
require_once __DIR__.'/../../app/auth.php';
require_once __DIR__.'/../../app/middlewares/user.php';

session_destroy();

header('Location: /');
