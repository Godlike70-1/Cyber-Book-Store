<?php
include 'config.php'; // Include database connection

$message = ''; // Initialize the $message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Check for duplicate entry based on title
    $check_stmt = $conn->prepare("SELECT * FROM books WHERE title = ?");
    $check_stmt->bind_param('s', $title);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Book already exists
        $message = "Book is already added.";
    } else {
        // Handle the file upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/'; // Directory to save uploaded files
            $cover_image = basename($_FILES['cover_image']['name']);
            $target_file = $upload_dir . $cover_image;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_file)) {
                // Insert the book into the database
                $stmt = $conn->prepare("INSERT INTO books (title, author, price, description, cover_image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('ssdss', $title, $author, $price, $description, $target_file);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $message = "Book added successfully.";
                } else {
                    $message = "Failed to add book.";
                }

                $stmt->close();
            } else {
                $message = "Failed to upload cover image.";
            }
        } else {
            $message = "No cover image uploaded.";
        }
    }

    $check_stmt->close();
    $conn->close();
} else {
    $message = "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .message-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .message-box p {
            margin: 0 0 20px;
            font-size: 18px;
            color: <?php echo ($message === "Book is already added.") ? "red" : "black"; ?>;
        }
        .message-box button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .message-box button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <p><?php echo htmlspecialchars($message); ?></p>
        <button onclick="navigateBack()">OK</button>
    </div>
    
    <script>
        function navigateBack() {
            window.location.href = 'admin.php';
        }
    </script>
</body>
</html>
