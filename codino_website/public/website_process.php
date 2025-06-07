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
    \$_SESSION['flash_message'] = '[لطفا برای مدیریت وب سایت ها وارد شوید.]';
    \$_SESSION['flash_type'] = 'danger';
    header('Location: ' . BASE_URL . '/index.php?route=login');
    exit;
}

// Ensure this script is accessed via POST
if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
    \$_SESSION['flash_message'] = '[روش درخواست نامعتبر برای ثبت وب سایت.]';
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
    \$errors['domain_name'] = '[نام دامنه الزامی است.]';
} elseif (strlen(\$domain_name) > 255) {
    \$errors['domain_name'] = '[نام دامنه بیش از حد طولانی است (حداکثر ۲۵۵ کاراکتر).]';
} elseif (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\$/i', \$domain_name)) {
    // Basic domain validation regex - can be improved for more strict TLD checking if needed
    \$errors['domain_name'] = '[فرمت نام دامنه نامعتبر است.]';
}


// Website type (optional, length check if provided)
if (!empty(\$website_type) && strlen(\$website_type) > 50) {
    \$errors['website_type'] = '[نوع وب سایت بیش از حد طولانی است (حداکثر ۵۰ کاراکتر).]';
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
        \$_SESSION['flash_message'] = '[شما قبلا این دامنه را ثبت کرده اید: ]' . htmlspecialchars(\$domain_name);
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

    \$_SESSION['flash_message'] = '[وب سایت " ]' . htmlspecialchars(\$domain_name) . '" [با موفقیت ثبت شد!]';
    \$_SESSION['flash_type'] = 'success';
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;

} catch (PDOException \$e) {
    error_log("Database error during website registration: " . \$e->getMessage());
    \$_SESSION['flash_message'] = '[هنگام ثبت وب سایت یک خطای پایگاه داده رخ داد. لطفا بعدا دوباره تلاش کنید.]';
    \$_SESSION['flash_type'] = 'danger';
    \$_SESSION['form_old_input']['website_registration'] = \$old_input; // Preserve input
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;
} catch (Exception \$e) {
    error_log("General error during website registration: " . \$e->getMessage());
    \$_SESSION['flash_message'] = '[یک خطای غیر منتظره رخ داد. لطفا دوباره تلاش کنید.]';
    \$_SESSION['flash_type'] = 'danger';
    \$_SESSION['form_old_input']['website_registration'] = \$old_input; // Preserve input
    header('Location: ' . BASE_URL . '/index.php?route=dashboard_websites');
    exit;
}

?>
