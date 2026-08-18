<?php
/**
 * Waiting List Page
 */
require_once __DIR__ . '/../../controllers/WaitingListController.php';

$waitingController = new WaitingListController();
$data = $waitingController->getMyPosition();

$user = $_SESSION['user_name'] ?? 'User';
$pageTitle = 'Waiting List';

include __DIR__ . '/../layouts/header.php';
?>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:border-amber-400 transition-all duration-300 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">⏳ Your Waiting List Position</h2>
            <p class="text-gray-500 text-sm mb-6">View your current queue position for quarter allocation.</p>
            
            <?php if ($data['is_waiting']): ?>
                <div class="w-32 h-32 mx-auto border-4 border-amber-400 rounded-full flex items-center justify-center bg-amber-50 mb-4">
                    <span class="text-5xl font-bold text-[#5c060d]"><?php echo htmlspecialchars($data['position']); ?></span>
                </div>
                <p class="text-lg font-semibold text-gray-700">Your Position</p>
                <p class="text-sm text-gray-400 mt-1">You are currently in the waiting list</p>
            <?php else: ?>
                <div class="py-8 px-4 bg-amber-50 rounded-lg border border-dashed border-amber-300">
                    <p class="text-gray-600">You are not currently on the waiting list.</p>
                    <a href="<?php echo baseUrl('application/request'); ?>" class="inline-block mt-3 text-[#b59410] hover:underline font-semibold">
                        Submit an application →
                    </a>
                </div>
            <?php endif; ?>

            <div class="mt-8">
                <a href="<?php echo baseUrl('dashboard'); ?>" class="inline-block bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold px-6 py-3 rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>