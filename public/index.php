<?php
/**
 * Front Controller - Entry point for all routes
 */

// Start session ONLY if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
// AUTHENTICATION CHECK
// ==========================================
function checkAuth() {
    // Get the current path without base URL
    $currentPath = $_SERVER['REQUEST_URI'];
    
    // Remove base URL from path
    if (strpos($currentPath, BASE_URL) === 0) {
        $currentPath = substr($currentPath, strlen(BASE_URL));
    }
    
    // Remove /public from path if present
    if (strpos($currentPath, '/public') === 0) {
        $currentPath = substr($currentPath, 7);
    }
    
    // Remove query string
    if (strpos($currentPath, '?') !== false) {
        $currentPath = strtok($currentPath, '?');
    }
    
    // Clean up the path
    $currentPath = rtrim($currentPath, '/');
    if (empty($currentPath)) {
        $currentPath = '/';
    }
    
    // Define public pages (no login required)
    $publicPaths = ['/login', '/register', '/logout'];
    foreach ($publicPaths as $publicPath) {
        if ($currentPath === $publicPath || strpos($currentPath, $publicPath . '?') === 0) {
            return true;
        }
    }
    
    // If accessing public/index.php directly, allow it
    if (strpos($currentPath, '/public/index.php') !== false) {
        return true;
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['nic']) || !isset($_SESSION['session_valid'])) {
        // Store the intended URL for redirect after login (CLEAN VERSION)
        $cleanPath = $_SERVER['REQUEST_URI'];
        // Remove base URL
        if (strpos($cleanPath, BASE_URL) === 0) {
            $cleanPath = substr($cleanPath, strlen(BASE_URL));
        }
        // Remove /public if present
        if (strpos($cleanPath, '/public') === 0) {
            $cleanPath = substr($cleanPath, 7);
        }
        // Remove query string if present
        if (strpos($cleanPath, '?') !== false) {
            $cleanPath = strtok($cleanPath, '?');
        }
        // Ensure it starts with /
        if (!empty($cleanPath) && $cleanPath[0] !== '/') {
            $cleanPath = '/' . $cleanPath;
        }
        $_SESSION['redirect_after_login'] = $cleanPath;
        
        header('Location: ' . baseUrl('login'));
        exit();
    }
    
    return true;
}

// ==========================================
// ROUTE HANDLER
// ==========================================
function route($path, $method = 'GET') {
    // Remove query string
    if (strpos($path, '?') !== false) {
        $path = strtok($path, '?');
    }
    
    // Remove base path if running in subdirectory
    if (strpos($path, BASE_URL) === 0) {
        $path = substr($path, strlen(BASE_URL));
    }
    
    // Remove /public from path if present
    if (strpos($path, '/public') === 0) {
        $path = substr($path, 7);
    }
    
    // Clean up path
    if (empty($path) || $path === '/' || $path === '') {
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
        '/register' => '/auth/register.php',
        '/logout' => '/auth/logout.php',
    ];
    
    // Handle AJAX requests first
    if ($method === 'POST' && isset($_POST['ajax_action'])) {
        handleAjaxRequest($_POST['ajax_action'], $_POST);
        return;
    }
    
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
    
    // Handle root path
    if ($path === '/') {
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
// EXECUTE ROUTING
// ==========================================
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Check authentication for all pages
checkAuth();

// Route the request
route($requestUri, $requestMethod);
?>