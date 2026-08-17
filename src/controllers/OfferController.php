<?php
require_once __DIR__ . '/../models/Offer.php';
require_once __DIR__ . '/AuthController.php';

class OfferController {
    private $auth;
    private $offerModel;
    
    public function __construct() {
        $this->auth = new AuthController();
        $this->auth->requireLogin();
        $this->offerModel = new Offer();
    }
    
    /**
     * Get current offer
     */
    public function getOffer() {
        $nic = $_SESSION['nic'];
        return $this->offerModel->getActiveOffer($nic);
    }
    
    /**
     * Process offer response
     */
    public function respond($offerId, $response) {
        $nic = $_SESSION['nic'];
        $result = false;
        $message = '';
        
        switch ($response) {
            case 'accept':
                $result = $this->offerModel->accept($offerId, $nic);
                $message = 'Offer accepted successfully!';
                break;
                
            case 'later':
                $result = $this->offerModel->postpone($offerId, $nic);
                $message = 'Offer postponed. You can respond later.';
                break;
                
            case 'deny':
                $result = $this->offerModel->deny($offerId, $nic);
                $message = 'Offer denied.';
                break;
                
            default:
                return ['success' => false, 'message' => 'Invalid response option'];
        }
        
        return [
            'success' => $result,
            'message' => $message
        ];
    }
}
?>