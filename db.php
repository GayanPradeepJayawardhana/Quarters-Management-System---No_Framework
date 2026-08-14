<?php
$host = "localhost";
$username = "root";       // Your MySQL username (Default: root)
$password = "";           // Your MySQL password (XAMPP default is empty)
$dbname = "applicants_db"; // Database name

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return error in JSON format
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'error' => 'Database Connection Failed: ' . $conn->connect_error]));
}

// Set UTF-8 character set support
$conn->set_charset("utf8mb4");
?>