<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $query = "SELECT full_name, address, city, state, zip_code, country, card_number, expiry_date, cvv FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'info' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No checkout information found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
}
?>
