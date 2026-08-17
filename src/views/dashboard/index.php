<?php
/**
 * Dashboard Home Page
 */
require_once __DIR__ . '/../../controllers/DashboardController.php';

$dashboardController = new DashboardController();
$data = $dashboardController->getDashboardData();
$user = $data['user'];
$unreadCount = $data['unread_count'];
$pageTitle = 'Applicant Dashboard';

include __DIR__ . '/../layouts/header.php';
?>

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
                    <img src="/QMS/applicants_dashboard/public/assets/images/Request Quarters.png" alt="Home" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Request Quarters</h3>
                <p class="text-gray-500 text-sm mb-6">Start a new application for a government quarter.</p>
                <a href="/QMS/applicants_dashboard/public/application/request" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>New Application</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 2: View Status -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="/QMS/applicants_dashboard/public/assets/images/View Status.png" alt="Checklist" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">View Status</h3>
                <p class="text-gray-500 text-sm mb-6">Track your application verification progress.</p>
                <a href="/QMS/applicants_dashboard/public/application/status" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>Check Status</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 3: Waiting List -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="/QMS/applicants_dashboard/public/assets/images/Waiting List.png" alt="Waiting List" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Waiting List</h3>
                <p class="text-gray-500 text-sm mb-6">View your position and queue details.</p>
                <a href="/QMS/applicants_dashboard/public/waiting-list" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>View Waiting List</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 4: Notification -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center relative">
                <?php if ($unreadCount > 0): ?>
                    <span class="bg-red-600 text-white px-2.5 py-0.5 text-xs font-bold rounded-full absolute top-4 right-4"><?php echo $unreadCount; ?> New</span>
                <?php endif; ?>
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="/QMS/applicants_dashboard/public/assets/images/Notification.png" alt="Bell" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Notification</h3>
                <p class="text-gray-500 text-sm mb-6">Check updates and official messages.</p>
                <a href="/QMS/applicants_dashboard/public/notifications" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
                    <span>View Notifications</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>

            <!-- Tile 5: Respond to Offer -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:border-amber-400 hover:ring-4 hover:ring-amber-200/50 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
                <div class="w-20 h-20 flex items-center justify-center mb-4">
                    <img src="/QMS/applicants_dashboard/public/assets/images/Respond to Offer.png" alt="Envelope" class="w-20 h-20 object-contain">
                </div>
                <h3 class="text-xl font-bold mb-1 text-gray-900">Respond to Offer</h3>
                <p class="text-gray-500 text-sm mb-6">View and respond to quarter allocation offers.</p>
                <a href="/QMS/applicants_dashboard/public/offer/respond" class="mt-auto bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-5 py-2 rounded-lg text-sm transition shadow-sm inline-flex items-center space-x-1">
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>