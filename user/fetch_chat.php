<?php
session_start();
include("include/function.php");

$conn = connection();

// Sender ID = admin logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "Admin not logged in"]);
    exit;
}

$sender_id = $_SESSION['id'];  // ADMIN ID

// Receiver ID = user clicked from sidebar
if (!isset($_POST['receiver_id'])) {
    echo json_encode(["error" => "Receiver ID missing"]);
    exit;
}

$receiver_id = $_POST['receiver_id'];

// Fetch chat messages
$sql = "SELECT * FROM messages 
        WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id')
        OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id')
        ORDER BY timestamp ASC";

$result = mysqli_query($conn, $sql);

$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
