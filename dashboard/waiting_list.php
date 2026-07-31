<?php
session_start();

// Database Connection (applicants_db සඳහා)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'applicants_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// applied_date සහ employee_marks මත පදනම්ව ස්වයංක්‍රීයව rank එක ගණනය කිරීම
$sql = "SELECT a1.*, 
               (SELECT COUNT(*) 
                FROM waiting_list a2 
                WHERE (a2.applied_date < a1.applied_date) 
                   OR (a2.applied_date = a1.applied_date AND a2.employee_marks > a1.employee_marks)
                   OR (a2.applied_date = a1.applied_date AND a2.employee_marks = a1.employee_marks AND a2.id <= a1.id)
               ) AS calculated_position
        FROM waiting_list a1 
        WHERE a1.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$waiting = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Waiting List Position</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            border: 1.5px solid #a5b4fc;
            border-radius: 16px;
            padding: 40px;
            width: 480px;
            height: 480px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .container:hover {
            border-color: #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .main-heading {
            font-size: 22px;
            font-weight: bold;
            color: #222;
            line-height: 1.4;
            margin-top: 10px;
        }

        .position-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .position-box {
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            width: 85px;
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
        }

        .position-number {
            font-size: 32px;
            font-weight: bold;
            color: #222;
        }

        .position-label {
            font-size: 16px;
            color: #555;
            font-weight: 500;
        }

        .back-btn-container {
            width: 100%;
            margin-bottom: 10px;
        }

        .back-btn {
            display: block;
            width: 100%;
            background: #fbbf24;
            border: none;
            color: #111;
            padding: 14px 0;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .back-btn:hover {
            background: #d97706;
            color: #ffffff;
        }

        .no-position {
            font-size: 15px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="main-heading">
            Your Waiting List Position
        </div>

        <?php if ($waiting): ?>
            <div class="position-wrapper">
                <div class="position-box">
                    <div class="position-number"><?php echo htmlspecialchars($waiting['calculated_position']); ?></div>
                </div>
                <div class="position-label">Your Position</div>
            </div>
        <?php else: ?>
            <div class="no-position">
                <p>You are not currently on the waiting list.</p>
                <p style="margin-top: 5px;"><a href="request_quarters.php" style="color: #f59e0b; text-decoration: none; font-weight: 600;">Submit an application &rarr;</a></p>
            </div>
        <?php endif; ?>

        <div class="back-btn-container">
            <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>
        </div>
    </div>

</body>
</html>