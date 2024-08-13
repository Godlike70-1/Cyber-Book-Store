<?php
session_start();
include 'config.php'; // Ensure this file contains the correct database connection setup

$messageDisplayed = false; // Flag to track if a message is displayed

// Check if the form data is posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $messageContent = $_POST['message'] ?? '';

    // Prepare SQL Query
    $query = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssss", $name, $email, $subject, $messageContent);

        // Execute and check
        if ($stmt->execute()) {
            $messageDisplayed = true; // Set the flag to true on success
            $successMessage = "Your message has been sent to admin.";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorMessage = "Error preparing statement: " . $conn->error;
    }

    $conn->close();
} else {
    $errorMessage = "No data received.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submission Result</title>
    <style>
        .success-message, .error-message {
            color: green; /* Default for success */
            text-align: center;
            margin-top: 20px;
        }
        .error-message {
            color: red; /* Error color */
        }
    </style>
</head>
<body>
    <div class="w3-container" style="width: 300px; margin: auto; padding-top: 50px;">
        <?php if ($messageDisplayed): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php else: ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <button onclick="window.location.href='dashboard.php'" style="display: block; width: 100%; padding: 10px 0; background: #4CAF50; color: white; border: none; cursor: pointer;">
            OK
        </button>
    </div>
</body>
</html>
