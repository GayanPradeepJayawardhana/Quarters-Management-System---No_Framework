<?php
session_start();
require_once '../../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$currentDate = date('Y-m-d');

// Get notifications for this user
$notif_sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result();
$notification = $notif_result->fetch_assoc();

// Button clicks handle කිරීම
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_read'])) {
        if ($notification) {
            $update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $notification['id']);
            $update_stmt->execute();
        }
        echo "<script>alert('✅ Notification marked as read!');</script>";
    } elseif (isset($_POST['delete'])) {
        if ($notification) {
            $delete_sql = "DELETE FROM notifications WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $notification['id']);
            $delete_stmt->execute();
        }
        echo "<script>alert('🗑️ Notification deleted!');</script>";
        // Refresh page to show updated data
        header("Refresh:0");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification UI</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .notification-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 450px;
            text-align: center;
            position: relative;
            border: 1px solid #b3d7ff; /* Light blue border */
        }

        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #dc3545; /* Red */
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .title-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .bell-icon {
            font-size: 24px;
            color: #ffc107;
        }

        .date-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            text-align: left;
        }

        .date-label {
            font-weight: bold;
            width: 60px;
        }

        .date-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            color: #495057;
            outline: none;
        }

        .message-box {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 30px 20px;
            margin-bottom: 30px;
            color: #6c757d;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 100px;
        }

        .message-content {
            font-size: 15px;
            color: #333;
            line-height: 1.5;
            text-align: center;
        }

        .no-notification {
            color: #6c757d;
            font-size: 15px;
        }

        /* === බොත්තම් සඳහා ස්ටයිල් === */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 12px;
        }

        .btn {
            background-color: #eef2f7;
            border: 1px solid #d0d7de;
            color: #007bff;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
            text-decoration: none;
        }

        /* Mark as read සහ Delete බොත්තම් */
        .btn-mark-read, .btn-delete {
            flex: 1;
        }

        /* Back to Dashboard බොත්තම ටිකක් කුඩා කර සැකසූ අයුරු */
        .btn-back {
            background-color: #eef2f7;
            border: 1px solid #d0d7de;
            color: #007bff;
            padding: 7px 14px;          /* ප්‍රමාණය අඩු කරන ලදී */
            border-radius: 6px;        /* border radius එක ටිකක් අඩු කළා */
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;            /* font size එක ටිකක් කුඩා කළා */
            font-weight: 500;
            transition: background-color 0.2s;
            text-decoration: none;
            width: auto;               /* සම්පූර්ණ පළල වෙනුවට අන්තර්ගතයට අනුව සකස් වේ */
            margin: 0 auto;
        }

        .btn:hover, .btn-back:hover {
            background-color: #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="notification-card">
        <!-- Status Badge (Read / New) -->
        <div class="status-badge">
            <?php echo ($notification && $notification['is_read'] == 0) ? '1 New' : 'Read'; ?>
        </div>

        <div class="title-container">
            <h2>Notification</h2>
            <span class="bell-icon"><i class="fa-solid fa-bell"></i></span>
        </div>

        <form method="POST" action="" style="width: 100%;">
            <div class="date-row">
                <span class="date-label">Date</span>
                <input type="text" class="date-input" name="date" value="<?php echo $currentDate; ?>" readonly>
            </div>

            <div class="message-box">
                <?php if ($notification): ?>
                    <div class="message-content">
                        <strong><?php echo htmlspecialchars($notification['title']); ?></strong><br><br>
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </div>
                <?php else: ?>
                    <div class="no-notification">
                        <i class="fas fa-envelope"></i> No notifications available
                    </div>
                <?php endif; ?>
            </div>

            <!-- === බොත්තම් කොටස === -->
            <div class="button-container">
                <button type="submit" name="mark_read" class="btn btn-mark-read">
                    <i class="fas fa-check"></i> Mark as read
                </button>
                <button type="submit" name="delete" class="btn btn-delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
            
            <div style="text-align: center;">
                <a href="../../dashboard/index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>

    </div>

</body>
</html>