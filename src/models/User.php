<?php
require_once __DIR__ . '/../../config/config.php';

class User {
    private $conn;
    private $table = 'users';
    
    public $nic;
    public $name;
    public $email;
    public $password;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Find user by NIC
     */
    public function findByNic($nic) {
        $sql = "SELECT * FROM {$this->table} WHERE nic = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $nic);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($nic, $name, $email) {
        $sql = "UPDATE {$this->table} SET name = ?, email = ? WHERE nic = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $nic);
        return $stmt->execute();
    }
    
    /**
     * Verify login credentials
     */
    public function verifyLogin($nic, $password) {
        $user = $this->findByNic($nic);
        if ($user && $user['password'] === md5($password)) {
            return $user;
        }
        return false;
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>