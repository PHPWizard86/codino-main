<?php
// public/login_process.php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/src/Core/Database.php';

use Codino\Core\Database;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure this script is accessed via POST
if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
    \$_SESSION['flash_message'] = 'Invalid request method.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

// --- Input Validation ---
\$email = trim(\$_POST['email'] ?? '');
\$password = \$_POST['password'] ?? '';

\$errors = [];
\$old_input = ['email' => \$email];

if (empty(\$email)) {
    \$errors['email'] = 'Email is required.';
} elseif (!filter_var(\$email, FILTER_VALIDATE_EMAIL)) {
    \$errors['email'] = 'Invalid email format.';
}

if (empty(\$password)) {
    \$errors['password'] = 'Password is required.';
}

if (!empty(\$errors)) {
    \$_SESSION['errors'] = \$errors;
    \$_SESSION['old_input'] = \$old_input;
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

// --- Database Interaction & Authentication ---
try {
    \$db = Database::getInstance()->getConnection();

    // Fetch user by email
    // Consider also allowing login by phone_number later if desired
    \$stmt = \$db->prepare("SELECT id, name, email, password_hash, plan_id FROM users WHERE email = :email LIMIT 1");
    \$stmt->bindParam(':email', \$email);
    \$stmt->execute();
    \$user = \$stmt->fetch(PDO::FETCH_ASSOC);

    if (\$user && password_verify(\$password, \$user['password_hash'])) {
        // Password is correct, set session variables
        \$_SESSION['user_id'] = \$user['id'];
        \$_SESSION['user_email'] = \$user['email'];
        \$_SESSION['user_name'] = \$user['name'] ?? 'User'; // Use name or a default
        \$_SESSION['user_plan_id'] = \$user['plan_id']; // Store plan_id for quick access

        // Regenerate session ID for security (session fixation prevention)
        session_regenerate_id(true);

        // Redirect to dashboard (placeholder - dashboard route/page not yet created)
        \$_SESSION['flash_message'] = 'Login successful! Welcome back, ' . htmlspecialchars(\$_SESSION['user_name']) . '.';
        \$_SESSION['flash_type'] = 'success';
        header('Location: ' . BASE_URL . '/index.php?route=dashboard_home'); // Target dashboard route
        exit;
    } else {
        // Invalid credentials
        \$errors['credentials'] = 'Invalid email or password.';
        \$_SESSION['errors'] = \$errors;
        \$_SESSION['old_input'] = \$old_input;
        header('Location: ' . BASE_URL . '/index.php?route=login');
        exit;
    }

} catch (PDOException \$e) {
    error_log("Database error during login: " . \$e->getMessage());
    \$_SESSION['errors'] = ['credentials' => 'A database error occurred. Please try again later.'];
    \$_SESSION['old_input'] = \$old_input;
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
} catch (Exception \$e) {
    error_log("General error during login: " . \$e->getMessage());
    \$_SESSION['errors'] = ['credentials' => 'An unexpected error occurred. Please try again.'];
    \$_SESSION['old_input'] = \$old_input;
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

?>
