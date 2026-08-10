<?php
session_start();

// Database Connection Parameters for applicants_db
$host = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "applicants_db";

// MySQL සම්බන්ධතාවය සෑදීම
$conn = new mysqli($host, $username, $password, $dbname);

// සම්බන්ධතාවය පරීක්ෂා කිරීම
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// AJAX ඉල්ලීම් (Requests) හැසිරවීම (Mark as read සහ Delete)
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
        $update_stmt->close();
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
        $delete_stmt->close();
        exit();
    }
}

// නොටිෆිකේෂන් දත්ත ලබා ගැනීම
$notifications = [];
$notif_sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC";
$notif_stmt = $conn->prepare($notif_sql);
if ($notif_stmt) {
    $notif_stmt->bind_param("i", $user_id);
    $notif_stmt->execute();
    $notif_result = $notif_stmt->get_result();
    while ($row = $notif_result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $notif_stmt->close();
}

$total_notifications = count($notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Department of Railways</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome අයිකන සඳහා -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fcfbfa;
            color: #111111;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .slr-logo {
            height: 65px !important;
            width: auto !important;
            object-fit: contain;
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 30px 20px;
        }
        .status-container {
            width: 100%;
            max-width: 950px; 
            padding: 50px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            max-height: 88vh;
            overflow-y: auto;
        }
        .status-container:hover {
            border-color: #b59410;
            box-shadow: 0 12px 30px rgba(74, 14, 21, 0.08);
        }
        .status-container h2 {
            color: #2c1d1d;
            margin-bottom: 20px;
            text-align: center;
            font-size: 30px;
            font-weight: 700;
        }
        .notif-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-left: 5px solid #b59410;
            border-radius: 10px;
            padding: 22px 25px;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        .notif-card:hover {
            border-color: #5c060d;
            border-left-color: #5c060d;
            box-shadow: 0 6px 15px rgba(92, 6, 13, 0.06);
        }
        .btn {
            background-color: #fdf8f0;
            border: 1px solid #b59410;
            color: #5c060d;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn:hover {
            background-color: #f3d47d;
        }
        .delete-btn {
            background-color: #fff;
            border: 1px solid #fca5a5;
            color: #dc2626;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .delete-btn:hover {
            background-color: #fef2f2;
            border-color: #dc2626;
        }
        .back-btn {
            background-color: #5c060d;
            border: 1px solid #5c060d;
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-top: 30px;
            box-shadow: 0 3px 6px rgba(92, 6, 13, 0.2);
        }
        .back-btn:hover {
            background-color: #4a050a;
            color: #f3d47d;
            border-color: #4a050a;
        }

        /* Toast Message Styling */
        #toast-message {
            position: fixed;
            top: 25px;
            left: 50%;
            transform: translateX(-50%) translateY(-30px);
            background-color: #5c060d;
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            pointer-events: none;
            border: 1px solid #b59410;
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

    <!-- Top Dark Red Header Bar with Gold Border -->
    <header class="bg-[#5c060d] text-white py-4 px-6 md:px-12 shadow-md flex flex-col md:flex-row justify-between items-center relative border-b-4 border-[#b59410]">
        <!-- Logo and Titles -->
        <div class="flex items-center space-x-4">
            <!-- නිවැරදි path එක සහ slr-logo class එක මෙහි යොදා ඇත -->
            <img src="../../dashboard/images2/logo.png" alt="Notification Logo" class="slr-logo">
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-wider">SRI LANKA RAILWAY</h1>
                <h2 class="text-sm md:text-base font-semibold tracking-wide text-amber-200">QUARTER MANAGEMENT SYSTEM</h2>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="status-container text-center">
            <div class="text-[#b59410] text-4xl mb-2"><i class="fas fa-bell"></i></div>
            <h2>Notifications</h2>

            <div class="text-left" id="notificationsContainer">
                <?php if ($total_notifications > 0): ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="notif-card" id="notif-box-<?php echo $notif['id']; ?>">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs md:text-sm text-gray-500 font-medium">
                                    <i class="far fa-calendar-alt mr-1 text-[#b59410]"></i> 
                                    <?php echo htmlspecialchars($notif['date'] ?? $notif['created_at'] ?? '2026-08-04'); ?>
                                </span>
                                <span id="status-text-<?php echo $notif['id']; ?>">
                                    <?php if (isset($notif['is_read']) && $notif['is_read'] == 1): ?>
                                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Read
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded border border-red-200">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Unread
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <h3 class="font-bold text-[#2c1d1d] text-lg mb-2">
                                <?php echo htmlspecialchars($notif['title'] ?? 'Interview Schedule'); ?>
                            </h3>
                            <p class="text-sm md:text-base text-gray-600 mb-4 leading-relaxed">
                                <?php echo htmlspecialchars($notif['message'] ?? $notif['description'] ?? ''); ?>
                            </p>
                            <hr class="border-gray-100 mb-4">
                            <div class="flex items-center justify-end space-x-3">
                                <div id="mark-read-btn-wrap-<?php echo $notif['id']; ?>">
                                    <?php if (!isset($notif['is_read']) || $notif['is_read'] == 0): ?>
                                        <button type="button" onclick="handleAction(<?php echo $notif['id']; ?>, 'mark_read')" class="btn">
                                            <i class="fas fa-check"></i> Mark as read
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <button type="button" onclick="handleAction(<?php echo $notif['id']; ?>, 'delete')" class="delete-btn">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12 text-gray-500 bg-[#fdf8f0] rounded-lg border border-dashed border-[#b59410]">
                        <p class="font-medium text-base">No notifications available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Back to Dashboard Button -->
            <div class="text-center">
                <a href="../../dashboard/index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- JavaScript සඳහා Toast සහ AJAX හැසිරවීම -->
    <script>
        function showToast(message) {
            let toast = document.getElementById('toast-message');
            toast.innerText = message;
            toast.classList.add('show');

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
                    showToast(data.message);

                    if (action === 'mark_read') {
                        document.getElementById('status-text-' + id).innerHTML = '<span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded border border-green-200"><i class="fas fa-check-circle mr-1"></i> Read</span>';
                        let readBtnWrap = document.getElementById('mark-read-btn-wrap-' + id);
                        if (readBtnWrap) readBtnWrap.innerHTML = '';
                    } else if (action === 'delete') {
                        let box = document.getElementById('notif-box-' + id);
                        if (box) box.remove();

                        let container = document.getElementById('notificationsContainer');
                        if (container && container.querySelectorAll('.notif-card').length === 0) {
                            location.reload();
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
<?php 
if (isset($conn)) {
    $conn->close(); 
}
?>