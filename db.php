<?php
$host = "localhost";
$username = "root";       // Your MySQL username (Default: root)
$password = "";           // Your MySQL password (XAMPP default is empty)
$dbname = "applicants_db"; // Database name changed to applicants_db

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // JSON ආකෘතියෙන් error එක ලබා දීම (Frontend එකට බාධාවකින් තොරව catch කර ගැනීමට)
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'error' => 'Database Connection Failed: ' . $conn->connect_error]));
}

// Set UTF-8 character set support
$conn->set_charset("utf8mb4");
?>