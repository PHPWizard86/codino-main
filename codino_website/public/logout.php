<?php
// public/logout.php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/config/config.php'; // For BASE_URL

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
\$_SESSION = array();

// Destroy the session
if (ini_get("session.use_cookies")) {
    \$params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        \$params["path"], \$params["domain"],
        \$params["secure"], \$params["httponly"]
    );
}
session_destroy();

// Redirect to homepage or login page with a message
// For now, just redirect to home. A flash message could be set if desired.
header('Location: ' . BASE_URL . '/index.php?route=home');
exit;
?>
