<?php
// templates/dashboard/index.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
require_once BASE_PATH . '/config/config.php'; // For SITE_NAME, BASE_URL

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = 'Please log in to access the dashboard.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

\$user_name = htmlspecialchars(\$_SESSION['user_name'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <style>
        .dashboard-container { max-width: 960px; margin: 20px auto; padding: 20px; }
        .dashboard-nav ul { list-style-type: none; padding: 0; }
        .dashboard-nav ul li { margin-bottom: 10px; }
        .dashboard-nav ul li a { text-decoration: none; color: #007bff; font-size: 1.1em; }
        .dashboard-nav ul li a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1><a href="<?php echo BASE_URL; ?>/index.php?route=home" style="color:white;text-decoration:none;"><?php echo SITE_NAME; ?></a> - Dashboard</h1>
        <nav>
            <a href="<?php echo BASE_URL; ?>/index.php?route=home">Home</a>
            <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_profile">Profile</a>
            {# Add links to website and ticket management later #}
            <a href="<?php echo BASE_URL; ?>/logout.php">Logout (<?php echo \$user_name; ?>)</a>
        </nav>
    </header>

    <main class="dashboard-container">
        <h2>Welcome to Your Dashboard, <?php echo \$user_name; ?>!</h2>

        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo \$_SESSION['flash_type'] ?? 'info'; ?>" style="margin-bottom:20px; padding: 10px; border-radius: 4px;
                <?php if((\$_SESSION['flash_type'] ?? 'info') == 'success'): ?> background-color:#d4edda; color:#155724; border:1px solid #c3e6cb;
                <?php else: ?> background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; <?php endif; ?>
            ">
                <?php echo \$_SESSION['flash_message']; ?>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <p>This is your central hub for managing your account, websites, and support tickets.</p>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_profile">View/Edit Profile</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_websites">Manage My Websites</a></li>
                <li><a href="#">Manage Support Tickets (Coming Soon)</a></li>
            </ul>
        </nav>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
