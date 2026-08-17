<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

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
    
    public function isLoggedIn() {
        return isset($_SESSION['nic']);
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'nic' => $_SESSION['nic'],
                'name' => $_SESSION['user_name'] ?? 'User'
            ];
        }
        return null;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            redirect('/login');
            exit();
        }
    }
    
    public function login($nic, $password) {
        $user = $this->userModel->verifyLogin($nic, $password);
        if ($user) {
            $_SESSION['nic'] = $user['nic'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        redirect('/login');
        exit();
    }
}
?>