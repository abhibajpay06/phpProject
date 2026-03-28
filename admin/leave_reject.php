<?php
session_start();
require("include/function.php");

if (isset($_GET['id'])) {
    $leave_id = $_GET['id'];
    rejectLeaveApplication($leave_id); // pass ID only
}
?>
