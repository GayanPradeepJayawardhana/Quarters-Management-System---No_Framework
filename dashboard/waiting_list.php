<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM waiting_list WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$waiting = $result->fetch_assoc();

// Get total waiting count
$total_sql = "SELECT COUNT(*) as total FROM waiting_list";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_waiting = $total_row['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .waiting-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            text-align: center;
        }
        .waiting-container h2 {
            color: #111;
            margin-bottom: 20px;
        }
        .position-box {
            background: #f3f4f6;
            padding: 40px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .position-number {
            font-size: 72px;
            font-weight: 700;
            color: #f59e0b;
        }
        .position-label {
            font-size: 18px;
            color: #4b5563;
        }
        .total-waiting {
            color: #6b7280;
            font-size: 16px;
        }
        .info-card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #111;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { color: #f59e0b; }
        .no-position {
            padding: 30px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="waiting-container">
            <h2>📋 Your Waiting List Position</h2>
            
            <?php if ($waiting): ?>
                <div class="position-box">
                    <div class="position-number">#<?php echo htmlspecialchars($waiting['position']); ?></div>
                    <div class="position-label">Your Position</div>
                </div>
                
                <div class="info-card">
                    <strong>Quarter Type:</strong> <?php echo htmlspecialchars($waiting['quarter_type']); ?>
                </div>
                
                <div class="total-waiting">
                    Total applicants in queue: <strong><?php echo $total_waiting; ?></strong>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #fef3c7; border-radius: 8px; color: #92400e;">
                    ⏳ Estimated waiting time: Approximately 2-4 weeks
                </div>
            <?php else: ?>
                <div class="no-position">
                    <p>You are not currently on the waiting list.</p>
                    <p style="margin-top: 10px;"><a href="request_quarters.php" style="color: #f59e0b;">Submit an application →</a></p>
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="back-link">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>