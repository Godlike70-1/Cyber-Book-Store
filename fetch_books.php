<?php
include 'config.php';

$query = "SELECT * FROM books";
$result = $conn->query($query);

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode(['availableBooks' => $books]);

$conn->close();
?>
