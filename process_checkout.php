<?php
session_start();
include 'config.php';  // Make sure your database connection settings are correct

if (isset($_POST['submit_checkout'])) {
    // Retrieve data from form
    $user_id = $_SESSION['user_id'] ?? die('User not logged in.');
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $zip = $_POST['zip'];
    $payment_method = $_POST['payment_method'];
    $credit_card_number = $_POST['credit_card_number'];
    $cvv = $_POST['cvv'];
    $expiry_date = $_POST['expiry_date'];

    // Prepare an INSERT statement
    $stmt = $conn->prepare("INSERT INTO checkout_info (user_id, fullname, address, city, country, zip, payment_method, credit_card_number, cvv, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssssss', $user_id, $fullname, $address, $city, $country, $zip, $payment_method, $credit_card_number, $cvv, $expiry_date);

    if ($stmt->execute()) {
        echo "Checkout information saved successfully.";
        // Further logic after successful insertion
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
