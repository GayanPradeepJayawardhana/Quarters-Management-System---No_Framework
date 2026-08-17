<?php
/**
 * View and Respond to Offers Page
 */
require_once __DIR__ . '/../../controllers/OfferController.php';

$offerController = new OfferController();
$message = '';
$offerData = null;
$offerExists = false;
$isAccepted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = $_POST['response'] ?? '';
    $offerId = $_POST['offer_id'] ?? '';
    
    if ($response && $offerId) {
        $result = $offerController->respond($offerId, $response);
        
        if ($result['success']) {
            if ($response === 'later' || $response === 'deny') {
                header("Location: /dashboard");
                exit();
            }
            $message = $result['message'];
        }
    }
}

// Get current offer
$offerData = $offerController->getOffer();

if ($offerData) {
    $offerExists = true;
    if ($offerData['status'] == 'accepted') {
        $isAccepted = true;
        $message = 'Collect your quarter documents within 2 weeks through BDF, Unless your quarter allocation will be removed.';
    }
}

$user = $_SESSION['user_name'] ?? 'User';
$pageTitle = 'Respond to Offer';

include __DIR__ . '/../layouts/header.php';
?>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:border-amber-400 transition-all duration-300">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">📨 Respond to Offer</h2>
            
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg text-center font-medium <?php echo $isAccepted ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($offerExists): ?>
                <?php if (!$isAccepted): ?>
                    <p class="text-center text-gray-600 mb-6">You have been assigned to a quarter. Do you,</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="offer_id" value="<?php echo $offerData['id']; ?>">
                        
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <button type="submit" name="response" value="accept" 
                                    class="py-6 px-4 border-2 border-green-200 bg-green-50 hover:bg-green-100 rounded-xl transition flex flex-col items-center">
                                <span class="text-3xl">✅</span>
                                <span class="mt-2 font-semibold text-green-700">Accept now</span>
                            </button>
                            
                            <button type="submit" name="response" value="later" 
                                    class="py-6 px-4 border-2 border-yellow-200 bg-yellow-50 hover:bg-yellow-100 rounded-xl transition flex flex-col items-center">
                                <span class="text-3xl">⏰</span>
                                <span class="mt-2 font-semibold text-yellow-700">Later</span>
                            </button>
                            
                            <button type="submit" name="response" value="deny" 
                                    class="py-6 px-4 border-2 border-red-200 bg-red-50 hover:bg-red-100 rounded-xl transition flex flex-col items-center">
                                <span class="text-3xl">❌</span>
                                <span class="mt-2 font-semibold text-red-700">Deny</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-lg">
                    <p class="text-lg">No offers available at the moment.</p>
                    <p class="text-sm mt-1">You will be notified when a quarter becomes available.</p>
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