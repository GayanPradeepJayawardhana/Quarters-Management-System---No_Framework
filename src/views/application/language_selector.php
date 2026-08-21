<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireLogin();

$user = $_SESSION['user_name'] ?? 'User';
$pageTitle = 'Select Language - Quarter Application';
include __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
        <h1 class="text-3xl font-bold text-[#0f4c81] mb-2">දුම්රිය නිවාස අයදුම්පත්‍රය</h1>
        <h2 class="text-xl text-gray-600 mb-6">Railway Quarters Application Form</h2>
        <p class="text-lg font-medium mb-8">ඔබගේ භාෂාව තෝරන්න / Please select your language:</p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?php echo baseUrl('application/form/si'); ?>" 
               class="inline-block bg-[#0f4c81] hover:bg-[#0b3961] text-white font-bold py-4 px-8 rounded-lg shadow transition text-lg">
                සිංහල
            </a>
            <a href="<?php echo baseUrl('application/form/en'); ?>" 
               class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg shadow transition text-lg">
                English
            </a>
        </div>

        <div class="mt-8">
            <a href="<?php echo baseUrl('dashboard'); ?>" class="text-[#5c060d] hover:text-amber-600 font-medium">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>