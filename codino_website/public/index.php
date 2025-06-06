<?php
// Codino - Main Entry Point

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php'; // For SITE_NAME, BASE_URL, TEMPLATES_PATH

\$request_uri = \$_SERVER['REQUEST_URI'];
\$script_name = \$_SERVER['SCRIPT_NAME'];
\$base_path_uri = str_replace('/index.php', '', \$script_name);
\$route_path = str_replace(\$base_path_uri, '', \$request_uri);
\$route_path = trim(\$route_path, '/');

// Basic query string router: index.php?route=myroute
\$route = \$_GET['route'] ?? DEFAULT_ROUTE;


// Simple router
switch (\$route) {
    case 'home':
        require_once TEMPLATES_PATH . '/home.php';
        break;
    case 'register':
        require_once TEMPLATES_PATH . '/auth/register.php';
        break;
    case 'login':
        require_once TEMPLATES_PATH . '/auth/login.php';
        break;
    case 'dashboard_home':
        require_once TEMPLATES_PATH . '/dashboard/index.php';
        break;
    case 'dashboard_profile':
        require_once TEMPLATES_PATH . '/dashboard/profile.php';
        break;
    case 'dashboard_websites':
        require_once TEMPLATES_PATH . '/dashboard/websites.php';
        break;
    default:
        // For now, just a simple message. Later, a proper 404 template.
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you requested for route '<strong>" . htmlspecialchars(\$route) . "</strong>' could not be found.</p>";
        echo "<p><a href='index.php?route=home'>Go to Homepage</a></p>";
        break;
}

?>
