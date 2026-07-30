<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quarter_type = trim($_POST['quarter_type']);
    $application_date = date('Y-m-d');
    
    $sql = "INSERT INTO applications (user_id, quarter_type, application_date, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $quarter_type, $application_date);
    
    if ($stmt->execute()) {
        $message = '<div style="color: green; text-align: center;">Your application has been submitted successfully!</div>';
        
        // Add to waiting list
        $pos_sql = "SELECT COUNT(*) as count FROM waiting_list";
        $pos_result = $conn->query($pos_sql);
        $pos_row = $pos_result->fetch_assoc();
        $position = $pos_row['count'] + 1;
        
        $wait_sql = "INSERT INTO waiting_list (user_id, position, quarter_type) VALUES (?, ?, ?)";
        $wait_stmt = $conn->prepare($wait_sql);
        $wait_stmt->bind_param("iis", $user_id, $position, $quarter_type);
        $wait_stmt->execute();
        
        // Add notification
        $notif_sql = "INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, 'Application Received', 'Your quarter application has been received and is under review.', 0)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("i", $user_id);
        $notif_stmt->execute();
    } else {
        $message = '<div style="color: red; text-align: center;">Error submitting application. Please try again.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Quarters</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .form-container h2 {
            color: #111;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #111;
        }
        .form-group select, .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
            background: #fff;
        }
        .form-group select:focus, .form-group input:focus {
            border-color: #f59e0b;
            outline: none;
        }
        .submit-btn {
            background: #ffc107;
            color: #111;
            border: 1px solid #e0a800;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        .submit-btn:hover {
            background: #e0a800;
            color: #fff;
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #111;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            color: #f59e0b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="form-container">
            <h2>🏠 Request a Quarter</h2>
            
            <?php echo $message; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Quarter Type</label>
                    <select name="quarter_type" required>
                        <option value="">-- Select --</option>
                        <option value="Type A">Type A (2 Bedroom)</option>
                        <option value="Type B">Type B (3 Bedroom)</option>
                        <option value="Type C">Type C (4 Bedroom)</option>
                    </select>
                </div>
                
                <button type="submit" class="submit-btn">Submit Application</button>
            </form>
            
            <a href="index.php" class="back-link">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>