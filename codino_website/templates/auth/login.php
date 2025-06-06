<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/config/config.php';
if (session_status() == PHP_SESSION_NONE) session_start();
\$errors = \$_SESSION['errors'] ?? [];
\$old_input = \$_SESSION['old_input'] ?? [];
unset(\$_SESSION['errors']); unset(\$_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
     <style>
        .form-container { max-width: 500px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;}
        .form-container h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="email"], .form-group input[type="password"] { width: 95%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group .error-message { color: red; font-size: 0.9em; margin-top: 5px;}
        .form-group button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .form-group button:hover { background-color: #0056b3; }
        .alert { padding: 10px; margin-bottom:15px; border-radius:4px; }
        .alert-success { background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .alert-danger { background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    </style>
</head>
<body>
    <header>
        <h1><a href="<?php echo BASE_URL; ?>/index.php?route=home" style="color:white;text-decoration:none;"><?php echo SITE_NAME; ?></a></h1>
        <nav>
            <a href="<?php echo BASE_URL; ?>/index.php?route=home">Home</a>
            <?php if (isset(\$_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=register">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <h2>Login to Your Account</h2>
            <?php if (isset(\$_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo \$_SESSION['flash_type'] ?? 'info'; ?>">
                    <?php echo \$_SESSION['flash_message']; ?>
                </div>
                <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
            <?php endif; ?>
            <?php if (isset(\$errors['credentials'])): ?>
                <div class="alert alert-danger"><?php echo \$errors['credentials']; ?></div>
            <?php endif; ?>
            <form action="<?php echo BASE_URL; ?>/login_process.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars(\$old_input['email'] ?? ''); ?>" required>
                    <?php if (isset(\$errors['email'])): ?><p class="error-message"><?php echo \$errors['email']; ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (isset(\$errors['password'])): ?><p class="error-message"><?php echo \$errors['password']; ?></p><?php endif; ?>
                </div>
                <div class="form-group"><button type="submit">Login</button></div>
            </form>
            <p style="text-align:center; margin-top:15px;">Don't have an account? <a href="<?php echo BASE_URL; ?>/index.php?route=register">Register here</a>.</p>
        </div>
    </main>
    <footer><p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p></footer>
</body>
</html>
