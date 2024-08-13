<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT username, email, profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$query_cart = "SELECT title, price FROM books INNER JOIN cart ON books.book_id = cart.book_id WHERE cart.user_id = ?";
$stmt_cart = $conn->prepare($query_cart);
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$cart_books = $stmt_cart->get_result()->fetch_all(MYSQLI_ASSOC);

$query_bought = "SELECT title, price FROM books INNER JOIN orders ON books.book_id = orders.book_id WHERE orders.user_id = ?";
$stmt_bought = $conn->prepare($query_bought);
$stmt_bought->bind_param("i", $user_id);
$stmt_bought->execute();
$bought_books = $stmt_bought->get_result()->fetch_all(MYSQLI_ASSOC);

$response = [
    'success' => true,
    'info' => [
        'username' => $user['username'],
        'email' => $user['email'],
        'profile_picture' => $user['profile_picture'],
        'cart_books' => $cart_books,
        'bought_books' => $bought_books
    ]
];

echo json_encode($response);
?>
