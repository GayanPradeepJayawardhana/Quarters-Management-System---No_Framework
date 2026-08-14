<?php
// Database connection include
require_once '../db.php';

// Session start and get user details
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$nic = isset($_SESSION['nic']) ? $_SESSION['nic'] : '';

// Get unread notification count from database
$unread_count = 0;
if (!empty($nic) && isset($conn)) {
    $stmt = $conn->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE nic = ? AND is_read = 0");
    if ($stmt) {
        $stmt->bind_param("s", $nic);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $unread_count = (int)$row['unread_count'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department of Railways - Applicant Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .slr-logo {
            height: 70px !important;
            width: auto !important;
            max-width: none;
            object-fit: contain;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 m-0 p-0">

    <!-- Top Dark Red Header Bar -->
    <header class="bg-[#5c060d] text-white py-4 px-6 md:px-12 shadow-md flex flex-col md:flex-row justify-between items-center relative border-b-4 border-[#b59410]">
        <!-- Logo and Titles -->
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <img src="images2/logo.png" alt="Sri Lanka Railway Logo" class="slr-logo">
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-wider">SRI LANKA RAILWAY</h1>
                <h2 class="text-sm md:text-base font-semibold tracking-wide text-amber-200">QUARTER MANAGEMENT SYSTEM</h2>
            </div>
        </div>

        <!-- Profile Dropdown Menu -->
        <div class="profile-dropdown-container relative">
            <button onclick="toggleDropdown()" class="flex items-center space-x-3 border border-amber-500/50 rounded-full px-5 py-2.5 bg-[#4a050a] text-white shadow-sm hover:bg-[#6e0710] focus:outline-none transition">
                <img src="images2/user.png" alt="User" class="w-7 h-7 rounded-full object-contain bg-white p-0.5">
                <span class="text-base md:text-lg font-medium">Hi, <?php echo htmlspecialchars($user_name); ?></span>
                <i class="fa-solid fa-chevron-down text-xs text-amber-300"></i>
            </button>
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 border border-gray-200 rounded-lg shadow-lg py-1 z-50">
                <a href="../edit_profile/edit_profile.php" class="block px-4 py-2 text-sm hover:bg-gray-100"><i class="fa-solid fa-user mr-2 text-gray-600"></i> Profile</a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100"><i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Welcome Section -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold font-serif text-gray-900">Applicant Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Welcome back! Manage your quarter application easily.</p>
        </div>

        <!-- Dashboard Tiles Grid Container -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Tile 1: Request Quarters -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="images2/Request Quarters.png" alt="Home" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Request Quarters</h3>
                <p class="text-gray-500 text-sm mb-6">Start a new application for a government quarter.</p>
                <a href="request_quarters.php" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>New Application</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 2: View Status -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="images2/View Status.png" alt="Checklist" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">View Status</h3>
                <p class="text-gray-500 text-sm mb-6">Track your application verification progress.</p>
                <a href="view_status.php" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>Check Status</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 3: Waiting List -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="images2/Waiting List.png" alt="Waiting List" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Waiting List</h3>
                <p class="text-gray-500 text-sm mb-6">View your position and queue details.</p>
                <a href="waiting_list.php" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>View Waiting List</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 4: Notification -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center relative">
                <?php if ($unread_count > 0): ?>
                    <span class="bg-red-600 text-white px-2.5 py-0.5 text-xs font-bold rounded-full absolute top-4 right-4"><?php echo $unread_count; ?> New</span>
                <?php endif; ?>
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="images2/Notification.png" alt="Bell" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Notification</h3>
                <p class="text-gray-500 text-sm mb-6">Check updates and official messages.</p>
                <a href="../Dashboard1/notification/notification.php" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>View Notifications</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 5: Respond to Offer -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="images2/Respond to Offer.png" alt="Envelope" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Respond to Offer</h3>
                <p class="text-gray-500 text-sm mb-6">View and respond to quarter allocation offers.</p>
                <a href="view_offers.php" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>View Offers</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Dropdown Script -->
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("profileDropdown");
            dropdown.classList.toggle("hidden");
        }
        window.onclick = function(event) {
            if (!event.target.closest('.profile-dropdown-container')) {
                var dropdown = document.getElementById("profileDropdown");
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
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