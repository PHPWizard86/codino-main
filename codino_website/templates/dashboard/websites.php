<?php
// templates/dashboard/websites.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/src/Core/Database.php'; // To fetch websites

use Codino\Core\Database;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = 'Please log in to manage your websites.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

\$user_id = \$_SESSION['user_id'];
\$user_name = htmlspecialchars(\$_SESSION['user_name'] ?? 'User');

// Fetch user's registered websites
\$user_websites = [];
try {
    \$db = Database::getInstance()->getConnection();
    \$stmt = \$db->prepare("SELECT id, domain_name, website_type, description, DATE_FORMAT(registration_date, '%Y-%m-%d') as formatted_reg_date FROM websites WHERE user_id = :user_id ORDER BY registration_date DESC");
    \$stmt->bindParam(':user_id', \$user_id, PDO::PARAM_INT);
    \$stmt->execute();
    \$user_websites = \$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception \$e) {
    error_log("Error fetching user websites: " . \$e->getMessage());
    // Set a flash message or handle error display on the page
    \$_SESSION['flash_message'] = 'Could not retrieve your websites due to a database error.';
    \$_SESSION['flash_type'] = 'danger';
}

// For the form
\$form_errors = \$_SESSION['form_errors']['website_registration'] ?? [];
\$form_old_input = \$_SESSION['form_old_input']['website_registration'] ?? [];
unset(\$_SESSION['form_errors']['website_registration']);
unset(\$_SESSION['form_old_input']['website_registration']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Websites - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <style>
        .dashboard-container { max-width: 960px; margin: 20px auto; padding: 20px; }
        .form-container { max-width: 600px; margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;}
        .form-container h3 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group textarea {
            width: 95%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;
        }
        .form-group textarea { min-height: 80px; }
        .form-group .error-message { color: red; font-size: 0.9em; margin-top: 5px;}
        .form-group button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .form-group button:hover { background-color: #218838; }
        .alert { padding: 10px; margin-bottom:15px; border-radius:4px; }
        .alert-success { background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .alert-danger { background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .websites-list { margin-top: 30px; }
        .websites-list table { width: 100%; border-collapse: collapse; }
        .websites-list th, .websites-list td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .websites-list th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <header>
        <h1><a href="<?php echo BASE_URL; ?>/index.php?route=home" style="color:white;text-decoration:none;"><?php echo SITE_NAME; ?></a> - Dashboard</h1>
        <nav>
            <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_home">Dashboard Home</a>
            <a href="<?php echo BASE_URL; ?>/index.php?route=dashboard_profile">Profile</a>
            <a href="<?php echo BASE_URL; ?>/logout.php">Logout (<?php echo \$user_name; ?>)</a>
        </nav>
    </header>

    <main class="dashboard-container">
        <h2>Manage Your Websites</h2>

        <?php if (isset(\$_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo \$_SESSION['flash_type'] ?? 'info'; ?>" style="margin-bottom:20px;">
                <?php echo \$_SESSION['flash_message']; ?>
            </div>
            <?php unset(\$_SESSION['flash_message']); unset(\$_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="form-container">
            <h3>Register a New Website</h3>
            <form action="<?php echo BASE_URL; ?>/website_process.php" method="POST">
                <div class="form-group">
                    <label for="domain_name">Domain Name (e.g., example.com)</label>
                    <input type="text" id="domain_name" name="domain_name" value="<?php echo htmlspecialchars(\$form_old_input['domain_name'] ?? ''); ?>" required>
                    <?php if (isset(\$form_errors['domain_name'])): ?><p class="error-message"><?php echo \$form_errors['domain_name']; ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="website_type">Website Type (e.g., Blog, Store, Corporate)</label>
                    <input type="text" id="website_type" name="website_type" value="<?php echo htmlspecialchars(\$form_old_input['website_type'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description or Notes</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars(\$form_old_input['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Register Website</button>
                </div>
            </form>
        </div>

        <div class="websites-list">
            <h3>Your Registered Websites</h3>
            <?php if (!empty(\$user_websites)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Domain Name</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Registered On</th>
                            {# <th>Actions</th> #}
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (\$user_websites as \$website): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(\$website['domain_name']); ?></td>
                                <td><?php echo htmlspecialchars(\$website['website_type'] ?: 'N/A'); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(\$website['description'] ?: 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars(\$website['formatted_reg_date']); ?></td>
                                {# <td><a href="#">Edit</a> | <a href="#" onclick="return confirm('Are you sure?')">Delete</a></td> #}
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have not registered any websites yet.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
