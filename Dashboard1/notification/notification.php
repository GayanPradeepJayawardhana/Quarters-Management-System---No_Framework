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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification View</title>
    <!-- FontAwesome link for the Bell Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* මුළු පිටුවේ පසුබිම */
        body {
            background-color: #f4f7f6; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Notification Card එකේ ප්‍රධාන ඩිසයින් එක */
        .notification-card {
            background-color: #ffffff;
            border: 2px solid #1a365d;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 10px 25px rgba(26, 54, 93, 0.15);
        }

        /* Badge එක */
        .badge-notification {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #e53e3e;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        /* Title එක සහ Bell Icon එක */
        .header {
            text-align: center;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 32px;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .fa-bell {
            color: #ffc107;
            font-size: 35px;
        }

        /* Date එක තියෙන පේළිය */
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-row label {
            width: 80px;
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .form-row input {
            flex: 1;
            max-width: 250px;
            padding: 10px 15px;
            border: 1px solid #1a365d;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            color: #333;
            box-sizing: border-box;
        }

        /* මැද තියෙන ලොකු Message කොටුව */
        .message-box {
            width: 100%;
            min-height: 150px;
            border: 1px solid #1a365d;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            box-sizing: border-box;
            padding: 30px 20px;
            text-align: center;
        }

        .message-box h3 {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin: 0;
        }
        
        .message-content {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
        }

        /* Buttons ටික */
        .actions {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        /* ==========================================
           BUTTONS & HOVER EFFECTS STYLES
           ========================================== */
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #ffc107;
            border: 1px solid #e0a800;
            border-radius: 8px;
            color: #111111;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            width: 160px;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }

        /* Mouse එක Button එක උඩට ගෙන ගිය විට (Hover effect) */
        .btn:hover {
            background-color: #e0a800;
            color: #ffffff;
            border-color: #d39e00;
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.4);
            transform: translateY(-2px);
        }

        .no-notification {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }

        /* --- Mobile Responsive Rules --- */
        @media (max-width: 600px) {
            .notification-card {
                padding: 25px 20px;
            }

            .header h2 {
                font-size: 24px;
            }

            .fa-bell {
                font-size: 26px;
            }

            .form-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .form-row input {
                max-width: 100%;
                width: 100%;
            }

            .message-box {
                min-height: 120px;
                padding: 20px;
            }

            .message-box h3 {
                font-size: 20px;
            }
            
            .message-content {
                font-size: 16px;
            }

            .actions {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
            }

            .badge-notification {
                top: 15px;
                right: 15px;
                font-size: 12px;
                padding: 5px 10px;
            }
        }
    </style>
</head>
<body>

    <div class="notification-card">
        <!-- New Badge -->
        <div class="badge-notification">
            <?php echo ($notification && $notification['is_read'] == 0) ? '1 New' : 'Read'; ?>
        </div>
        
        <!-- Header with Bell Icon -->
        <div class="header">
            <h2>Notification <i class="fa-solid fa-bell"></i></h2>
        </div>
        
        <form method="POST" action="">
            <!-- Date Row -->
            <div class="form-row">
                <label for="date">Date</label>
                <input type="text" id="date" name="date" value="<?php echo $currentDate; ?>" readonly>
            </div>
            
            <!-- Message Area -->
            <div class="message-box">
                <?php if ($notification): ?>
                    <div class="message-content">
                        <strong><?php echo htmlspecialchars($notification['title']); ?></strong><br><br>
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </div>
                <?php else: ?>
                    <div class="no-notification">
                        <p>📭 No notifications available</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <div class="actions">
                <button type="submit" name="mark_read" class="btn" <?php echo (!$notification || $notification['is_read'] == 1) ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''; ?>>
                    Mark as read
                </button>
                <button type="submit" name="delete" class="btn" <?php echo !$notification ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''; ?>>
                    Delete
                </button>
            </div>
        </form>
    </div>

</body>
</html>