<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the statement
    if ($stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE username = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $password_hash);
        
        if ($stmt->fetch() && password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
    } else {
        $error = "Database query failed.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cyber Book Store</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            animation: slideIn 1s ease-in-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
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
        input[type="text"], input[type="password"] {
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #71b7e6;
            box-shadow: 0 0 8px rgba(113, 183, 230, 0.6);
            outline: none;
        }
        input[type="submit"] {
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
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
            background: linear-gradient(135deg, #5a98d0, #8e44ad);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .register-link, .back-button {
            margin-top: 15px;
        }
        a {
            color: #71b7e6;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #9b59b6;
            text-decoration: underline;
        }
        .back-button a {
            background-color: #ccc;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button a:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Your Account</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        <div class="back-button">
            <a href="index.php">Back</a>
        </div>
    </div>
</body>
</html>
