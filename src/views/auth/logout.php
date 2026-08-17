<?php
/**
 * Logout Page
 */
require_once __DIR__ . '/../../../config/config.php';

session_start();
session_destroy();

// Redirect to login page
redirect('/login');
exit();
?>