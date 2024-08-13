<?php
// Include the database configuration file
include 'config.php'; // This should contain your database connection setup

// Clear the cart
$query = "DELETE FROM cart";
if ($conn->query($query) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
