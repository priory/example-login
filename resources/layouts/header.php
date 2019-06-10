<?php
    require_once __DIR__.'/../../app/auth.php';
?>

<header>
    <div style="width: 100%; border: 1px solid black;">
        <a href="/">Home</a>
    <?php if ($user): ?>
        <a href="/dashboard.php">Dashboard</a>
        <span>Welcome, <?=$user['name']?> <button type="button" onclick="window.logout.submit();">Logout</button></span>
    <?php else: ?>
        <a href="./login.php">Login</a>
        <a href="./register.php">Register</a>
    <?php endif ?>
    </div>

    <form name="logout" method="post" action="../requests/logout.php" onsubmit="return false;">
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
    </form>
</header>

