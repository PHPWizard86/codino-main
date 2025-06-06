<?php
// Codino - Main Entry Point

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php'; // For SITE_NAME, BASE_URL, TEMPLATES_PATH

// --- Routing Logic ---
\$request_uri = \$_SERVER['REQUEST_URI'] ?? '';
\$script_name = \$_SERVER['SCRIPT_NAME'] ?? '';

// Calculate the base path of the URI if the script is not in the web root
\$base_uri_path = str_replace('/index.php', '', \$script_name);
\$path_to_match = \$base_uri_path;

// If the request URI starts with the base URI path, remove it to get the clean route
if (strpos(\$request_uri, \$path_to_match) === 0) {
    \$route_param_part = substr(\$request_uri, strlen(\$path_to_match));
} else {
    \$route_param_part = \$request_uri;
}

// Parse the query string part to get the 'route' parameter
// Example: /codino/public/index.php?route=login  or /codino/public/?route=login
\$query_string = parse_url(\$route_param_part, PHP_URL_QUERY);
parse_str(\$query_string ?? '', \$query_params);
\$route = \$query_params['route'] ?? DEFAULT_ROUTE;
// --- End Routing Logic ---


// Simple router based on the 'route' GET parameter
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
    // Add other cases for dashboard tickets etc. later
    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you requested for route '<strong>" . htmlspecialchars(\$route) . "</strong>' could not be found.</p>";
        echo "<p><a href='" . htmlspecialchars(BASE_URL) . "/index.php?route=home'>Go to Homepage</a></p>";
        break;
}

?>
