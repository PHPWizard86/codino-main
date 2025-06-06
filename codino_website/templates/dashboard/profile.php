<?php
// templates/dashboard/profile.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
require_once BASE_PATH . '/config/config.php'; // For SITE_NAME, BASE_URL
require_once BASE_PATH . '/src/Core/Database.php'; // To fetch fresh user data if needed

use Codino\Core\Database;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = 'Please log in to access your profile.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

// Fetch user data from DB for up-to-date info (optional, session data can also be used)
\$user = null;
try {
    \$db = Database::getInstance()->getConnection();
    \$stmt = \$db->prepare("SELECT u.name, u.email, u.phone_number, p.name as plan_name
                           FROM users u
                           LEFT JOIN plans p ON u.plan_id = p.id
                           WHERE u.id = :user_id");
    \$stmt->bindParam(':user_id', \$_SESSION['user_id'], PDO::PARAM_INT);
    \$stmt->execute();
    \$user = \$stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception \$e) {
    error_log("Error fetching user profile data: " . \$e->getMessage());
    // Handle error, maybe redirect or show a message
}

if (!\$user) {
    // This case should ideally not happen if user_id in session is valid
    \$_SESSION['flash_message'] = 'Could not retrieve your profile data.';
    \$_SESSION['flash_type'] = 'danger';
    // Potentially log out user or redirect to dashboard home
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_home');
    exit;
}

\$user_name_display = htmlspecialchars(\$user['name'] ?? 'N/A');
\$user_email_display = htmlspecialchars(\$user['email'] ?? 'N/A');
\$user_phone_display = htmlspecialchars(\$user['phone_number'] ?? 'N/A');
\$user_plan_display = htmlspecialchars(\$user['plan_name'] ?? 'No active plan');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <style>
        .profile-container { max-width: 700px; margin: 20px auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;}
        .profile-details { margin-top: 20px; }
        .profile-details p { font-size: 1.1em; margin-bottom: 10px; padding: 8px; border-bottom: 1px solid #eee; }
        .profile-details strong { display: inline-block; width: 150px; color: #333; }
        /* Edit button styling (placeholder for now) */
        .edit-profile-btn {
            display: inline-block; padding: 10px 15px; margin-top:20px;
            background-color: #007bff; color: white; text-decoration: none;
            border-radius: 4px; border: none; cursor: pointer;
        }
        .edit-profile-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <header>
        <h1><a href="<?php echo BASE_URL; ?>/index.php?route=home" style="color:white;text-decoration:none;"><?php echo SITE_NAME; ?></a> - User Profile</h1>
        <nav>
            <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">Dashboard Home</a>
            <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
        </nav>
    </header>

    <main class="profile-container">
        <h2>Your Profile Information</h2>

        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo \$_SESSION['flash_type'] ?? 'info'; ?>" style="margin-bottom:20px; padding: 10px; border-radius: 4px;
                 <?php if((\$_SESSION['flash_type'] ?? 'info') == 'success'): ?> background-color:#d4edda; color:#155724; border:1px solid #c3e6cb;
                 <?php else: ?> background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; <?php endif; ?>
            ">
                <?php echo \$_SESSION['flash_message']; ?>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo \$user_name_display; ?></p>
            <p><strong>Email:</strong> <?php echo \$user_email_display; ?></p>
            <p><strong>Phone Number:</strong> <?php echo \$user_phone_display; ?></p>
            <p><strong>Subscription Plan:</strong> <?php echo \$user_plan_display; ?></p>
        </div>

        {# Placeholder for edit profile form/link #}
        <button class="edit-profile-btn" onclick="alert('Edit profile functionality coming soon!');">Edit Profile</button>
        <button class="edit-profile-btn" onclick="alert('Change password functionality coming soon!');">Change Password</button>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
