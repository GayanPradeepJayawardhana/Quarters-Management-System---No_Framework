<?php
/**
 * Front Controller - Entry point for all routes
 */

session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================
// LOAD CONFIGURATION
// ==========================================
require_once __DIR__ . '/../config/config.php';

// ==========================================
// AUTOLOADING
// ==========================================
spl_autoload_register(function ($class) {
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
    
    // Remove base path if running in subdirectory
    if (strpos($path, BASE_URL) === 0) {
        $path = substr($path, strlen(BASE_URL));
    }
    
    // Remove /public from path if present
    if (strpos($path, '/public') === 0) {
        $path = substr($path, 7);
    }
    
    if (empty($path) || $path === '/') {
        $path = '/dashboard';
    }
    
    // Define routes - map URL paths to view files
    $routes = [
        '/dashboard' => '/dashboard/index.php',
        '/application/request' => '/dashboard/request_quarters.php',
        '/application/status' => '/dashboard/view_status.php',
        '/waiting-list' => '/dashboard/waiting_list.php',
        '/offer/respond' => '/dashboard/view_offers.php',
        '/notifications' => '/notifications/notification.php',
        '/profile/edit' => '/profile/edit_profile.php',
        '/login' => '/auth/login.php',
        '/logout' => '/auth/logout.php',
    ];
    
    // Check if path is in routes
    if (isset($routes[$path])) {
        $viewPath = __DIR__ . '/../src/views' . $routes[$path];
        if (file_exists($viewPath)) {
            require_once $viewPath;
            return;
        }
    }
    
    // Handle direct PHP file requests
    if (strpos($path, '.php') !== false) {
        $viewPath = __DIR__ . '/../src/views' . $path;
        if (file_exists($viewPath)) {
            require_once $viewPath;
            return;
        }
    }
    
    // Handle AJAX requests
    if ($method === 'POST' && isset($_POST['ajax_action'])) {
        handleAjaxRequest($_POST['ajax_action'], $_POST);
        return;
    }
    
    // Handle login/logout separately
    if ($path === '/login') {
        if (isset($_SESSION['nic'])) {
            redirect('/dashboard');
            exit();
        }
        require_once __DIR__ . '/../src/views/auth/login.php';
        return;
    }
    
    if ($path === '/logout') {
        session_destroy();
        redirect('/login');
        exit();
    }
    
    // If path is / or empty, redirect to dashboard
    if ($path === '/' || $path === '') {
        redirect('/dashboard');
        exit();
    }
    
    // 404 - Not Found
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
    echo '<p>The page you are looking for does not exist.</p>';
    echo '<p><a href="' . baseUrl('/dashboard') . '">Go to Dashboard</a></p>';
}

// ==========================================
// AJAX REQUEST HANDLER
// ==========================================
function handleAjaxRequest($action, $data) {
    header('Content-Type: application/json');
    
    switch ($action) {
        case 'mark_read':
            require_once __DIR__ . '/../src/controllers/NotificationController.php';
            $controller = new NotificationController();
            $result = $controller->markAsRead($data['action_id'] ?? 0);
            echo json_encode($result);
            break;
            
        case 'delete':
            require_once __DIR__ . '/../src/controllers/NotificationController.php';
            $controller = new NotificationController();
            $result = $controller->delete($data['action_id'] ?? 0);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
}

// ==========================================
// Check if user is logged in (except for login page)
// ==========================================
function checkAuth() {
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/login') !== false) {
        return true;
    }
    if (!isset($_SESSION['nic'])) {
        redirect('/login');
        exit();
    }
    return true;
}

// ==========================================
// EXECUTE ROUTING
// ==========================================
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Check authentication for all pages except login
if (strpos($requestUri, '/login') === false && strpos($requestUri, '/logout') === false) {
    checkAuth();
}

route($requestUri, $requestMethod);
?>