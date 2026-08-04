<?php
session_start();
require_once '../../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// AJAX ඉල්ලීම් (Requests) හැසිරවීම
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $target_id = isset($_POST['action_id']) ? (int)$_POST['action_id'] : 0;

    if ($_POST['ajax_action'] === 'mark_read' && $target_id > 0) {
        $update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $target_id, $user_id);
        if ($update_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => '✅ Notification marked as read!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update']);
        }
        exit();
    } 
    elseif ($_POST['ajax_action'] === 'delete' && $target_id > 0) {
        $delete_sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $target_id, $user_id);
        if ($delete_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => '🗑️ Notification deleted!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete']);
        }
        exit();
    }
}

// සියලුම notifications ලබාගැනීම
$notif_sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result();

$notifications = [];
while ($row = $notif_result->fetch_assoc()) {
    $notifications[] = $row;
}
$total_notifications = count($notifications);
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications UI</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .notification-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            width: 750px;
            max-height: 85vh;
            overflow-y: auto;
            text-align: center;
            position: relative;
            border: 1px solid #b3d7ff;
        }

        .title-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
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

        .counter-badge {
            font-size: 13px;
            background: #e9ecef;
            padding: 4px 10px;
            border-radius: 12px;
            color: #495057;
            margin-bottom: 20px;
            display: inline-block;
            font-weight: bold;
        }

        .single-notification-box {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafbfc;
            text-align: left;
        }

        .date-row {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 10px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-content {
            font-size: 15px;
            color: #333;
            line-height: 1.5;
            word-break: break-word;
            margin-bottom: 15px;
        }

        .notification-footer-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #e9ecef;
            padding-top: 12px;
            margin-top: 10px;
        }

        .no-notification {
            color: #6b7280;
            font-size: 15px;
            padding: 30px;
            text-align: center;
        }

        .btn {
            background-color: #eef2f7;
            border: 1px solid #d0d7de;
            color: #007bff;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.2s;
            text-decoration: none;
        }

        .btn-delete {
            color: #dc3545;
            border-color: #f5c6cb;
            background-color: #fff5f5;
        }

        .btn-delete:hover {
            background-color: #ffe3e3;
        }

        .btn-mark-read:hover {
            background-color: #e2e8f0;
        }

        /* ඔබ ඉල්ලා සිටි වර්ණ හා විලාසිතාවන්ම යොදා, width එක කුඩා කර (auto) සකස් කළ .back-btn එක */
        .back-btn {
            background-color: #eef2f7;
            border: 1px solid #d0d7de;
            color: #007bff;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
            width: auto;
            text-decoration: none;
            box-shadow: none;
            margin-top: 15px;
        }

        .back-btn:hover {
            background-color: #e2e8f0;
            color: #0056b3;
        }

        .no-position {
            font-size: 15px;
            color: #6b7280;
        }

        /* Toast Message Styling (උඩ මැදින් වැටෙන අයුරින් සහ ලොකු අකුරින් සකසා ඇත) */
        #toast-message {
            position: fixed;
            top: 25px;
            left: 50%;
            transform: translateX(-50%) translateY(-30px);
            background-color: #212529;
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            pointer-events: none;
            border: 1px solid #495057;
            text-align: center;
        }

        #toast-message.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

    <!-- Toast Message Container -->
    <div id="toast-message"></div>

    <div class="notification-card" id="notificationCard">
        <div class="title-container">
            <h2>Notifications</h2>
            <span class="bell-icon"><i class="fa-solid fa-bell"></i></span>
        </div>

        <?php if ($total_notifications > 0): ?>
            <div class="counter-badge" id="counterBadge">
                Total Notifications: <?php echo $total_notifications; ?>
            </div>

            <div id="notificationsContainer">
                <?php foreach ($notifications as $notif): ?>
                    <div class="single-notification-box" id="notif-box-<?php echo $notif['id']; ?>">
                        <div class="date-row">
                            <span><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($notif['created_at'])); ?></span>
                            <span id="status-text-<?php echo $notif['id']; ?>">
                                <?php if ($notif['is_read'] == 1): ?>
                                    <span style="color: green; font-size: 12px;"><i class="fas fa-check-circle"></i> Read</span>
                                <?php else: ?>
                                    <span style="color: red; font-size: 12px;"><i class="fas fa-exclamation-circle"></i> Unread</span>
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="message-content">
                            <strong><?php echo htmlspecialchars($notif['title']); ?></strong>
                            <br><br>
                            <?php echo htmlspecialchars($notif['message']); ?>
                        </div>

                        <div class="notification-footer-row">
                            <span id="mark-read-btn-wrap-<?php echo $notif['id']; ?>">
                                <?php if ($notif['is_read'] == 0): ?>
                                    <button type="button" onclick="handleAction(<?php echo $notif['id']; ?>, 'mark_read')" class="btn btn-mark-read">
                                        <i class="fas fa-check"></i> Mark as read
                                    </button>
                                <?php endif; ?>
                            </span>

                            <button type="button" onclick="handleAction(<?php echo $notif['id']; ?>, 'delete')" class="btn btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="no-notification no-position" id="noNotificationDiv">
                <i class="fas fa-envelope" style="font-size: 30px; margin-bottom: 10px; display: block;"></i> No notifications available
            </div>
        <?php endif; ?>
        
        <div>
            <a href="../../dashboard/index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- JavaScript සහ Toast මැසේජ් පෙන්වන කේතය -->
    <script>
        function showToast(message) {
            let toast = document.getElementById('toast-message');
            toast.innerText = message;
            toast.classList.add('show');

            // තත්පර 3 කින් මැසේජ් එක ස්වයංක්‍රීයව අතුරුදහන් වීම
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function handleAction(id, action) {
            let formData = new URLSearchParams();
            formData.append('ajax_action', action);
            formData.append('action_id', id);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // උඩ මැදින් ලොකු අකුරින් මැසේජ් එක පෙන්වීම
                    showToast(data.message);

                    if (action === 'mark_read') {
                        // Read තත්ත්වයට මාරු කිරීම
                        document.getElementById('status-text-' + id).innerHTML = '<span style="color: green; font-size: 12px;"><i class="fas fa-check-circle"></i> Read</span>';
                        let readBtnWrap = document.getElementById('mark-read-btn-wrap-' + id);
                        if (readBtnWrap) readBtnWrap.innerHTML = '';
                    } else if (action === 'delete') {
                        // අදාළ box එක ඉවත් කිරීම
                        let box = document.getElementById('notif-box-' + id);
                        if (box) box.remove();

                        // Total count එක අඩු කිරීම
                        let badge = document.getElementById('counterBadge');
                        if (badge) {
                            let currentCountMatch = badge.innerText.match(/\d+/);
                            if (currentCountMatch) {
                                let newCount = parseInt(currentCountMatch[0]) - 1;
                                badge.innerText = 'Total Notifications: ' + newCount;
                                if (newCount <= 0) {
                                    location.reload(); // කිසිදු notification එකක් නැති නම් එකවරම No notifications පෙන්වීමට
                                }
                            }
                        }
                    }
                } else {
                    showToast('❌ Action failed!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>

</body>
</html>