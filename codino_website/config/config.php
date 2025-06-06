<?php
// Codino - Configuration File

// Database Configuration (MySQL/MariaDB)
define('DB_HOST', 'localhost'); // Replace with your DB host
define('DB_USER', 'codino_user');    // Replace with your DB username
define('DB_PASS', 'your_password'); // Replace with your DB password
define('DB_NAME', 'codino_db');    // Replace with your DB name

// Site Configuration
define('SITE_NAME', 'Codino');
define('BASE_URL', 'http://localhost/codino/public'); // Adjust if not in a subdir or using a different port

// Paths (already defined BASE_PATH in index.php, but could add more here)
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('SRC_PATH', BASE_PATH . '/src');

// Other settings
define('DEFAULT_ROUTE', 'home');

// Session settings (example)
// ini_set('session.cookie_lifetime', 0); // Session cookie lasts for the browser session
// ini_set('session.use_only_cookies', 1); // Use cookies only for sessions
// ini_set('session.cookie_httponly', 1); // HttpOnly flag for cookies
// ini_set('session.cookie_secure', isset(\$_SERVER['HTTPS'])); // Secure flag if HTTPS

?>
