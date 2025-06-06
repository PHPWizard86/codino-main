<?php
// public/website_process.php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/src/Core/Database.php';

use Codino\Core\Database;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check: Ensure user is logged in
if (!isset(\$_SESSION['user_id'])) {
    \$_SESSION['flash_message'] = 'Please log in to manage websites.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

// Ensure this script is accessed via POST
if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
    \$_SESSION['flash_message'] = 'Invalid request method for website registration.';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;
}

// --- Input Collection and Validation ---
\$user_id = \$_SESSION['user_id'];
\$domain_name = trim(\$_POST['domain_name'] ?? '');
\$website_type = trim(\$_POST['website_type'] ?? '');
\$description = trim(\$_POST['description'] ?? '');

\$errors = [];
\$old_input = [
    'domain_name' => \$domain_name,
    'website_type' => \$website_type,
    'description' => \$description
];

// Domain name validation
if (empty(\$domain_name)) {
    \$errors['domain_name'] = 'Domain name is required.';
} elseif (strlen(\$domain_name) > 255) {
    \$errors['domain_name'] = 'Domain name is too long (max 255 characters).';
} elseif (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\$/i', \$domain_name)) {
    // Basic domain validation regex - can be improved for more strict TLD checking if needed
    \$errors['domain_name'] = 'Invalid domain name format.';
}


// Website type (optional, length check if provided)
if (!empty(\$website_type) && strlen(\$website_type) > 50) {
    \$errors['website_type'] = 'Website type is too long (max 50 characters).';
}

// Description (optional, no specific validation other than trim for now)

if (!empty(\$errors)) {
    \$_SESSION['form_errors']['website_registration'] = \$errors;
    \$_SESSION['form_old_input']['website_registration'] = \$old_input;
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;
}

// --- Database Interaction ---
try {
    \$db = Database::getInstance()->getConnection();

    // Check if this user has already registered this domain name
    \$stmt_check = \$db->prepare("SELECT id FROM websites WHERE user_id = :user_id AND domain_name = :domain_name");
    \$stmt_check->bindParam(':user_id', \$user_id, PDO::PARAM_INT);
    \$stmt_check->bindParam(':domain_name', \$domain_name, PDO::PARAM_STR);
    \$stmt_check->execute();

    if (\$stmt_check->fetch()) {
        \$_SESSION['flash_message'] = 'You have already registered the domain: ' . htmlspecialchars(\$domain_name);
        \$_SESSION['flash_type'] = 'warning';
        \$_SESSION['form_old_input']['website_registration'] = \$old_input; // Preserve input
        header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
        exit;
    }

    // Insert new website
    \$sql = "INSERT INTO websites (user_id, domain_name, website_type, description) VALUES (:user_id, :domain_name, :website_type, :description)";
    \$stmt_insert = \$db->prepare(\$sql);

    \$stmt_insert->bindParam(':user_id', \$user_id, PDO::PARAM_INT);
    \$stmt_insert->bindParam(':domain_name', \$domain_name, PDO::PARAM_STR);

    // Bind optional fields carefully
    \$wt = !empty(\$website_type) ? \$website_type : null;
    \$desc = !empty(\$description) ? \$description : null;
    \$stmt_insert->bindParam(':website_type', \$wt, PDO::PARAM_STR);
    \$stmt_insert->bindParam(':description', \$desc, PDO::PARAM_STR);

    \$stmt_insert->execute();

    \$_SESSION['flash_message'] = 'Website "' . htmlspecialchars(\$domain_name) . '" registered successfully!';
    \$_SESSION['flash_type'] = 'success';
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;

} catch (PDOException \$e) {
    error_log("Database error during website registration: " . \$e->getMessage());
    \$_SESSION['flash_message'] = 'A database error occurred while registering the website. Please try again later.';
    \$_SESSION['flash_type'] = 'danger';
    \$_SESSION['form_old_input']['website_registration'] = \$old_input; // Preserve input
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;
} catch (Exception \$e) {
    error_log("General error during website registration: " . \$e->getMessage());
    \$_SESSION['flash_message'] = 'An unexpected error occurred. Please try again.';
    \$_SESSION['flash_type'] = 'danger';
    \$_SESSION['form_old_input']['website_registration'] = \$old_input; // Preserve input
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;
}

?>
