<?php
$host = "localhost";
$username = "root";       // Your MySQL username (Default: root)
$password = "";           // Your MySQL password (XAMPP default is empty)
$dbname = "applicants_db"; // Database name changed to applicants_db

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set UTF-8 character set support
$conn->set_charset("utf8mb4");
?>