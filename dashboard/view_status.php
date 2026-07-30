<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Status</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .status-container h2 {
            color: #111;
            margin-bottom: 20px;
            text-align: center;
        }
        .status-table {
            width: 100%;
            border-collapse: collapse;
        }
        .status-table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .status-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .no-applications {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #111;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { color: #f59e0b; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="status-container">
            <h2>📊 Application Status</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <table class="status-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quarter Type</th>
                            <th>Application Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['quarter_type']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['application_date'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-applications">
                    <p>You haven't submitted any applications yet.</p>
                    <p style="margin-top: 10px;"><a href="request_quarters.php" style="color: #f59e0b;">Start your first application →</a></p>
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="back-link">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>