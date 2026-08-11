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
    header("Location: ../login.php");
    exit();
}

$search_query = "";
$application = null;
$error_message = "";
$calculated_position = null;

// Handle Search by Computer No within the same page
if (isset($_GET['search'])) {
    $search_query = trim($_GET['computer_no']);
    if (!empty($search_query)) {
        $sql = "SELECT * FROM applications WHERE computer_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $application = $result->fetch_assoc();
            
            // Get the user_id associated with this application to calculate waiting list position
            $app_user_id = $application['user_id'] ?? null;
            
            if ($app_user_id) {
                $pos_sql = "SELECT (SELECT COUNT(*) 
                             FROM waiting_list a2 
                             WHERE (a2.applied_date < a1.applied_date) 
                                OR (a2.applied_date = a1.applied_date AND a2.employee_marks > a1.employee_marks)
                                OR (a2.applied_date = a1.applied_date AND a2.employee_marks = a1.employee_marks AND a2.id <= a1.id)
                             ) AS calculated_position
                     FROM waiting_list a1 
                     WHERE a1.user_id = ?";
                
                $pos_stmt = $conn->prepare($pos_sql);
                $pos_stmt->bind_param("i", $app_user_id);
                $pos_stmt->execute();
                $pos_result = $pos_stmt->get_result();
                if ($pos_row = $pos_result->fetch_assoc()) {
                    $calculated_position = $pos_row['calculated_position'];
                }
                $pos_stmt->close();
            }
        } else {
            $error_message = "No application found with Computer No: " . htmlspecialchars($search_query);
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Status - Department of Railways</title>
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
            height: 70px !important;
            width: auto !important;
            max-width: none;
            object-fit: contain;
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 30px 15px;
        }
        .status-container {
            width: 100%;
            max-width: 750px;
            padding: 40px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }
        .status-container:hover {
            border-color: #b59410;
            box-shadow: 0 10px 25px rgba(74, 14, 21, 0.08);
        }
        .status-container h2 {
            color: #2c1d1d;
            margin-bottom: 5px;
            text-align: center;
            font-size: 26px;
            font-weight: 700;
        }
        .search-box-wrapper {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .search-box-wrapper input[type="text"] {
            width: 65%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, ring 0.2s;
        }
        .search-box-wrapper input[type="text"]:focus {
            border-color: #5c060d;
            box-shadow: 0 0 0 3px rgba(92, 6, 13, 0.1);
        }
        .search-box-wrapper button {
            padding: 12px 24px;
            background-color: #5c060d;
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
        }
        .search-box-wrapper button:hover {
            background-color: #4a050a;
            color: #f3d47d;
        }
        .approval-progress-title {
            font-size: 16px;
            font-weight: bold;
            color: #5c060d;
            border-bottom: 2px solid #b59410;
            padding-bottom: 6px;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        .approval-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }
        .approval-label {
            width: 150px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        .checkbox-box {
            width: 35px;
            height: 35px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            background: #fdf8f0;
        }
        .reason-label {
            width: 70px;
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
        }
        .reason-input {
            flex-grow: 1;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background-color: #f9fafb;
            color: #374151;
            font-size: 14px;
        }
        .additional-info {
            margin-top: 30px;
            font-size: 15px;
            background-color: #fdf8f0;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #b59410;
        }
        .additional-info ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }
        .additional-info li {
            margin-bottom: 8px;
            color: #374151;
        }
        .error-msg {
            color: #dc2626;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            background-color: #fef2f2;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #fca5a5;
        }
        .back-btn {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #5c060d;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-top: 25px;
        }
        .back-btn:hover {
            background-color: #5c060d;
            border-color: #5c060d;
            color: #f3d47d;
        }
    </style>
</head>
<body>

    <!-- Top Dark Red Header Bar -->
    <header class="bg-[#5c060d] text-white py-4 px-6 md:px-12 shadow-md flex flex-col md:flex-row justify-between items-center relative border-b-4 border-[#b59410]">
        <!-- Logo and Titles -->
        <div class="flex items-center space-x-4">
            <img src="images2/logo.png" alt="Sri Lanka Railway Logo" class="slr-logo">
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-wider">SRI LANKA RAILWAY</h1>
                <h2 class="text-sm md:text-base font-semibold tracking-wide text-amber-200">QUARTER MANAGEMENT SYSTEM</h2>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="status-container">
            <div class="text-[#b59410] text-2xl text-center mb-1"><i class="fas fa-clipboard-list"></i></div>
            <h2>View Status</h2>
            <p class="text-center text-gray-500 text-sm mb-6">Track your application verification progress.</p>
            
            <!-- Search Form pointing to the same page -->
            <form method="GET" action="">
                <div class="search-box-wrapper">
                    <input type="text" name="computer_no" placeholder="Search by Computer No" value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" name="search"><i class="fas fa-search mr-1"></i> Search</button>
                </div>
            </form>

            <?php if (!empty($error_message)): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($application): ?>
                <div class="approval-progress-title">Approval Progress</div>

                <?php 
                // පිළිවෙළට ස්ටෙප්ස් 4 නිර්වචනය කිරීම
                $boss_status  = $application['boss_status'] ?? 'pending';
                $boss_reason  = $application['boss_reason'] ?? '';

                $file_status  = $application['file_status'] ?? 'pending';
                $file_reason  = $application['file_reason'] ?? '';

                $clerk_status = $application['clerk_status'] ?? 'pending';
                $clerk_reason = $application['clerk_reason'] ?? '';

                $final_status = $application['final_status'] ?? 'pending';
                $final_reason = $application['final_reason'] ?? '';

                // 1. Immediate Boss reject කර ඇත්නම් අනෙක් සියල්ලටම ❌ යෙදීම
                if ($boss_status === 'rejected') {
                    if ($file_status === 'pending') { $file_status = 'rejected'; $file_reason = 'Previous step rejected'; }
                    if ($clerk_status === 'pending') { $clerk_status = 'rejected'; $clerk_reason = 'Previous step rejected'; }
                    if ($final_status === 'pending') { $final_status = 'rejected'; $final_reason = 'Previous step rejected'; }
                }

                // 2. Personal File reject කර ඇත්නම් අනෙක් පසුව එන ඒවාට ❌ යෙදීම
                if ($file_status === 'rejected') {
                    if ($clerk_status === 'pending') { $clerk_status = 'rejected'; $clerk_reason = 'Previous step rejected'; }
                    if ($final_status === 'pending') { $final_status = 'rejected'; $final_reason = 'Previous step rejected'; }
                }

                // 3. Subject Clerk reject කර ඇත්නම් Final Approval එකට ❌ යෙදීම
                if ($clerk_status === 'rejected') {
                    if ($final_status === 'pending') { $final_status = 'rejected'; $final_reason = 'Previous step rejected'; }
                }

                // Helper function to render checkbox symbol and color
                function renderStatusBox($status) {
                    if ($status === 'approved') {
                        return '<span style="color: #16a34a;">✅</span>';
                    } elseif ($status === 'rejected') {
                        return '<span style="color: #dc2626;">❌</span>';
                    }
                    return ''; // Blank if pending
                }
                ?>

                <!-- 1. Immediate Boss -->
                <div class="approval-row">
                    <div class="approval-label">Immediate Boss</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($boss_status); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($boss_reason); ?>" readonly>
                </div>

                <!-- 2. Personal File -->
                <div class="approval-row">
                    <div class="approval-label">Personal File</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($file_status); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($file_reason); ?>" readonly>
                </div>

                <!-- 3. Subject Clerk -->
                <div class="approval-row">
                    <div class="approval-label">Subject clerk</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($clerk_status); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($clerk_reason); ?>" readonly>
                </div>

                <!-- 4. Final Approval -->
                <div class="approval-row">
                    <div class="approval-label">Final Approval</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($final_status); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($final_reason); ?>" readonly>
                </div>

                <!-- Additional Details -->
                <div class="additional-info">
                    <ul>
                        <li><strong>Marks =</strong> <?php echo htmlspecialchars($application['marks'] !== null ? $application['marks'] : 'Pending'); ?></li>
                        <li><strong>Waiting list number is</strong> <?php echo htmlspecialchars($calculated_position !== null ? $calculated_position : ($application['waiting_list_no'] !== null ? $application['waiting_list_no'] : 'Pending')); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
<?php 
if (isset($conn)) {
    $conn->close(); 
}
?>