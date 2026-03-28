<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include("include/function.php");
$conn = connection();


header("Content-Type: application/json");

/* ================= AUTH ================= */
if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "msg" => "Not logged in"]);
    exit;
}

/* ================= VARIABLES ================= */
$sender_id   = (int) $_SESSION['id'];
$sender_type = $_SESSION['type'] ?? 'user';

$receiver_id   = (int) ($_POST['receiver_id'] ?? 0);
$receiver_type = $_POST['receiver_type'] ?? '';

$message    = trim($_POST['message'] ?? '');
$filePath   = '';
$created_at = date('Y-m-d H:i:s');

if ($receiver_id <= 0 || empty($receiver_type)) {
    echo json_encode(["status" => "error", "msg" => "Invalid receiver"]);
    exit;
}

/* ================= FILE UPLOAD ================= */
if (!empty($_FILES['file']['name'])) {

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['file']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        echo json_encode(["status" => "error", "msg" => "File upload failed"]);
        exit;
    }
}

/* ================= INSERT ================= */
$sql = "INSERT INTO messages
(sender_id, sender_type, receiver_id, receiver_type, message, status, file_path, created_at)
VALUES (?, ?, ?, ?, ?, 'sent', ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "msg" => $conn->error]);
    exit;
}

$stmt->bind_param(
    "isissss", 
    $sender_id,
    $sender_type,
    $receiver_id,
    $receiver_type,
    $message,
    $filePath,
    $created_at
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => $stmt->error]);
}
