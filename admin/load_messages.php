<?php
session_start();
include("include/function.php");
$conn = connection();
date_default_timezone_set('Asia/Kolkata');

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['id'], $_SESSION['type'])) {
    exit;
}

$my_id   = (int) $_SESSION['id'];
$my_type = $_SESSION['type'];

if (!isset($_POST['receiver_id'], $_POST['receiver_type'])) {
    exit;
}

$receiver_id   = (int) $_POST['receiver_id'];
$receiver_type = $_POST['receiver_type'];

/* ================= MARK MESSAGES AS SEEN ================= */
mysqli_query($conn, "
    UPDATE messages SET status = 'seen', is_read = 1 WHERE sender_id = $receiver_id AND sender_type = '$receiver_type' AND receiver_id = $my_id AND receiver_type = '$my_type'");

/* ================= FETCH CHAT ================= */
$sql = "SELECT id, sender_id, sender_type, receiver_id, receiver_type, message, file_path, status, created_at FROM messages
		WHERE (sender_id = $my_id AND sender_type = '$my_type' AND receiver_id = $receiver_id AND receiver_type = '$receiver_type')
        OR(sender_id = $receiver_id AND sender_type = '$receiver_type' AND receiver_id = $my_id AND receiver_type = '$my_type' ) ORDER BY id ASC";

$result = mysqli_query($conn, $sql);
if (!$result) exit;

/* ================= DISPLAY ================= */
$lastDate = '';

while ($row = mysqli_fetch_assoc($result)) {

    /* ---------- DATE SEPARATOR ---------- */
    $msgDate = date('Y-m-d', strtotime($row['created_at']));

    if ($msgDate !== $lastDate) {

        if ($msgDate === date('Y-m-d')) {
            $label = "Today";
        } elseif ($msgDate === date('Y-m-d', strtotime('-1 day'))) {
            $label = "Yesterday";
        } else {
            $label = date('d M Y', strtotime($msgDate));
        }

        echo "
        <div class='text-center my-2'>
            <span style='background:#e0e0e0;padding:5px 14px;border-radius:20px;font-size:12px'>
                $label
            </span>
        </div>
        ";

        $lastDate = $msgDate;
    }

    /* ---------- MESSAGE ALIGN ---------- */
    $isMe     = ($row['sender_id'] == $my_id);
    $rowClass = $isMe ? 'me' : 'other';
    $msgClass = $isMe ? 'msg me' : 'msg other';

    /* ---------- MESSAGE CONTENT ---------- */
    $message = nl2br(htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'));
    $time    = date("h:i A", strtotime($row['created_at']));

    /* ---------- STATUS TICKS ---------- */
    $tick = '';
    if ($isMe) {
        if ($row['status'] === 'sent') {
            $tick = '✓';
        } elseif ($row['status'] === 'delivered') {
            $tick = '✓✓';
        } elseif ($row['status'] === 'seen') {
            $tick = '<span style="color:#4fc3f7">✓✓</span>';
        }
    }

    /* ---------- OUTPUT ---------- */
    echo "
    <div class='message-row $rowClass'>
        <div class='$msgClass'>
            <p>$message</p>
    ";

    /* ---------- FILE ATTACHMENT ---------- */
    if (!empty($row['file_path'])) {
        $filePath = htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8');
        echo "<a href='$filePath' target='_blank' class='d-block mt-1 text-light'>View File</a>";
    }

    echo "
            <span class='timestamp'>$time $tick</span>
        </div>
    </div>
    ";
}
?>
