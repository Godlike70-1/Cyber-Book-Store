<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the statement to check credentials
    if ($stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE username = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password_hash);
            $stmt->fetch();

            if (password_verify($password, $password_hash)) {
                $_SESSION['admin_id'] = $id;
                header("Location: admin.php");
                exit();
            } else {
                $error = "Invalid credentials";
            }
        } else {
            $error = "Invalid credentials";
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
    <title>Admin Login</title>
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
            overflow: hidden;
        }

        .login-container {
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

        input[type="text"], input[type="password"], input[type="submit"] {
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 8px rgba(110, 142, 251, 0.6);
            outline: none;
        }

        input[type="submit"] {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        input[type="submit"]:hover {
            background: linear-gradient(135deg, #5a75e6, #946cd1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .register-link, .back-button {
            margin-top: 20px;
        }

        .back-button a {
            color: #6e8efb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .back-button a:hover {
            color: #a777e3;
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="admin_login.php">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
            <div class="back-button">
                <a href="index.php">Back</a>
            </div>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="admin_reg.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
