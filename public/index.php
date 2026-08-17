<?php
/**
 * Front Controller - Entry point for all routes
 * 
 * This file handles routing for the application using clean URLs
 * with simple path-based routing.
 */

session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================
// AUTOLOADING
// ==========================================
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = __DIR__ . '/../src/';
    
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ==========================================
// ROUTE HANDLER
// ==========================================
function route($path, $method = 'GET') {
    // Remove query string
    $path = strtok($path, '?');
    
    // Define routes
    $routes = [
        '/' => ['controller' => 'DashboardController', 'action' => 'index'],
        '/dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
        '/application/request' => ['controller' => 'ApplicationController', 'action' => 'request'],
        '/application/status' => ['controller' => 'ApplicationController', 'action' => 'status'],
        '/waiting-list' => ['controller' => 'WaitingListController', 'action' => 'index'],
        '/offer/respond' => ['controller' => 'OfferController', 'action' => 'respond'],
        '/notifications' => ['controller' => 'NotificationController', 'action' => 'index'],
        '/profile/edit' => ['controller' => 'ProfileController', 'action' => 'edit'],
        '/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    ];
    
    // Handle view paths (PHP files directly)
    if (strpos($path, '.php') !== false) {
        require_once __DIR__ . '/../src/views' . $path;
        return;
    }
    
    // Match route
    if (isset($routes[$path])) {
        $route = $routes[$path];
        $controllerClass = $route['controller'];
        $action = $route['action'];
        
        $controllerFile = __DIR__ . '/../src/controllers/' . $controllerClass . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerClass();
            
            // Handle AJAX requests
            if ($method === 'POST' && isset($_POST['ajax_action'])) {
                if (method_exists($controller, $action)) {
                    return $controller->$action();
                }
            }
            
            // Render view based on action
            $viewMap = [
                'DashboardController' => [
                    'index' => '/dashboard/index.php'
                ],
                'ApplicationController' => [
                    'request' => '/dashboard/request_quarters.php',
                    'status' => '/dashboard/view_status.php'
                ],
                'WaitingListController' => [
                    'index' => '/dashboard/waiting_list.php'
                ],
                'OfferController' => [
                    'respond' => '/dashboard/view_offers.php'
                ],
                'NotificationController' => [
                    'index' => '/notifications/notification.php'
                ],
                'ProfileController' => [
                    'edit' => '/profile/edit_profile.php'
                ]
            ];
            
            if (isset($viewMap[$controllerClass][$action])) {
                $viewPath = __DIR__ . '/../src/views' . $viewMap[$controllerClass][$action];
                if (file_exists($viewPath)) {
                    require_once $viewPath;
                    return;
                }
            }
            
            // Fallback: call controller action directly
            if (method_exists($controller, $action)) {
                return $controller->$action();
            }
        }
    }
    
    // 404 - Not Found
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
    echo '<p>The page you are looking for does not exist.</p>';
}

// ==========================================
// EXECUTE ROUTING
// ==========================================
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove base path if running in subdirectory
$basePath = '/applicants_dashboard/public';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}
if (empty($requestUri)) $requestUri = '/';

route($requestUri, $requestMethod);
?>