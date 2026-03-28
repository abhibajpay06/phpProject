<?php
session_start();
include("include/function.php");
$conn = connection();

if (!isset($_SESSION['id'], $_SESSION['type'])) exit;

$my_id   = (int) $_SESSION['id'];
$my_type = $_SESSION['type']; // admin | user

$sql = "

/* ================= ADMIN ================= */
SELECT 
    a.id,
    a.name,
    'admin' AS usertype,

    (
        SELECT COUNT(*) 
        FROM messages m
        WHERE 
            m.sender_id = a.id
            AND m.sender_type = 'admin'
            AND m.receiver_id = $my_id
            AND m.receiver_type = '$my_type'
            AND m.is_read = 0
    ) AS unread_count,

    (
        SELECT MAX(created_at)
        FROM messages m2
        WHERE 
            (
                m2.sender_id = a.id AND m2.sender_type = 'admin'
                AND m2.receiver_id = $my_id AND m2.receiver_type = '$my_type'
            )
            OR
            (
                m2.sender_id = $my_id AND m2.sender_type = '$my_type'
                AND m2.receiver_id = a.id AND m2.receiver_type = 'admin'
            )
    ) AS last_msg

FROM admin_login a

UNION ALL

/* ================= USERS ================= */
SELECT 
    u.id,
    u.name,
    'user' AS usertype,

    (
        SELECT COUNT(*) 
        FROM messages m
        WHERE 
            m.sender_id = u.id
            AND m.sender_type = 'user'
            AND m.receiver_id = $my_id
            AND m.receiver_type = '$my_type'
            AND m.is_read = 0
    ) AS unread_count,

    (
        SELECT MAX(created_at)
        FROM messages m2
        WHERE 
            (
                m2.sender_id = u.id AND m2.sender_type = 'user'
                AND m2.receiver_id = $my_id AND m2.receiver_type = '$my_type'
            )
            OR
            (
                m2.sender_id = $my_id AND m2.sender_type = '$my_type'
                AND m2.receiver_id = u.id AND m2.receiver_type = 'user'
            )
    ) AS last_msg

FROM user_login u
WHERE NOT (u.id = $my_id AND '$my_type' = 'user')

ORDER BY last_msg DESC
";

$res = mysqli_query($conn, $sql);
if (!$res) exit;

while ($u = mysqli_fetch_assoc($res)) {
?>
<div class="user-item d-flex justify-content-between align-items-center"
     data-userid="<?= $u['id'] ?>"
     data-usertype="user">

    <div>
        <strong><?= $u['name'] ?></strong>

        <?php if ($u['typing'] == 1) { ?>
            <div class="typing-text text-muted" style="font-size:12px;">
                typing...
            </div>
        <?php } ?>
    </div>

    <?php if ($u['unread_count'] > 0) { ?>
        <span class="badge bg-danger">
            <?= $u['unread_count'] ?>
        </span>
    <?php } ?>

</div>
<?php } ?>

