<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Home</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to <?php echo SITE_NAME; ?> (PHP Version)</h1>
        <nav>
            <a href="<?php echo BASE_URL; ?>/index.php?route=home">Home</a>
            <?php if (isset(\$_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/logout.php">Logout (<?php echo htmlspecialchars(\$_SESSION['user_name']); ?>)</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=login">Login</a>
                <a href="<?php echo BASE_URL; ?>/index.php?route=register">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo \$_SESSION['flash_type'] ?? 'info'; ?>" style="max-width: 920px; margin: 0 auto 20px auto; padding: 10px; border-radius: 4px; text-align:center;
                <?php if((\$_SESSION['flash_type'] ?? 'info') == 'success'): ?> background-color:#d4edda; color:#155724; border:1px solid #c3e6cb;
                <?php else: ?> background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; <?php endif; ?>
            ">
                <?php echo \$_SESSION['flash_message']; ?>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <p>This is the homepage of the Codino application, built with pure PHP.</p>
        <p>Our goal is to provide manual and personalized technical support for developers, programmers, and website owners.</p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
