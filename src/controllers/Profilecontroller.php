<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AuthController.php';

class ProfileController {
    private $auth;
    private $userModel;
    
    public function __construct() {
        $this->auth = new AuthController();
        $this->auth->requireLogin();
        $this->userModel = new User();
    }
    
    /**
     * Get user profile
     */
    public function getProfile() {
        $nic = $_SESSION['nic'];
        return $this->userModel->findByNic($nic);
    }
    
    /**
     * Update profile
     */
    public function updateProfile($name, $email) {
        $nic = $_SESSION['nic'];
        $result = $this->userModel->updateProfile($nic, $name, $email);
        
        if ($result) {
            $_SESSION['user_name'] = $name;
            return ['success' => true, 'message' => 'Profile updated successfully!'];
        }
        
        return ['success' => false, 'message' => 'Error updating profile'];
    }
}
?>