<?php
/**
 * Login Page
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle = 'Login - Quarter Management System';

// If already logged in, redirect to dashboard
if (isset($_SESSION['nic'])) {
    redirect('/dashboard');
    exit();
}

$error = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . '/../../controllers/AuthController.php';
    $auth = new AuthController();
    
    $nic = trim($_POST['nic'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($nic && $password) {
        if ($auth->login($nic, $password)) {
            redirect('/dashboard');
            exit();
        } else {
            $error = 'Invalid NIC or Password';
        }
    } else {
        $error = 'Please enter both NIC and Password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full border-t-4 border-[#b59410]">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="<?php echo assetUrl('images/logo.png'); ?>" alt="Railway Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-[#5c060d]">SRI LANKA RAILWAY</h1>
            <p class="text-sm text-gray-500">Quarter Management System</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-700 border border-red-200 rounded-lg text-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="nic" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-id-card mr-2"></i> NIC Number
                </label>
                <input type="text" 
                       id="nic" 
                       name="nic" 
                       placeholder="Enter your NIC" 
                       value="<?php echo htmlspecialchars($_POST['nic'] ?? ''); ?>"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b59410] focus:border-transparent transition">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i> Password
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b59410] focus:border-transparent transition">
            </div>

            <button type="submit" 
                    class="w-full bg-[#5c060d] hover:bg-[#4a050a] text-white font-semibold py-3 rounded-lg transition shadow-sm hover:shadow-md hover:text-amber-300">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">Default test credentials:</p>
            <p class="text-xs text-gray-400 font-mono mt-1">NIC: 199012345678 | Password: password123</p>
        </div>
    </div>

</body>
</html>