<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include('db.php');
$user_id = $_SESSION["user_id"];

// Fetch user details
$stmt = $conn->prepare("SELECT name, dob, profession, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle password update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_password"])) {
    $new_password = password_hash($_POST["new_password"], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $user_id);

    if ($stmt->execute()) {
        echo "Password updated successfully!";
    } else {
        echo "Error updating password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f2ff 0%, #e2e6ff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .profile-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
        }

        .profile-header {
            margin-bottom: 2.5rem;
        }

        .profile-header h1 {
            color: #1a1a1a;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .profile-info {
            background: #f8faff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eef1ff;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #1a1a1a;
            font-weight: 600;
        }

        .password-section {
            margin-top: 2rem;
        }

        .password-section h2 {
            color: #1a1a1a;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #eef1ff;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6b7aff;
            box-shadow: 0 0 0 3px rgba(107, 122, 255, 0.1);
        }

        .update-btn {
            width: 100%;
            padding: 1rem;
            background: #6b7aff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .update-btn:hover {
            background: #5563e8;
            transform: translateY(-1px);
        }

        .nav-links {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        .nav-links a {
            color: #6b7aff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #5563e8;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 1.5rem;
            }

            .profile-header h1 {
                font-size: 1.75rem;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>User Profile</h1>
        </div>

        <div class="profile-info">
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value"><?= htmlspecialchars($user["name"]) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date of Birth:</span>
                <span class="info-value"><?= htmlspecialchars($user["dob"]) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Profession:</span>
                <span class="info-value"><?= htmlspecialchars($user["profession"]) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Username:</span>
                <span class="info-value"><?= htmlspecialchars($user["username"]) ?></span>
            </div>
        </div>

        <div class="password-section">
            <h2>Update Password</h2>
            <form method="post">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <button type="submit" class="update-btn">Update Password</button>
            </form>
        </div>

        <div class="nav-links">
            <a href="index.php">Back to To-Do List</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
