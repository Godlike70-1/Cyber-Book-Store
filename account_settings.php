<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Clear the session message after displaying

// Function to redirect and set message
function redirectWithMessage($url, $message) {
    $_SESSION['message'] = $message;
    header('Location: ' . $url);
    exit();
}

// Handle account settings updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_username'])) {
        $new_username = $_POST['new_username'];
        $stmt = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $admin_id);
        if ($stmt->execute()) {
            redirectWithMessage('account_settings.php', 'success:Username updated successfully.');
        } else {
            redirectWithMessage('account_settings.php', 'error:Error updating username: ' . $stmt->error);
        }
    } elseif (isset($_POST['change_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password, $admin_id);
        if ($stmt->execute()) {
            redirectWithMessage('account_settings.php', 'success:Password updated successfully.');
        } else {
            redirectWithMessage('account_settings.php', 'error:Error updating password: ' . $stmt->error);
        }
    } elseif (isset($_POST['delete_account'])) {
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: admin_login.php");
            exit();
        } else {
            redirectWithMessage('account_settings.php', 'error:Error deleting account: ' . $stmt->error);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #485563 0%, #29323c 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            animation: colorShift 10s ease-in-out infinite;
        }
        main {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 360px; /* Adjusted width */
            width: 100%;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        form {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label, input, button {
            display: block;
            width: calc(100% - 24px); /* Adjusted width for padding */
            margin-bottom: 15px;
        }
        input, button {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: all 0.3s ease;
        }
        input:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 8px rgba(93, 156, 236, 0.6);
            outline: none;
        }
        button {
            background-color: #6e8efb;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #555;
        }
        button[name="delete_account"]:hover {
            background-color: #e74c3c; /* Red color for delete account button hover */
        }
        .message, .error {
            padding: 10px;
            color: #fff;
            border-radius: 8px;
            margin-top: 20px;
        }
        .message {
            background-color: #4a89dc;
        }
        .error {
            background-color: #da4453;
        }
        a.back-button {
            display: inline-block;
            background-color: #666;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        a.back-button:hover {
            background-color: #777;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes colorShift {
            0%, 100% {
                background: linear-gradient(135deg, #485563, #29323c);
            }
            50% {
                background: linear-gradient(135deg, #29323c, #485563);
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>Account Settings</h1>
        <?php if (!empty($message)) {
            list($type, $text) = explode(':', $message, 2);
            echo "<p class='" . ($type == 'success' ? 'message' : 'error') . "'>$text</p>";
        } ?>
        <form action="account_settings.php" method="POST">
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username" required>
            <button type="submit" name="change_username">Change Username</button>
        </form>
        <form action="account_settings.php" method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="submit" name="change_password">Change Password</button>
        </form>
        <form action="account_settings.php" method="POST">
            <button type="submit" name="delete_account" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');">Delete Account</button>
        </form>
        <a href="admin.php" class="back-button">Back to Dashboard</a>
    </main>
</body>
</html>
