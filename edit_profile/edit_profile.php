<?php
session_start();

// Database connection
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Form submitted - Update Database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name  = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_name, $new_email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['user_name'] = $new_name;
        $success = "Profile updated successfully!";
        
        // Update display name
        $user_name = $new_name;
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Get current user data
$stmt_fetch = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: ../login.php");
    exit();
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $user['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f0f0f0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            background-color: #ffffff; 
            padding: 30px; 
            border-radius: 12px; 
            width: 100%; 
            max-width: 450px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
            border: 1px solid #d1d5db;
        }
        h2 { 
            text-align: center; 
            color: #111111; 
            margin-bottom: 25px; 
            font-size: 24px; 
            font-weight: 700;
        }
        .form-group { 
            margin-bottom: 20px; 
            display: flex; 
            flex-direction: column; 
        }
        label { 
            margin-bottom: 8px; 
            font-size: 16px; 
            color: #111111; 
            font-weight: 600; 
        }
        input[type="text"], input[type="email"] { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #d1d5db; 
            background-color: #fff; 
            font-size: 16px; 
            border-radius: 6px; 
            outline: none; 
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="email"]:focus {
            border-color: #f59e0b; 
            box-shadow: 0 0 5px rgba(245, 158, 11, 0.3);
        }
        .btn-container { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 30px; 
            gap: 15px; 
        }
        .btn-custom { 
            background-color: #ffc107; 
            color: #111111; 
            border: 1px solid #e0a800; 
            padding: 10px 25px; 
            font-size: 16px; 
            font-weight: bold;
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s ease-in-out; 
        }
        .btn-custom:hover { 
            background-color: #e0a800; 
            color: #ffffff;
            border-color: #d39e00;
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.4);
            transform: translateY(-2px);
        }
        .error-msg { 
            text-align: center; 
            margin-bottom: 15px; 
            color: #dc2626; 
            font-weight: bold; 
            font-size: 14px; 
        }
        .success-msg { 
            text-align: center; 
            margin-bottom: 15px; 
            color: #16a34a; 
            font-weight: bold; 
            font-size: 14px; 
        }
        @media (min-width: 480px) {
            .container {
                padding: 40px;
            }
            .form-group { 
                flex-direction: row; 
                align-items: center; 
            }
            label { 
                width: 90px; 
                margin-bottom: 0; 
                font-size: 16px; 
                font-weight: 600; 
            }
            input[type="text"], input[type="email"] { 
                flex: 1; 
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✏️ Edit Profile</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div class="success-msg"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name :</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </div>

        <div class="btn-container">
            <a href="../dashboard/index.php" class="btn-custom">&larr; Back</a>
            <button type="submit" class="btn-custom">Save</button>
        </div>
    </form>
</div>

</body>
</html>
<?php 
if (isset($conn)) { 
    $conn->close(); 
} 
?>