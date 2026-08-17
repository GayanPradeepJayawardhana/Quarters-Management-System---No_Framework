<?php
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/AuthController.php';

class NotificationController {
    private $auth;
    private $notificationModel;
    
    public function __construct() {
        $this->auth = new AuthController();
        $this->auth->requireLogin();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Get all notifications
     */
    public function getAll() {
        $nic = $_SESSION['nic'];
        return $this->notificationModel->getByNic($nic);
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount() {
        $nic = $_SESSION['nic'];
        return $this->notificationModel->getUnreadCount($nic);
    }
    
    /**
     * Mark notification as read (AJAX)
     */
    public function markAsRead($id) {
        $nic = $_SESSION['nic'];
        $result = $this->notificationModel->markAsRead($id, $nic);
        
        if ($result) {
            return ['success' => true, 'message' => '✅ Notification marked as read!'];
        }
        return ['success' => false, 'message' => 'Failed to mark as read'];
    }
    
    /**
     * Delete notification (AJAX)
     */
    public function delete($id) {
        $nic = $_SESSION['nic'];
        $result = $this->notificationModel->delete($id, $nic);
        
        if ($result) {
            return ['success' => true, 'message' => '🗑️ Notification deleted!'];
        }
        return ['success' => false, 'message' => 'Failed to delete notification'];
    }
}
?>