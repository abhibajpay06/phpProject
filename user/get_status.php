<?php
session_start();
require("include/function.php");

$conn = connection();
$registration_no = $_SESSION['registration_no'];
$today = date("Y-m-d");

$sql = "SELECT * FROM attendance WHERE registration_no = '$registration_no' AND date = '$today'";
$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) == 0) {
    echo json_encode(["status" => "not_punched_in"]);
    exit;
}

$data = mysqli_fetch_assoc($res);

if ($data['punch_in'] != NULL && $data['punch_out'] == NULL) {
    echo json_encode([
        "status" => "punched_in",
        "punch_in" => $data['punch_in']
    ]);
    exit;
}

echo json_encode([
    "status" => "completed",
    "punch_in" => $data['punch_in'],
    "punch_out" => $data['punch_out']
]);
?>
