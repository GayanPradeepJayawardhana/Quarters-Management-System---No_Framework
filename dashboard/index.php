<?php
// Database connection include කිරීම
require_once '../db.php';

// Session එකකින් user name එක ලබාගැනීම
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Applicant';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="dashboard-container">
        
        <!-- HEADER SECTION -->
        <div class="dashboard-header-container">
            
            <div class="top-left-greeting">
                <h3>Hi, <?php echo htmlspecialchars($user_name); ?> 👋</h3>
            </div>
            
            <div class="dashboard-title-section">
                <h1 class="dashboard-title">Applicant Dashboard</h1>
                <p class="dashboard-subtitle">Welcome back! Manage your quarter application easily</p>
            </div>

            <div class="profile-dropdown-container">
                <div class="profile-trigger" onclick="toggleDropdown()">
                    <span class="user-greeting"> <?php echo htmlspecialchars($user_name); ?> </span>
                    <i class="dropdown-icon">▼</i>
                </div>

                <div class="dropdown-menu" id="profileDropdown">
                     <a href="../edit_profile/edit_profile.php" class="dropdown-item">
                        <i class="icon-edit"></i> Profile Edit
                    </a>
                    <hr class="dropdown-divider">
                    <a href="logout.php" class="dropdown-item logout-btn">
                        <i class="icon-logout"></i> Logout
                    </a>
                </div>
            </div>

        </div>

        <!-- ROW 1: CARDS 1, 2, AND 3 -->
        <div class="dashboard-grid-3">
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <img src="images/home1.png" alt="Request Quarters">
                </div>
                <div class="card-content">
                    <h3>Request Quarters</h3>
                    <p>Start a new application for a government quarter.</p>
                    <a href="request_quarters.php" class="btn btn-primary">New Application &rarr;</a>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">
                    <img src="images/status1.png" alt="View Status">
                </div>
                <div class="card-content">
                    <h3>View Status</h3>
                    <p>Track your application verification progress.</p>
                    <a href="view_status.php" class="btn btn-primary">Check Status &rarr;</a>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">
                    <img src="images/Membership1.png" alt="Waiting List">
                </div>
                <div class="card-content">
                    <h3>Waiting List</h3>
                    <p>View your position and queue details.</p>
                    <a href="waiting_list.php" class="btn btn-primary">View Waiting List &rarr;</a>
                </div>
            </div>

        </div>

        <!-- ROW 2: CARDS 4 AND 5 -->
        <div class="dashboard-grid-2">
            
            <div class="dashboard-card position-relative">
                <span class="badge badge-notification">1 New</span>
                <div class="card-icon">
                    <img src="images/bell1.png" alt="Notifications">
                </div>
                <div class="card-content">
                    <h3>Notification</h3>
                    <p>Check updates and official messages.</p>
                    <a href="../Dashboard1/notification/notification.php" class="btn btn-primary">View Notifications &rarr;</a>
                </div>
            </div>

            <div class="dashboard-card position-relative">
                <div class="card-icon">
                    <img src="images/letter1.png" alt="Respond to Offer">
                </div>
                <div class="card-content">
                    <h3>Respond to Offer</h3>
                    <p>View and respond to quarter allocation offers.</p>
                    <a href="view_offers.php" class="btn btn-primary">View Offers &rarr;</a>
                </div>
            </div>

        </div>

    </div>

    <!-- Dropdown Script -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById("profileDropdown");
            dropdown.classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.closest('.profile-dropdown-container')) {
                const dropdowns = document.getElementsByClassName("dropdown-menu");
                for (let i = 0; i < dropdowns.length; i++) {
                    let openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>

<?php 
if (isset($conn)) {
    $conn->close(); 
}
?>