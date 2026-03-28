<?php 
session_start();
require("include/function.php");


if (isset($_GET['id'])) {
    $leave_id = $_GET['id'];
    approveLeaveApplication($leave_id); 
}
?>
