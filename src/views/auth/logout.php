<?php
/**
 * Logout Page
 */
session_start();

// Destroy all session data
session_destroy();

// Redirect to login page
header('Location: /QMS/applicants_dashboard/public/login');
exit();
?>