<?php
session_start();
include 'config.php';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
} else {
    $error = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check for existing username or email
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Username or email already in use. Please choose another.";
    } else {
        // Validate the password strength
        if (strlen($password) < 8 || !preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/\W/", $password)) {
            $_SESSION['error'] = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one symbol.";
        } else {
            // Save user to the database
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sss", $username, $email, $password_hash);

            if ($insertStmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "An error occurred during registration.";
            }
            $insertStmt->close();
        }
    }
    $stmt->close();
    header("Location: register.php"); // Redirect to the same page to avoid resubmission
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cyber Book Store</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
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
        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 8px rgba(110, 142, 251, 0.6);
            outline: none;
        }
        input[type="submit"] {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            border: none;
            padding: 12px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        input[type="submit"]:hover {
            background: linear-gradient(135deg, #5a75e6, #946cd1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .admin-link {
            margin-top: 20px;
        }
        a {
            color: #6e8efb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #a777e3;
            text-decoration: underline;
        }
        .back-button {
            background: #ccc;
            color: black;
            border: none;
            padding: 10px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            background: #bbb;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register for an Account</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Register">
        </form>
        <a href="index.php" class="back-button">Back</a>
        <div class="admin-link">
            <a href="admin_reg.php">Admin Registration</a>
        </div>
    </div>
</body>
</html>
