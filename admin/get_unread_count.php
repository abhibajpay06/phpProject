<?php
session_start();
include("include/function.php");
$conn = connection();

if (!isset($_SESSION['id'])) {
    echo json_encode(["count" => 0]);
    exit;
}

$my_id = $_SESSION['id'];

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS unread 
     FROM messages 
     WHERE receiver_id = '$my_id'
       AND is_read = 0"
);

$row = mysqli_fetch_assoc($q);

echo json_encode([
    "count" => (int)$row['unread']
]);
