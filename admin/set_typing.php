<?php
session_start();
include("include/function.php");
$conn = connection();

$id   = $_SESSION['id'];
$type = $_SESSION['type']; // admin/user
$typing = $_POST['typing'];

$table = ($type == 'admin') ? 'admin_login' : 'user_login';

mysqli_query($conn, "UPDATE $table SET typing='$typing' WHERE id='$id'");
