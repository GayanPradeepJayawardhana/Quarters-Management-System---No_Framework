<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['nic'])) {
    header("Location: ../login.php");
    exit();
}

$nic = $_SESSION['nic'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = $_POST['response'] ?? '';
    $offer_id = $_POST['offer_id'] ?? '';
    
    if ($response == 'accept') {
        if (!empty($offer_id)) {
            // Update status to 'accepted' in database
            $update_sql = "UPDATE respond_to_offer SET status = 'accepted' WHERE id = ? AND nic = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $offer_id, $nic);
            $update_stmt->execute();
        }
    } elseif ($response == 'later') {
        if (!empty($offer_id)) {
            $update_sql = "UPDATE respond_to_offer SET created_at = NOW(), status = 'pending' WHERE id = ? AND nic = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $offer_id, $nic);
            $update_stmt->execute();
        }
        header("Location: index.php");
        exit();
    } elseif ($response == 'deny') {
        if (!empty($offer_id)) {
            $delete_sql = "DELETE FROM respond_to_offer WHERE id = ? AND nic = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("is", $offer_id, $nic);
            $delete_stmt->execute();
        }
        header("Location: index.php");
        exit();
    }
}

// Check if user has an offer with status 'approved' or 'accepted'
$check_sql = "SELECT * FROM respond_to_offer WHERE nic = ? AND status IN ('approved', 'accepted') ORDER BY created_at DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $nic);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

$offer_exists = false;
$offer_data = null;
$is_accepted = false;

if ($check_result->num_rows > 0) {
    $offer_exists = true;
    $offer_data = $check_result->fetch_assoc();
    if ($offer_data['status'] == 'accepted') {
        $is_accepted = true;
        $message = '<div class="alert-success">
            Collect your quarter documents within 2 weeks through BDF, Unless your quarter allocation will be removed.
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Offer - Department of Railways</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fcfbfa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .railway-header {
            width: 100%;
            background: linear-gradient(90deg, #5c060d 0%, #3d0307 100%);
            border-bottom: 4px solid #b59410;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .railway-header img {
            height: 60px;
            width: auto;
            margin-right: 20px;
        }

        .railway-header .header-text h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .railway-header .header-text h2 {
            color: #fde047;
            font-size: 15px;
            font-weight: 600;
            margin: 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: none;
            border: none;
            padding: 0;
            line-height: 1.2;
        }

        .railway-header .header-text p {
            color: #e5e7eb;
            font-size: 13px;
            font-style: italic;
            margin: 0;
            line-height: 1.2;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .offer-card {
            background: #ffffff;
            width: 550px;
            padding: 40px;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .offer-card:hover {
            border-color: #b59410;
            box-shadow: 0 12px 30px rgba(74, 14, 21, 0.08);
        }

        .offer-card h2 {
            color: #2c1d1d;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 700;
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

        .accept {
            background-color: #fdf8f0;
            border-color: #b59410;
        }
        .accept .icon {
            background-color: #5c060d;
            color: white;
        }
        .accept span {
            color: #5c060d;
            font-weight: 600;
        }

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

        .back-btn {
            background-color: #5c060d;
            border: 1px solid #5c060d;
            color: #ffffff;
            padding: 12px 22px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            float: left;
            text-decoration: none;
            box-shadow: 0 3px 6px rgba(92, 6, 13, 0.2);
        }

        .back-btn:hover {
            background-color: #4a050a;
            color: #f3d47d;
            border-color: #4a050a;
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

    <header class="railway-header">
        <img src="images2/logo.png" alt="Sri Lanka Railway Logo">
        <div class="header-text">
            <h1>SRI LANKA RAILWAY</h1>
            <h2>QUARTER MANAGEMENT SYSTEM</h2>
        </div>
    </header>

    <div class="main-content">
        <div class="offer-card">
            <h2>Respond to Offer</h2>
            
            <?php echo $message; ?>

            <?php if ($offer_exists): ?>
                <?php if (!$is_accepted): ?>
                    <p style="color: #555; font-size: 15px; margin-bottom: 35px;">You have been assigned to a quarter, Do you,</p>
                    
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
    </div>

</body>
</html>