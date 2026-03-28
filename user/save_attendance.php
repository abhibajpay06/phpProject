<?php
session_start();
require("include/function.php");

$conn = connection();

if (!isset($_SESSION['id'])) {
    header("Location: index");
    exit;
}
$registration_no = $_SESSION['registration_no'];
$today = date("Y-m-d");
$now = date("H:i:s");

// Check today's entry
$sql = "SELECT * FROM attendance WHERE registration_no='$registration_no' AND date='$today'";
$res = mysqli_query($conn, $sql);

// FIRST PUNCH → PUNCH IN
if (mysqli_num_rows($res) == 0) {

    mysqli_query($conn, "
        INSERT INTO attendance (registration_no, date, punch_in)
        VALUES ('$registration_no', '$today', '$now')
    ");

    echo "punch_in_done";
    exit;
}

$data = mysqli_fetch_assoc($res);

// SECOND PUNCH → PUNCH OUT
if ($data['punch_in'] != NULL && $data['punch_out'] == NULL) {

    mysqli_query($conn, "
        UPDATE attendance 
        SET punch_out='$now'
        WHERE id = '".$data['id']."'
    ");

    echo "punch_out_done";
    exit;
}

// Already completed
echo "done";
?>
