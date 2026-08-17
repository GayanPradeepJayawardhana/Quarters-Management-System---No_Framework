<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->initSession();
    }
    
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['nic']);
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'nic' => $_SESSION['nic'],
                'name' => $_SESSION['user_name'] ?? 'User'
            ];
        }
        return null;
    }
    
    /**
     * Require login
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: /QMS/applicants_dashboard/public/login");
            exit();
        }
    }
    
    /**
     * Verify user credentials
     */
    public function login($nic, $password) {
        $user = $this->userModel->verifyLogin($nic, $password);
        if ($user) {
            $_SESSION['nic'] = $user['nic'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
        return false;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        header("Location: /QMS/applicants_dashboard/public/login");
        exit();
    }
}
?>