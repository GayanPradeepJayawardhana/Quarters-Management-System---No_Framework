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
    <title>Quarter Status</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .status-container {
            width: 100%;
            max-width: 750px;
            margin: 20px;
            padding: 40px;
            background: #ffffff;
            border: 2px solid #6fa8dc;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .status-container h2 {
            color: #111;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
        }
        .search-box-wrapper {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .search-box-wrapper input[type="text"] {
            width: 60%;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-box-wrapper button {
            padding: 10px 20px;
            background-color: #9fc5e8;
            border: 1px solid #3b78c2;
            color: #000;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-box-wrapper button:hover {
            background-color: #76a5df;
        }
        .approval-progress-title {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        .approval-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }
        .approval-label {
            width: 140px;
            font-weight: 500;
        }
        .checkbox-box {
            width: 25px;
            height: 25px;
            border: 1px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            background: #fff;
        }
        .reason-label {
            width: 70px;
            font-weight: 500;
        }
        .reason-input {
            flex-grow: 1;
            padding: 8px 12px;
            border: 1px solid #999;
            border-radius: 4px;
            background-color: #fff;
            color: #333;
            font-size: 14px;
        }
        .additional-info {
            margin-top: 30px;
            font-size: 15px;
        }
        .additional-info ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        .additional-info li {
            margin-bottom: 10px;
        }
        .error-msg {
            color: #cc0000;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .back-link {
            display: block;
            width: fit-content;
            box-sizing: border-box;
            margin-top: 30px;
            padding: 12px 20px;
            background-color: #eef2f7;
            border: 1.5px solid #b0c4de;
            border-radius: 8px;
            color: #0066cc;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: background-color 0.2s, border-color 0.2s;
        }
        .back-link:hover { 
            background-color: #e2e8f0; 
            border-color: #3b78c2;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="status-container">
            <h2>View Status</h2>
            
            <!-- Search Form pointing to the same page -->
            <form method="GET" action="">
                <div class="search-box-wrapper">
                    <input type="text" name="computer_no" placeholder="Search by Computer No" value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" name="search">Search</button>
                </div>
            </form>

            <?php if (!empty($error_message)): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($application): ?>
                <div class="approval-progress-title">Approval Progress</div>

                <?php 
                // Helper function to render checkbox symbol and color
                function renderStatusBox($status) {
                    if ($status === 'approved') {
                        return '<span style="color: green;">✅</span>';
                    } elseif ($status === 'rejected') {
                        return '<span style="color: red;">❌</span>';
                    }
                    return ''; // Blank if pending
                }
                ?>

                <!-- 1. Immediate Boss -->
                <div class="approval-row">
                    <div class="approval-label">Immediate Boss</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($application['boss_status']); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($application['boss_reason'] ?? ''); ?>" readonly>
                </div>

                <!-- 2. Personal File -->
                <div class="approval-row">
                    <div class="approval-label">Personal File</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($application['file_status']); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($application['file_reason'] ?? ''); ?>" readonly>
                </div>

                <!-- 3. Subject Clerk -->
                <div class="approval-row">
                    <div class="approval-label">Subject clerk</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($application['clerk_status']); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($application['clerk_reason'] ?? ''); ?>" readonly>
                </div>

                <!-- 4. Final Approval -->
                <div class="approval-row">
                    <div class="approval-label">Final Approval</div>
                    <div class="checkbox-box"><?php echo renderStatusBox($application['final_status']); ?></div>
                    <div class="reason-label">Reason:</div>
                    <input type="text" class="reason-input" value="<?php echo htmlspecialchars($application['final_reason'] ?? ''); ?>" readonly>
                </div>

                <!-- Additional Details -->
                <div class="additional-info">
                    <ul>
                        <li><strong>Marks =</strong> <?php echo htmlspecialchars($application['marks'] !== null ? $application['marks'] : 'Pending'); ?></li>
                        <li><strong>Waiting list number is</strong> <?php echo htmlspecialchars($calculated_position !== null ? $calculated_position : ($application['waiting_list_no'] !== null ? $application['waiting_list_no'] : 'Pending')); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="back-link">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>