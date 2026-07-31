<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle form submission before checking for offers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = $_POST['response'] ?? '';
    $offer_id = $_POST['offer_id'] ?? '';
    
    if ($response == 'accept') {
        // Show success alert message for accepting
        $message = '<div class="alert alert-success">
            Collect your quarter documents within 2 weeks through BDF, Unless your quarter allocation will be removed.
        </div>';
    } elseif ($response == 'later') {
        if (!empty($offer_id)) {
            // Update created_at timestamp to move the request to the bottom of the queue/list
            // Also change status from 'approved' back to pending/waiting if required by your logic, or keep it updated.
            $update_sql = "UPDATE applications SET created_at = NOW(), status = 'pending' WHERE id = ? AND user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $offer_id, $user_id);
            $update_stmt->execute();
        }
        // Redirect directly to the dashboard (index.php) after clicking 'Later' without showing any message here
        header("Location: index.php");
        exit();
    } elseif ($response == 'deny') {
        if (!empty($offer_id)) {
            // Completely delete the user application from the database
            $delete_sql = "DELETE FROM applications WHERE id = ? AND user_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $offer_id, $user_id);
            $delete_stmt->execute();
        }
        // No message is set, so the page will automatically fall through and display "No offers available at the moment."
    }
}

// Check if user still has an approved active application offer
$check_sql = "SELECT * FROM applications WHERE user_id = ? AND status = 'approved' ORDER BY created_at DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

$offer_exists = false;
$offer_data = null;

if ($check_result->num_rows > 0) {
    $offer_exists = true;
    $offer_data = $check_result->fetch_assoc();
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
            max-width: 650px;
            margin: 40px auto;
            padding: 35px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            font-family: Arial, sans-serif;
        }
        .offer-container h2 {
            color: #1e293b;
            margin-bottom: 25px;
            text-align: center;
            font-size: 24px;
        }
        .offer-box {
            background: #ffffff;
            border: 1px solid #93c5fd;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        .offer-box p {
            font-size: 16px;
            color: #334155;
            margin-bottom: 25px;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        .btn-accept-now, .btn-later, .btn-deny {
            background: #93c5fd;
            color: #1e3a8a;
            border: 1px solid #3b82f6;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-accept-now:hover, .btn-later:hover, .btn-deny:hover {
            background: #3b82f6;
            color: white;
        }
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
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
        .back-link:hover { color: #3b82f6; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="offer-container">
            <h2>Respond to Offer</h2>
            
            <?php echo $message; ?>
            
            <?php if ($offer_exists): ?>
                <div class="offer-box">
                    <p>• You have been assigned to a quarter, Do you,</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="offer_id" value="<?php echo $offer_data['id']; ?>">
                        <div class="btn-group">
                            <button type="submit" name="response" value="accept" class="btn-accept-now">Accept now</button>
                            <button type="submit" name="response" value="later" class="btn-later">Later</button>
                            <button type="submit" name="response" value="deny" class="btn-deny">Deny</button>
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