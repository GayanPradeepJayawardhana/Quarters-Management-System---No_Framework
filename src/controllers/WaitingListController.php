<?php
require_once __DIR__ . '/../models/WaitingList.php';
require_once __DIR__ . '/AuthController.php';

class WaitingListController {
    private $auth;
    private $waitingListModel;
    
    public function __construct() {
        $this->auth = new AuthController();
        $this->auth->requireLogin();
        $this->waitingListModel = new WaitingList();
    }
    
    /**
     * Get user's waiting list position
     */
    public function getMyPosition() {
        $nic = $_SESSION['nic'];
        $position = $this->waitingListModel->getPosition($nic);
        
        return [
            'position' => $position,
            'is_waiting' => $position !== null
        ];
    }
}
?>