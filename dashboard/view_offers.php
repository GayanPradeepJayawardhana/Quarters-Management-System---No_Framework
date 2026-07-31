<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$accepted = false; // Flag to track if the offer has been accepted

// Handle form submission before checking for offers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = $_POST['response'] ?? '';
    $offer_id = $_POST['offer_id'] ?? '';
    
    if ($response == 'accept') {
        $accepted = true; // Set flag to true so buttons are hidden and success box shows
        // Show success alert message for accepting
        $message = '<div class="alert-success">
            Collect your quarter documents within 2 weeks through BDF, Unless your quarter allocation will be removed.
        </div>';
    } elseif ($response == 'later') {
        if (!empty($offer_id)) {
            // Update created_at timestamp to move the request to the bottom of the queue/list
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
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Main Card with Updated Border and Hover Glow Effect */
        .offer-card {
            background: #ffffff;
            width: 550px;
            padding: 40px;
            border-radius: 25px;
            border: 2px solid #007bff;
            box-shadow: 0 1px 2px rgba(0, 123, 255, 0.15);
            text-align: center;
            box-sizing: border-box;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .offer-card:hover {
            
            border-color: #3b82f6;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }

        .offer-card h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 24px;
        }

        .offer-card p {
            color: #555;
            font-size: 15px;
            margin-bottom: 35px;
        }

        .options-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 35px;
        }

        .option-box {
            flex: 1;
            padding: 20px 10px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid transparent;
            background: none;
            font-family: inherit;
        }

        .option-box:hover {
            transform: translateY(-3px);
        }

        /* Accept Box Styles */
        .accept {
            background-color: #e8f5e9;
            border-color: #c8e6c9;
        }
        .accept .icon {
            background-color: #2e7d32;
            color: white;
        }
        .accept span {
            color: #2e7d32;
            font-weight: 600;
        }

        /* Later Box Styles */
        .later {
            background-color: #fffde7;
            border-color: #fff9c4;
        }
        .later .icon {
            background-color: #fbc02d;
            color: white;
        }
        .later span {
            color: #f57f17;
            font-weight: 600;
        }

        /* Deny Box Styles */
        .deny {
            background-color: #ffebee;
            border-color: #ffcdd2;
        }
        .deny .icon {
            background-color: #c62828;
            color: white;
        }
        .deny span {
            color: #c62828;
            font-weight: 600;
        }

        .icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        /* Back Button Styles */
        .back-btn {
            background-color: #eef2f7;
            border: 1px solid #d0d7de;
            color: #007bff;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
            float: left;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #e2e8f0;
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
            font-size: 14px;
        }

        .no-offer {
            margin-bottom: 20px;
            color: #555;
            font-size: 15px;
        }
    </style>
</head>
<body>

    <div class="offer-card">
        <h2>Respond to Offer</h2>
        
        <?php echo $message; ?>

        <?php if ($offer_exists): ?>
            <?php if (!$accepted): ?>
                <p>You have been assigned to a quarter, Do you,</p>
                
                <form method="POST" action="">
                    <input type="hidden" name="offer_id" value="<?php echo $offer_data['id']; ?>">
                    
                    <div class="options-container">
                        <!-- Accept Option -->
                        <button type="submit" name="response" value="accept" class="option-box accept">
                            <div class="icon">&#10003;</div>
                            <span>Accept now</span>
                        </button>

                        <!-- Later Option -->
                        <button type="submit" name="response" value="later" class="option-box later">
                            <div class="icon">&#9202;</div>
                            <span>Later</span>
                        </button>

                        <!-- Deny Option -->
                        <button type="submit" name="response" value="deny" class="option-box deny">
                            <div class="icon">&#10005;</div>
                            <span>Deny</span>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-offer">
                <p>No offers available at the moment.<br>You will be notified when a quarter becomes available.</p>
            </div>
        <?php endif; ?>

        <a href="index.php" class="back-btn">
            &#8592; Back to Dashboard
        </a>
        <div style="clear: both;"></div>
    </div>

</body>
</html>