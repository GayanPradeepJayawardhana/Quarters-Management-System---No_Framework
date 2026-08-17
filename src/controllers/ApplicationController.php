<?php
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/WaitingList.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/AuthController.php';

class ApplicationController {
    private $auth;
    private $applicationModel;
    private $waitingListModel;
    private $notificationModel;
    
    public function __construct() {
        $this->auth = new AuthController();
        $this->auth->requireLogin();
        $this->applicationModel = new Application();
        $this->waitingListModel = new WaitingList();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Submit new application
     */
    public function submit($quarterType) {
        $nic = $_SESSION['nic'];
        
        $result = $this->applicationModel->create($nic, $quarterType);
        
        if ($result['success']) {
            // Add to waiting list
            $position = $this->getNextWaitingListPosition();
            $this->waitingListModel->add($nic, $quarterType, $position);
            
            // Create notification
            $this->notificationModel->create(
                $nic,
                'Application Received',
                'Your quarter application has been received and is under review.'
            );
            
            return [
                'success' => true,
                'message' => 'Your application has been submitted successfully!',
                'computer_no' => $result['computer_no']
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error submitting application. Please try again.'
        ];
    }
    
    /**
     * Get application status
     */
    public function getStatus($computerNo = null) {
        $nic = $_SESSION['nic'];
        
        if ($computerNo) {
            $application = $this->applicationModel->findByComputerNo($computerNo, $nic);
        } else {
            $application = $this->applicationModel->findByNic($nic);
        }
        
        if ($application) {
            $position = $this->waitingListModel->getPosition($nic);
            $application['position'] = $position;
            return $application;
        }
        
        return null;
    }
    
    /**
     * Get next waiting list position
     */
    private function getNextWaitingListPosition() {
        $sql = "SELECT COUNT(*) as count FROM waiting_list";
        $conn = getDBConnection();
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        return ($row['count'] ?? 0) + 1;
    }
}
?>