<?php
/**
 * View Status Page
 */
require_once __DIR__ . '/../../controllers/ApplicationController.php';

$appController = new ApplicationController();
$searchQuery = '';
$application = null;
$errorMessage = '';

if (isset($_GET['search']) && isset($_GET['computer_no'])) {
    $searchQuery = trim($_GET['computer_no']);
    if (!empty($searchQuery)) {
        $application = $appController->getStatus($searchQuery);
        if (!$application) {
            $errorMessage = "No application found with Computer No: " . htmlspecialchars($searchQuery);
        }
    }
}

$user = $_SESSION['user_name'] ?? 'User';
$pageTitle = 'View Status';

include __DIR__ . '/../layouts/header.php';
?>

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:border-amber-400 transition-all duration-300">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">📋 View Status</h2>
            <p class="text-gray-500 text-center text-sm mb-6">Track your application verification progress.</p>
            
            <!-- Search Form -->
            <form method="GET" action="" class="mb-8">
                <div class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                    <input type="text" 
                           name="computer_no" 
                           placeholder="Enter Computer No" 
                           value="<?php echo htmlspecialchars($searchQuery); ?>" 
                           required
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-transparent transition">
                    <button type="submit" name="search" 
                            class="px-6 py-3 bg-[#5c060d] hover:bg-[#4a050a] text-white font-semibold rounded-lg transition shadow-sm hover:shadow-md hover:text-amber-300">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </form>

            <?php if ($errorMessage): ?>
                <div class="mb-6 p-4 rounded-lg text-center font-medium bg-red-50 text-red-700 border border-red-200">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <?php if ($application): ?>
                <div class="border-t-2 border-amber-200 pt-6">
                    <h4 class="text-lg font-semibold text-[#5c060d] mb-4">Approval Progress</h4>

                    <?php 
                    // Define statuses and helper function
                    $statuses = [
                        'Immediate Boss' => $application['boss_status'] ?? 'pending',
                        'Personal File' => $application['file_status'] ?? 'pending',
                        'Subject Clerk' => $application['clerk_status'] ?? 'pending',
                        'Final Approval' => $application['final_status'] ?? 'pending'
                    ];
                    
                    function renderStatusBox($status) {
                        if ($status === 'approved') {
                            return '<span class="text-green-600 text-xl">✅</span>';
                        } elseif ($status === 'rejected') {
                            return '<span class="text-red-600 text-xl">❌</span>';
                        }
                        return '<span class="text-gray-300 text-xl">⬜</span>';
                    }
                    ?>
                    
                    <?php foreach ($statuses as $label => $status): ?>
                    <div class="flex items-center gap-4 py-2 border-b border-gray-50">
                        <div class="w-32 font-medium text-gray-700 text-sm"><?php echo $label; ?></div>
                        <div class="w-10 text-center"><?php echo renderStatusBox($status); ?></div>
                        <div class="text-xs text-gray-400"><?php echo $status === 'pending' ? 'Waiting for review' : ($status === 'approved' ? 'Approved' : 'Rejected'); ?></div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Additional Info -->
                    <div class="mt-6 bg-amber-50 border-l-4 border-amber-400 p-4 rounded">
                        <p class="text-sm text-gray-700">
                            <strong>Marks:</strong> <?php echo htmlspecialchars($application['marks'] !== null ? $application['marks'] : 'Pending'); ?>
                        </p>
                        <p class="text-sm text-gray-700 mt-1">
                            <strong>Waiting List Position:</strong> <?php echo htmlspecialchars($application['position'] !== null ? $application['position'] : 'Pending'); ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <p>Enter a Computer Number to check your application status.</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-6 text-center">
                <a href="/dashboard" class="text-[#5c060d] hover:text-amber-600 font-medium text-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>