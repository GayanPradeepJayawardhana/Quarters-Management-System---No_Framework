<?php
session_start();

// Database Connection Parameters for applicants_db
$host = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "applicants_db";

// Create MySQL connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['nic'])) {
    header("Location: ../login.php");
    exit();
}

$nic = $_SESSION['nic'];
$calculated_position = null;
$waiting = false;

// Get waiting list position for this NIC
$sql = "SELECT waiting_list_no FROM applications WHERE nic = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nic);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['waiting_list_no'] !== null) {
        $calculated_position = $row['waiting_list_no'];
        $waiting = true;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting List - Department of Railways</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome icons -->
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
            max-width: 650px;
            padding: 40px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            text-align: center;
        }
        .status-container:hover {
            border-color: #b59410;
            box-shadow: 0 10px 25px rgba(74, 14, 21, 0.08);
        }
        .status-container h2 {
            color: #2c1d1d;
            margin-bottom: 5px;
            font-size: 26px;
            font-weight: 700;
        }
        .position-box {
            width: 110px;
            height: 90px;
            border: 2px solid #b59410;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 36px;
            color: #5c060d;
            background: #fdf8f0;
            margin: 25px auto 8px auto;
            box-shadow: 0 2px 8px rgba(181, 148, 16, 0.15);
        }
        .back-btn {
            background-color: #5c060d;
            border: 1px solid #5c060d;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-top: 30px;
            box-shadow: 0 2px 5px rgba(92, 6, 13, 0.2);
            width: 100%;
        }
        .back-btn:hover {
            background-color: #4a050a;
            color: #f3d47d;
            border-color: #4a050a;
        }
        .no-position {
            font-size: 16px;
            color: #6b7280;
            margin: 30px 0;
            padding: 20px;
            background-color: #fdf8f0;
            border-radius: 8px;
            border: 1px dashed #b59410;
        }
    </style>
</head>
<body>

    <!-- Top Dark Red Header Bar with Gold Border -->
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

    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="status-container">
            
            <h2>Your Waiting List Position</h2>
            
            <?php if ($waiting && $calculated_position !== null): ?>
                <p class="text-gray-500 text-sm mb-4">View your current queue position for quarter allocation.</p>
                
                <!-- Position Display Box -->
                <div class="position-box">
                    <?php echo htmlspecialchars($calculated_position); ?>
                </div>
                <div class="text-gray-600 font-semibold text-sm mb-6">Your Position</div>
            <?php else: ?>
                <div class="no-position">
                    <p class="font-medium text-gray-700">You are not currently on the waiting list.</p>
                    <p style="margin-top: 8px;"><a href="request_quarters.php" class="text-[#b59410] hover:underline font-semibold">Submit an application &rarr;</a></p>
                </div>
            <?php endif; ?>

            <!-- Back to Dashboard Button -->
            <div>
                <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>

</body>
</html>
<?php 
if (isset($conn)) {
    $conn->close(); 
}
?>