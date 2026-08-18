<?php
/**
 * Edit Profile Page
 */
require_once __DIR__ . '/../../controllers/ProfileController.php';

$profileController = new ProfileController();
$userData = $profileController->getProfile();
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if ($name && $email) {
        $result = $profileController->updateProfile($name, $email);
        if ($result['success']) {
            $success = $result['message'];
            $userData['name'] = $name;
            $userData['email'] = $email;
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Please fill in all fields';
    }
}

$pageTitle = 'Edit Profile';

include __DIR__ . '/../layouts/header.php';
?>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:border-amber-400 transition-all duration-300">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">✏️ Edit Profile</h2>
            <p class="text-gray-500 text-center text-sm mb-6">Update your personal information</p>
            
            <?php if ($error): ?>
                <div class="mb-4 p-3 rounded-lg text-center font-medium bg-red-50 text-red-700 border border-red-200">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="mb-4 p-3 rounded-lg text-center font-medium bg-green-50 text-green-700 border border-green-200">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-transparent transition">
                </div>
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-transparent transition">
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo baseUrl('dashboard'); ?>" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                    <button type="submit" class="flex-1 bg-[#5c060d] hover:bg-[#4a050a] text-white hover:text-amber-300 font-semibold py-3 rounded-lg transition shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>