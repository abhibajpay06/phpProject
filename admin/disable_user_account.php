<?php 
session_start();
require("include/function.php");


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    disableUser($id); 
}
?>
