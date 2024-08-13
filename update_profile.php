<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id']; // Assuming the user_id is stored in the session
$username = $_POST['username'];
$password = $_POST['password'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET username = ?, password_hash = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $username, $hashed_password, $user_id);

$response = [];

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
