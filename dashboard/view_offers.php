<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Check if there are any offers (simulated)
$offer_exists = false;
$offer_data = null;

// For demo purposes, check if user has an approved application
$check_sql = "SELECT * FROM applications WHERE user_id = ? AND status = 'approved' ORDER BY created_at DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $offer_exists = true;
    $offer_data = $check_result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = $_POST['response'];
    $offer_id = $_POST['offer_id'];
    
    if ($response == 'accept') {
        $message = '<div style="color: green; text-align: center;">✅ You have accepted the offer! Please wait for confirmation.</div>';
    } elseif ($response == 'decline') {
        $message = '<div style="color: red; text-align: center;">❌ You have declined the offer. You will remain on the waiting list.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Offer</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .offer-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .offer-container h2 {
            color: #111;
            margin-bottom: 20px;
            text-align: center;
        }
        .offer-box {
            background: #f9fafb;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
        }
        .offer-box h3 {
            color: #f59e0b;
            margin-bottom: 10px;
        }
        .offer-detail {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .offer-detail:last-child {
            border-bottom: none;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            justify-content: center;
        }
        .btn-accept {
            background: #22c55e;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-accept:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }
        .btn-decline {
            background: #ef4444;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-decline:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        .no-offer {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #111;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { color: #f59e0b; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="offer-container">
            <h2>📨 Respond to Offer</h2>
            
            <?php echo $message; ?>
            
            <?php if ($offer_exists): ?>
                <div class="offer-box">
                    <h3>🏠 Quarter Allocation Offer</h3>
                    
                    <div class="offer-detail">
                        <strong>Quarter Type:</strong> <?php echo htmlspecialchars($offer_data['quarter_type']); ?>
                    </div>
                    <div class="offer-detail">
                        <strong>Location:</strong> Colombo 7, Sri Lanka
                    </div>
                    <div class="offer-detail">
                        <strong>Rent per Month:</strong> LKR 25,000
                    </div>
                    <div class="offer-detail">
                        <strong>Offer Date:</strong> <?php echo date('d M Y'); ?>
                    </div>
                    <div class="offer-detail">
                        <strong>Response Deadline:</strong> <?php echo date('d M Y', strtotime('+7 days')); ?>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="offer_id" value="<?php echo $offer_data['id']; ?>">
                        <div class="btn-group">
                            <button type="submit" name="response" value="accept" class="btn-accept">✅ Accept Offer</button>
                            <button type="submit" name="response" value="decline" class="btn-decline">❌ Decline Offer</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="no-offer">
                    <p>No offers available at the moment.</p>
                    <p style="margin-top: 10px;">You will be notified when a quarter becomes available.</p>
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="back-link">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>