<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check the number of registered admins
    $result = $conn->query("SELECT COUNT(*) as count FROM admins");
    $row = $result->fetch_assoc();
    $admin_count = $row['count'];

    if ($admin_count < 2) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Server-side password validation
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || 
            !preg_match('/[\W_]/', $password)) {
            $error = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hash);
            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: admin_login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Admin registration limit reached.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Cyber Book Store</title>
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
        .back-button {
            margin-top: 15px;
            color: #6e8efb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .back-button:hover {
            color: #a777e3;
            text-decoration: underline;
        }
    </style>
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var error = "";
            if (password.length < 8) {
                error = "Password must be at least 8 characters long.";
            } else if (!/[A-Z]/.test(password)) {
                error = "Password must contain at least one uppercase letter.";
            } else if (!/[a-z]/.test(password)) {
                error = "Password must contain at least one lowercase letter.";
            } else if (!/[0-9]/.test(password)) {
                error = "Password must contain at least one number.";
            } else if (!/[\W_]/.test(password)) {
                error = "Password must contain at least one special character.";
            }

            if (error) {
                document.getElementById("error").innerText = error;
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <h2>Register as Admin</h2>
        <?php if (!empty($error)): ?>
            <p class="error" id="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="admin_reg.php" onsubmit="return validateForm()">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="submit" value="Register">
        </form>
        <a href="index.php" class="back-button">Back to Home</a>
    </div>
</body>
</html>
