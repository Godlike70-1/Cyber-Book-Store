<?php
session_start();
include 'config.php';

// Initialize a variable to hold messages for user feedback
$message = $_SESSION['message'] ?? '';

// Clear the message after it has been displayed once
if (!empty($message)) {
    unset($_SESSION['message']);
}

// Handling logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Processing form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $message = $stmt->affected_rows > 0 ? "User deleted successfully." : "Failed to delete user. User ID might not exist.";
        $stmt->close();
    } elseif (isset($_POST['delete_book'])) {
        $book_id = $_POST['book_id'];
        $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        $message = $stmt->affected_rows > 0 ? "Book deleted successfully." : "Failed to delete book. Book ID might not exist.";
        $stmt->close();
    } elseif (isset($_POST['approve_message'])) {
        $message_id = $_POST['message_id'];
        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();

        $message = $stmt->affected_rows > 0 ? "Message approved and removed from list." : "Failed to approve message.";
        $stmt->close();
    } elseif (isset($_POST['update_admin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $admin_id = $_SESSION['admin_id'];

        $update_stmt = $conn->prepare("UPDATE admins SET username=?, password_hash=? WHERE id=?");
        $update_stmt->bind_param("ssi", $username, $hashed_password, $admin_id);
        $update_stmt->execute();

        $message = $update_stmt->affected_rows > 0 ? "Admin credentials updated successfully." : "No changes made or update failed.";
        $update_stmt->close();
    }
    $_SESSION['message'] = $message;
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent form resubmission
    exit();
}

// Fetch data
$users = $conn->query("SELECT user_id, username FROM users")->fetch_all(MYSQLI_ASSOC);
$books = $conn->query("SELECT book_id, title FROM books")->fetch_all(MYSQLI_ASSOC);
$messages = $conn->query("SELECT id, name, email, subject, message, created_at FROM contacts")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users and Books</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            font-family: 'Roboto', sans-serif;
            background: #f4f4f9;
            margin: 0;
        }
        .management-section, .messages-section {
            width: 90%;
            max-width: 800px;
            padding: 20px;
            background: #ffffff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .management-section:hover, .messages-section:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            width: 100%;
            background-color: #333;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: white;
        }
        .navbar .brand {
            font-size: 20px;
            font-weight: 500;
        }
        .navbar .actions {
            display: flex;
            gap: 10px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #007BFF;
            border-radius: 5px;
            transition: background-color 0.2s;
        }
        .navbar a:hover {
            background-color: #0056b3;
        }
        ul {
            list-style: none;
            padding: 0;
        }

        li {
            padding: 8px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li:last-child {
            border-bottom: none;
        }

        form {
            margin: 0;
        }

        .delete-btn {
            color: white;
            background-color: #28a745;
            border: none;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .delete-btn:hover {
            background-color: #218838;
        }

        input[type="text"], input[type="password"], input[type="file"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            width: calc(100% - 22px);
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus, input[type="file"]:focus {
            border-color: #0056b3;
        }

        label {
            width: 100%;
        }

        /* Message display */
        .message-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            max-width: 300px;
            width: 100%;
        }
        .message-box.success {
            border-left: 4px solid #28a745;
        }
        .message-box.error {
            border-left: 4px solid #dc3545;
        }
        .message-box p {
            margin: 0;
            padding: 0;
            font-size: 16px;
        }
        .message-box button {
            margin-top: 15px;
            padding: 8px 16px;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .message-box button:hover {
            background-color: #0056b3;
        }
        .add-btn{
            color: white;
            background-color: #28a745;
            border: none;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .add-btn:hover{
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Cyber Book Store (Admin)</div>
        <div class="actions">
            <a href="?update=true">Update Account</a>
            <a href="?logout=true">Logout</a>
        </div>
    </div>
    <?php if (isset($_GET['update'])): ?>
    <div class="management-section">
        <h2>Update Admin Account</h2>
        <form method="post">
            <label for="username">New Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="update_admin" class="add-btn">Update</button>
        </form>
    </div>
    <?php endif; ?>
    <div class="management-section" id="users">
        <h2>User Management</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <?php echo htmlspecialchars($user['username']); ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                        <button type="submit" name="delete_user" class="delete-btn">Delete User</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="management-section" id="books">
        <h2>Book Management</h2>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <?php echo htmlspecialchars($book['title']); ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <button type="submit" name="delete_book" class="delete-btn">Delete Book</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="management-section" id="add-books" >
        <h2>Add New Book</h2>
        <form enctype="multipart/form-data" action="upload_book.php" method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>
            <label for="description">Description:</label>
            <input type="text" id="description" name="description" required>
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required>
            <label for="cover_image">Cover Image:</label>
            <input type="file" id="cover_image" name="cover_image" required>
            <button type="submit" name="add_book" class="add-btn">Add Book</button>
        </form>
    </div>
    <div class="messages-section">
        <h2>Contact Messages</h2>
        <ul>
            <?php foreach ($messages as $msg): ?>
                <li>
                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong> (<?php echo htmlspecialchars($msg['email']); ?>)
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?></p>
                    <p><?php echo htmlspecialchars($msg['message']); ?></p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" name="approve_message" class="delete-btn">Approve</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Message Display -->
    <?php if (!empty($message)): ?>
        <div class="message-box <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <p><?php echo $message; ?></p>
            <button onclick="this.parentElement.style.display='none';">OK</button>
        </div>
    <?php endif; ?>
</body>
</html>


