<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cyber_book_store";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
